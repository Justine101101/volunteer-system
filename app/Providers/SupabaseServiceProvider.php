<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SupabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Only register the binding if the Supabase SDK is available
        if (class_exists('\\Supabase\\SupabaseClient')) {
            $this->app->singleton('supabase', function ($app) {
                $url = env('SUPABASE_URL');
                $anonKey = env('SUPABASE_ANON_KEY');

                return new \Supabase\SupabaseClient([
                    'url' => $url,
                    'key' => $anonKey,
                ]);
            });
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}