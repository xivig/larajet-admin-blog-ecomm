<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user('admin') ?? $request->user();

        if (!$user) {
            return redirect()->route('admin.login');
        }

        // Super Admin bypass
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return $next($request);
        }

        // Admin guard bypass
        if ($user instanceof \App\Models\Admin) {
            return $next($request);
        }

        // Check if user has any of the requested permissions
        if (method_exists($user, 'hasAnyPermission') && $user->hasAnyPermission($permissions)) {
            return $next($request);
        }

        abort(403, 'Unauthorized access: You do not possess the required permission (' . implode(', ', $permissions) . ').');
    }
}
