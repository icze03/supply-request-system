<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supply;
use Illuminate\Http\Request;

class LowStockController extends Controller
{
    public function index()
{
    $lowStockSupplies = Supply::whereColumn('stock_quantity', '<=', 'minimum_stock')
        ->paginate(15);
    
    $criticalStock = Supply::whereRaw('stock_quantity <= (minimum_stock * 0.5)')
        ->count();
        
    return view('admin.low-stock.index', compact('lowStockSupplies', 'criticalStock'));
}
}