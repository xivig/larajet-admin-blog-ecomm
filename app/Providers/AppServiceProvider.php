<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        // Apply dynamically configured system timezone globally
        try {
            $configuredTz = \App\Models\Setting::get('system_timezone');
            if ($configuredTz && in_array($configuredTz, \DateTimeZone::listIdentifiers())) {
                date_default_timezone_set($configuredTz);
                config(['app.timezone' => $configuredTz]);
            }
        } catch (\Throwable $e) {
            // Ignore during early migrations or database uninitialized state
        }
    }
}
