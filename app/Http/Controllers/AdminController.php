<?php

namespace App\Http\Controllers;

use App\Models\Supply;
use App\Models\SupplyRequest;
use App\Models\Department;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function dashboard()
    {
        $totalSupplies  = Supply::count();
        $activeSupplies = Supply::where('is_active', true)->count();

        // Awaiting manager approval (status = 'pending') ← blade line 60 needs this
        $pendingApproval = SupplyRequest::where('status', 'pending')->count();

        // Awaiting admin release (manager already approved)
        $pendingReleases = SupplyRequest::where('status', 'manager_approved')->count();

        $releasedToday = SupplyRequest::where('status', 'admin_released')
            ->whereDate('admin_released_at', today())
            ->count();

        $departmentStats = Department::withCount(['supplyRequests' => function ($query) {
            $query->where('created_at', '>=', now()->subDays(30));
        }])->get();

        $pendingReleasesList = SupplyRequest::with(['user', 'department', 'managerApprover'])
            ->where('status', 'manager_approved')
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();

        $lowStockSupplies = Supply::whereColumn('stock_quantity', '<=', 'minimum_stock')
            ->where('is_active', true)
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalSupplies',
            'activeSupplies',
            'pendingApproval',    // ← was missing
            'pendingReleases',
            'releasedToday',
            'departmentStats',
            'pendingReleasesList',
            'lowStockSupplies'
        ));
    }

    // ==================== SUPPLY MANAGEMENT ====================

    public function suppliesIndex()
    {
        $supplies   = Supply::orderBy('created_at', 'desc')->paginate(20);
        $categories = Supply::select('category')->distinct()->pluck('category');
        return view('admin.supplies.index', compact('supplies', 'categories'));
    }

    public function suppliesCreate()
    {
        $categories = Supply::select('category')->distinct()->pluck('category');
        return view('admin.supplies.create', compact('categories'));
    }

    public function suppliesStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'category'       => 'required|string|max:100',
            'unit'           => 'required|string|max:50',
            'description'    => 'nullable|string',
            'stock_quantity' => 'required|integer|min:0',
            'minimum_stock'  => 'required|integer|min:0',
            'enable_budget'  => 'nullable|boolean',
            'unit_cost'      => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()
                ->with('error', 'Validation failed. Please check your inputs.');
        }

        DB::beginTransaction();
        try {
            $supply                 = new Supply();
            $supply->name           = $request->name;
            $supply->category       = $request->category;
            $supply->unit           = $request->unit;
            $supply->description    = $request->description;
            $supply->is_active      = $request->has('is_active');
            $supply->stock_quantity = (int) $request->stock_quantity;
            $supply->minimum_stock  = (int) $request->minimum_stock;
            $supply->unit_cost      = $request->boolean('enable_budget') ? $request->unit_cost : null;

            $supply->save();

            DB::commit();

            return redirect()->route('admin.supplies.index')
                ->with('success', 'Supply created successfully! Item Code: ' . $supply->item_code);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Supply Creation Failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create supply: ' . $e->getMessage());
        }
    }

    public function suppliesEdit($id)
    {
        $supply     = Supply::findOrFail($id);
        $categories = Supply::select('category')->distinct()->pluck('category');
        return view('admin.supplies.edit', compact('supply', 'categories'));
    }

    public function suppliesUpdate(Request $request, $id)
    {
        $supply = Supply::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'item_code'      => 'required|string|max:50|unique:supplies,item_code,' . $id,
            'name'           => 'required|string|max:255',
            'category'       => 'required|string|max:100',
            'unit'           => 'required|string|max:50',
            'description'    => 'nullable|string',
            'stock_quantity' => 'required|integer|min:0',
            'minimum_stock'  => 'required|integer|min:0',
            'enable_budget'  => 'nullable|boolean',
            'unit_cost'      => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $supply->update([
                'item_code'      => $request->item_code,
                'name'           => $request->name,
                'category'       => $request->category,
                'unit'           => $request->unit,
                'description'    => $request->description,
                'is_active'      => $request->has('is_active'),
                'stock_quantity' => (int) $request->stock_quantity,
                'minimum_stock'  => (int) $request->minimum_stock,
                'unit_cost'      => $request->boolean('enable_budget') ? $request->unit_cost : null,
            ]);

            return redirect()->route('admin.supplies.index')
                ->with('success', 'Supply updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Supply update error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update: ' . $e->getMessage());
        }
    }

    public function suppliesToggle($id)
    {
        $supply            = Supply::findOrFail($id);
        $supply->is_active = !$supply->is_active;
        $supply->save();

        return response()->json(['success' => true, 'is_active' => $supply->is_active]);
    }

    public function suppliesDestroy($id)
    {
        $supply = Supply::findOrFail($id);

        $usedInRequests = \App\Models\RequestItem::where('supply_id', $id)->exists();

        if ($usedInRequests) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete supply that has been used in requests',
            ], 422);
        }

        $supply->delete();

        return response()->json(['success' => true, 'message' => 'Supply deleted successfully']);
    }

    // ==================== LOW STOCK PAGE ====================

    public function lowStockIndex()
    {
        $lowStockSupplies = Supply::whereColumn('stock_quantity', '<=', 'minimum_stock')
            ->where('is_active', true)
            ->orderBy('stock_quantity', 'asc')
            ->paginate(20);

        $criticalStock = Supply::where('stock_quantity', '<=', 0)
            ->where('is_active', true)
            ->count();

        return view('admin.low-stock.index', compact('lowStockSupplies', 'criticalStock'));
    }

    // ==================== RELEASE MANAGEMENT ====================

    public function releasesIndex()
    {
        $pendingReleases = SupplyRequest::with(['user', 'department', 'items.supply', 'managerApprover'])
            ->where('status', 'manager_approved')
            ->orderBy('created_at', 'desc')
            ->paginate(20, ['*'], 'pending');

        $releasedToday = SupplyRequest::with(['user', 'department', 'adminReleaser'])
            ->where('status', 'admin_released')
            ->whereDate('admin_released_at', today())
            ->orderBy('created_at', 'desc')
            ->paginate(20, ['*'], 'today');

        $releaseHistory = SupplyRequest::with(['user', 'department', 'adminReleaser'])
            ->where('status', 'admin_released')
            ->orderBy('admin_released_at', 'desc')
            ->paginate(20, ['*'], 'history');

        return view('admin.releases.index', compact('pendingReleases', 'releasedToday', 'releaseHistory'));
    }

    public function releaseRequest(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $supplyRequest = SupplyRequest::findOrFail($id);

        if ($supplyRequest->status !== 'manager_approved') {
            return response()->json(['success' => false, 'message' => 'Only manager-approved requests can be released'], 422);
        }

        DB::beginTransaction();
        try {
            if (!$supplyRequest->serial_number) {
                $department             = $supplyRequest->department;
                $date                   = now()->format('Ymd');
                $count                  = SupplyRequest::where('status', 'admin_released')
                    ->whereDate('admin_released_at', today())
                    ->count() + 1;
                $supplyRequest->serial_number = sprintf('%s-%s-%04d', $department->code, $date, $count);
            }

            if ($supplyRequest->request_type === 'standard') {
                foreach ($supplyRequest->items as $item) {
                    if ($item->supply) {
                        DB::table('supplies')
                            ->where('id', $item->supply_id)
                            ->decrement('stock_quantity', $item->quantity);
                    }
                }
            }

            $supplyRequest->update([
                'status'            => 'admin_released',
                'admin_released_by' => auth()->id(),
                'admin_released_at' => now(),
                'admin_notes'       => $request->notes,
            ]);

            DB::commit();

            return response()->json([
                'success'       => true,
                'message'       => 'Request released successfully',
                'serial_number' => $supplyRequest->serial_number,
                'voucher_url'   => route('admin.voucher', $supplyRequest->id),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Release error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to release request: ' . $e->getMessage()], 500);
        }
    }

    public function rejectRequest(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $supplyRequest = SupplyRequest::findOrFail($id);

        if ($supplyRequest->status !== 'manager_approved') {
            return response()->json(['success' => false, 'message' => 'Only manager-approved requests can be rejected'], 422);
        }

        $supplyRequest->update([
            'status'            => 'admin_rejected',
            'admin_rejected_by' => auth()->id(),
            'admin_rejected_at' => now(),
            'admin_notes'       => $request->notes,
        ]);

        return response()->json(['success' => true, 'message' => 'Request rejected successfully']);
    }

    public function voucher($id)
    {
        $request = SupplyRequest::with([
            'user', 'department', 'items.supply', 'managerApprover', 'adminReleaser',
        ])->findOrFail($id);

        if ($request->status !== 'admin_released') {
            abort(404, 'Voucher not available for this request');
        }

        return view('admin.voucher', compact('request'));
    }
}