<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
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
        if (Menu::count() === 0) {
            Menu::ensureDefaultMenus();
        }

        $menus = Menu::with('items')->orderBy('id', 'asc')->get();

        $selectedMenuId = (int) $request->query('menu', $menus->first()?->id);
        $currentMenu = $menus->firstWhere('id', $selectedMenuId) ?? $menus->first();
        $hierarchicalItems = $currentMenu ? $currentMenu->hierarchicalItems() : collect();
        $rootItems = $currentMenu
            ? $currentMenu->items->whereNull('parent_id')->values()
            : collect();

        $slotMenus = [
            'header' => $menus->where('location', 'header')->sortByDesc('id')->first(),
            'footer' => $menus->where('location', 'footer')->sortByDesc('id')->first(),
            'footer_ecosystem' => $menus->where('location', 'footer_ecosystem')->sortByDesc('id')->first(),
        ];

        $locations = Menu::LOCATIONS;
        $pages = Page::where('status', true)->orderBy('title')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.menu.index', compact(
            'menus',
            'currentMenu',
            'hierarchicalItems',
            'rootItems',
            'slotMenus',
            'locations',
            'pages',
            'categories'
        ));
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

        $baseSlug = Str::slug($request->name) ?: 'menu';
        $slug = $baseSlug;
        $suffix = 1;
        while (Menu::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . (++$suffix);
        }

        $menu = Menu::create([
            'name' => $request->name,
            'slug' => $slug,
            'location' => $request->location,
        ]);

        return redirect()->route('admin.menu.index', ['menu' => $menu->id])
            ->with('success', "Menu '{$menu->name}' created and synced to " . Menu::locationLabel($menu->location) . '.');
    }

    /**
     * Create any missing default Header/Footer menus without altering custom menus.
     */
    public function syncDefaults(): RedirectResponse
    {
        Menu::ensureDefaultMenus();

        return redirect()->route('admin.menu.index')
            ->with('success', 'Missing default Header and Footer menus synchronized.');
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
            'parent_id' => 'nullable|integer',
        ]);

        $parentId = null;
        if ($request->filled('parent_id')) {
            $candidateParent = (int) $request->input('parent_id');
            if ($menu->items()->whereNull('parent_id')->whereKey($candidateParent)->exists()) {
                $parentId = $candidateParent;
            }
        }

        $maxOrder = $menu->items()->max('order') ?? 0;

        MenuItem::create([
            'menu_id' => $menu->id,
            'parent_id' => $parentId,
            'title' => $request->title,
            'url' => $request->url,
            'target' => $request->input('target', '_self'),
            'order' => $maxOrder + 1,
        ]);

        return redirect()->route('admin.menu.index', ['menu' => $menu->id])
            ->with('success', 'Menu link added and synchronized.');
    }

    /**
     * Update an existing menu item.
     */
    public function updateItem(Request $request, MenuItem $item): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'target' => 'nullable|in:_self,_blank',
            'parent_id' => 'nullable|integer',
        ]);

        $parentId = null;
        if ($request->filled('parent_id')) {
            $candidateParent = (int) $request->input('parent_id');
            $validParent = MenuItem::where('menu_id', $item->menu_id)
                ->whereNull('parent_id')
                ->where('id', '!=', $item->id)
                ->whereKey($candidateParent)
                ->exists();

            if ($validParent) {
                $parentId = $candidateParent;
            }
        }

        // Prevent multi-level orphan chains if a parent becomes a child link.
        if ($parentId !== null) {
            MenuItem::where('parent_id', $item->id)->update(['parent_id' => null]);
        }

        $item->update([
            'title' => $request->title,
            'url' => $request->url,
            'target' => $request->input('target', '_self'),
            'parent_id' => $parentId,
        ]);

        return redirect()->route('admin.menu.index', ['menu' => $item->menu_id])
            ->with('success', 'Menu item updated and synchronized.');
    }

    /**
     * Delete an item from the menu.
     */
    public function deleteItem(MenuItem $item): RedirectResponse
    {
        $menuId = $item->menu_id;
        MenuItem::where('parent_id', $item->id)->update(['parent_id' => $item->parent_id]);
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

    /**
     * Reorder and update parent hierarchy of menu items.
     */
    public function reorder(Request $request): JsonResponse
    {
        $items = $request->input('items', []);
        $menuId = $request->input('menu_id');

        foreach ($items as $index => $itemData) {
            $itemId = isset($itemData['id']) ? (int) $itemData['id'] : null;
            if (!$itemId) {
                continue;
            }

            $parentId = !empty($itemData['parent_id']) ? (int) $itemData['parent_id'] : null;
            if ($parentId === $itemId) {
                $parentId = null;
            }

            $query = MenuItem::whereKey($itemId);
            if ($menuId) {
                $query->where('menu_id', (int) $menuId);
            }

            $query->update([
                'order' => $index + 1,
                'parent_id' => $parentId,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
