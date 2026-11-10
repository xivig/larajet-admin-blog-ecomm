<?php

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Populate full default Header and Footer menus if missing or still at initial stub seed.
     */
    public function up(): void
    {
        if (!Schema::hasTable('menus') || !Schema::hasTable('menu_items')) {
            return;
        }

        $headerMenu = Menu::where('slug', 'main-navigation')
            ->where('location', 'header')
            ->first();

        if ($headerMenu) {
            $existingUrls = $headerMenu->items()->orderBy('order')->pluck('url')->all();
            $legacyStubUrls = ['/', '/blog', '/page/about-us', '/contact'];

            if ($existingUrls === $legacyStubUrls) {
                $headerMenu->items()->delete();

                $headerDefaults = [
                    ['title' => 'Home', 'url' => '/'],
                    ['title' => 'Services', 'url' => '/services'],
                    ['title' => 'Pricing', 'url' => '/pricing'],
                    ['title' => 'Portfolio', 'url' => '/project'],
                    ['title' => 'Blog', 'url' => '/blog'],
                    ['title' => 'Pages', 'url' => '#pages'],
                    ['title' => 'Store', 'url' => '/ecomm'],
                    ['title' => 'Contact', 'url' => '/contact'],
                ];

                foreach ($headerDefaults as $index => $item) {
                    MenuItem::create([
                        'menu_id' => $headerMenu->id,
                        'title' => $item['title'],
                        'url' => $item['url'],
                        'target' => '_self',
                        'order' => $index + 1,
                    ]);
                }
            }
        }

        Menu::ensureDefaultMenus();
    }

    /**
     * Reverse is a safe no-op to preserve user-customized navigation structures.
     */
    public function down(): void
    {
        // Intentionally left blank.
    }
};
