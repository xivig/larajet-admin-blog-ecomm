<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Boot the Auditable trait for Eloquent models.
     */
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            self::recordAuditActivity('created', $model);
        });

        static::updated(function ($model) {
            self::recordAuditActivity('updated', $model);
        });

        static::deleted(function ($model) {
            self::recordAuditActivity('deleted', $model);
        });
    }

    /**
     * Record an audit log entry in the database.
     */
    protected static function recordAuditActivity(string $action, $model): void
    {
        try {
            $user = Auth::user();
            $admin = Auth::guard('admin')->user();
            $userId = $admin?->id ?? $user?->id;
            $userName = $admin?->name ?? $user?->name ?? 'System';

            ActivityLog::create([
                'user_id' => $userId,
                'action' => $action,
                'description' => "{$userName} {$action} " . class_basename($model) . " #{$model->id}",
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => request()->userAgent() ?? 'CLI/System',
            ]);
        } catch (\Throwable $e) {
            // Silently pass during migrations or non-database contexts
        }
    }
}
