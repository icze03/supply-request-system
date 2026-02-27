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
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403);
        }

        // Check if page is unlocked (session valid for 30 minutes)
        $unlockedAt = session('users_page_unlocked_at');
        $isUnlocked = session('users_page_unlocked')
            && $unlockedAt
            && (now()->timestamp - $unlockedAt) < 1800;

        if (!$isUnlocked) {
            session()->forget(['users_page_unlocked', 'users_page_unlocked_at']);
            return view('admin.users.index', [
                'users'       => collect(),
                'departments' => collect(),
                'locked'      => true,
            ]);
        }

        $users = User::with('department')
            ->where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->get();

        $departments = Department::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'departments'))->with('locked', false);
    }

    /**
     * Verify users page PIN
     */
    public function verifyPin(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate(['pin' => 'required|string']);

        $setting = DB::table('system_settings')
            ->where('key', 'users_page_pin')
            ->first();

        if (!$setting || !Hash::check($request->pin, $setting->value)) {
            return response()->json(['success' => false, 'message' => 'Incorrect PIN. Please try again.'], 403);
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
            'password'      => 'required|string|min:8',
            'role'          => 'required|in:employee,manager',
            'department_id' => 'required|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role'          => $request->role,
            'department_id' => $request->department_id,
        ]);

        try {
            $department = Department::find($request->department_id);
            AuditLog::create([
                'action'        => 'user_created',
                'model_type'    => get_class($user),
                'model_id'      => $user->id,
                'user_id'       => auth()->id(),
                'department_id' => auth()->user()?->department_id,
                'ip_address'    => request()->ip(),
                'user_agent'    => request()->userAgent(),
                'new_values'    => json_encode(['name' => $user->name, 'email' => $user->email, 'role' => $user->role]),
                'description'   => "New user created: {$user->name} ({$user->role})",
                'metadata'      => json_encode(['department' => $department?->name, 'created_by' => auth()->user()?->name]),
            ]);
        } catch (\Exception $e) {
            \Log::error('Audit log failed for user creation: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'User account created successfully!']);
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return response()->json(['success' => false, 'message' => 'Cannot edit admin accounts'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $id,
            'role'          => 'required|in:employee,manager',
            'department_id' => 'required|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $oldValues = ['name' => $user->name, 'email' => $user->email, 'role' => $user->role, 'department_id' => $user->department_id];

        $user->update([
            'name'          => $request->name,
            'email'         => $request->email,
            'role'          => $request->role,
            'department_id' => $request->department_id,
        ]);

        try {
            AuditLog::create([
                'action'        => 'user_updated',
                'model_type'    => get_class($user),
                'model_id'      => $user->id,
                'user_id'       => auth()->id(),
                'department_id' => auth()->user()?->department_id,
                'ip_address'    => request()->ip(),
                'user_agent'    => request()->userAgent(),
                'old_values'    => json_encode($oldValues),
                'new_values'    => json_encode(['name' => $user->name, 'email' => $user->email, 'role' => $user->role]),
                'description'   => "User updated: {$user->name}",
                'metadata'      => json_encode(['updated_by' => auth()->user()?->name]),
            ]);
        } catch (\Exception $e) {
            \Log::error('Audit log failed for user update: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'User updated successfully!']);
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return response()->json(['success' => false, 'message' => 'Cannot change admin password'], 403);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        try {
            AuditLog::create([
                'action'        => 'user_password_changed',
                'model_type'    => get_class($user),
                'model_id'      => $user->id,
                'user_id'       => auth()->id(),
                'department_id' => auth()->user()?->department_id,
                'ip_address'    => request()->ip(),
                'user_agent'    => request()->userAgent(),
                'description'   => "Password changed for: {$user->name}",
                'metadata'      => json_encode(['changed_by' => auth()->user()?->name]),
            ]);
        } catch (\Exception $e) {
            \Log::error('Audit log failed for password change: ' . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Password changed successfully!']);
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return response()->json(['success' => false, 'message' => 'Cannot delete admin accounts'], 403);
        }

        $pendingRequests = $user->supplyRequests()
            ->whereIn('status', ['pending', 'manager_approved'])
            ->count();

        if ($pendingRequests > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete user with pending requests'], 400);
        }

        try {
            AuditLog::create([
                'action'        => 'user_deleted',
                'model_type'    => get_class($user),
                'model_id'      => $user->id,
                'user_id'       => auth()->id(),
                'department_id' => auth()->user()?->department_id,
                'ip_address'    => request()->ip(),
                'user_agent'    => request()->userAgent(),
                'old_values'    => json_encode(['name' => $user->name, 'email' => $user->email, 'role' => $user->role]),
                'description'   => "User deleted: {$user->name} ({$user->role})",
                'metadata'      => json_encode(['deleted_by' => auth()->user()?->name]),
            ]);
        } catch (\Exception $e) {
            \Log::error('Audit log failed for user deletion: ' . $e->getMessage());
        }

        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully!']);
    }
}