<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (file_exists(app_path('Helpers/helpers.php'))) {
            require_once app_path('Helpers/helpers.php');
        }

        $this->app->register(\App\Providers\RepositoryServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->isProduction() && str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        // Model Observers
        \App\Models\Post::observe(\App\Observers\PostObserver::class);
        \App\Models\Setting::observe(\App\Observers\SettingObserver::class);
        \App\Models\Comment::observe(\App\Observers\CommentObserver::class);

        // Authentication & Login History Event Listeners
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\RecordLoginHistory::class,
        );

        \Illuminate\Support\Facades\Event::listen(
            \App\Events\PostViewed::class,
            \App\Listeners\RecordPostViewListener::class,
        );

        \Illuminate\Support\Facades\Event::listen(
            \App\Events\ContactSubmitted::class,
            \App\Listeners\SendContactNotificationListener::class,
        );

        // Permission-Based Access Control Gates
        \Illuminate\Support\Facades\Gate::before(function ($user, string $ability) {
            if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                return true;
            }
        });

        \Illuminate\Support\Facades\Gate::after(function ($user, string $ability) {
            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission($ability);
            }
            return false;
        });

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
