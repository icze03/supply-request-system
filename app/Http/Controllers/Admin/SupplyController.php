<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\AuditLog;

class SupplyController extends Controller
{
    // List all supplies
    public function index(Request $request)
    {
        $query = Supply::orderBy('category')->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $supplies    = $query->paginate(20)->withQueryString();
        $categories  = Supply::select('category')->distinct()->orderBy('category')->pluck('category');

        return view('admin.supplies.index', compact('supplies', 'categories'));
    }

    // Create form
    public function create()
    {
        $categories = Supply::select('category')->distinct()->orderBy('category')->pluck('category');
        return view('admin.supplies.create', compact('categories'));
    }

    // Store new supply
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'unit'        => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $supply = Supply::create($request->all());

        try {
            AuditLog::create([
                'action'        => 'supply_created',
                'model_type'    => get_class($supply),
                'model_id'      => $supply->id,
                'user_id'       => auth()->id(),
                'department_id' => auth()->user()?->department_id,
                'ip_address'    => request()->ip(),
                'user_agent'    => request()->userAgent(),
                'new_values'    => json_encode($supply->getAttributes()),
                'description'   => "New supply item added: {$supply->name}",
                'metadata'      => json_encode([
                    'category'     => $supply->category,
                    'unit'         => $supply->unit,
                    'initial_stock'=> $supply->stock_quantity ?? 0,
                    'item_code'    => $supply->item_code,
                    'created_by'   => auth()->user()?->name ?? 'System',
                ]),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create audit log for supply creation', [
                'supply_id' => $supply->id,
                'error'     => $e->getMessage(),
            ]);
        }

        return redirect()->route('admin.supplies.index')
            ->with('success', 'Supply added successfully!');
    }

    // Edit form
    public function edit($id)
    {
        $supply     = Supply::findOrFail($id);
        $categories = Supply::select('category')->distinct()->orderBy('category')->pluck('category');

        return view('admin.supplies.edit', compact('supply', 'categories'));
    }

    // Update supply
    public function update(Request $request, $id)
    {
        $supply = Supply::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'item_code'      => 'required|string|max:50|unique:supplies,item_code,' . $id,
            'name'           => 'required|string|max:255',
            'category'       => 'required|string|max:100',
            'unit'           => 'required|string|max:50',
            'description'    => 'nullable|string|max:1000',
            'is_active'      => 'nullable|boolean',
            'stock_quantity' => 'required|integer|min:0',
            'minimum_stock'  => 'required|integer|min:0',
            'enable_budget'  => 'nullable|boolean',
            'unit_cost'      => 'nullable|numeric|min:0|required_if:enable_budget,1',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $oldValues = $supply->getOriginal();

        $supply->update([
            'item_code'      => $request->input('item_code'),
            'name'           => $request->input('name'),
            'category'       => $request->input('category'),
            'unit'           => $request->input('unit'),
            'description'    => $request->input('description'),
            'is_active'      => $request->boolean('is_active'),
            'stock_quantity' => $request->input('stock_quantity'),
            'minimum_stock'  => $request->input('minimum_stock'),
            'unit_cost'      => $request->boolean('enable_budget') ? $request->input('unit_cost') : null,
        ]);

        $changes = $supply->getChanges();

        if (!empty($changes)) {
            try {
                AuditLog::create([
                    'action'        => 'supply_updated',
                    'model_type'    => get_class($supply),
                    'model_id'      => $supply->id,
                    'user_id'       => auth()->id(),
                    'department_id' => auth()->user()?->department_id,
                    'ip_address'    => request()->ip(),
                    'user_agent'    => request()->userAgent(),
                    'old_values'    => json_encode(array_intersect_key($oldValues, $changes)),
                    'new_values'    => json_encode($changes),
                    'description'   => "Supply item updated: {$supply->name}",
                    'metadata'      => json_encode([
                        'changed_fields' => array_keys($changes),
                        'item_code'      => $supply->item_code,
                        'category'       => $supply->category,
                        'updated_by'     => auth()->user()?->name ?? 'System',
                    ]),
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to create audit log for supply update', [
                    'supply_id' => $supply->id,
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        return redirect()->route('admin.supplies.index')
            ->with('success', 'Supply updated successfully!');
    }

    // Toggle active status
    public function toggleStatus($id)
    {
        $supply    = Supply::findOrFail($id);
        $oldStatus = $supply->is_active;
        $supply->update(['is_active' => !$supply->is_active]);
        $newStatus = $supply->is_active;

        try {
            AuditLog::create([
                'action'        => $newStatus ? 'supply_activated' : 'supply_deactivated',
                'model_type'    => get_class($supply),
                'model_id'      => $supply->id,
                'user_id'       => auth()->id(),
                'department_id' => auth()->user()?->department_id,
                'ip_address'    => request()->ip(),
                'user_agent'    => request()->userAgent(),
                'old_values'    => json_encode(['is_active' => $oldStatus]),
                'new_values'    => json_encode(['is_active' => $newStatus]),
                'description'   => 'Supply item ' . ($newStatus ? 'activated' : 'deactivated') . ": {$supply->name}",
                'metadata'      => json_encode([
                    'item_code' => $supply->item_code,
                    'category'  => $supply->category,
                    'action_by' => auth()->user()?->name ?? 'System',
                ]),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create audit log for status toggle', [
                'supply_id' => $supply->id,
                'error'     => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success'   => true,
            'is_active' => $supply->is_active,
            'message'   => $supply->is_active ? 'Supply activated.' : 'Supply deactivated.',
        ]);
    }

    // Delete supply
    public function destroy($id)
    {
        $supply = Supply::findOrFail($id);

        if ($supply->requestItems()->count() > 0) {
            try {
                AuditLog::create([
                    'action'        => 'supply_deletion_failed',
                    'model_type'    => get_class($supply),
                    'model_id'      => $supply->id,
                    'user_id'       => auth()->id(),
                    'department_id' => auth()->user()?->department_id,
                    'ip_address'    => request()->ip(),
                    'user_agent'    => request()->userAgent(),
                    'description'   => "Failed to delete supply item: {$supply->name} (in use)",
                    'metadata'      => json_encode([
                        'reason'        => 'Supply has been used in requests',
                        'request_count' => $supply->requestItems()->count(),
                        'item_code'     => $supply->item_code,
                        'attempted_by'  => auth()->user()?->name ?? 'System',
                    ]),
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to create audit log for failed deletion', [
                    'supply_id' => $supply->id,
                    'error'     => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Cannot delete supply that has been used in requests.',
            ], 422);
        }

        $supplyData = [
            'name'           => $supply->name,
            'item_code'      => $supply->item_code,
            'category'       => $supply->category,
            'unit'           => $supply->unit,
            'stock_quantity' => $supply->stock_quantity,
            'description'    => $supply->description,
        ];

        try {
            AuditLog::create([
                'action'        => 'supply_deleted',
                'model_type'    => get_class($supply),
                'model_id'      => $supply->id,
                'user_id'       => auth()->id(),
                'department_id' => auth()->user()?->department_id,
                'ip_address'    => request()->ip(),
                'user_agent'    => request()->userAgent(),
                'old_values'    => json_encode($supply->getAttributes()),
                'description'   => "Supply item deleted: {$supply->name}",
                'metadata'      => json_encode(array_merge($supplyData, [
                    'deleted_by'      => auth()->user()?->name ?? 'System',
                    'deletion_reason' => 'Manual deletion by admin',
                ])),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create audit log for deletion', [
                'supply_id' => $supply->id,
                'error'     => $e->getMessage(),
            ]);
        }

        $supply->delete();

        return response()->json([
            'success' => true,
            'message' => 'Supply deleted successfully.',
        ]);
    }
}