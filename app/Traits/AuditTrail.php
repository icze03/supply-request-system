<?php

namespace App\Traits;

use App\Models\AuditLog;

trait AuditTrail
{
    /**
     * Boot the trait
     */
    protected static function bootAuditTrail()
    {
        // Log when model is created
        static::created(function ($model) {
            if (auth()->check()) {
                AuditLog::logAction(
                    action: strtolower(class_basename($model)) . '_created',
                    model: $model,
                    newValues: $model->getAttributes(),
                    description: class_basename($model) . " created: " . ($model->name ?? $model->id)
                );
            }
        });

        // Log when model is updated
        static::updated(function ($model) {
            if (auth()->check() && $model->wasChanged()) {
                $changes = $model->getChanges();
                $original = array_intersect_key($model->getOriginal(), $changes);

                AuditLog::logAction(
                    action: strtolower(class_basename($model)) . '_updated',
                    model: $model,
                    oldValues: $original,
                    newValues: $changes,
                    description: class_basename($model) . " updated: " . ($model->name ?? $model->id)
                );
            }
        });

        // Log when model is deleted
        static::deleted(function ($model) {
            if (auth()->check()) {
                AuditLog::logAction(
                    action: strtolower(class_basename($model)) . '_deleted',
                    model: $model,
                    oldValues: $model->getAttributes(),
                    description: class_basename($model) . " deleted: " . ($model->name ?? $model->id)
                );
            }
        });
    }

    /**
     * Get audit logs for this model
     */
    public function auditLogs()
    {
        return AuditLog::forModel($this)->orderBy('created_at', 'desc')->get();
    }

    /**
     * Log a custom action
     */
    public function logAction(string $action, ?string $description = null, ?array $metadata = null)
    {
        return AuditLog::logAction(
            action: $action,
            model: $this,
            description: $description,
            metadata: $metadata
        );
    }
}