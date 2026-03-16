<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'route_pattern',
        'url_prefix',
        'sort_order',
    ];

    /**
     * All role_permission rows that grant this permission.
     */
    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'permission_id');
    }
}
