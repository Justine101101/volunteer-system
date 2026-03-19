<?php

namespace App\Console\Commands;

use App\Services\DatabaseQueryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PushPasswordResetTokensToSupabase extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'supabase:push-password-reset-tokens {--limit=0 : Limit number of rows to sync (0 = all)}';

    /**
     * The console command description.
     */
    protected $description = 'Push local SQLite password_reset_tokens rows into Supabase (idempotent by email).';

    public function handle(DatabaseQueryService $queryService): int
    {
        $limit = (int) $this->option('limit');

        $query = DB::table('password_reset_tokens')->orderBy('email', 'asc');
        if ($limit > 0) {
            $query->limit($limit);
        }

        $rows = $query->get();

        $this->info('Pushing password reset tokens to Supabase...');
        $this->info('Rows to process: ' . $rows->count());

        $success = 0;
        $failed = 0;

        foreach ($rows as $row) {
            $result = $queryService->upsertPasswordResetToken([
                'email' => (string) $row->email,
                'token' => (string) $row->token,
                'created_at' => $row->created_at ? (string) $row->created_at : null,
            ]);

            if (is_array($result) && isset($result['error'])) {
                $failed++;
                $this->warn('Failed ' . $row->email . ': ' . ($result['error'] ?? 'Unknown error'));
                continue;
            }

            $success++;
        }

        $this->info("Done. Synced: {$success}. Failed: {$failed}.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}

