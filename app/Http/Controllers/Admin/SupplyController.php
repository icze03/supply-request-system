<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplyController extends Controller
{
    // List all supplies
    public function index()
    {
        $supplies = Supply::orderBy('category')->orderBy('name')->paginate(20);
        $categories = Supply::getCategories();

        return view('admin.supplies.index', compact('supplies', 'categories'));
    }

    // Create form
    public function create()
    {
        $categories = Supply::getCategories();
        return view('admin.supplies.create', compact('categories'));
    }

    // Store new supply
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Supply::create($request->all());

        return redirect()->route('admin.supplies.index')
            ->with('success', 'Supply added successfully!');
    }

    // Edit form
    public function edit($id)
    {
        $supply = Supply::findOrFail($id);
        $categories = Supply::getCategories();

        return view('admin.supplies.edit', compact('supply', 'categories'));
    }

    // Update supply
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $supply = Supply::findOrFail($id);
        $supply->update($request->all());

        return redirect()->route('admin.supplies.index')
            ->with('success', 'Supply updated successfully!');
    }

    // Toggle active status
    public function toggleStatus($id)
    {
        $supply = Supply::findOrFail($id);
        $supply->update(['is_active' => !$supply->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $supply->is_active,
            'message' => $supply->is_active ? 'Supply activated.' : 'Supply deactivated.'
        ]);
    }

    // Delete supply
    public function destroy($id)
    {
        $supply = Supply::findOrFail($id);
        
        // Check if used in any requests
        if ($supply->requestItems()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete supply that has been used in requests.'
            ], 422);
        }

        $supply->delete();

        return response()->json([
            'success' => true,
            'message' => 'Supply deleted successfully.'
        ]);
    }
}