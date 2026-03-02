<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplyRequest;
use App\Models\Supply;
use App\Models\RequestItem;
use App\Models\ReleaseTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;

class ReleaseController extends Controller
{
    public function index()
    {
        $pendingReleases = SupplyRequest::with(['user', 'department', 'items.supply', 'managerApprover'])
            ->where('status', 'manager_approved')
            ->latest('manager_approved_at')
            ->paginate(20, ['*'], 'pending_page');

        $releasedToday = ReleaseTransaction::with([
                'supplyRequest.user',
                'supplyRequest.department',
                'releasedBy',
            ])
            ->whereDate('created_at', today())
            ->latest('created_at')
            ->paginate(20, ['*'], 'today_page');

        $releaseHistory = ReleaseTransaction::with([
                'supplyRequest.user',
                'supplyRequest.department',
                'releasedBy',
            ])
            ->latest('created_at')
            ->paginate(20, ['*'], 'history_page');

        return view('admin.releases.index', compact('pendingReleases', 'releasedToday', 'releaseHistory'));
    }

    public function show($id)
    {
        $request = SupplyRequest::with([
            'user',
            'department',
            'items.supply',
            'managerApprover',
            'adminReleaser',
            'releaseTransactions.releasedBy',
        ])->findOrFail($id);

        return view('admin.releases.show', compact('request'));
    }

    public function details($id)
    {
        $supplyRequest = SupplyRequest::with([
            'user',
            'department',
            'items.supply',
            'managerApprover'
        ])->findOrFail($id);

        if ($supplyRequest->status !== 'manager_approved') {
            return response()->json([
                'success' => false,
                'message' => 'This request is not available for release.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'request' => [
                'id'                       => $supplyRequest->id,
                'sr_number'                => $supplyRequest->sr_number,
                'request_type'             => $supplyRequest->request_type,
                'budget_type'              => $supplyRequest->budget_type,
                'special_item_description' => $supplyRequest->special_item_description,
                'department' => [
                    'name' => $supplyRequest->department->name,
                    'code' => $supplyRequest->department->code,
                ],
                'user' => [
                    'name'  => $supplyRequest->user->name,
                    'email' => $supplyRequest->user->email,
                ],
                'items' => $supplyRequest->items->map(function($item) {
                    $remaining = $item->remaining_quantity ?? $item->quantity;
                    $released  = $item->released_quantity  ?? 0;
                    $original  = $item->original_quantity  ?? $item->quantity;

                    return [
                        'id'                => $item->id,
                        'item_name'         => $item->item_name,
                        'item_code'         => $item->item_code,
                        'original_quantity' => $original,
                        'quantity'          => $remaining,
                        'released_quantity' => $released,
                        'supply' => $item->supply ? [
                            'stock_quantity' => $item->supply->stock_quantity,
                            'unit'           => $item->supply->unit,
                        ] : null,
                    ];
                }),
            ]
        ]);
    }

   private function generateSerialNumber($departmentId, $departmentCode)
{
    $yearMonth = now()->format('Ym');
    $prefix    = "{$departmentCode}-{$yearMonth}-";

    // Find the highest existing serial number for this dept/month
    $last = DB::table('supply_requests')
        ->where('serial_number', 'like', "{$prefix}%")
        ->orderByRaw('CAST(SUBSTRING_INDEX(serial_number, "-", -1) AS UNSIGNED) DESC')
        ->value('serial_number');

    $nextNumber = 1;
    if ($last) {
        $parts      = explode('-', $last);
        $nextNumber = ((int) end($parts)) + 1;
    }

    // Keep incrementing until we find one that doesn't exist
    while (DB::table('supply_requests')->where('serial_number', $prefix . sprintf('%04d', $nextNumber))->exists()) {
        $nextNumber++;
    }

    return sprintf('%s%04d', $prefix, $nextNumber);
}

    public function release(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'notes'                       => 'nullable|string|max:500',
            'ro_number'                   => 'required|string|max:100',
            'allocations'                 => 'nullable|array',
            'allocations.*.item_id'       => 'required|exists:request_items,id',
            'allocations.*.allocated_qty' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Check status before opening transaction
        $check = SupplyRequest::findOrFail($id);
        if ($check->status !== 'manager_approved') {
            return response()->json([
                'success' => false,
                'message' => 'Only manager-approved requests can be released. Current status: ' . $check->status,
            ], 422);
        }

        // Pull allocations before transaction
        $allocations = $request->input('allocations', []);

        DB::beginTransaction();
        try {
            // Reload with lock INSIDE the transaction
            $supplyRequest = SupplyRequest::with(['items.supply', 'department', 'user'])
                ->lockForUpdate()
                ->findOrFail($id);

            $hasUnfulfilledItems = false;
            $totalItems          = 0;
            $totalFullyDone      = 0;
            $itemsSnapshot       = [];

            $itemIds    = $supplyRequest->items->pluck('id')->toArray();
            $freshItems = DB::table('request_items')
                ->whereIn('id', $itemIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($supplyRequest->items as $item) {
                $totalItems++;
                $fresh = $freshItems[$item->id];

                $originalQty      = (int) ($fresh->original_quantity ?? $fresh->quantity);
                $previousReleased = (int) ($fresh->released_quantity ?? 0);
                // Always recalculate remaining from source of truth — never trust remaining_quantity directly
                $currentRemaining = $originalQty - $previousReleased;

                $allocation   = collect($allocations)->firstWhere('item_id', $item->id);
                $allocatedQty = $allocation !== null
                    ? (int) $allocation['allocated_qty']
                    : $currentRemaining;

                if ($allocatedQty > $currentRemaining) {
                    throw new \Exception(
                        "Cannot allocate {$allocatedQty} for '{$fresh->item_name}'. Only {$currentRemaining} remaining."
                    );
                }

                $stockBefore = 0;
                if ($fresh->supply_id && $allocatedQty > 0) {
                    $stockBefore = DB::table('supplies')
                        ->where('id', $fresh->supply_id)
                        ->lockForUpdate()
                        ->value('stock_quantity') ?? 0;

                    if ($allocatedQty > $stockBefore) {
                        throw new \Exception(
                            "Insufficient stock for '{$fresh->item_name}'. Available: {$stockBefore}, requested: {$allocatedQty}."
                        );
                    }
                }

                $newReleased  = $previousReleased + $allocatedQty;
                $newRemaining = $currentRemaining - $allocatedQty;
                $isFullyDone  = ($newRemaining === 0);

                if ($isFullyDone) {
                    $totalFullyDone++;
                } else {
                    $hasUnfulfilledItems = true;
                }

                $itemUpdate = [
                    'original_quantity'  => $originalQty,
                    'released_quantity'  => $newReleased,
                    'remaining_quantity' => $newRemaining,
                    'updated_at'         => now(),
                ];

                $riColumns = Schema::getColumnListing('request_items');
                if (in_array('allocated_quantity', $riColumns)) {
                    $itemUpdate['allocated_quantity'] = $allocatedQty;
                }
                if (in_array('is_fully_allocated', $riColumns)) {
                    $itemUpdate['is_fully_allocated'] = $isFullyDone ? 1 : 0;
                }

                DB::table('request_items')->where('id', $fresh->id)->update($itemUpdate);

                $stockAfter = $stockBefore;
                if ($allocatedQty > 0 && $fresh->supply_id) {
                    DB::table('supplies')
                        ->where('id', $fresh->supply_id)
                        ->decrement('stock_quantity', $allocatedQty);

                    $stockAfter = $stockBefore - $allocatedQty;

                    $this->logAudit([
                        'action'        => 'inventory_adjusted',
                        'model_type'    => Supply::class,
                        'model_id'      => $fresh->supply_id,
                        'description'   => "Stock adjusted: {$fresh->item_name} reduced by {$allocatedQty} units",
                        'old_values'    => ['stock_quantity' => $stockBefore],
                        'new_values'    => ['stock_quantity' => $stockAfter],
                        'metadata'      => [
                            'supply_request_id' => $supplyRequest->id,
                            'quantity_change'   => -$allocatedQty,
                            'released_by'       => auth()->user()->name,
                        ],
                    ]);
                }

                $itemsSnapshot[] = [
                    'item_id'             => $fresh->id,
                    'item_name'           => $fresh->item_name,
                    'item_code'           => $fresh->item_code,
                    'original_requested'  => $originalQty,
                    'qty_released'        => $allocatedQty,
                    'qty_cumulative'      => $newReleased,
                    'qty_remaining_after' => $newRemaining,
                    'stock_before'        => $stockBefore,
                    'stock_after'         => $stockAfter,
                    'status'              => $isFullyDone ? 'fully_done' : ($allocatedQty > 0 ? 'partial' : 'skipped'),
                ];
            }

            $serialNumber = $supplyRequest->serial_number;
            if (!$serialNumber) {
                $serialNumber = $this->generateSerialNumber(
                    $supplyRequest->department_id,
                    $supplyRequest->department->code
                );
            }

            $roundNumber = ReleaseTransaction::where('supply_request_id', $supplyRequest->id)->count() + 1;

            $transaction = ReleaseTransaction::create([
                'supply_request_id'               => $supplyRequest->id,
                'round'                           => $roundNumber,
                'serial_number'                   => $serialNumber,
                'ro_number'                       => $request->ro_number,
                'notes'                           => $request->notes,
                'released_by'                     => auth()->id(),
                'is_final_release'                => !$hasUnfulfilledItems,
                'items_snapshot'                  => $itemsSnapshot,
                'total_items_in_request'          => $totalItems,
                'items_fully_released_this_round' => $totalFullyDone,
                'items_still_pending_after'       => $hasUnfulfilledItems ? ($totalItems - $totalFullyDone) : 0,
            ]);

            if ($hasUnfulfilledItems) {
                DB::table('supply_requests')->where('id', $supplyRequest->id)->update([
                    'serial_number' => $serialNumber,
                    'ro_number'     => $request->ro_number ?? $supplyRequest->ro_number,
                    'admin_notes'   => $request->notes    ?? $supplyRequest->admin_notes,
                    'updated_at'    => now(),
                ]);
            } else {
                DB::table('supply_requests')->where('id', $supplyRequest->id)->update([
                    'status'            => 'admin_released',
                    'admin_released_by' => auth()->id(),
                    'admin_released_at' => now(),
                    'serial_number'     => $serialNumber,
                    'ro_number'         => $request->ro_number,
                    'admin_notes'       => $request->notes,
                    'updated_at'        => now(),
                ]);
            }

            $this->logAudit([
                'action'        => $hasUnfulfilledItems ? 'supply_request_partial_release' : 'supply_request_released',
                'model_type'    => SupplyRequest::class,
                'model_id'      => $supplyRequest->id,
                'description'   => $hasUnfulfilledItems
                    ? "SR#{$supplyRequest->sr_number} partially released — round {$roundNumber} ({$totalFullyDone}/{$totalItems} items done)"
                    : "SR#{$supplyRequest->sr_number} fully released — round {$roundNumber}",
                'old_values'    => ['status' => 'manager_approved'],
                'new_values'    => ['status' => $hasUnfulfilledItems ? 'manager_approved (partial)' : 'admin_released'],
                'metadata'      => [
                    'transaction_id' => $transaction->id,
                    'serial_number'  => $serialNumber,
                    'round'          => $roundNumber,
                    'released_by'    => auth()->user()->name,
                ],
            ]);

            DB::commit();

            return response()->json([
                'success'         => true,
                'message'         => $hasUnfulfilledItems
                    ? 'Partial release saved. Remaining items stay in pending.'
                    : 'Request fully released.',
                'serial_number'   => $serialNumber,
                'ro_number'       => $request->ro_number,
                'allocation_type' => $hasUnfulfilledItems ? 'partial' : 'full',
                'has_remaining'   => $hasUnfulfilledItems,
                'round'           => $roundNumber,
                'transaction_id'  => $transaction->id,
                'voucher_url'     => route('admin.voucher', $supplyRequest->id),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Release error', [
                'request_id' => $id,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process release: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function closeAndRequeue(Request $request, $id)
    {
        $supplyRequest = SupplyRequest::with(['items.supply', 'department', 'user', 'managerApprover'])
            ->lockForUpdate()
            ->findOrFail($id);

        if ($supplyRequest->status !== 'manager_approved') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending requests can be re-queued.',
            ], 422);
        }

        $hasAnyRelease = $supplyRequest->items->contains(fn($i) => ($i->released_quantity ?? 0) > 0);
        if (!$hasAnyRelease) {
            return response()->json([
                'success' => false,
                'message' => 'No items have been released yet. Use Reject instead.',
            ], 422);
        }

        $remainingItems = $supplyRequest->items->filter(fn($i) => ($i->remaining_quantity ?? $i->quantity) > 0);
        if ($remainingItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'All items are already fully released.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            DB::table('supply_requests')->where('id', $supplyRequest->id)->update([
                'status'            => 'admin_released',
                'admin_released_by' => auth()->id(),
                'admin_released_at' => now(),
                'updated_at'        => now(),
            ]);

            $latestSr = DB::table('supply_requests')
                ->where('sr_number', 'like', 'SR-%')
                ->orderByDesc('id')
                ->value('sr_number');

            $nextNum  = $latestSr ? ((int) substr($latestSr, 3)) + 1 : 1;
            $newSrNum = 'SR-' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);

            $newRequestId = DB::table('supply_requests')->insertGetId([
                'sr_number'                => $newSrNum,
                'user_id'                  => $supplyRequest->user_id,
                'department_id'            => $supplyRequest->department_id,
                'request_type'             => $supplyRequest->request_type,
                'budget_type'              => $supplyRequest->budget_type,
                'purpose'                  => $supplyRequest->purpose . ' [Re-queued from ' . $supplyRequest->sr_number . ']',
                'status'                   => 'manager_approved',
                'manager_approved_by'      => $supplyRequest->manager_approved_by,
                'manager_approved_at'      => now(),
                'manager_notes'            => 'Auto re-queued from ' . $supplyRequest->sr_number,
                'special_item_description' => $supplyRequest->special_item_description,
                'parent_sr_id'             => $supplyRequest->id,
                'created_at'               => now(),
                'updated_at'               => now(),
            ]);

            foreach ($remainingItems as $item) {
                $qty = $item->remaining_quantity ?? $item->quantity;

                DB::table('request_items')->insert([
                    'supply_request_id'  => $newRequestId,
                    'supply_id'          => $item->supply_id,
                    'item_name'          => $item->item_name,
                    'item_code'          => $item->item_code,
                    'quantity'           => $qty,
                    'original_quantity'  => $qty,
                    'remaining_quantity' => $qty,
                    'released_quantity'  => 0,
                    'allocated_quantity' => 0,
                    'is_fully_allocated' => 0,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }

            $this->logAudit([
                'action'        => 'supply_request_requeued',
                'model_type'    => SupplyRequest::class,
                'model_id'      => $supplyRequest->id,
                'description'   => "SR#{$supplyRequest->sr_number} closed. Remaining items re-queued as SR#{$newSrNum}.",
                'metadata'      => [
                    'original_sr'    => $supplyRequest->sr_number,
                    'new_sr'         => $newSrNum,
                    'items_requeued' => $remainingItems->count(),
                    'requeued_by'    => auth()->user()->name,
                ],
            ]);

            DB::commit();

            return response()->json([
                'success'       => true,
                'message'       => "Closed. Remaining items re-queued as {$newSrNum}.",
                'new_sr_number' => $newSrNum,
                'new_sr_id'     => $newRequestId,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Requeue error', ['id' => $id, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to re-queue: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $supplyRequest = SupplyRequest::findOrFail($id);

        if ($supplyRequest->status !== 'manager_approved') {
            return response()->json([
                'success' => false,
                'message' => 'Only manager-approved requests can be rejected',
            ], 422);
        }

        try {
            DB::table('supply_requests')->where('id', $supplyRequest->id)->update([
                'status'              => 'admin_rejected',
                'admin_rejected_by'   => auth()->id(),
                'admin_rejected_at'   => now(),
                'admin_notes'         => $request->notes,
                'updated_at'          => now(),
            ]);

            $this->logAudit([
                'action'        => 'supply_request_admin_rejected',
                'model_type'    => SupplyRequest::class,
                'model_id'      => $supplyRequest->id,
                'description'   => "SR#{$supplyRequest->sr_number} rejected by admin",
                'old_values'    => ['status' => 'manager_approved'],
                'new_values'    => ['status' => 'admin_rejected'],
                'metadata'      => [
                    'reason'      => $request->notes,
                    'rejected_by' => auth()->user()->name,
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Request rejected successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Rejection error', ['id' => $id, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject request: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function voucher($id)
    {
        $request = SupplyRequest::with([
            'user',
            'department',
            'items.supply',
            'managerApprover',
            'adminReleaser',
            'releaseTransactions.releasedBy',
        ])->findOrFail($id);

        if (!$request->serial_number) {
            abort(404, 'Voucher not available — no serial number assigned yet.');
        }

        $round           = request()->query('round');
        $allTransactions = $request->releaseTransactions->sortBy('round');
        $focusedTx       = null;

        if ($round) {
            $focusedTx = $allTransactions->firstWhere('round', (int) $round);
            if (!$focusedTx) {
                abort(404, "Release round {$round} not found for this request.");
            }
        }

        $this->logAudit([
            'action'        => 'voucher_viewed',
            'model_type'    => SupplyRequest::class,
            'model_id'      => $request->id,
            'description'   => $round
                ? "Round {$round} voucher viewed for SR#{$request->sr_number}"
                : "Full voucher viewed for SR#{$request->sr_number}",
            'metadata'      => [
                'serial_number' => $request->serial_number,
                'round'         => $round,
                'viewed_by'     => auth()->user()->name,
            ],
        ]);

        return view('admin.voucher', compact('request', 'focusedTx', 'allTransactions', 'round'));
    }

    public function destroyTransaction($txId)
    {
        try {
            $tx = ReleaseTransaction::with('supplyRequest')->findOrFail($txId);

            $this->logAudit([
                'action'        => 'release_transaction_deleted',
                'model_type'    => ReleaseTransaction::class,
                'model_id'      => $tx->id,
                'description'   => "Release transaction deleted: Round {$tx->round} of SR#{$tx->supplyRequest->sr_number} ({$tx->serial_number})",
                'old_values'    => [
                    'serial_number' => $tx->serial_number,
                    'round'         => $tx->round,
                    'sr_number'     => $tx->supplyRequest->sr_number ?? '—',
                ],
                'metadata'      => ['deleted_by' => auth()->user()->name],
            ]);

            $tx->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transaction record deleted.',
            ]);

        } catch (\Exception $e) {
            Log::error('Transaction delete error', ['tx_id' => $txId, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $supplyRequest = SupplyRequest::with(['user', 'department'])->findOrFail($id);

            if ($supplyRequest->status !== 'admin_released') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only fully released requests can be deleted.',
                ], 400);
            }

            $this->logAudit([
                'action'        => 'supply_request_history_deleted',
                'model_type'    => SupplyRequest::class,
                'model_id'      => $supplyRequest->id,
                'description'   => "Release history deleted: SR#{$supplyRequest->sr_number}",
                'old_values'    => $supplyRequest->getAttributes(),
                'metadata'      => ['deleted_by' => auth()->user()->name],
            ]);

            $supplyRequest->delete();

            return response()->json([
                'success' => true,
                'message' => 'Release history deleted successfully.',
            ]);

        } catch (\Exception $e) {
            Log::error('Delete request error', ['id' => $id, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function logAudit(array $data)
    {
        try {
            AuditLog::create([
                'action'        => $data['action'],
                'model_type'    => $data['model_type'],
                'model_id'      => $data['model_id'] ?? null,
                'user_id'       => auth()->id(),
                'department_id' => auth()->user()?->department_id,
                'ip_address'    => request()->ip(),
                'user_agent'    => request()->userAgent(),
                'old_values'    => isset($data['old_values']) ? json_encode($data['old_values']) : null,
                'new_values'    => isset($data['new_values']) ? json_encode($data['new_values']) : null,
                'description'   => $data['description'],
                'metadata'      => isset($data['metadata']) ? json_encode($data['metadata']) : null,
            ]);
        } catch (\Exception $e) {
            Log::warning('Audit log failed: ' . $e->getMessage());
        }
    }
}