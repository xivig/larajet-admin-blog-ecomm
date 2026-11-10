<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Menu extends Model
{
    use HasFactory;

    public const LOCATIONS = [
        'header' => 'Main Header Navbar',
        'footer' => 'Footer — Platform Column',
        'footer_ecosystem' => 'Footer — Ecosystem Column',
        'footer_pages' => 'Footer — Pages Column',
        'sidebar' => 'Sidebar Widget',
        'mobile' => 'Mobile Drawer',
    ];

    protected $fillable = [
        'name',
        'slug',
        'location',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Root-level menu items with their ordered children eager-loaded.
     */
    public function rootItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)
            ->whereNull('parent_id')
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->with(['children' => function ($query) {
                $query->orderBy('order', 'asc')->orderBy('id', 'asc');
            }]);
    }

    /**
     * Flatten items in hierarchical display order (parent followed by its children).
     *
     * @return Collection<int, MenuItem>
     */
    public function hierarchicalItems(): Collection
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();
        $grouped = $items->groupBy(fn (MenuItem $item) => $item->parent_id ?: 0);

        $ordered = collect();
        $roots = $grouped->get(0, collect());

        foreach ($roots as $root) {
            $ordered->push($root);
            foreach ($grouped->get($root->id, collect()) as $child) {
                $ordered->push($child);
            }
        }

        // Preserve any orphaned items whose parent no longer exists rather than hiding them.
        $pushedIds = $ordered->pluck('id')->all();
        foreach ($items as $item) {
            if (!in_array($item->id, $pushedIds, true)) {
                $ordered->push($item);
            }
        }

        return $ordered;
    }

    /**
     * Human-readable label for a menu display location.
     */
    public static function locationLabel(string $location): string
    {
        return self::LOCATIONS[$location] ?? ucfirst(str_replace('_', ' ', $location));
    }

    /**
     * Fetch the active menu tree for a front-end display location.
     */
    public static function menuForLocation(string $location): ?self
    {
        try {
            return static::query()
                ->where('location', $location)
                ->with('rootItems')
                ->orderByDesc('id')
                ->first();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Seed default header and footer menus for any missing core location.
     */
    public static function ensureDefaultMenus(): void
    {
        $definitions = [
            'header' => [
                'name' => 'Main Navigation',
                'slug' => 'main-navigation',
                'items' => [
                    ['title' => 'Home', 'url' => '/'],
                    ['title' => 'Services', 'url' => '/services'],
                    ['title' => 'Pricing', 'url' => '/pricing'],
                    ['title' => 'Portfolio', 'url' => '/project'],
                    ['title' => 'Blog', 'url' => '/blog'],
                    ['title' => 'Pages', 'url' => '#pages'],
                    ['title' => 'Store', 'url' => '/ecomm'],
                    ['title' => 'Contact', 'url' => '/contact'],
                ],
            ],
            'footer' => [
                'name' => 'Platform',
                'slug' => 'footer-platform',
                'items' => [
                    ['title' => 'Home', 'url' => '/'],
                    ['title' => 'About Us', 'url' => '/page/about-us'],
                    ['title' => 'Services', 'url' => '/services'],
                    ['title' => 'Pricing Plans', 'url' => '/pricing'],
                    ['title' => 'Portfolio', 'url' => '/project'],
                ],
            ],
            'footer_ecosystem' => [
                'name' => 'Ecosystem',
                'slug' => 'footer-ecosystem',
                'items' => [
                    ['title' => 'Blog Magazine', 'url' => '/blog-list'],
                    ['title' => 'All Articles', 'url' => '/blog'],
                    ['title' => 'Commerce Store', 'url' => '/ecomm'],
                    ['title' => 'Contact Support', 'url' => '/contact'],
                    ['title' => 'Admin Portal', 'url' => '/admin/login'],
                ],
            ],
        ];

        foreach ($definitions as $location => $config) {
            if (static::where('location', $location)->exists()) {
                continue;
            }

            $slug = $config['slug'];
            $suffix = 1;
            while (static::where('slug', $slug)->exists()) {
                $slug = $config['slug'] . '-' . (++$suffix);
            }

            $menu = static::create([
                'name' => $config['name'],
                'slug' => $slug,
                'location' => $location,
            ]);

            foreach ($config['items'] as $index => $item) {
                MenuItem::create([
                    'menu_id' => $menu->id,
                    'title' => $item['title'],
                    'url' => $item['url'],
                    'target' => '_self',
                    'order' => $index + 1,
                ]);
            }
        }
    }
}
