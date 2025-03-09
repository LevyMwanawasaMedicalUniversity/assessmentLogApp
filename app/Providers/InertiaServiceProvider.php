<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Http\Middleware\HandleInertiaRequests;

class InertiaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register the Inertia middleware
        $this->app['router']->pushMiddlewareToGroup('web', HandleInertiaRequests::class);
    }
}
