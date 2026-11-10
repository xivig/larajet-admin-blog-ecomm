<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\NotFoundLog;
use App\Models\RedirectRule;
use App\Models\Setting;
use App\Models\SlugHistory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UrlManagementController extends Controller
{
    /**
     * Display URL Management Dashboard.
     */
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'redirects');

        // 1. Redirect Rules
        $redirectsQuery = RedirectRule::query()->latest();
        if ($request->filled('search_redirect')) {
            $sr = $request->search_redirect;
            $redirectsQuery->where(function ($q) use ($sr) {
                $q->where('source_path', 'like', "%{$sr}%")
                  ->orWhere('target_path', 'like', "%{$sr}%")
                  ->orWhere('description', 'like', "%{$sr}%");
            });
        }
        $redirectRules = $redirectsQuery->paginate(15, ['*'], 'redirects_page')->withQueryString();

        // 2. 404 Not Found Logs
        $notFoundQuery = NotFoundLog::query()->orderBy('hits_count', 'desc');
        if ($request->filled('search_404')) {
            $s404 = $request->search_404;
            $notFoundQuery->where('url', 'like', "%{$s404}%")
                          ->orWhere('referer', 'like', "%{$s404}%")
                          ->orWhere('ip', 'like', "%{$s404}%");
        }
        if ($request->filled('filter_resolved')) {
            $notFoundQuery->where('is_resolved', $request->filter_resolved === '1');
        }
        $notFoundLogs = $notFoundQuery->paginate(15, ['*'], 'logs_page')->withQueryString();

        // 3. Slug Histories
        $slugHistories = SlugHistory::latest()->paginate(20, ['*'], 'slugs_page');

        // Statistics
        $stats = [
            'total_redirects' => RedirectRule::count(),
            'active_redirects' => RedirectRule::where('is_active', true)->count(),
            'total_redirect_hits' => RedirectRule::sum('hits_count'),
            'total_404_errors' => NotFoundLog::sum('hits_count'),
            'unresolved_404s' => NotFoundLog::where('is_resolved', false)->count(),
            'total_slug_changes' => SlugHistory::count(),
        ];

        $robotsTxt = Setting::get('seo_robots_txt', "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /login\n\nSitemap: " . url('/sitemap.xml'));
        $canonicalBase = Setting::get('seo_canonical_url', url('/'));

        return view('admin.url-management.index', compact(
            'tab',
            'redirectRules',
            'notFoundLogs',
            'slugHistories',
            'stats',
            'robotsTxt',
            'canonicalBase'
        ));
    }

    /**
     * Store a new 301/302 Redirect Rule.
     */
    public function storeRedirect(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source_path' => 'required|string|max:1000',
            'target_path' => 'required|string|max:1000',
            'status_code' => 'required|in:301,302',
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $sourcePath = '/' . ltrim(trim($validated['source_path']), '/');
        $targetPath = trim($validated['target_path']);

        RedirectRule::create([
            'source_path' => $sourcePath,
            'target_path' => $targetPath,
            'status_code' => (int) $validated['status_code'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'hits_count' => 0,
        ]);

        // If source_path was logged as 404, mark it resolved automatically!
        NotFoundLog::where('url', 'like', "%{$sourcePath}%")
            ->orWhere('url', url($sourcePath))
            ->update(['is_resolved' => true]);

        ActivityLog::record('create', 'URL Management', "Created {$validated['status_code']} redirect from '{$sourcePath}' to '{$targetPath}'");

        return redirect()->route('admin.url-management.index', ['tab' => 'redirects'])
            ->with('success', "Redirect rule successfully created for {$sourcePath}");
    }

    /**
     * Update an existing Redirect Rule.
     */
    public function updateRedirect(Request $request, RedirectRule $rule): RedirectResponse
    {
        $validated = $request->validate([
            'source_path' => 'required|string|max:1000',
            'target_path' => 'required|string|max:1000',
            'status_code' => 'required|in:301,302',
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $sourcePath = '/' . ltrim(trim($validated['source_path']), '/');
        $targetPath = trim($validated['target_path']);

        $rule->update([
            'source_path' => $sourcePath,
            'target_path' => $targetPath,
            'status_code' => (int) $validated['status_code'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.url-management.index', ['tab' => 'redirects'])
            ->with('success', 'Redirect rule updated successfully.');
    }

    /**
     * Delete a Redirect Rule.
     */
    public function destroyRedirect(RedirectRule $rule): RedirectResponse
    {
        $rule->delete();
        return redirect()->route('admin.url-management.index', ['tab' => 'redirects'])
            ->with('success', 'Redirect rule removed.');
    }

    /**
     * Toggle active status of a redirect rule.
     */
    public function toggleRedirect(RedirectRule $rule): RedirectResponse
    {
        $rule->update(['is_active' => !$rule->is_active]);
        return back()->with('success', 'Redirect status updated.');
    }

    /**
     * Mark 404 log as resolved.
     */
    public function resolve404(NotFoundLog $log): RedirectResponse
    {
        $log->update(['is_resolved' => true]);
        return back()->with('success', '404 error marked as resolved.');
    }

    /**
     * Clear all resolved 404 logs.
     */
    public function clearResolved404(): RedirectResponse
    {
        NotFoundLog::where('is_resolved', true)->delete();
        return back()->with('success', 'Resolved 404 records purged successfully.');
    }

    /**
     * Export 404 logs to CSV.
     */
    public function export404(): StreamedResponse
    {
        $fileName = '404_logs_' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'URL', 'Referer', 'IP Address', 'Hits Count', 'Resolved', 'Last Seen At']);

            NotFoundLog::orderBy('hits_count', 'desc')->chunk(500, function ($logs) use ($handle) {
                foreach ($logs as $log) {
                    fputcsv($handle, [
                        $log->id,
                        $log->url,
                        $log->referer ?: 'Direct',
                        $log->ip,
                        $log->hits_count,
                        $log->is_resolved ? 'Yes' : 'No',
                        $log->last_seen_at ? $log->last_seen_at->toDateTimeString() : '',
                    ]);
                }
            });

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }

    /**
     * Trigger manual sitemap generation from admin.
     */
    public function generateSitemapNow(): RedirectResponse
    {
        Artisan::call('sitemap:generate');
        return back()->with('success', 'Fresh XML Sitemap generated and deployed successfully!');
    }
}
