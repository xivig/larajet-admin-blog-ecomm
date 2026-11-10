<?php

namespace App\Http\Middleware;

use App\Models\NotFoundLog;
use App\Models\RedirectRule;
use App\Models\SlugHistory;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UrlRedirectMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip redirects and 404 tracking for admin, api, or static asset requests
        if ($request->is('admin', 'admin/*', 'api/*', 'sanctum/*', '_debugbar/*')) {
            return $next($request);
        }

        $rawPath = '/' . ltrim($request->path(), '/');
        $altPath = ltrim($request->path(), '/');

        // 1. Check custom 301 / 302 Redirect Rules
        $rule = RedirectRule::where('is_active', true)
            ->where(function ($q) use ($rawPath, $altPath) {
                $q->where('source_path', $rawPath)
                  ->orWhere('source_path', $altPath)
                  ->orWhere('source_path', '/' . $altPath);
            })
            ->first();

        if ($rule) {
            $rule->increment('hits_count');
            $rule->update(['last_accessed_at' => now()]);

            $target = $rule->target_path;
            if (!str_starts_with($target, 'http://') && !str_starts_with($target, 'https://')) {
                $target = url($target);
            }
            if (($qs = $request->getQueryString()) && !str_contains($target, '?')) {
                $target .= '?' . $qs;
            }

            return redirect()->to($target, $rule->status_code ?: 301);
        }

        // 2. Check Automatic Slug History (e.g. /blog/{old_slug} or /post/{old_slug})
        $segments = $request->segments();
        if (count($segments) >= 1) {
            $lastSegment = end($segments);
            $slugHistory = SlugHistory::where('old_slug', $lastSegment)->latest()->first();

            if ($slugHistory) {
                // If it's a post
                if ($slugHistory->model_type === 'Post' || str_contains($rawPath, '/blog/') || str_contains($rawPath, '/post/')) {
                    return redirect()->to(url('/blog/' . $slugHistory->new_slug), 301);
                }
                // If it's a page
                if ($slugHistory->model_type === 'Page' || str_contains($rawPath, '/page/')) {
                    return redirect()->to(url('/page/' . $slugHistory->new_slug), 301);
                }
            }
        }

        $response = $next($request);

        // 3. Log 404 Not Found requests
        if ($response->getStatusCode() === 404 && !$request->is('favicon.ico', '*.png', '*.jpg', '*.css', '*.js')) {
            $currentUrl = $request->fullUrl();
            $log = NotFoundLog::where('url', $currentUrl)->first();

            if ($log) {
                $log->increment('hits_count');
                $log->update([
                    'referer' => $request->header('referer'),
                    'ip' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 500),
                    'last_seen_at' => now(),
                ]);
            } else {
                NotFoundLog::create([
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

        return $response;
    }
}
