<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supply;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Validator;

class LowStockController extends Controller
{
    /**
     * Display low stock items
     */
    public function index()
    {
        $lowStockSupplies = Supply::whereColumn('stock_quantity', '<=', 'minimum_stock')
            ->paginate(15);
        
        $criticalStock = Supply::whereRaw('stock_quantity <= (minimum_stock * 0.5)')
            ->count();
            
        return view('admin.low-stock.index', compact('lowStockSupplies', 'criticalStock'));
    }
    
    /**
     * Update stock quantity (restock)
     */
    public function restock(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $supply = Supply::findOrFail($id);
        
        $oldStock = $supply->stock_quantity;
        $addedQuantity = $request->quantity;
        $newStock = $oldStock + $addedQuantity;
        
        $supply->update(['stock_quantity' => $newStock]);
        
        // LOG STOCK RESTOCK
        try {
            AuditLog::create([
                'action' => 'inventory_restocked',
                'model_type' => get_class($supply),
                'model_id' => $supply->id,
                'user_id' => auth()->id(),
                'department_id' => auth()->user() ? auth()->user()->department_id : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'old_values' => json_encode(['stock_quantity' => $oldStock]),
                'new_values' => json_encode(['stock_quantity' => $newStock]),
                'description' => "Stock restocked: {$supply->item_name} (+{$addedQuantity} units)",
                'metadata' => json_encode([
                    'adjustment_type' => 'restock',
                    'item_code' => $supply->item_code,
                    'quantity_added' => $addedQuantity,
                    'old_stock' => $oldStock,
                    'new_stock' => $newStock,
                    'restocked_by' => auth()->user() ? auth()->user()->name : 'System',
                    'notes' => $request->notes
                ])
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create audit log for restock', [
                'supply_id' => $supply->id,
                'error' => $e->getMessage()
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Stock restocked successfully',
            'new_stock' => $newStock
        ]);
    }
    
    /**
     * Adjust stock quantity (manual correction)
     */
    public function adjust(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'new_quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:500'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $supply = Supply::findOrFail($id);
        
        $oldStock = $supply->stock_quantity;
        $newStock = $request->new_quantity;
        $difference = $newStock - $oldStock;
        
        $supply->update(['stock_quantity' => $newStock]);
        
        // LOG STOCK ADJUSTMENT
        try {
            AuditLog::create([
                'action' => 'inventory_adjusted_manual',
                'model_type' => get_class($supply),
                'model_id' => $supply->id,
                'user_id' => auth()->id(),
                'department_id' => auth()->user() ? auth()->user()->department_id : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'old_values' => json_encode(['stock_quantity' => $oldStock]),
                'new_values' => json_encode(['stock_quantity' => $newStock]),
                'description' => "Stock manually adjusted: {$supply->item_name} (" . ($difference >= 0 ? '+' : '') . "{$difference} units)",
                'metadata' => json_encode([
                    'adjustment_type' => 'manual_correction',
                    'item_code' => $supply->item_code,
                    'old_stock' => $oldStock,
                    'new_stock' => $newStock,
                    'difference' => $difference,
                    'reason' => $request->reason,
                    'adjusted_by' => auth()->user() ? auth()->user()->name : 'System'
                ])
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create audit log for adjustment', [
                'supply_id' => $supply->id,
                'error' => $e->getMessage()
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Stock adjusted successfully',
            'new_stock' => $newStock
        ]);
    }
    
    /**
     * Update minimum stock level
     */
    public function updateMinimum(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'minimum_stock' => 'required|integer|min:0'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $supply = Supply::findOrFail($id);
        
        $oldMinimum = $supply->minimum_stock;
        $newMinimum = $request->minimum_stock;
        
        $supply->update(['minimum_stock' => $newMinimum]);
        
        // LOG MINIMUM STOCK UPDATE
        try {
            AuditLog::create([
                'action' => 'minimum_stock_updated',
                'model_type' => get_class($supply),
                'model_id' => $supply->id,
                'user_id' => auth()->id(),
                'department_id' => auth()->user() ? auth()->user()->department_id : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'old_values' => json_encode(['minimum_stock' => $oldMinimum]),
                'new_values' => json_encode(['minimum_stock' => $newMinimum]),
                'description' => "Minimum stock updated: {$supply->item_name} ({$oldMinimum} → {$newMinimum})",
                'metadata' => json_encode([
                    'item_code' => $supply->item_code,
                    'old_minimum' => $oldMinimum,
                    'new_minimum' => $newMinimum,
                    'updated_by' => auth()->user() ? auth()->user()->name : 'System'
                ])
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create audit log for minimum stock update', [
                'supply_id' => $supply->id,
                'error' => $e->getMessage()
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Minimum stock level updated successfully',
            'new_minimum' => $newMinimum
        ]);
    }
}