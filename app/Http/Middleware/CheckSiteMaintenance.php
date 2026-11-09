<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSiteMaintenance
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $isMaintenance = Setting::get('site_maintenance') === '1';
        } catch (\Throwable $e) {
            $isMaintenance = false;
        }

        if ($isMaintenance) {
            // Always allow admin routes (login, dashboard, settings, etc.) so administrators can manage the system
            if ($request->is('admin') || $request->is('admin/*')) {
                return $next($request);
            }

            // Always allow authenticated administrators to preview and test the public site
            if (Auth::guard('admin')->check() || (Auth::check() && (Auth::user()->is_admin ?? false))) {
                return $next($request);
            }

            // Trigger the 503 Maintenance Page
            return response()->view('errors.503', [], 503);
        }

        return $next($request);
    }
}
