<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\AuditLog;

class DepartmentController extends Controller
{
    /**
     * Display department management page
     */
    public function index()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access. Admin privileges required.');
        }

        // Check if page is unlocked (session valid for 30 minutes)
        $unlockedAt = session('dept_page_unlocked_at');
        $isUnlocked = session('dept_page_unlocked')
            && $unlockedAt
            && (now()->timestamp - $unlockedAt) < 1800;

        if (!$isUnlocked) {
            session()->forget(['dept_page_unlocked', 'dept_page_unlocked_at']);
            return view('admin.departments.index', [
                'departments' => collect(),
                'locked'      => true,
            ]);
        }

        $departments = Department::with('users')->orderBy('name')->get();

        return view('admin.departments.index', compact('departments'))->with('locked', false);
    }

    /**
     * Verify department page PIN
     */
    public function verifyPin(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate(['pin' => 'required|string']);

        $setting = DB::table('system_settings')
            ->where('key', 'department_page_pin')
            ->first();

        if (!$setting || !Hash::check($request->pin, $setting->value)) {
            return response()->json(['success' => false, 'message' => 'Incorrect PIN. Please try again.'], 403);
        }

        session([
            'dept_page_unlocked'    => true,
            'dept_page_unlocked_at' => now()->timestamp,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Store new department
     */
    public function store(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255|unique:departments',
            'code'        => 'required|string|max:10|unique:departments',
            'cost_center' => 'required|string|max:50',
            'passcode'    => 'required|string|min:4|max:6|regex:/^[0-9]+$/',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $department = Department::create([
                'name'             => $request->name,
                'code'             => strtoupper($request->code),
                'cost_center'      => $request->cost_center,
                'passcode'         => $request->passcode,
                'annual_budget'    => 0,
                'allocated_budget' => 0,
                'spent_budget'     => 0,
                'remaining_budget' => 0,
                'budget_year'      => date('Y'),
            ]);

            return response()->json([
                'success'    => true,
                'message'    => 'Department created successfully!',
                'department' => $department,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create department: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create department.'], 500);
        }
    }

    /**
     * Update department
     */
    public function update(Request $request, $id)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $department = Department::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255|unique:departments,name,' . $id,
            'code'        => 'required|string|max:10|unique:departments,code,' . $id,
            'cost_center' => 'required|string|max:50',
            'passcode'    => 'nullable|string|min:4|max:6|regex:/^[0-9]+$/',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $updateData = [
                'name'        => $request->name,
                'code'        => strtoupper($request->code),
                'cost_center' => $request->cost_center,
            ];

            if ($request->filled('passcode')) {
                $updateData['passcode'] = $request->passcode;
            }

            $department->update($updateData);

            return response()->json([
                'success'    => true,
                'message'    => 'Department updated successfully!',
                'department' => $department,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to update department', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update department.'], 500);
        }
    }

    /**
     * Delete department
     */
    public function destroy($id)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $department = Department::findOrFail($id);

            if ($department->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete department with assigned users.',
                ], 400);
            }

            if ($department->supplyRequests()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete department with existing supply requests.',
                ], 400);
            }

            $name = $department->name;
            $department->delete();

            return response()->json([
                'success' => true,
                'message' => "Department '{$name}' deleted successfully!",
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to delete department: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete department.'], 500);
        }
    }

    /**
     * Reset all department budgets (kept for compatibility)
     */
    public function resetBudgets(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'budget_year' => 'required|integer|min:2020|max:2100',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            $count = 0;
            foreach (Department::all() as $dept) {
                $dept->resetBudget(null, $request->budget_year);
                $count++;
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully reset budgets for {$count} department(s)!",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to reset budgets.'], 500);
        }
    }
}