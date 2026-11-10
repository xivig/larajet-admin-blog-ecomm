<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\RedirectRule;
use App\Models\SearchLog;
use App\Models\Widget;
use Illuminate\Database\Seeder;

class EnterprisePagesAndWidgetsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pages
        $pages = [
            [
                'title' => 'About XIVIG Maison',
                'slug' => 'about-us',
                'template' => 'about',
                'excerpt' => 'Architecting resilient media platforms, cloud infrastructure, and technical publishing tools.',
                'content' => '<p>XIVIG Maison is an independent technology journal and software research collective founded in 2026. Our focus spans high-throughput backend systems, modern web engineering, distributed clouds, and AI-assisted workflows.</p><p>We believe digital publications must be blisteringly fast, accessible without friction, and engineered to withstand enterprise traffic demands.</p>',
                'status' => true,
                'meta_title' => 'About XIVIG Maison - High-Scale Technical Publication',
                'meta_description' => 'Learn about our engineering mission, architecture philosophy, and global developer reach.',
            ],
            [
                'title' => 'Contact & Inquiries',
                'slug' => 'contact-us',
                'template' => 'contact',
                'excerpt' => 'Connect directly with our editorial staff, engineering contributors, and media relations.',
                'content' => '<p>For editorial submissions, technical reviews, sponsorship proposals, or security disclosures, please reach out via our direct communications desk.</p>',
                'status' => true,
                'meta_title' => 'Contact XIVIG - Editorial & Technical Inquiries',
                'meta_description' => 'Direct contact information for editorial team, engineering desk, and partnerships.',
            ],
            [
                'title' => 'Privacy & Data Protection Policy',
                'slug' => 'privacy-policy',
                'template' => 'privacy',
                'excerpt' => 'Transparent overview of how we safeguard reader data, comply with GDPR/CCPA, and eliminate tracker bloat.',
                'content' => '<p>We take reader privacy seriously. We collect minimal telemetry required to operate high-availability services and never sell personal identifiable records.</p>',
                'status' => true,
                'meta_title' => 'Privacy Policy - Data Protection & Compliance',
                'meta_description' => 'Comprehensive information on data collection, cookie usage, and reader privacy rights.',
            ],
            [
                'title' => 'Terms of Service & Licensing',
                'slug' => 'terms-and-conditions',
                'template' => 'terms',
                'excerpt' => 'Read our terms of use, content syndication policies, and code licensing standards.',
                'content' => '<p>All technical articles and architectural diagrams are published under standard attribution licenses. Code samples in tutorials are free to adapt in enterprise and commercial projects.</p>',
                'status' => true,
                'meta_title' => 'Terms & Conditions - XIVIG Technical Platform',
                'meta_description' => 'Standard user terms, content reproduction guidelines, and software licensing.',
            ],
            [
                'title' => 'Reactive Cloud Blueprints 2027',
                'slug' => 'cloud-blueprints-landing',
                'template' => 'landing',
                'excerpt' => 'Master the new generation of ultra-low latency, multi-region distributed cloud architectures.',
                'content' => '<h3>Architected for Extreme Scale</h3><p>Discover blueprints engineered to handle millions of concurrent connections with sub-20ms global edge latency. Built for Laravel 13, Go micro-daemons, and distributed caching clusters.</p><div class="my-4 p-4 bg-light border rounded"><h5>Included in the Blueprint Series:</h5><ul><li>Zero-downtime Blue/Green Deployment Pipelines</li><li>Multi-Region SQLite and PostgreSQL Replication</li><li>Micro-frontend Edge SSR Optimization</li><li>Automated WebP / AVIF Asset Delivery Pipelines</li></ul></div>',
                'status' => true,
                'meta_title' => 'Reactive Cloud Blueprints 2027 - High-Scale Infrastructure',
                'meta_description' => 'Enterprise architectural guides and deployment patterns for modern cloud applications.',
            ],
        ];

        foreach ($pages as $pData) {
            Page::updateOrCreate(['slug' => $pData['slug']], $pData);
        }

        // 2. Sample 301 Redirect Rules
        $redirects = [
            [
                'source_path' => '/legacy-feed',
                'target_path' => '/blog/feed',
                'status_code' => 301,
                'description' => 'Redirect old RSS feed to modern web feed',
                'is_active' => true,
                'hits_count' => 14,
            ],
            [
                'source_path' => '/old-articles',
                'target_path' => '/blog',
                'status_code' => 301,
                'description' => 'Redirect deprecated articles index to current blog index',
                'is_active' => true,
                'hits_count' => 28,
            ],
            [
                'source_path' => '/contact',
                'target_path' => '/page/contact-us',
                'status_code' => 301,
                'description' => 'Canonical redirect for contact endpoint',
                'is_active' => true,
                'hits_count' => 9,
            ],
        ];

        foreach ($redirects as $r) {
            RedirectRule::updateOrCreate(['source_path' => $r['source_path']], $r);
        }

        // 3. Search Intelligence Sample Queries
        $searches = [
            ['query' => 'laravel 13', 'results_count' => 12, 'hits_count' => 148],
            ['query' => 'cloud blueprints', 'results_count' => 8, 'hits_count' => 95],
            ['query' => 'webp optimization', 'results_count' => 6, 'hits_count' => 74],
            ['query' => 'microservices', 'results_count' => 10, 'hits_count' => 62],
            ['query' => 'kubernetes', 'results_count' => 5, 'hits_count' => 45],
            ['query' => 'database sharding', 'results_count' => 4, 'hits_count' => 38],
            ['query' => 'quantum computing', 'results_count' => 0, 'hits_count' => 7],
        ];

        foreach ($searches as $s) {
            SearchLog::updateOrCreate(['query' => $s['query']], array_merge($s, ['last_searched_at' => now()]));
        }

        // 4. Default Widgets
        if (Widget::count() === 0) {
            $defaultWidgets = [
                ['area' => 'sidebar', 'widget_type' => 'popular', 'title' => 'Popular Stories', 'order' => 1, 'settings' => ['limit' => 4], 'status' => true],
                ['area' => 'sidebar', 'widget_type' => 'newsletter', 'title' => 'Stay Ahead in Tech', 'order' => 2, 'content' => 'Receive hand-curated engineering analyses each Tuesday.', 'status' => true],
                ['area' => 'sidebar', 'widget_type' => 'categories', 'title' => 'Explore Topics', 'order' => 3, 'settings' => ['limit' => 10], 'status' => true],
                ['area' => 'sidebar', 'widget_type' => 'tags', 'title' => 'Trending Keywords', 'order' => 4, 'settings' => ['limit' => 25], 'status' => true],
                ['area' => 'sidebar', 'widget_type' => 'ads', 'title' => 'Sponsored Partner', 'order' => 5, 'settings' => ['ad_code' => '<div class="p-3 text-center bg-light border rounded"><small class="text-uppercase text-muted font-weight-bold">Sponsor</small><div class="py-2 font-weight-bold text-primary">XIVIG Cloud Blueprints 2027</div><small class="text-muted">Enterprise Cloud Architecture</small></div>'], 'status' => true],
            ];

            foreach ($defaultWidgets as $dw) {
                Widget::create($dw);
            }
        }
    }
}
