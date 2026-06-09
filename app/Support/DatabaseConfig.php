<?php

namespace App\Support;

class DatabaseConfig
{
    /**
     * Override direct Supabase DB host with the IPv4 session pooler when needed.
     *
     * Laravel Cloud and many hosts cannot resolve db.{ref}.supabase.co (IPv6-only).
     * Supabase recommends the session pooler for external connections.
     */
    public static function supabasePgsqlOverrides(): array
    {
        if (env('DB_CONNECTION') !== 'pgsql') {
            return [];
        }

        if (env('DB_URL')) {
            return [];
        }

        $host = (string) env('DB_HOST', '');
        $isDirectSupabaseHost = str_starts_with($host, 'db.')
            && str_ends_with($host, '.supabase.co');

        $usePooler = filter_var(
            env('SUPABASE_DB_USE_POOLER', $isDirectSupabaseHost ? 'true' : 'false'),
            FILTER_VALIDATE_BOOL
        );

        if (! $usePooler) {
            return [];
        }

        $projectRef = self::projectRefFromUrl(env('SUPABASE_URL'));
        if (! $projectRef) {
            return [];
        }

        $poolerHost = env('SUPABASE_DB_POOLER_HOST');
        if (! $poolerHost) {
            // Default region for this project; override with SUPABASE_DB_REGION if needed.
            $region = env('SUPABASE_DB_REGION', $isDirectSupabaseHost ? 'ap-southeast-1' : null);
            if ($region) {
                $poolerHost = "aws-0-{$region}.pooler.supabase.com";
            }
        }

        if (! $poolerHost) {
            return [];
        }

        return [
            'host' => $poolerHost,
            'port' => env('SUPABASE_DB_POOLER_PORT', env('DB_PORT', '5432')),
            'database' => env('DB_DATABASE', 'postgres'),
            'username' => env('SUPABASE_DB_USERNAME', "postgres.{$projectRef}"),
            'password' => env('SUPABASE_DB_PASSWORD', env('DB_PASSWORD', '')),
            'sslmode' => env('DB_SSLMODE', 'require'),
        ];
    }

    public static function projectRefFromUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (preg_match('#https?://([^.]+)\.supabase\.co#', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
