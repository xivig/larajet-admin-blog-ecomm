<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    /**
     * Display the visual menu builder.
     */
    public function index(Request $request): View
    {
        $menus = Menu::with('items')->get();

        if ($menus->isEmpty()) {
            $defaultMenu = Menu::create([
                'name' => 'Main Navigation',
                'slug' => 'main-navigation',
                'location' => 'header',
            ]);

            // Add sample menu items
            MenuItem::create(['menu_id' => $defaultMenu->id, 'title' => 'Home', 'url' => '/', 'order' => 1]);
            MenuItem::create(['menu_id' => $defaultMenu->id, 'title' => 'Articles', 'url' => '/blog', 'order' => 2]);
            MenuItem::create(['menu_id' => $defaultMenu->id, 'title' => 'About', 'url' => '/about', 'order' => 3]);
            MenuItem::create(['menu_id' => $defaultMenu->id, 'title' => 'Contact', 'url' => '/contact', 'order' => 4]);

            $menus = Menu::with('items')->get();
        }

        $selectedMenuId = $request->query('menu', $menus->first()?->id);
        $currentMenu = $menus->firstWhere('id', $selectedMenuId) ?? $menus->first();

        $pages = Page::where('status', true)->get();
        $categories = Category::all();

        return view('admin.menu.index', compact('menus', 'currentMenu', 'pages', 'categories'));
    }

    /**
     * Create a new menu.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'location' => 'required|string|max:50',
        ]);

        $menu = Menu::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'location' => $request->location,
        ]);

        return redirect()->route('admin.menu.index', ['menu' => $menu->id])
            ->with('success', "Menu '{$menu->name}' created successfully.");
    }

    /**
     * Add an item to the current menu.
     */
    public function addItem(Request $request, Menu $menu): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'target' => 'nullable|in:_self,_blank',
        ]);

        $maxOrder = $menu->items()->max('order') ?? 0;

        MenuItem::create([
            'menu_id' => $menu->id,
            'title' => $request->title,
            'url' => $request->url,
            'target' => $request->input('target', '_self'),
            'order' => $maxOrder + 1,
        ]);

        return redirect()->route('admin.menu.index', ['menu' => $menu->id])
            ->with('success', 'Menu link added successfully.');
    }

    /**
     * Delete an item from the menu.
     */
    public function deleteItem(MenuItem $item): RedirectResponse
    {
        $menuId = $item->menu_id;
        $item->delete();

        return redirect()->route('admin.menu.index', ['menu' => $menuId])
            ->with('success', 'Menu item removed.');
    }

    /**
     * Delete an entire menu.
     */
    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();
        return redirect()->route('admin.menu.index')->with('success', 'Menu deleted successfully.');
    }
}
