<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }
            return route('login');
        });

        $middleware->redirectUsersTo(function (\Illuminate\Http\Request $request) {
            if (\Illuminate\Support\Facades\Auth::guard('admin')->check()) {
                return route('admin.dashboard');
            }
            return route('dashboard');
        });

        $middleware->append([
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
            \App\Http\Middleware\UrlRedirectMiddleware::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\CheckSiteMaintenance::class,
            \App\Http\Middleware\TrackVisitorMiddleware::class,
        ]);

        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhooks/*',
            'ecomm/callback/razorpay/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            if (!$request->is('admin', 'admin/*', 'api/*', 'favicon.ico', '*.png', '*.jpg', '*.css', '*.js')) {
                $currentUrl = $request->fullUrl();
                $log = \App\Models\NotFoundLog::where('url', $currentUrl)->first();
                if ($log) {
                    $log->increment('hits_count');
                    $log->update([
                        'referer' => $request->header('referer'),
                        'ip' => $request->ip(),
                        'user_agent' => substr((string) $request->userAgent(), 0, 500),
                        'last_seen_at' => now(),
                    ]);
                } else {
                    \App\Models\NotFoundLog::create([
                        'url' => $currentUrl,
                        'referer' => $request->header('referer'),
                        'ip' => $request->ip(),
                        'user_agent' => substr((string) $request->userAgent(), 0, 500),
                        'hits_count' => 1,
                        'is_resolved' => false,
                        'last_seen_at' => now(),
                    ]);
                }
            }
            return null;
        });
    })->create();
