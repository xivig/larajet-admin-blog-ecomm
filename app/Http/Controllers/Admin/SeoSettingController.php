<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SeoSettingController extends Controller
{
    /**
     * Default robots.txt template.
     */
    public const DEFAULT_ROBOTS_TXT = "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /login\nDisallow: /register\nDisallow: /storage/app/backups/\n\nSitemap: ";

    /**
     * Display the SEO & Search Engine Intelligence Suite.
     */
    public function seo(): View
    {
        return $this->index();
    }

    /**
     * Display the SEO & Search Engine Intelligence Suite.
     */
    public function index(): View
    {
        $settings = Setting::getAll();

        // Calculate SEO Health Score
        $score = 0;
        $maxScore = 100;
        $checks = [
            'meta_title' => !empty($settings->get('seo_meta_title')),
            'meta_description' => !empty($settings->get('seo_meta_description')) && strlen($settings->get('seo_meta_description')) >= 50,
            'canonical_url' => !empty($settings->get('seo_canonical_url')),
            'og_image' => !empty($settings->get('seo_og_image')),
            'twitter_card' => !empty($settings->get('seo_twitter_site')),
            'schema_org' => !empty($settings->get('seo_schema_name')),
            'sitemap' => $settings->get('seo_sitemap_enable', '1') === '1',
            'robots_txt' => !empty($settings->get('seo_robots_txt')),
            'google_verification' => !empty($settings->get('seo_google_verification')),
            'keywords' => !empty($settings->get('seo_meta_keywords')),
        ];

        $weights = [
            'meta_title' => 15,
            'meta_description' => 15,
            'canonical_url' => 10,
            'og_image' => 15,
            'twitter_card' => 10,
            'schema_org' => 10,
            'sitemap' => 10,
            'robots_txt' => 5,
            'google_verification' => 5,
            'keywords' => 5,
        ];

        foreach ($checks as $key => $passed) {
            if ($passed) {
                $score += $weights[$key] ?? 5;
            }
        }

        // Generate JSON-LD preview
        $schemaType = $settings->get('seo_schema_type', 'NewsMediaOrganization');
        $schemaName = $settings->get('seo_schema_name', $settings->get('gazette_title', 'XIVIG Maison Gazette'));
        $schemaLogo = $settings->get('seo_schema_logo', url('/admin-dist/img/favicon-DgHNGhkV.ico'));
        $sameAsRaw = $settings->get('seo_schema_same_as', "https://twitter.com/xivigmaison\nhttps://instagram.com/xivigmaison\nhttps://linkedin.com/company/xivigmaison");
        $sameAsList = array_values(array_filter(array_map('trim', explode("\n", $sameAsRaw))));

        $jsonLdArray = [
            '@context' => 'https://schema.org',
            '@type' => $schemaType,
            'name' => $schemaName,
            'url' => url('/'),
            'logo' => $schemaLogo,
            'sameAs' => $sameAsList,
        ];
        $jsonLdFormatted = json_encode($jsonLdArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return view('admin.settings.seo', compact('settings', 'score', 'checks', 'jsonLdFormatted'));
    }

    /**
     * Update all SEO & Search Engine configurations.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Meta SERP
            'seo_meta_title' => 'required|string|max:255',
            'seo_title_separator' => 'required|string|max:10',
            'seo_meta_description' => 'required|string|max:500',
            'seo_meta_keywords' => 'nullable|string|max:500',
            'seo_canonical_url' => 'nullable|url|max:255',
            'seo_author' => 'nullable|string|max:100',

            // Social & OpenGraph
            'seo_og_title' => 'nullable|string|max:255',
            'seo_og_description' => 'nullable|string|max:500',
            'seo_og_image' => 'nullable|string|max:500',
            'seo_og_type' => 'required|string|in:website,article,blog',
            'seo_og_locale' => 'required|string|max:20',
            'seo_og_site_name' => 'nullable|string|max:100',

            // Twitter Cards
            'seo_twitter_card' => 'required|string|in:summary,summary_large_image',
            'seo_twitter_site' => 'nullable|string|max:50',
            'seo_twitter_creator' => 'nullable|string|max:50',

            // Robots & Indexing
            'seo_robots_index' => 'required|string|in:index,noindex',
            'seo_robots_follow' => 'required|string|in:follow,nofollow',
            'seo_robots_advanced' => 'nullable|array',
            'seo_robots_txt' => 'required|string|max:5000',

            // Schema.org
            'seo_schema_type' => 'required|string|in:NewsMediaOrganization,Organization,Blog,LocalBusiness,Person',
            'seo_schema_name' => 'required|string|max:255',
            'seo_schema_logo' => 'nullable|string|max:500',
            'seo_schema_same_as' => 'nullable|string|max:2000',

            // Webmaster Verifications
            'seo_google_verification' => 'nullable|string|max:255',
            'seo_bing_verification' => 'nullable|string|max:255',
            'seo_yandex_verification' => 'nullable|string|max:255',
            'seo_pinterest_verification' => 'nullable|string|max:255',

            // Sitemap Settings
            'seo_sitemap_enable' => 'nullable|boolean',
            'seo_sitemap_include_posts' => 'nullable|boolean',
            'seo_sitemap_include_categories' => 'nullable|boolean',
            'seo_sitemap_include_tags' => 'nullable|boolean',
        ]);

        // Process advanced robots directives
        $advancedDirectives = $request->input('seo_robots_advanced', []);
        $advancedDirectivesString = is_array($advancedDirectives) ? implode(', ', $advancedDirectives) : '';

        // Store settings with corresponding group namespaces
        $seoSettings = [
            // Meta SERP
            ['key' => 'seo_meta_title', 'value' => $validated['seo_meta_title'], 'group' => 'seo_meta'],
            ['key' => 'seo_title_separator', 'value' => $validated['seo_title_separator'], 'group' => 'seo_meta'],
            ['key' => 'seo_meta_description', 'value' => $validated['seo_meta_description'], 'group' => 'seo_meta'],
            ['key' => 'seo_meta_keywords', 'value' => $validated['seo_meta_keywords'] ?? '', 'group' => 'seo_meta'],
            ['key' => 'seo_canonical_url', 'value' => $validated['seo_canonical_url'] ?? url('/'), 'group' => 'seo_meta'],
            ['key' => 'seo_author', 'value' => $validated['seo_author'] ?? 'XIVIG Maison', 'group' => 'seo_meta'],

            // Social & OpenGraph
            ['key' => 'seo_og_title', 'value' => $validated['seo_og_title'] ?? $validated['seo_meta_title'], 'group' => 'seo_social'],
            ['key' => 'seo_og_description', 'value' => $validated['seo_og_description'] ?? $validated['seo_meta_description'], 'group' => 'seo_social'],
            ['key' => 'seo_og_image', 'value' => $validated['seo_og_image'] ?? '', 'group' => 'seo_social'],
            ['key' => 'seo_og_type', 'value' => $validated['seo_og_type'], 'group' => 'seo_social'],
            ['key' => 'seo_og_locale', 'value' => $validated['seo_og_locale'], 'group' => 'seo_social'],
            ['key' => 'seo_og_site_name', 'value' => $validated['seo_og_site_name'] ?? $validated['seo_meta_title'], 'group' => 'seo_social'],

            // Twitter Cards
            ['key' => 'seo_twitter_card', 'value' => $validated['seo_twitter_card'], 'group' => 'seo_social'],
            ['key' => 'seo_twitter_site', 'value' => $validated['seo_twitter_site'] ?? '', 'group' => 'seo_social'],
            ['key' => 'seo_twitter_creator', 'value' => $validated['seo_twitter_creator'] ?? '', 'group' => 'seo_social'],

            // Robots & Indexing
            ['key' => 'seo_robots_index', 'value' => $validated['seo_robots_index'], 'group' => 'seo_robots'],
            ['key' => 'seo_robots_follow', 'value' => $validated['seo_robots_follow'], 'group' => 'seo_robots'],
            ['key' => 'seo_robots_advanced', 'value' => $advancedDirectivesString, 'group' => 'seo_robots'],
            ['key' => 'seo_robots_txt', 'value' => $validated['seo_robots_txt'], 'group' => 'seo_robots'],

            // Schema.org
            ['key' => 'seo_schema_type', 'value' => $validated['seo_schema_type'], 'group' => 'seo_schema'],
            ['key' => 'seo_schema_name', 'value' => $validated['seo_schema_name'], 'group' => 'seo_schema'],
            ['key' => 'seo_schema_logo', 'value' => $validated['seo_schema_logo'] ?? '', 'group' => 'seo_schema'],
            ['key' => 'seo_schema_same_as', 'value' => $validated['seo_schema_same_as'] ?? '', 'group' => 'seo_schema'],

            // Webmaster Verifications
            ['key' => 'seo_google_verification', 'value' => $validated['seo_google_verification'] ?? '', 'group' => 'seo_verification'],
            ['key' => 'seo_bing_verification', 'value' => $validated['seo_bing_verification'] ?? '', 'group' => 'seo_verification'],
            ['key' => 'seo_yandex_verification', 'value' => $validated['seo_yandex_verification'] ?? '', 'group' => 'seo_verification'],
            ['key' => 'seo_pinterest_verification', 'value' => $validated['seo_pinterest_verification'] ?? '', 'group' => 'seo_verification'],

            // Sitemap
            ['key' => 'seo_sitemap_enable', 'value' => $request->boolean('seo_sitemap_enable') ? '1' : '0', 'group' => 'seo_sitemap'],
            ['key' => 'seo_sitemap_include_posts', 'value' => $request->boolean('seo_sitemap_include_posts') ? '1' : '0', 'group' => 'seo_sitemap'],
            ['key' => 'seo_sitemap_include_categories', 'value' => $request->boolean('seo_sitemap_include_categories') ? '1' : '0', 'group' => 'seo_sitemap'],
            ['key' => 'seo_sitemap_include_tags', 'value' => $request->boolean('seo_sitemap_include_tags') ? '1' : '0', 'group' => 'seo_sitemap'],
        ];

        foreach ($seoSettings as $item) {
            Setting::set($item['key'], $item['value'], $item['group']);
        }

        // Record Activity Log
        ActivityLog::record(
            'update',
            'SEO Suite',
            'Updated search engine optimization and social metadata settings',
            [
                'meta_title' => $validated['seo_meta_title'],
                'robots_index' => $validated['seo_robots_index'],
                'schema_type' => $validated['seo_schema_type'],
            ]
        );

        return redirect()->route('admin.settings.seo')->with('success', 'Full-fledged SEO settings saved and synced successfully!');
    }

    /**
     * Reset robots.txt to standard production template.
     */
    public function resetRobots(): RedirectResponse
    {
        $defaultRobots = self::DEFAULT_ROBOTS_TXT . url('/sitemap.xml');
        Setting::set('seo_robots_txt', $defaultRobots, 'seo_robots');

        ActivityLog::record('reset', 'SEO Suite', 'Reset robots.txt to default production ruleset');

        return redirect()->route('admin.settings.seo')->with('success', 'robots.txt ruleset reset to production defaults.');
    }

    /**
     * Serve Dynamic /robots.txt.
     */
    public function robotsTxt(): Response
    {
        $robotsContent = Setting::get('seo_robots_txt');

        if (empty($robotsContent)) {
            $robotsContent = self::DEFAULT_ROBOTS_TXT . url('/sitemap.xml');
        }

        return response($robotsContent, 200)->header('Content-Type', 'text/plain');
    }
}
