<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the department that owns the user.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the supply requests for the user.
     */
    public function supplyRequests()
    {
        return $this->hasMany(SupplyRequest::class);
    }

    /**
     * Get requests where user is manager approver.
     */
    public function managerApprovals()
    {
        return $this->hasMany(SupplyRequest::class, 'manager_approved_by');
    }

    /**
     * Get requests where user is admin releaser.
     */
    public function adminReleases()
    {
        return $this->hasMany(SupplyRequest::class, 'admin_released_by');
    }

    // =============================================
    // ROLE HELPER METHODS
    // =============================================

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is manager.
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if user is employee.
     */
    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    /**
     * Get role badge color for UI.
     */
    public function getRoleBadgeColor(): string
    {
        return match($this->role) {
            'admin' => 'bg-red-100 text-red-800',
            'manager' => 'bg-blue-100 text-blue-800',
            'employee' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}