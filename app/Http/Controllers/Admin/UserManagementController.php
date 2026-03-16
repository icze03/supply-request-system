<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;

class UserManagementController extends Controller
{
    /**
     * Display user management page
     */
    public function index()
    {
        $users = User::with('department')
            ->whereIn('role', ['employee', 'manager', 'hr_manager'])
            ->orderBy('created_at', 'desc')
            ->get();

        $departments = Department::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users', 'departments'))
            ->with('locked', false);
    }

    /**
     * Verify users page PIN (kept for backwards compatibility)
     */
    public function verifyPin(Request $request)
    {
        $request->validate(['pin' => 'required|string']);

        $setting = DB::table('system_settings')
            ->where('key', 'users_page_pin')
            ->first();

        if (!$setting || !Hash::check($request->pin, $setting->value)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect PIN. Please try again.',
            ], 403);
        }

        session([
            'users_page_unlocked'    => true,
            'users_page_unlocked_at' => now()->timestamp,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
            'role'          => 'required|in:employee,manager,hr_manager',
            'department_id' => 'required|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role'          => $request->role,
            'department_id' => $request->department_id,
        ]);

        AuditLog::logAction(
            action:      'user_created',
            model:       $user,
            description: "New user created: {$user->name} ({$user->role})",
            metadata:    ['created_by' => auth()->user()->name]
        );

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user'    => $user->load('department'),
        ]);
    }

    /**
     * Update user details
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot modify admin accounts',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $id,
            'role'          => 'required|in:employee,manager,hr_manager',
            'department_id' => 'required|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user->update([
            'name'          => $request->name,
            'email'         => $request->email,
            'role'          => $request->role,
            'department_id' => $request->department_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'user'    => $user->load('department'),
        ]);
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot modify admin accounts',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
        ]);
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete admin accounts',
            ], 403);
        }

        $hasPendingRequests = $user->supplyRequests()
            ->whereIn('status', ['pending', 'manager_approved'])
            ->exists();

        if ($hasPendingRequests) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete user with pending requests',
            ], 422);
        }

        AuditLog::logAction(
            action:      'user_deleted',
            model:       $user,
            description: "User deleted: {$user->name}",
            metadata:    ['deleted_by' => auth()->user()->name]
        );

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }
}