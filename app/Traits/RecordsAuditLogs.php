<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Trait RecordsAuditLogs
 *
 * Provides helper methods to record structured audit logs from
 * models, services, or controllers.
 */
trait RecordsAuditLogs
{
    /**
     * Record a generic audit log entry.
     *
     * @param  string  $action         Short action name, e.g. 'event.created'
     * @param  string  $resourceType   Logical resource type, e.g. 'Event', 'User'
     * @param  string|int|null  $resourceId  Flexible ID (integer or UUID)
     * @param  array|null  $payload    Additional context (old/new values, request data, etc.)
     */
    protected function audit(string $action, string $resourceType, string|int|null $resourceId = null, ?array $payload = null): void
    {
        try {
            AuditLog::create([
                'user_id' => Auth::id(), // nullable for system actions
                'action' => $action,
                'resource_type' => $resourceType,
                'resource_id' => $resourceId === null ? null : (string) $resourceId,
                'payload' => $payload,
            ]);
        } catch (\Throwable $e) {
            // Never let audit failures break the main request
            report($e);
        }
    }

    /**
     * Convenience method to log create/update/delete for Eloquent models.
     */
    protected function auditModelChange(string $action, Model $model, ?array $payload = null): void
    {
        $resourceType = class_basename($model);
        $resourceId = $model->getKey();

        // Include basic model data if no explicit payload is provided
        $payload = $payload ?? [
            'attributes' => $model->getAttributes(),
        ];

        $this->audit($action, $resourceType, $resourceId, $payload);
    }
}

