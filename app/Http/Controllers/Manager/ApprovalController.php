<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\SupplyRequest;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApprovalController extends Controller
{
    // Passcode verification page
    public function verifyPasscode()
    {
        return view('manager.verify-passcode');
    }

    // Check passcode
    public function checkPasscode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'passcode' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid passcode format.'
            ], 422);
        }

        $department = auth()->user()->department;

        if (!$department) {
            return response()->json([
                'success' => false,
                'message' => 'No department assigned.'
            ], 403);
        }

        if ($department->verifyPasscode($request->passcode)) {
            // Store verification in session
            session(['manager_verified' => true]);
            session(['manager_verified_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Access granted.',
                'redirect' => route('manager.approvals')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid passcode.'
        ], 403);
    }

    // View pending requests
public function index()
{
    // Check session verification
    if (!session('manager_verified')) {
        return redirect()->route('manager.verify');
    }
    
    $departmentId = auth()->user()->department_id;
    
    // Pending requests - paginated
    $pendingRequests = SupplyRequest::with(['user', 'items.supply', 'department'])
        ->where('department_id', $departmentId)
        ->where('status', 'pending')
        ->orderBy('created_at', 'desc')
        ->paginate(20);
    
    // Recently approved (last 7 days) - paginated
    $approvedRequests = SupplyRequest::with(['user', 'items', 'department'])
        ->where('department_id', $departmentId)
        ->where('status', 'manager_approved')
        ->where('manager_approved_at', '>=', now()->subDays(7))
        ->orderBy('manager_approved_at', 'desc')
        ->paginate(20);
    
    return view('manager.approvals', compact('pendingRequests', 'approvedRequests'));
}

    // View single request details
    public function show($id)
    {
        // Check session verification
        if (!session('manager_verified')) {
            return redirect()->route('manager.verify');
        }
        
        $request = SupplyRequest::with(['user', 'department', 'items.supply'])
            ->findOrFail($id);
        
        // Verify request belongs to manager's department
        if ($request->department_id !== auth()->user()->department_id) {
            abort(403, 'Unauthorized access');
        }
        
        return view('manager.requests.show', compact('request'));
    }

    // Approve request
    public function approve(Request $request, $id)
    {
        if (!session('manager_verified')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $supplyRequest = SupplyRequest::forDepartment(auth()->user()->department_id)
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        $supplyRequest->update([
            'status' => 'manager_approved',
            'manager_approved_by' => auth()->id(),
            'manager_approved_at' => now(),
            'manager_notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request approved successfully!'
        ]);
    }

    // Reject request
    public function reject(Request $request, $id)
    {
        if (!session('manager_verified')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'notes' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $supplyRequest = SupplyRequest::forDepartment(auth()->user()->department_id)
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        $supplyRequest->update([
            'status' => 'manager_rejected',
            'manager_approved_by' => auth()->id(),
            'manager_approved_at' => now(),
            'manager_notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Request rejected.'
        ]);
    }

    /**
     * Update individual request item
     */
    public function updateItem(Request $request, $id)
    {
        // Check session verification
        if (!session('manager_verified')) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please verify again.'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:request_items,id',
            'quantity' => 'required|integer|min:1|max:9999',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $supplyRequest = SupplyRequest::findOrFail($id);
        
        // Verify request belongs to manager's department
        if ($supplyRequest->department_id !== auth()->user()->department_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        if ($supplyRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Can only edit pending requests'
            ], 422);
        }
        
        $item = \App\Models\RequestItem::findOrFail($request->item_id);
        
        // Verify item belongs to this request
        if ($item->supply_request_id !== $supplyRequest->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid item'
            ], 422);
        }
        
        $item->update(['quantity' => $request->quantity]);
        
        return response()->json([
            'success' => true,
            'message' => 'Quantity updated successfully'
        ]);
    }
}