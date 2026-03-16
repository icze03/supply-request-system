<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function supplyRequests()
    {
        return $this->hasMany(SupplyRequest::class);
    }

    public function managerApprovals()
    {
        return $this->hasMany(SupplyRequest::class, 'manager_approved_by');
    }

    public function adminReleases()
    {
        return $this->hasMany(SupplyRequest::class, 'admin_released_by');
    }

    // =============================================
    // ROLE HELPERS  (existing — unchanged)
    // =============================================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function isHrManager(): bool
    {
        return $this->role === 'hr_manager';
    }

    // =============================================
    // SUPER ADMIN  (new)
    // =============================================

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if this user's role has a named permission slug.
     * Super Admin always returns true.
     */
    public function hasPermission(string $slug): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($slug, $this->getPermissionSlugs());
    }

    /**
     * Return flat array of permission slugs for this user's role.
     * Cached per role for 60 minutes; cleared when Super Admin saves permissions.
     */
    public function getPermissionSlugs(): array
    {
        if ($this->isSuperAdmin()) {
            return Permission::pluck('slug')->toArray();
        }

        return Cache::remember(
            "role_permissions_{$this->role}",
            now()->addMinutes(60),
            fn () => RolePermission::where('role', $this->role)
                ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                ->pluck('permissions.slug')
                ->toArray()
        );
    }

    /**
     * Get full Permission models for this role (used by nav blade).
     */
    public function getAllPermissions()
    {
        if ($this->isSuperAdmin()) {
            return Permission::orderBy('sort_order')->get();
        }

        return Cache::remember(
            "role_permissions_full_{$this->role}",
            now()->addMinutes(60),
            fn () => Permission::whereIn(
                'id',
                RolePermission::where('role', $this->role)->pluck('permission_id')
            )->orderBy('sort_order')->get()
        );
    }

    /**
     * Wipe the permission cache for this user's role.
     * Call after Super Admin modifies role_permissions.
     */
    public function clearPermissionCache(): void
    {
        Cache::forget("role_permissions_{$this->role}");
        Cache::forget("role_permissions_full_{$this->role}");
    }

    // =============================================
    // UI HELPERS
    // =============================================

    public function getRoleBadgeColor(): string
{
    return match ($this->role) {
        'super_admin' => 'bg-purple-100 text-purple-800',
        'admin'       => 'bg-red-100 text-red-800',
        'manager'     => 'bg-blue-100 text-blue-800',
        'hr_manager'  => 'bg-orange-100 text-orange-800',  // ← add this
        'employee'    => 'bg-green-100 text-green-800',
        default       => 'bg-gray-100 text-gray-800',
    };
}

public function getRoleLabel(): string
{
    return match ($this->role) {
        'super_admin' => 'Super Admin',
        'admin'       => 'Admin',
        'manager'     => 'Manager',
        'hr_manager'  => 'HR Manager',   // ← add this
        'employee'    => 'Employee',
        default       => ucfirst($this->role),
    };
}
}
