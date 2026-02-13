<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display user management page
     */
    public function index()
    {
        $users = User::with('department')
            ->where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $departments = Department::where('is_active', true)->get();
        
        return view('admin.users.index', compact('users', 'departments'));
    }
    
    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:employee,manager',
            'department_id' => 'required|exists:departments,id',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department_id' => $request->department_id,
        ]);
        
        return back()->with('success', 'User account created successfully!');
    }
    
    /**
     * Update user information
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Prevent editing admin accounts
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit admin accounts'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:employee,manager',
            'department_id' => 'required|exists:departments,id',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'department_id' => $request->department_id,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'User updated successfully!'
        ]);
    }
    
    /**
     * Change user password
     */
    public function changePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Prevent changing admin passwords
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot change admin password'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully!'
        ]);
    }
    
    /**
     * Delete user account
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting admin accounts
        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete admin accounts'
            ], 403);
        }
        
        // Check if user has pending requests
        $pendingRequests = $user->supplyRequests()
            ->whereIn('status', ['pending', 'manager_approved'])
            ->count();
            
        if ($pendingRequests > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete user with pending requests'
            ], 400);
        }
        
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully!'
        ]);
    }
}