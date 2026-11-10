<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Widget extends Model
{
    use HasFactory;

    /**
     * Canonical supported widget types across sidebar and footer zones.
     */
    public const WIDGET_TYPES = [
        'brand_info'      => ['label' => 'Brand & Contact Block', 'icon' => 'fas fa-building', 'desc' => 'Logo heading, short bio, email, and phone info'],
        'navigation_menu' => ['label' => 'Navigation Menu Links', 'icon' => 'fas fa-bars', 'desc' => 'Synchronized links from Navigation Menu Builder'],
        'pages_list'      => ['label' => 'Static Pages List', 'icon' => 'fas fa-file-alt', 'desc' => 'Synchronized published static pages (or footer_pages menu)'],
        'recent_posts'    => ['label' => 'Recent Posts', 'icon' => 'fas fa-clock', 'desc' => 'Displays latest published articles'],
        'trending'        => ['label' => 'Trending Posts', 'icon' => 'fas fa-fire', 'desc' => 'Displays top viewed articles this week'],
        'popular'         => ['label' => 'Popular Posts', 'icon' => 'fas fa-star', 'desc' => 'Displays all-time most viewed articles'],
        'ads'             => ['label' => 'Advertisement Banner', 'icon' => 'fas fa-ad', 'desc' => 'Custom display banner or Google AdSense snippet'],
        'newsletter'      => ['label' => 'Newsletter Subscribe', 'icon' => 'fas fa-envelope-open-text', 'desc' => 'Lead capture subscription card'],
        'categories'      => ['label' => 'Categories List', 'icon' => 'fas fa-folder', 'desc' => 'List of blog categories with post counters'],
        'tags'            => ['label' => 'Tags Cloud', 'icon' => 'fas fa-tags', 'desc' => 'Visual cloud of popular tags'],
        'archive'         => ['label' => 'Monthly Archive', 'icon' => 'fas fa-calendar-alt', 'desc' => 'Monthly post archive links'],
        'custom_html'     => ['label' => 'Custom HTML / Embed', 'icon' => 'fas fa-code', 'desc' => 'Raw HTML, iframe, or Javascript widget'],
    ];

    /**
     * All modular widget placement slots.
     */
    public const AREAS = [
        'sidebar'  => 'Main Blog Sidebar',
        'footer_1' => 'Footer Col 1 — Brand & Contact',
        'footer_2' => 'Footer Col 2 — Platform Links',
        'footer_3' => 'Footer Col 3 — Ecosystem Links',
        'footer_4' => 'Footer Col 4 — Pages & Legal',
        'footer_5' => 'Footer Col 5 — Newsletter CTA',
        'header'   => 'Header Notification Bar',
    ];

    /**
     * Grid classes for all five front-end footer columns.
     */
    public const FOOTER_COLUMNS = [
        'footer_1' => 'col-lg-3 col-sm-6 mb-4 mb-lg-0',
        'footer_2' => 'col-lg-2 col-md-6 col-sm-6 mb-4 mb-lg-0',
        'footer_3' => 'col-lg-2 col-md-6 col-sm-6 mb-4 mb-lg-0',
        'footer_4' => 'col-lg-2 col-md-6 col-sm-6 mb-4 mb-lg-0',
        'footer_5' => 'col-lg-3 col-md-6 col-sm-6 mb-4 mb-lg-0',
    ];

    protected $fillable = [
        'area',
        'widget_type',
        'title',
        'content',
        'settings',
        'order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'order' => 'integer',
            'status' => 'boolean',
        ];
    }

    /**
     * Fetch active ordered widgets for a placement slot safely.
     */
    public static function getAreaWidgets(string $area): Collection
    {
        try {
            return static::where('area', $area)
                ->where('status', true)
                ->orderBy('order', 'asc')
                ->orderBy('id', 'asc')
                ->get();
        } catch (\Throwable) {
            return collect();
        }
    }

    /**
     * Check whether any footer column widget has been configured in storage.
     */
    public static function hasConfiguredFooterWidgets(): bool
    {
        try {
            return static::whereIn('area', array_keys(self::FOOTER_COLUMNS))->exists();
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Populate default widgets for any footer column missing widgets.
     */
    public static function ensureDefaultFooterWidgets(): void
    {
        $defaults = [
            'footer_1' => [
                'widget_type' => 'brand_info',
                'title' => 'Xivig',
                'content' => 'Modern Elite Enterprise Framework built with Laravel 13 compatibility, Fortify, and Jetstream.',
                'settings' => [
                    'email' => 'support@xivig.com',
                    'phone' => '+91 98367 26662',
                ],
            ],
            'footer_2' => [
                'widget_type' => 'navigation_menu',
                'title' => 'Platform',
                'content' => null,
                'settings' => [
                    'menu_location' => 'footer',
                ],
            ],
            'footer_3' => [
                'widget_type' => 'navigation_menu',
                'title' => 'Ecosystem',
                'content' => null,
                'settings' => [
                    'menu_location' => 'footer_ecosystem',
                ],
            ],
            'footer_4' => [
                'widget_type' => 'pages_list',
                'title' => 'Pages',
                'content' => null,
                'settings' => [
                    'limit' => 6,
                    'menu_location' => 'footer_pages',
                ],
            ],
            'footer_5' => [
                'widget_type' => 'newsletter',
                'title' => 'Stay Ahead',
                'content' => 'Subscribe for architecture insights, software engineering masterclasses, and releases.',
                'settings' => null,
            ],
        ];

        foreach ($defaults as $area => $config) {
            if (static::where('area', $area)->exists()) {
                continue;
            }

            static::create([
                'area' => $area,
                'widget_type' => $config['widget_type'],
                'title' => $config['title'],
                'content' => $config['content'],
                'settings' => $config['settings'],
                'order' => 1,
                'status' => true,
            ]);
        }
    }
}
