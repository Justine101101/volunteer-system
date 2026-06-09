<?php

namespace App\Support;

class DatabaseConfig
{
    /**
     * Pick the database connection for this runtime.
     *
     * Laravel Cloud cannot use Supabase's direct db.{ref}.supabase.co host, and the
     * shared pooler hostname must be copied from the Supabase dashboard (aws-0 vs aws-1
     * varies per project). Without an explicit pooler URI/host we use SQLite for Laravel
     * auth tables, while business data continues to use the Supabase REST API.
     */
    public static function resolveDefaultConnection(): string
    {
        $requested = (string) env('DB_CONNECTION', 'sqlite');

        if ($requested !== 'pgsql' || ! self::isLaravelCloud()) {
            return $requested;
        }

        $host = strtolower((string) env('DB_HOST', ''));

        if (
            self::isLaravelCloudDatabaseHost($host)
            || env('DB_URL')
            || env('SUPABASE_DB_POOLER_HOST')
        ) {
            return 'pgsql';
        }

        return 'sqlite';
    }

    /**
     * Override direct Supabase DB host with an explicit IPv4 session pooler host.
     */
    public static function supabasePgsqlOverrides(): array
    {
        if (env('DB_CONNECTION') !== 'pgsql') {
            return [];
        }

        if (env('DB_URL')) {
            return [];
        }

        $poolerHost = env('SUPABASE_DB_POOLER_HOST');
        if (! $poolerHost) {
            return [];
        }

        $projectRef = self::projectRefFromUrl(env('SUPABASE_URL'));
        if (! $projectRef) {
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

    public static function sqliteDatabasePath(): string
    {
        return (string) env('DB_DATABASE', database_path('database.sqlite'));
    }

    public static function ensureSqliteDatabaseExists(): void
    {
        $path = self::sqliteDatabasePath();

        if ($path === '' || $path === ':memory:') {
            return;
        }

        if (file_exists($path)) {
            return;
        }

        $directory = dirname($path);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        touch($path);
    }

    public static function isLaravelCloud(): bool
    {
        if (filter_var(env('LARAVEL_CLOUD'), FILTER_VALIDATE_BOOL)) {
            return true;
        }

        return str_contains(strtolower((string) env('APP_URL', '')), 'laravel.cloud');
    }

    public static function isSupabaseDirectHost(string $host): bool
    {
        return str_starts_with($host, 'db.') && str_ends_with($host, '.supabase.co');
    }

    public static function isLaravelCloudDatabaseHost(string $host): bool
    {
        return str_contains($host, '.pg.laravel.cloud')
            || str_contains($host, '.mysql.laravel.cloud');
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
