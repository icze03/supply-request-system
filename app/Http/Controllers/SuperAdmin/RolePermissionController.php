<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RolePermissionController extends Controller
{
    private const MANAGEABLE_ROLES = [
        'admin'    => 'Admin',
        'manager'  => 'Manager',
        'employee' => 'Employee',
        'hr_manager' => 'HR Manager',
    ];

    public function index(Request $request)
    {
        $roles       = self::MANAGEABLE_ROLES;
        $permissions = Permission::orderBy('sort_order')->get();

        $selectedRole = $request->get('role', 'admin');
        if (!array_key_exists($selectedRole, $roles)) {
            $selectedRole = 'admin';
        }

        $assignedIds = RolePermission::where('role', $selectedRole)
            ->pluck('permission_id')
            ->toArray();

        return view('super_admin.role_permissions', compact(
            'roles',
            'permissions',
            'selectedRole',
            'assignedIds'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'role'             => 'required|in:admin,manager,hr_manager,employee',
            'permission_ids'   => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role          = $request->input('role');
        $permissionIds = $request->input('permission_ids', []);

        RolePermission::where('role', $role)->delete();

        foreach ($permissionIds as $permId) {
            RolePermission::create([
                'role'          => $role,
                'permission_id' => (int) $permId,
            ]);
        }

        Cache::forget("role_permissions_{$role}");
        Cache::forget("role_permissions_full_{$role}");

        return redirect()
            ->route('super_admin.role_permissions.index', ['role' => $role])
            ->with('success', ucfirst($role) . ' permissions updated successfully.');
    }

    public function dashboard()
    {
        $totalUsers  = User::count();
        $roleStats   = User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role');

        $permissions = Permission::withCount([
            'rolePermissions as roles_count',
        ])->orderBy('sort_order')->get();

        $roles = self::MANAGEABLE_ROLES;

        $rolePermissionCounts = [];
        foreach (array_keys($roles) as $role) {
            $rolePermissionCounts[$role] = RolePermission::where('role', $role)->count();
        }

        return view('super_admin.dashboard', compact(
            'totalUsers',
            'roleStats',
            'permissions',
            'roles',
            'rolePermissionCounts'
        ));
    }
}