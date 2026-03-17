<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\User;
use App\Models\Supply;
use App\Models\SupplyRequest;
use App\Models\Department;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RolePermissionController extends Controller
{
    private const MANAGEABLE_ROLES = [
        'admin'      => 'Admin',
        'manager'    => 'Manager',
        'hr_manager' => 'HR Manager',
        'employee'   => 'Employee',
    ];

    public function index(Request $request)
    {
        $roles        = self::MANAGEABLE_ROLES;
        $permissions  = Permission::orderBy('sort_order')->get();
        $selectedRole = $request->get('role', 'admin');

        if (!array_key_exists($selectedRole, $roles)) {
            $selectedRole = 'admin';
        }

        $assignedIds = RolePermission::where('role', $selectedRole)
            ->pluck('permission_id')
            ->toArray();

        return view('super_admin.role_permissions', compact(
            'roles', 'permissions', 'selectedRole', 'assignedIds'
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
            ->with('success', ucfirst(str_replace('_', ' ', $role)) . ' permissions updated successfully.');
    }

    public function dashboard()
    {
        $roles = self::MANAGEABLE_ROLES;

        // ── User stats ───────────────────────────────────────────────
        $totalUsers = User::count();
        $roleStats  = User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role');

        // ── System stats ─────────────────────────────────────────────
        $totalSupplies    = Supply::count();
        $lowStockCount    = Supply::whereColumn('stock_quantity', '<=', 'minimum_stock')
                                  ->where('is_active', true)->count();
        $totalDepartments = Department::count();
        $totalRequests    = SupplyRequest::count();
        $pendingRequests  = SupplyRequest::where('status', 'pending')->count();
        $releasedToday    = SupplyRequest::where('status', 'admin_released')
                                         ->whereDate('admin_released_at', today())->count();

        // ── Permission coverage ──────────────────────────────────────
        $permissions = Permission::orderBy('sort_order')->get();
        $rolePermissionCounts = [];
        $totalPermissions = $permissions->count();
        foreach (array_keys($roles) as $role) {
            $rolePermissionCounts[$role] = RolePermission::where('role', $role)->count();
        }

        // ── Recent audit logs ────────────────────────────────────────
        $recentLogs = AuditLog::with('user')
            ->latest('created_at')
            ->take(8)
            ->get();

        // ── Recent users ─────────────────────────────────────────────
        $recentUsers = User::with('department')
            ->whereNotIn('role', ['super_admin'])
            ->latest()
            ->take(5)
            ->get();

        return view('super_admin.dashboard', compact(
            'roles',
            'totalUsers',
            'roleStats',
            'totalSupplies',
            'lowStockCount',
            'totalDepartments',
            'totalRequests',
            'pendingRequests',
            'releasedToday',
            'permissions',
            'totalPermissions',
            'rolePermissionCounts',
            'recentLogs',
            'recentUsers'
        ));
    }
}