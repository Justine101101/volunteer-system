<?php

namespace App\Providers;

use App\Models\Event;
use App\Policies\EventPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register policies (Laravel 11 minimal bootstrap doesn't include this by default)
        Gate::policy(Event::class, EventPolicy::class);
    }
}

