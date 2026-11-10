<?php

namespace App\Services;

use App\Models\Analytics;
use App\Models\Country;
use App\Models\PageView;
use App\Models\Post;
use App\Models\PostView;
use App\Models\SearchLog;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnalyticsService
{
    /**
     * Record visitor telemetry and page view on incoming public request.
     */
    public function recordPageView(Request $request): ?PageView
    {
        // Ignore admin, api, asset files, and crawlers
        if ($request->is('admin', 'admin/*', 'api/*', 'sanctum/*', '_debugbar/*', 'storage/*')) {
            return null;
        }

        $ip = $request->ip() ?: '127.0.0.1';
        $userAgent = (string) $request->userAgent();
        $sessionId = session()->getId();

        $device = $this->detectDevice($userAgent);
        $browser = $this->detectBrowser($userAgent);
        $platform = $this->detectPlatform($userAgent);
        $country = $this->detectCountry($ip);

        // Record or update Visitor session
        $visitor = Visitor::where('ip', $ip)
            ->where('user_agent', $userAgent)
            ->first();

        if ($visitor) {
            $visitor->increment('hits_count');
            $visitor->update([
                'session_id' => $sessionId,
                'last_visited_at' => now(),
            ]);
        } else {
            $visitor = Visitor::create([
                'ip' => $ip,
                'session_id' => $sessionId,
                'user_agent' => $userAgent,
                'device' => $device,
                'browser' => $browser,
                'platform' => $platform,
                'country' => $country['name'],
                'country_code' => $country['code'],
                'city' => 'Unknown',
                'hits_count' => 1,
                'first_visited_at' => now(),
                'last_visited_at' => now(),
            ]);

            // Increment country count
            Country::where('code', $country['code'])->increment('visitors_count');
        }

        // Record Page View
        $pageView = PageView::create([
            'visitor_id' => $visitor->id,
            'user_id' => auth()->id(),
            'url' => $request->fullUrl(),
            'path' => '/' . ltrim($request->path(), '/'),
            'referer' => $request->header('referer'),
            'ip' => $ip,
            'device' => $device,
            'browser' => $browser,
            'country' => $country['name'],
            'session_id' => $sessionId,
        ]);

        return $pageView;
    }

    /**
     * Record post view telemetry.
     */
    public function recordPostView(Post $post, Request $request): void
    {
        $ip = $request->ip();
        $userAgent = (string) $request->userAgent();
        $visitor = Visitor::where('ip', $ip)->first();

        PostView::create([
            'post_id' => $post->id,
            'visitor_id' => $visitor?->id,
            'ip' => $ip,
            'user_agent' => substr($userAgent, 0, 500),
            'referer' => $request->header('referer'),
        ]);

        $post->increment('views_count');
    }

    /**
     * Get comprehensive metrics for the Analytics Dashboard.
     */
    public function getDashboardMetrics(): array
    {
        $today = Carbon::today();
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // 1. Visitors & Page Views
        $totalVisitors = Visitor::count();
        $uniqueVisitorsToday = Visitor::whereDate('last_visited_at', $today)->count();
        $totalPageViews = PageView::count();
        $pageViewsToday = PageView::whereDate('created_at', $today)->count();

        // 2. Top Viewed Posts
        $topPosts = Post::published()
            ->orderBy('views_count', 'desc')
            ->take(10)
            ->get();

        // 3. Top Countries
        $countries = PageView::select('country', DB::raw('count(*) as views'))
            ->groupBy('country')
            ->orderBy('views', 'desc')
            ->take(8)
            ->get();

        // 4. Device Distribution
        $devices = PageView::select('device', DB::raw('count(*) as count'))
            ->groupBy('device')
            ->orderBy('count', 'desc')
            ->get();

        // 5. Browser Distribution
        $browsers = PageView::select('browser', DB::raw('count(*) as count'))
            ->groupBy('browser')
            ->orderBy('count', 'desc')
            ->take(6)
            ->get();

        // 6. Referrers
        $referrers = PageView::whereNotNull('referer')
            ->where('referer', '!=', '')
            ->select('referer', DB::raw('count(*) as count'))
            ->groupBy('referer')
            ->orderBy('count', 'desc')
            ->take(8)
            ->get();

        // 7. Popular Categories
        $popularCategories = \App\Models\Category::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->take(8)
            ->get();

        // 8. Search Keywords
        $searchKeywords = SearchLog::orderBy('hits_count', 'desc')
            ->take(10)
            ->get();

        // 9. Monthly Traffic Trend (Last 6 Months)
        $monthlyTrend = PageView::selectRaw("strftime('%Y-%m', created_at) as month, count(*) as views, count(distinct ip) as visitors")
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        return compact(
            'totalVisitors',
            'uniqueVisitorsToday',
            'totalPageViews',
            'pageViewsToday',
            'topPosts',
            'countries',
            'devices',
            'browsers',
            'referrers',
            'popularCategories',
            'searchKeywords',
            'monthlyTrend'
        );
    }

    protected function detectDevice(string $ua): string
    {
        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $ua)) {
            return 'Tablet';
        }
        if (preg_match('/(mobile|iphone|ipod|blackberry|opera mini|iemobile|wpdesktop)/i', $ua)) {
            return 'Mobile';
        }
        return 'Desktop';
    }

    protected function detectBrowser(string $ua): string
    {
        if (str_contains($ua, 'Edg/')) return 'Edge';
        if (str_contains($ua, 'Chrome/') && !str_contains($ua, 'Edg/')) return 'Chrome';
        if (str_contains($ua, 'Safari/') && !str_contains($ua, 'Chrome/')) return 'Safari';
        if (str_contains($ua, 'Firefox/')) return 'Firefox';
        if (str_contains($ua, 'MSIE') || str_contains($ua, 'Trident/')) return 'Internet Explorer';
        return 'Other';
    }

    protected function detectPlatform(string $ua): string
    {
        if (str_contains($ua, 'Windows')) return 'Windows';
        if (str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS X')) return 'macOS';
        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) return 'iOS';
        if (str_contains($ua, 'Android')) return 'Android';
        if (str_contains($ua, 'Linux')) return 'Linux';
        return 'Unknown';
    }

    protected function detectCountry(string $ip): array
    {
        // Default local fallback
        if ($ip === '127.0.0.1' || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return ['name' => 'United States', 'code' => 'US'];
        }

        // Could integrate GeoIP2 or free ip-api in production
        return ['name' => 'United States', 'code' => 'US'];
    }
}
