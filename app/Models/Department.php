<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'passcode',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the users for the department.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the managers for the department.
     */
    public function managers()
    {
        return $this->users()->where('role', 'manager');
    }

    /**
     * Get the employees for the department.
     */
    public function employees()
    {
        return $this->users()->where('role', 'employee');
    }

    /**
     * Get the supply requests for the department.
     */
    public function supplyRequests()
    {
        return $this->hasMany(SupplyRequest::class);
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Verify manager passcode.
     *
     * @param string $passcode
     * @return bool
     */
    public function verifyPasscode(string $passcode): bool
    {
        return $this->passcode === $passcode;
    }

    // =============================================
    // QUERY SCOPES
    // =============================================

    /**
     * Scope a query to only include active departments.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}