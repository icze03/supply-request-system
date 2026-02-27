<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SupplyRequest;
use App\Models\RequestItem;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\AuditLog;

class RequestController extends Controller
{
    // View all user's requests
    public function index()
    {
        $requests = SupplyRequest::where('user_id', auth()->id())
            ->with(['department', 'items.supply', 'managerApprover', 'adminReleaser'])
            ->latest()
            ->paginate(20);

        return view('employee.requests.index', compact('requests'));
    }

    // View single request
    public function show($id)
    {
        $request = SupplyRequest::where('user_id', auth()->id())
            ->with(['department', 'items.supply', 'managerApprover', 'adminReleaser'])
            ->findOrFail($id);

        return view('employee.requests.show', compact('request'));
    }

    // Submit standard request
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purpose' => 'required|string|max:500',
            'budget_type' => 'required|in:budgeted,not_budgeted',
            'items' => 'required|array|min:1',
            'items.*.supply_id' => 'required|exists:supplies,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $supplyRequest = SupplyRequest::create([
                'user_id' => auth()->id(),
                'department_id' => auth()->user()->department_id,
                'request_type' => 'standard',
                'budget_type' => $request->budget_type,
                'purpose' => $request->purpose,
                'status' => 'pending',
            ]);

            foreach ($request->items as $item) {
                $supply = Supply::find($item['supply_id']);
                
                $supplyRequest->items()->create([
                    'supply_id' => $item['supply_id'],
                    'item_code' => $supply->item_code,
                    'item_name' => $supply->name,
                    'quantity' => $item['quantity'],
                ]);
            }

            AuditLog::logAction(
                action: 'supply_request_created',
                model: $supplyRequest,
                description: "Supply request #{$supplyRequest->sr_number} created by " . auth()->user()->name
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Request submitted successfully',
                'sr_number' => $supplyRequest->sr_number
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit request'
            ], 500);
        }
    }

    // Submit special request (non-catalog item)
    public function storeSpecial(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'budget_type' => 'required|in:budgeted,not_budgeted',
            'item_description' => 'required|string|max:1000',
            'purpose' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $supplyRequest = SupplyRequest::create([
                'user_id' => auth()->id(),
                'department_id' => auth()->user()->department_id,
                'request_type' => 'special',
                'budget_type' => $request->budget_type,
                'purpose' => $request->purpose,
                'special_item_description' => $request->item_description,
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Custom request submitted successfully',
                'sr_number' => $supplyRequest->sr_number
            ]);
        } catch (\Exception $e) {
            \Log::error('Special request error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit request: ' . $e->getMessage()
            ], 500);
        }
    }

    // Cancel pending request
    public function cancel($id)
    {
        $supplyRequest = SupplyRequest::where('user_id', auth()->id())
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        $supplyRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Request cancelled successfully.'
        ]);
    }

    // Submit return request
    public function submitReturn(Request $request, $id)
    {
        $supplyRequest = SupplyRequest::where('user_id', auth()->id())
            ->findOrFail($id);

        if ($supplyRequest->status !== 'admin_released') {
            return response()->json([
                'success' => false,
                'message' => 'Only released requests can be returned.'
            ], 422);
        }

        if ($supplyRequest->return_requested_at !== null) {
            return response()->json([
                'success' => false,
                'message' => 'A return request has already been submitted.'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|min:5|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $supplyRequest->update([
            'return_reason'      => $request->input('reason'),
            'return_requested_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Return request submitted successfully.'
        ]);
    }

    public function voucher($id)
{
    $request = SupplyRequest::with([
        'user',
        'department',
        'items.supply',
        'managerApprover',
        'adminReleaser'
    ])->where('user_id', auth()->id())
      ->findOrFail($id);

    if (!$request->serial_number) {
        abort(404, 'Voucher not available for this request');
    }

    return view('admin.voucher', compact('request'));
}
}