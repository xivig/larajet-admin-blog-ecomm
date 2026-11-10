<?php

namespace App\Http\Middleware;

use App\Services\AnalyticsService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitorMiddleware
{
    public function __construct(protected AnalyticsService $analytics)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track successful GET requests on frontend
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            $this->analytics->recordPageView($request);
        }

        return $response;
    }
}
