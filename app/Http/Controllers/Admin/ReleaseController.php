<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ReleaseController extends Controller
{
    /**
     * Display releases page
     */
    public function index()
{
   $pendingReleases = SupplyRequest::with([ 'user', 'department', 'items.supply', 'managerApprover' ])
    ->where('status', 'manager_approved')
    ->latest()
    ->paginate(20, ['*'], 'pending_page');

$releasedToday = SupplyRequest::with(['user', 'department', 'adminReleaser'])
    ->where('status', 'admin_released')
    ->whereDate('admin_released_at', today())
    ->latest()
    ->paginate(20, ['*'], 'today_page');

$releaseHistory = SupplyRequest::with(['user', 'department', 'adminReleaser'])
    ->where('status', 'admin_released')
    ->latest('admin_released_at')
    ->paginate(20, ['*'], 'history_page');
    
    return view('admin.releases.index', compact('pendingReleases', 'releasedToday', 'releaseHistory'));
}
    
    /**
     * Display detailed view of a release request
     */
    public function show($id)
    {
        $request = SupplyRequest::with([
            'user',
            'department',
            'items.supply',
            'managerApprover',
            'adminReleaser'
        ])->findOrFail($id);
        
        return view('admin.releases.show', compact('request'));
    }
    
    /**
     * Release a request (generate serial number)
     */
    public function release(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'notes' => 'nullable|string|max:500'
    ]);
    
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }
    
    $supplyRequest = SupplyRequest::findOrFail($id);
    
    if ($supplyRequest->status !== 'manager_approved') {
        return response()->json([
            'success' => false,
            'message' => 'Only manager-approved requests can be released'
        ], 422);
    }
    
    DB::beginTransaction();
    try {
        // Generate serial number if not exists
        if (!$supplyRequest->serial_number) {
            $department = $supplyRequest->department;
            $date = now()->format('Ymd');
            $count = SupplyRequest::where('status', 'admin_released')
                ->whereDate('admin_released_at', today())
                ->count() + 1;
            
            $supplyRequest->serial_number = sprintf('%s-%s-%04d', $department->code, $date, $count);
        }
        
        // IMPORTANT: Deduct stock BEFORE updating status
        if ($supplyRequest->request_type === 'standard') {
            foreach ($supplyRequest->items as $item) {
                if ($item->supply) {
                    // Use DB query for atomic update
                    DB::table('supplies')
                        ->where('id', $item->supply_id)
                        ->decrement('stock_quantity', $item->quantity);
                    
                    \Log::info("Deducted {$item->quantity} from supply {$item->supply_id}");
                }
            }
        }
        
        // Update request status
        $supplyRequest->update([
            'status' => 'admin_released',
            'admin_released_by' => auth()->id(),
            'admin_released_at' => now(),
            'admin_notes' => $request->notes
        ]);
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Request released successfully',
            'serial_number' => $supplyRequest->serial_number,
            'voucher_url' => route('admin.voucher', $supplyRequest->id)
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Release error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to release request: ' . $e->getMessage()
        ], 500);
    }
}
    
    /**
     * Reject a request from admin
     */
    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'required|string|max:500'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $supplyRequest = SupplyRequest::findOrFail($id);
        
        if ($supplyRequest->status !== 'manager_approved') {
            return response()->json([
                'success' => false,
                'message' => 'Only manager-approved requests can be rejected'
            ], 422);
        }
        
        $supplyRequest->update([
            'status' => 'admin_rejected',
            'admin_rejected_by' => auth()->id(),
            'admin_rejected_at' => now(),
            'admin_notes' => $request->notes
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Request rejected successfully'
        ]);
    }
    
    /**
     * Display voucher for printing
     */
    public function voucher($id)
    {
        $request = SupplyRequest::with([
            'user',
            'department',
            'items.supply',
            'managerApprover',
            'adminReleaser'
        ])->findOrFail($id);
        
        if ($request->status !== 'admin_released') {
            abort(404, 'Voucher not available for this request');
        }
        
        return view('admin.voucher', compact('request'));
    }

    public function destroy($id)
{
    try {
        $request = \App\Models\SupplyRequest::findOrFail($id);
        
        // Only allow deletion of released requests
        if ($request->status !== 'admin_released') {
            return response()->json([
                'success' => false,
                'message' => 'Only released requests can be deleted from history'
            ], 400);
        }
        
        // Log the deletion for audit trail
        \Log::info('Release history deleted', [
            'request_id' => $id,
            'serial_number' => $request->serial_number,
            'employee' => $request->user->name,
            'department' => $request->department->name,
            'deleted_by' => auth()->id(),
            'deleted_at' => now()
        ]);
        
        // Delete the request and its items
        $request->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Release history deleted successfully'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Failed to delete release history', [
            'request_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete record: ' . $e->getMessage()
        ], 500);
    }
}
}