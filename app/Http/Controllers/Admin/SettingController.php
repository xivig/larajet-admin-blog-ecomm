<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use DateTime;
use DateTimeZone;

class SettingController extends Controller
{
    /**
     * Get all standard timezones grouped by region with formatted offsets.
     */
    public static function getAllTimezones(): array
    {
        $timezones = [];
        $now = new DateTime('now', new DateTimeZone('UTC'));

        foreach (DateTimeZone::listIdentifiers() as $tzId) {
            $parts = explode('/', $tzId, 2);
            $region = $parts[0];
            $city = isset($parts[1]) ? str_replace('_', ' ', $parts[1]) : $region;

            try {
                $tz = new DateTimeZone($tzId);
                $offset = $tz->getOffset($now);
                $hours = intdiv($offset, 3600);
                $minutes = abs(intdiv($offset % 3600, 60));
                $sign = $offset >= 0 ? '+' : '-';
                $formattedOffset = sprintf('UTC%s%02d:%02d', $sign, abs($hours), $minutes);
            } catch (\Throwable $e) {
                $offset = 0;
                $formattedOffset = 'UTC+00:00';
            }

            $timezones[$region][] = [
                'id' => $tzId,
                'name' => $city,
                'offset' => $offset,
                'formatted_offset' => $formattedOffset,
                'label' => "({$formattedOffset}) " . str_replace('_', ' ', $tzId),
            ];
        }

        // Sort timezones within each region by offset then name
        foreach ($timezones as $region => &$tzList) {
            usort($tzList, function ($a, $b) {
                if ($a['offset'] === $b['offset']) {
                    return strcmp($a['id'], $b['id']);
                }
                return $a['offset'] <=> $b['offset'];
            });
        }

        return $timezones;
    }

    /**
     * Display the site settings management dashboard.
     */
    public function index(): View
    {
        $settings = Setting::getAll();

        // 1. PHP & Database Engine Telemetry
        $phpVersion = 'PHP ' . PHP_VERSION;
        $dbConnection = config('database.default', 'mysql');
        $dbDriverName = match ($dbConnection) {
            'sqlite' => 'SQLite v3 (Dev Mode)',
            'mysql' => 'MySQL / MariaDB 8.x (InnoDB)',
            'pgsql' => 'PostgreSQL (Enterprise)',
            default => strtoupper($dbConnection),
        };

        // 2. Database Queue Telemetry
        $queueDriver = config('queue.default', 'database');
        try {
            $pendingJobsCount = DB::table('jobs')->count();
            $failedJobsCount = DB::table('failed_jobs')->count();
        } catch (\Throwable $e) {
            $pendingJobsCount = 0;
            $failedJobsCount = 0;
        }

        // 3. Database Cache Telemetry
        $cacheDriver = config('cache.default', 'database');
        try {
            $cacheCount = DB::table('cache')->count();
        } catch (\Throwable $e) {
            $cacheCount = 0;
        }

        // 4. Local Storage Health
        $storageDriver = ucfirst(config('filesystems.default', 'local')) . ' Storage';
        $storageLinkConnected = File::exists(public_path('storage'));
        $uploadsDir = public_path('uploads');
        $uploadsWritable = false;
        try {
            $testF = $uploadsDir . '/.tmp_chk_' . uniqid();
            if (@file_put_contents($testF, 'test') !== false) {
                @unlink($testF);
                $uploadsWritable = true;
            }
        } catch (\Throwable $e) {}

        // 5. Active Timezone & Preview Time
        $activeTimezone = $settings->get('system_timezone', 'Europe/Paris');
        try {
            $currentTimeInTz = Carbon::now($activeTimezone)->format('M d, Y h:i A');
        } catch (\Throwable $e) {
            $currentTimeInTz = Carbon::now()->format('M d, Y h:i A');
        }
        $allTimezones = self::getAllTimezones();

        // 6. Registered Scheduler Tasks
        $schedulerEvents = [];
        try {
            $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
            foreach ($schedule->events() as $event) {
                $cmd = $event->command ?? $event->description ?? 'Custom Job Handler';
                if (is_string($cmd) && str_contains($cmd, 'artisan')) {
                    $parts = explode('artisan', $cmd);
                    $cmd = 'php artisan' . ($parts[1] ?? '');
                }
                $schedulerEvents[] = [
                    'command' => $cmd,
                    'expression' => $event->expression,
                    'description' => $event->description ?? 'Automated Routine Task',
                ];
            }
        } catch (\Throwable $e) {}

        return view('admin.settings.index', compact(
            'settings',
            'phpVersion',
            'dbDriverName',
            'queueDriver',
            'pendingJobsCount',
            'failedJobsCount',
            'cacheDriver',
            'cacheCount',
            'storageDriver',
            'storageLinkConnected',
            'uploadsWritable',
            'activeTimezone',
            'currentTimeInTz',
            'allTimezones',
            'schedulerEvents'
        ));
    }

    /**
     * Update Site Maintenance state.
     */
    public function updateMaintenance(Request $request): RedirectResponse
    {
        $isMaintenance = $request->boolean('site_maintenance') ? '1' : '0';
        Setting::set('site_maintenance', $isMaintenance, 'maintenance');

        $statusLabel = $isMaintenance === '1' ? 'enabled' : 'disabled';
        return redirect()->route('admin.settings.index')->with('success', "Site Maintenance mode has been {$statusLabel}.");
    }

    /**
     * Update External Integrations credentials.
     */
    public function updateIntegrations(Request $request): RedirectResponse
    {
        $request->validate([
            'google_analytics_id' => 'nullable|string|max:100',
            'openai_api_key' => 'nullable|string|max:255',
            'mailchimp_api_key' => 'nullable|string|max:255',
            'mailchimp_list_id' => 'nullable|string|max:100',
        ]);

        if ($request->has('google_analytics_id')) {
            Setting::set('google_analytics_id', $request->google_analytics_id, 'integrations');
        }

        if ($request->has('openai_api_key') && !str_contains($request->openai_api_key, '••••')) {
            Setting::set('openai_api_key', $request->openai_api_key, 'integrations');
        }

        if ($request->has('mailchimp_api_key') && !str_contains($request->mailchimp_api_key, '••••')) {
            Setting::set('mailchimp_api_key', $request->mailchimp_api_key, 'integrations');
        }

        if ($request->has('mailchimp_list_id')) {
            Setting::set('mailchimp_list_id', $request->mailchimp_list_id, 'integrations');
        }

        return redirect()->route('admin.settings.index')->with('success', 'External integrations synchronized successfully.');
    }

    /**
     * Update Editorial Configuration & Branding.
     */
    public function updateEditorial(Request $request): RedirectResponse
    {
        $request->validate([
            'gazette_title' => 'required|string|max:255',
            'gazette_tagline' => 'nullable|string|max:255',
            'editorial_synopsis' => 'nullable|string|max:1000',
            'editorial_email' => 'required|email|max:255',
            'stories_per_page' => 'required|integer|min:1|max:100',
            'comments_moderation' => 'required|string|in:Auto-Approve,Manual Review,Strict Spam Filter',
            'system_timezone' => ['required', 'string', Rule::in(DateTimeZone::listIdentifiers())],
        ]);

        Setting::set('gazette_title', $request->gazette_title, 'editorial');
        Setting::set('gazette_tagline', $request->gazette_tagline, 'editorial');
        Setting::set('editorial_synopsis', $request->editorial_synopsis, 'editorial');
        Setting::set('editorial_email', $request->editorial_email, 'editorial');
        Setting::set('stories_per_page', (string)$request->stories_per_page, 'editorial');
        Setting::set('comments_moderation', $request->comments_moderation, 'editorial');
        Setting::set('system_timezone', $request->system_timezone, 'editorial');

        // Apply dynamically to current runtime
        date_default_timezone_set($request->system_timezone);
        config(['app.timezone' => $request->system_timezone]);

        return redirect()->route('admin.settings.index')->with('success', 'Editorial configuration & branding saved successfully.');
    }

    /**
     * Update or quickly apply Default System Timezone.
     */
    public function updateTimezone(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'system_timezone' => ['required', 'string', Rule::in(DateTimeZone::listIdentifiers())],
        ]);

        $tz = $request->system_timezone;
        Setting::set('system_timezone', $tz, 'editorial');

        // Apply immediately to current execution context
        date_default_timezone_set($tz);
        config(['app.timezone' => $tz]);

        try {
            $currentTime = Carbon::now($tz)->format('M d, Y h:i A');
        } catch (\Throwable $e) {
            $currentTime = Carbon::now()->format('M d, Y h:i A');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "System timezone updated to {$tz}.",
                'timezone' => $tz,
                'current_time' => $currentTime
            ]);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', "System timezone successfully updated to {$tz} (Current time: {$currentTime}).");
    }

    /**
     * Serve Dynamic RSS 2.0 XML Feed (/feed.xml).
     */
    public function feedXml(): Response
    {
        $xml = Cache::remember('cms_rss_feed_xml', 3600, function () {
            $settings = Setting::getAll();
            $title = $settings->get('gazette_title', 'XIVIG Maison Gazette');
            $tagline = $settings->get('gazette_tagline', 'The Haute Couture & Horology Journal');
            $synopsis = $settings->get('editorial_synopsis', 'Chronicles of bespoke craftsmanship and contemporary aesthetics.');

            $posts = Post::where('status', true)->latest()->take(20)->get();

            $output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $output .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
            $output .= "  <channel>\n";
            $output .= '    <title>' . htmlspecialchars($title) . "</title>\n";
            $output .= '    <link>' . url('/') . "</link>\n";
            $output .= '    <description>' . htmlspecialchars($tagline . ' - ' . $synopsis) . "</description>\n";
            $output .= '    <language>en-us</language>\n';
            $output .= '    <lastBuildDate>' . Carbon::now()->toRssString() . "</lastBuildDate>\n";
            $output .= '    <atom:link href="' . url('/feed.xml') . '" rel="self" type="application/rss+xml" />' . "\n";

            foreach ($posts as $post) {
                $output .= "    <item>\n";
                $output .= '      <title>' . htmlspecialchars($post->title) . "</title>\n";
                $output .= '      <link>' . url('/blog/' . $post->slug) . "</link>\n";
                $output .= '      <guid isPermaLink="true">' . url('/blog/' . $post->slug) . "</guid>\n";
                $output .= '      <pubDate>' . ($post->created_at ? $post->created_at->toRssString() : Carbon::now()->toRssString()) . "</pubDate>\n";
                $output .= '      <description>' . htmlspecialchars($post->excerpt ?: Str::limit(strip_tags($post->content), 200)) . "</description>\n";
                $output .= "    </item>\n";
            }

            $output .= "  </channel>\n";
            $output .= "</rss>";

            return $output;
        });

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    /**
     * Serve Dynamic XML Sitemap (/sitemap.xml).
     */
    public function sitemapXml(): Response
    {
        $xml = Cache::remember('cms_sitemap_xml', 3600, function () {
            $posts = Post::where('status', true)->latest()->get();
            $categories = Category::all();

            $output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            // Homepage
            $output .= "  <url>\n";
            $output .= '    <loc>' . url('/') . "</loc>\n";
            $output .= '    <changefreq>daily</changefreq>' . "\n";
            $output .= '    <priority>1.0</priority>' . "\n";
            $output .= "  </url>\n";

            // Blog Index
            $output .= "  <url>\n";
            $output .= '    <loc>' . url('/blog') . "</loc>\n";
            $output .= '    <changefreq>daily</changefreq>' . "\n";
            $output .= '    <priority>0.9</priority>' . "\n";
            $output .= "  </url>\n";

            // Posts
            foreach ($posts as $post) {
                $output .= "  <url>\n";
                $output .= '    <loc>' . url('/blog/' . $post->slug) . "</loc>\n";
                $output .= '    <lastmod>' . ($post->updated_at ? $post->updated_at->toAtomString() : Carbon::now()->toAtomString()) . "</lastmod>\n";
                $output .= '    <changefreq>weekly</changefreq>' . "\n";
                $output .= '    <priority>0.8</priority>' . "\n";
                $output .= "  </url>\n";
            }

            // Categories
            foreach ($categories as $cat) {
                $output .= "  <url>\n";
                $output .= '    <loc>' . url('/blog?category=' . $cat->slug) . "</loc>\n";
                $output .= '    <changefreq>weekly</changefreq>' . "\n";
                $output .= '    <priority>0.6</priority>' . "\n";
                $output .= "  </url>\n";
            }

            $output .= "</urlset>";

            return $output;
        });

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    /**
     * Purge all database cache.
     */
    public function purgeCache(Request $request): RedirectResponse|JsonResponse
    {
        Cache::flush();
        Setting::clearCache();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Database cache flushed completely.'
            ]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Database cache flushed successfully.');
    }

    /**
     * Pre-warm high-frequency Database Cache entries.
     */
    public function warmCache(Request $request): RedirectResponse|JsonResponse
    {
        Cache::forget('site_settings_all');
        Cache::forget('cms_sitemap_xml');
        Cache::forget('cms_rss_feed_xml');

        Setting::getAll();
        Category::all();
        Tag::all();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Database cache successfully pre-warmed.'
            ]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Database cache pre-warmed successfully.');
    }

    /**
     * Trigger scheduler execution on-demand.
     */
    public function runScheduler(Request $request): RedirectResponse|JsonResponse
    {
        Artisan::call('schedule:run');
        $output = trim(Artisan::output()) ?: 'All scheduled tasks evaluated and processed.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Scheduler executed on demand.',
                'output' => $output
            ]);
        }

        return redirect()->route('admin.settings.index')->with('success', "Scheduler ran: {$output}");
    }

    /**
     * Trigger queue worker batch on-demand.
     */
    public function runQueueWorker(Request $request): RedirectResponse|JsonResponse
    {
        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
            '--tries' => 3
        ]);
        $output = trim(Artisan::output()) ?: 'Processed pending jobs in database queue.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Queue batch processed.',
                'output' => $output
            ]);
        }

        return redirect()->route('admin.settings.index')->with('success', "Queue worker ran: {$output}");
    }

    /**
     * Retry all failed jobs in failed_jobs table.
     */
    public function retryFailedJobs(Request $request): RedirectResponse|JsonResponse
    {
        Artisan::call('queue:retry', ['id' => 'all']);
        $output = trim(Artisan::output()) ?: 'All failed jobs moved back to pending database queue.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Failed jobs retried.',
                'output' => $output
            ]);
        }

        return redirect()->route('admin.settings.index')->with('success', "Retried failed jobs: {$output}");
    }

    /**
     * Clear all failed jobs.
     */
    public function flushFailedJobs(Request $request): RedirectResponse|JsonResponse
    {
        Artisan::call('queue:flush');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Failed jobs flushed.'
            ]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Failed jobs cleared.');
    }
}
