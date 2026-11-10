<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Menu;
use App\Models\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    /**
     * Display visual widget management interface.
     */
    public function index(Request $request): View
    {
        $selectedArea = $request->query('area', 'sidebar');
        if (!array_key_exists($selectedArea, Widget::AREAS)) {
            $selectedArea = 'sidebar';
        }

        if (Widget::count() === 0) {
            $this->seedDefaultWidgets();
        }

        $widgets = Widget::where('area', $selectedArea)
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $areaCounts = Widget::query()
            ->selectRaw('area, COUNT(*) as total')
            ->groupBy('area')
            ->pluck('total', 'area');

        $hasMissingFooterCols = collect(array_keys(Widget::FOOTER_COLUMNS))
            ->contains(fn (string $area) => (int) ($areaCounts[$area] ?? 0) === 0);

        $allAreas = Widget::AREAS;
        $widgetTypes = Widget::WIDGET_TYPES;
        $menuLocations = Menu::LOCATIONS;

        return view('admin.widgets.index', compact(
            'widgets',
            'selectedArea',
            'allAreas',
            'widgetTypes',
            'areaCounts',
            'menuLocations',
            'hasMissingFooterCols'
        ));
    }

    /**
     * Store a newly created widget.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateWidgetPayload($request);
        $maxOrder = Widget::where('area', $validated['area'])->max('order') ?? 0;

        Widget::create([
            'area' => $validated['area'],
            'widget_type' => $validated['widget_type'],
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'settings' => $this->buildSettings($request),
            'order' => $maxOrder + 1,
            'status' => $request->boolean('status', true),
        ]);

        ActivityLog::record('create', 'Widgets', "Added widget '{$validated['title']}' to {$validated['area']}");

        return redirect()->route('admin.widgets.index', ['area' => $validated['area']])
            ->with('success', "Widget '{$validated['title']}' added and synchronized.");
    }

    /**
     * Update an existing widget.
     */
    public function update(Request $request, Widget $widget): RedirectResponse
    {
        $validated = $this->validateWidgetPayload($request, $widget);
        $targetArea = $validated['area'] ?? $widget->area;

        $widget->update([
            'area' => $targetArea,
            'widget_type' => $validated['widget_type'] ?? $widget->widget_type,
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'settings' => $this->buildSettings($request, $widget->settings ?? []),
            'status' => $request->boolean('status'),
        ]);

        ActivityLog::record('update', 'Widgets', "Updated widget '{$widget->title}' in {$targetArea}");

        return redirect()->route('admin.widgets.index', ['area' => $targetArea])
            ->with('success', "Widget '{$widget->title}' updated and synchronized.");
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
     * Reorder widgets via AJAX.
     */
    public function reorder(Request $request): JsonResponse
    {
        $items = $request->input('order', []);

        foreach ($items as $index => $id) {
            if ($id) {
                Widget::whereKey((int) $id)->update(['order' => $index + 1]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Populate any empty footer column slots with their default synchronized widgets.
     */
    public function syncFooterDefaults(): RedirectResponse
    {
        Widget::ensureDefaultFooterWidgets();

        return redirect()->route('admin.widgets.index', ['area' => 'footer_1'])
            ->with('success', 'All missing footer column widgets restored and synchronized.');
    }

    /**
     * Validate common widget fields for create/update requests.
     *
     * @return array<string, mixed>
     */
    protected function validateWidgetPayload(Request $request, ?Widget $existing = null): array
    {
        $rules = [
            'area' => ($existing ? 'nullable' : 'required') . '|string|in:' . implode(',', array_keys(Widget::AREAS)),
            'widget_type' => ($existing ? 'nullable' : 'required') . '|string|in:' . implode(',', array_keys(Widget::WIDGET_TYPES)),
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:50',
            'ad_code' => 'nullable|string',
            'menu_location' => 'nullable|string|max:60',
            'email' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:60',
            'status' => 'nullable|boolean',
        ];

        return $request->validate($rules);
    }

    /**
     * Extract structured settings payload cleanly without overwriting omitted keys unnecessarily.
     *
     * @param  array<string, mixed>  $existing
     * @return array<string, mixed>
     */
    protected function buildSettings(Request $request, array $existing = []): array
    {
        $settings = $existing;

        if ($request->has('limit')) {
            $settings['limit'] = $request->filled('limit') ? (int) $request->input('limit') : null;
        }
        if ($request->has('ad_code')) {
            $settings['ad_code'] = $request->input('ad_code');
        }
        if ($request->has('menu_location')) {
            $settings['menu_location'] = $request->input('menu_location') ?: null;
        }
        if ($request->has('email')) {
            $settings['email'] = $request->input('email') ?: null;
        }
        if ($request->has('phone')) {
            $settings['phone'] = $request->input('phone') ?: null;
        }

        return array_filter($settings, static fn ($val) => $val !== null && $val !== '');
    }

    /**
     * Seed default initial sidebar and footer widgets.
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

        Widget::ensureDefaultFooterWidgets();
    }
}
