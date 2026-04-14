<?php

namespace App\Traits;

use App\Models\AuditLog;
use App\Services\DatabaseQueryService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $localAuditId = null;
        $localUserId = Auth::id();
        $userEmail = null;

        try {
            $authUser = Auth::user();
            $userEmail = is_object($authUser) ? ($authUser->email ?? null) : null;
        } catch (\Throwable $e) {
            // Keep audit non-blocking if auth context is unavailable.
        }

        try {
            $created = AuditLog::create([
                'user_id' => $localUserId, // nullable for system actions
                'action' => $action,
                'resource_type' => $resourceType,
                'resource_id' => $resourceId === null ? null : (string) $resourceId,
                'payload' => $payload,
            ]);
            $localAuditId = $created->id;
        } catch (\Throwable $e) {
            // Never let audit failures break the main request
            report($e);
        }

        // Mirror to Supabase in real-time so cloud deployments stay current.
        // If local audit insert failed, use a synthetic negative ID to satisfy
        // Supabase `local_audit_log_id` NOT NULL + UNIQUE constraints.
        try {
            /** @var DatabaseQueryService $queryService */
            $queryService = app(DatabaseQueryService::class);

            $supabaseUserId = null;
            if (is_string($userEmail) && $userEmail !== '') {
                $sbUser = $queryService->getUserByEmail($userEmail);
                $supabaseUserId = is_array($sbUser) ? ($sbUser['id'] ?? null) : null;
            }

            $safeLocalAuditId = is_int($localAuditId)
                ? $localAuditId
                : (int) (0 - floor(microtime(true) * 1000000));

            $sync = $queryService->upsertAuditLog([
                'local_audit_log_id' => $safeLocalAuditId,
                'user_id' => $supabaseUserId,
                'local_user_id' => is_int($localUserId) ? $localUserId : null,
                'user_email' => $userEmail,
                'action' => $action,
                'resource_type' => $resourceType,
                'resource_id' => $resourceId === null ? null : (string) $resourceId,
                'payload' => $payload,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]);

            if (is_array($sync) && isset($sync['error'])) {
                Log::warning('Audit log mirror to Supabase failed', [
                    'action' => $action,
                    'resource_type' => $resourceType,
                    'resource_id' => $resourceId,
                    'error' => $sync['error'] ?? null,
                ]);
            }
        } catch (\Throwable $e) {
            // Never let audit mirror failures break the main request.
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

