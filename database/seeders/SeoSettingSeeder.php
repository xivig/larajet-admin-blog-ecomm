<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SeoSettingSeeder extends Seeder
{
    /**
     * Run the database seeds for SEO settings.
     */
    public function run(): void
    {
        $defaults = [
            // Meta SERP
            'seo_meta_title' => 'XIVIG Maison — Luxury Horology & Haute Couture Journal',
            'seo_title_separator' => '—',
            'seo_meta_description' => 'Chronicles of bespoke craftsmanship, luxury timepieces, and contemporary aesthetics. Read verified editorial deep-dives and engineering blueprints.',
            'seo_meta_keywords' => 'luxury, horology, haute couture, watches, bespoke, architecture, editorial, craftsmanship',
            'seo_canonical_url' => url('/'),
            'seo_author' => 'XIVIG Editorial Board',

            // Social & OpenGraph
            'seo_og_title' => 'XIVIG Maison Gazette',
            'seo_og_description' => 'Chronicles of bespoke craftsmanship, luxury timepieces, and contemporary aesthetics.',
            'seo_og_image' => url('/xivig-public/images/blog/laravel-hyperscale-architecture.jpg'),
            'seo_og_type' => 'website',
            'seo_og_locale' => 'en_US',
            'seo_og_site_name' => 'XIVIG Maison',

            // Twitter Cards
            'seo_twitter_card' => 'summary_large_image',
            'seo_twitter_site' => '@xivigmaison',
            'seo_twitter_creator' => '@xivigmaison',

            // Robots & Indexing
            'seo_robots_index' => 'index',
            'seo_robots_follow' => 'follow',
            'seo_robots_advanced' => 'max-snippet:-1, max-image-preview:large, max-video-preview:-1',
            'seo_robots_txt' => "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /login\nDisallow: /register\nDisallow: /storage/app/backups/\n\nSitemap: " . url('/sitemap.xml'),

            // Schema.org
            'seo_schema_type' => 'NewsMediaOrganization',
            'seo_schema_name' => 'XIVIG Maison Gazette',
            'seo_schema_logo' => url('/xivig-admin/img/favicon-DgHNGhkV.ico'),
            'seo_schema_same_as' => "https://twitter.com/xivigmaison\nhttps://instagram.com/xivigmaison\nhttps://linkedin.com/company/xivigmaison\nhttps://github.com/xivig",

            // Webmaster Verifications
            'seo_google_verification' => 'google-site-verification=XIVIG_PRO_GSC_SAMPLE_TOKEN_2026',
            'seo_bing_verification' => 'msvalidate.01=XIVIG_BING_SAMPLE_TOKEN_2026',
            'seo_yandex_verification' => '',
            'seo_pinterest_verification' => '',

            // Sitemap
            'seo_sitemap_enable' => '1',
            'seo_sitemap_include_posts' => '1',
            'seo_sitemap_include_categories' => '1',
            'seo_sitemap_include_tags' => '1',
        ];

        foreach ($defaults as $key => $val) {
            if (!Setting::where('key', $key)->exists()) {
                Setting::set($key, $val, 'seo');
            }
        }
    }
}
