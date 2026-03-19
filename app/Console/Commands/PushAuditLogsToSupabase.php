<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Services\DatabaseQueryService;
use Illuminate\Console\Command;

class PushAuditLogsToSupabase extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'supabase:push-audit-logs {--limit=0 : Limit number of logs to sync (0 = all)}';

    /**
     * The console command description.
     */
    protected $description = 'Push local SQLite audit_logs into Supabase audit_logs table (idempotent).';

    public function handle(DatabaseQueryService $queryService): int
    {
        $limit = (int) $this->option('limit');

        $query = AuditLog::with('user')->orderBy('id', 'asc');
        if ($limit > 0) {
            $query->limit($limit);
        }

        $logs = $query->get();

        $this->info('Pushing audit logs to Supabase...');
        $this->info('Logs to process: ' . $logs->count());

        $success = 0;
        $failed = 0;

        foreach ($logs as $log) {
            $userEmail = $log->user?->email;
            $supabaseUserId = null;

            // Best-effort map: find Supabase user UUID by email
            if ($userEmail) {
                $sbUser = $queryService->getUserByEmail($userEmail);
                $supabaseUserId = is_array($sbUser) ? ($sbUser['id'] ?? null) : null;
            }

            $result = $queryService->upsertAuditLog([
                'local_audit_log_id' => (int) $log->id,
                'user_id' => $supabaseUserId,
                'local_user_id' => $log->user_id ? (int) $log->user_id : null,
                'user_email' => $userEmail,
                'action' => (string) $log->action,
                'resource_type' => (string) $log->resource_type,
                'resource_id' => $log->resource_id ? (string) $log->resource_id : null,
                'payload' => $log->payload,
                'created_at' => $log->created_at?->toISOString(),
                'updated_at' => $log->updated_at?->toISOString(),
            ]);

            if (is_array($result) && isset($result['error'])) {
                $failed++;
                $this->warn('Failed log #' . $log->id . ': ' . ($result['error'] ?? 'Unknown error'));
                continue;
            }

            $success++;
        }

        $this->info("Done. Synced: {$success}. Failed: {$failed}.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}

