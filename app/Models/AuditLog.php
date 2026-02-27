<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false; // We only use created_at

    protected $fillable = [
        'action',
        'model_type',
        'model_id',
        'user_id',
        'department_id',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'description',
        'metadata',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the auditable model
     */
    public function auditable()
    {
        return $this->morphTo('model');
    }

    /**
     * Static method to log an action
     */
    public static function logAction(
        string $action,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null,
        ?array $metadata = null
    ) {
        return static::create([
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'user_id' => auth()->id(),
            'department_id' => auth()->user()?->department_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get formatted action name
     */
    public function getActionNameAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->action));
    }

    /**
     * Get action badge color
     */
    public function getActionColorAttribute()
    {
        return match(true) {
            str_contains($this->action, 'created') => 'blue',
            str_contains($this->action, 'approved') => 'green',
            str_contains($this->action, 'rejected') => 'red',
            str_contains($this->action, 'released') => 'indigo',
            str_contains($this->action, 'cancelled') => 'yellow',
            str_contains($this->action, 'updated') => 'purple',
            str_contains($this->action, 'deleted') => 'red',
            default => 'gray',
        };
    }

    /**
     * Get action icon
     */
    public function getActionIconAttribute()
    {
        return match(true) {
            str_contains($this->action, 'created') => '➕',
            str_contains($this->action, 'approved') => '✅',
            str_contains($this->action, 'rejected') => '❌',
            str_contains($this->action, 'released') => '📦',
            str_contains($this->action, 'cancelled') => '🚫',
            str_contains($this->action, 'updated') => '✏️',
            str_contains($this->action, 'deleted') => '🗑️',
            str_contains($this->action, 'login') => '🔐',
            str_contains($this->action, 'budget') => '💰',
            default => '📝',
        };
    }

    /**
     * Scope for specific actions
     */
    public function scopeOfAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for specific model
     */
    public function scopeForModel($query, Model $model)
    {
        return $query->where('model_type', get_class($model))
                     ->where('model_id', $model->id);
    }

    /**
     * Scope for specific user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific department
     */
    public function scopeByDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}