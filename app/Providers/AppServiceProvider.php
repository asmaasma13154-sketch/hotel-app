<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Middleware\RoleMiddleware;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
{
    // No bindings required for this application
}

    public function boot(): void
    {
        Schema::defaultStringLength(191);
        app('router')->aliasMiddleware('role', RoleMiddleware::class);
    }
}