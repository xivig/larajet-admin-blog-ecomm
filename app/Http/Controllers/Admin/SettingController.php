<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    /**
     * Display the site settings management dashboard.
     */
    public function index(): View
    {
        $settings = Setting::getAll();

        // System Engine Telemetry
        $storageDriver = ucfirst(config('filesystems.default', 'local')) . ' Disk';
        
        $dbConnection = config('database.default', 'mysql');
        $dbDriverName = match ($dbConnection) {
            'sqlite' => 'SQLite v3',
            'mysql' => 'MySQL / MariaDB',
            'pgsql' => 'PostgreSQL',
            default => strtoupper($dbConnection),
        };

        // Active Timezone and preview time
        $activeTimezone = $settings->get('system_timezone', 'Europe/Paris');
        try {
            $currentTimeInTz = Carbon::now($activeTimezone)->format('M d, Y h:i A');
        } catch (\Throwable $e) {
            $currentTimeInTz = Carbon::now()->format('M d, Y h:i A');
        }

        return view('admin.settings.index', compact('settings', 'storageDriver', 'dbDriverName', 'activeTimezone', 'currentTimeInTz'));
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
            'system_timezone' => 'required|string|max:100',
        ]);

        Setting::set('gazette_title', $request->gazette_title, 'editorial');
        Setting::set('gazette_tagline', $request->gazette_tagline, 'editorial');
        Setting::set('editorial_synopsis', $request->editorial_synopsis, 'editorial');
        Setting::set('editorial_email', $request->editorial_email, 'editorial');
        Setting::set('stories_per_page', (string)$request->stories_per_page, 'editorial');
        Setting::set('comments_moderation', $request->comments_moderation, 'editorial');
        Setting::set('system_timezone', $request->system_timezone, 'editorial');

        return redirect()->route('admin.settings.index')->with('success', 'Editorial configuration & branding saved.');
    }

    /**
     * Serve Dynamic RSS 2.0 XML Feed (/feed.xml).
     */
    public function feedXml(): Response
    {
        $settings = Setting::getAll();
        $title = $settings->get('gazette_title', 'XIVIG Maison Gazette');
        $tagline = $settings->get('gazette_tagline', 'The Haute Couture & Horology Journal');
        $synopsis = $settings->get('editorial_synopsis', 'Chronicles of bespoke craftsmanship and contemporary aesthetics.');

        $posts = Post::where('status', true)->latest()->take(20)->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        $xml .= "  <channel>\n";
        $xml .= '    <title>' . htmlspecialchars($title) . "</title>\n";
        $xml .= '    <link>' . url('/') . "</link>\n";
        $xml .= '    <description>' . htmlspecialchars($tagline . ' - ' . $synopsis) . "</description>\n";
        $xml .= '    <language>en-us</language>\n';
        $xml .= '    <lastBuildDate>' . Carbon::now()->toRssString() . "</lastBuildDate>\n";
        $xml .= '    <atom:link href="' . url('/feed.xml') . '" rel="self" type="application/rss+xml" />' . "\n";

        foreach ($posts as $post) {
            $xml .= "    <item>\n";
            $xml .= '      <title>' . htmlspecialchars($post->title) . "</title>\n";
            $xml .= '      <link>' . url('/blog/' . $post->slug) . "</link>\n";
            $xml .= '      <guid isPermaLink="true">' . url('/blog/' . $post->slug) . "</guid>\n";
            $xml .= '      <pubDate>' . ($post->created_at ? $post->created_at->toRssString() : Carbon::now()->toRssString()) . "</pubDate>\n";
            $xml .= '      <description>' . htmlspecialchars($post->excerpt ?: Str::limit(strip_tags($post->content), 200)) . "</description>\n";
            $xml .= "    </item>\n";
        }

        $xml .= "  </channel>\n";
        $xml .= "</rss>";

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    /**
     * Serve Dynamic XML Sitemap (/sitemap.xml).
     */
    public function sitemapXml(): Response
    {
        $posts = Post::where('status', true)->latest()->get();
        $categories = Category::all();
        $tags = Tag::all();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Homepage
        $xml .= "  <url>\n";
        $xml .= '    <loc>' . url('/') . "</loc>\n";
        $xml .= '    <changefreq>daily</changefreq>' . "\n";
        $xml .= '    <priority>1.0</priority>' . "\n";
        $xml .= "  </url>\n";

        // Blog Index
        $xml .= "  <url>\n";
        $xml .= '    <loc>' . url('/blog') . "</loc>\n";
        $xml .= '    <changefreq>daily</changefreq>' . "\n";
        $xml .= '    <priority>0.9</priority>' . "\n";
        $xml .= "  </url>\n";

        // Posts
        foreach ($posts as $post) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . url('/blog/' . $post->slug) . "</loc>\n";
            $xml .= '    <lastmod>' . ($post->updated_at ? $post->updated_at->toAtomString() : Carbon::now()->toAtomString()) . "</lastmod>\n";
            $xml .= '    <changefreq>weekly</changefreq>' . "\n";
            $xml .= '    <priority>0.8</priority>' . "\n";
            $xml .= "  </url>\n";
        }

        // Categories
        foreach ($categories as $cat) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . url('/blog?category=' . $cat->slug) . "</loc>\n";
            $xml .= '    <changefreq>weekly</changefreq>' . "\n";
            $xml .= '    <priority>0.6</priority>' . "\n";
            $xml .= "  </url>\n";
        }

        $xml .= "</urlset>";

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }
}
