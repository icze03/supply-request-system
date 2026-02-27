<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Supply;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Supply::where('is_active', true);
        
        // Search filter - searches across all pages
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('item_code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        
        // Category filter - persists across pages
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // Get supplies with pagination
        $supplies = $query->orderBy('category')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString(); // This preserves filters in pagination links!
        
        // Get categories for filter dropdown
        $categories = Supply::where('is_active', true)
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
        
        return view('employee.catalog', compact('supplies', 'categories'));
    }
}