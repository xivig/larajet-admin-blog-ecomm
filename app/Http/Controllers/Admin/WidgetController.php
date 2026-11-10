<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    /**
     * Supported widget types with descriptive labels and icons.
     */
    public const WIDGET_TYPES = [
        'recent_posts' => ['label' => 'Recent Posts', 'icon' => 'fas fa-clock', 'desc' => 'Displays latest published articles'],
        'trending'     => ['label' => 'Trending Posts', 'icon' => 'fas fa-fire', 'desc' => 'Displays top viewed articles this week'],
        'popular'      => ['label' => 'Popular Posts', 'icon' => 'fas fa-star', 'desc' => 'Displays all-time most viewed articles'],
        'ads'          => ['label' => 'Advertisement Banner', 'icon' => 'fas fa-ad', 'desc' => 'Custom display banner or Google AdSense snippet'],
        'newsletter'   => ['label' => 'Newsletter Subscribe', 'icon' => 'fas fa-envelope-open-text', 'desc' => 'Lead capture subscription card'],
        'categories'   => ['label' => 'Categories List', 'icon' => 'fas fa-folder', 'desc' => 'List of blog categories with post counters'],
        'tags'         => ['label' => 'Tags Cloud', 'icon' => 'fas fa-tags', 'desc' => 'Visual cloud of popular tags'],
        'archive'      => ['label' => 'Monthly Archive', 'icon' => 'fas fa-calendar-alt', 'desc' => 'Monthly post archive links'],
        'custom_html'  => ['label' => 'Custom HTML / Embed', 'icon' => 'fas fa-code', 'desc' => 'Raw HTML, iframe, or Javascript widget'],
    ];

    /**
     * Supported widget placement areas.
     */
    public const AREAS = [
        'sidebar'  => 'Main Blog Sidebar',
        'footer_1' => 'Footer Column 1',
        'footer_2' => 'Footer Column 2',
        'footer_3' => 'Footer Column 3',
        'header'   => 'Header Notification Bar',
    ];

    /**
     * Display visual widget management interface.
     */
    public function index(Request $request): View
    {
        $selectedArea = $request->query('area', 'sidebar');
        if (!array_key_exists($selectedArea, self::AREAS)) {
            $selectedArea = 'sidebar';
        }

        // Auto-seed default widgets if empty
        if (Widget::count() === 0) {
            $this->seedDefaultWidgets();
        }

        $widgets = Widget::where('area', $selectedArea)->orderBy('order', 'asc')->get();
        $allAreas = self::AREAS;
        $widgetTypes = self::WIDGET_TYPES;

        return view('admin.widgets.index', compact('widgets', 'selectedArea', 'allAreas', 'widgetTypes'));
    }

    /**
     * Store a newly created widget.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'area' => 'required|string|in:' . implode(',', array_keys(self::AREAS)),
            'widget_type' => 'required|string|in:' . implode(',', array_keys(self::WIDGET_TYPES)),
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:50',
            'ad_code' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $maxOrder = Widget::where('area', $validated['area'])->max('order') ?? 0;

        $settings = [];
        if ($request->filled('limit')) {
            $settings['limit'] = (int) $request->limit;
        }
        if ($request->filled('ad_code')) {
            $settings['ad_code'] = $request->ad_code;
        }

        Widget::create([
            'area' => $validated['area'],
            'widget_type' => $validated['widget_type'],
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'settings' => $settings,
            'order' => $maxOrder + 1,
            'status' => $request->boolean('status', true),
        ]);

        ActivityLog::record('create', 'Widgets', "Added widget '{$validated['title']}' to {$validated['area']}");

        return redirect()->route('admin.widgets.index', ['area' => $validated['area']])
            ->with('success', "Widget '{$validated['title']}' added successfully.");
    }

    /**
     * Update an existing widget.
     */
    public function update(Request $request, Widget $widget): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:50',
            'ad_code' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $settings = $widget->settings ?? [];
        if ($request->filled('limit')) {
            $settings['limit'] = (int) $request->limit;
        }
        if ($request->has('ad_code')) {
            $settings['ad_code'] = $request->ad_code;
        }

        $widget->update([
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'settings' => $settings,
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.widgets.index', ['area' => $widget->area])
            ->with('success', "Widget '{$widget->title}' updated successfully.");
    }

    /**
     * Toggle widget active state.
     */
    public function toggle(Widget $widget): RedirectResponse
    {
        $widget->update(['status' => !$widget->status]);
        return back()->with('success', "Widget '{$widget->title}' status toggled.");
    }

    /**
     * Remove the specified widget.
     */
    public function destroy(Widget $widget): RedirectResponse
    {
        $area = $widget->area;
        $title = $widget->title;
        $widget->delete();

        ActivityLog::record('delete', 'Widgets', "Removed widget '{$title}' from {$area}");

        return redirect()->route('admin.widgets.index', ['area' => $area])
            ->with('success', "Widget '{$title}' removed.");
    }

    /**
     * Reorder widgets via AJAX drag-and-drop.
     */
    public function reorder(Request $request): JsonResponse
    {
        $items = $request->input('order', []);

        foreach ($items as $index => $id) {
            Widget::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Seed default initial widgets.
     */
    protected function seedDefaultWidgets(): void
    {
        $defaults = [
            ['area' => 'sidebar', 'widget_type' => 'popular', 'title' => 'Popular Stories', 'order' => 1, 'settings' => ['limit' => 4]],
            ['area' => 'sidebar', 'widget_type' => 'newsletter', 'title' => 'Stay Ahead in Tech', 'order' => 2, 'content' => 'Receive hand-curated engineering analyses.'],
            ['area' => 'sidebar', 'widget_type' => 'categories', 'title' => 'Explore Topics', 'order' => 3, 'settings' => ['limit' => 10]],
            ['area' => 'sidebar', 'widget_type' => 'tags', 'title' => 'Trending Keywords', 'order' => 4, 'settings' => ['limit' => 25]],
            ['area' => 'sidebar', 'widget_type' => 'ads', 'title' => 'Sponsored Partner', 'order' => 5, 'settings' => ['ad_code' => '<div class="p-3 text-center bg-light border rounded"><small class="text-uppercase text-muted font-weight-bold">Sponsor</small><div class="py-3 font-weight-bold text-primary">XIVIG Cloud Blueprints 2027</div></div>']],
        ];

        foreach ($defaults as $def) {
            Widget::create(array_merge($def, ['status' => true]));
        }
    }
}
