<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SearchLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SearchAnalyticsController extends Controller
{
    /**
     * Display Search Analytics & Query Intelligence.
     */
    public function index(Request $request): View
    {
        $query = SearchLog::query()->orderBy('hits_count', 'desc');

        if ($request->filled('search')) {
            $query->where('query', 'like', "%{$request->search}%");
        }

        if ($request->filled('zero_results')) {
            $query->where('results_count', 0);
        }

        $logs = $query->paginate(20)->withQueryString();

        $stats = [
            'total_searches' => SearchLog::sum('hits_count'),
            'unique_queries' => SearchLog::count(),
            'zero_results_count' => SearchLog::where('results_count', 0)->count(),
            'top_query' => SearchLog::orderBy('hits_count', 'desc')->first()?->query ?? 'None',
        ];

        $topQueries = SearchLog::orderBy('hits_count', 'desc')->take(10)->get();
        $recentQueries = SearchLog::latest('last_searched_at')->take(10)->get();

        return view('admin.search-analytics.index', compact('logs', 'stats', 'topQueries', 'recentQueries'));
    }

    /**
     * Reset / Clear Search Analytics.
     */
    public function clear(): RedirectResponse
    {
        SearchLog::truncate();
        return back()->with('success', 'Search analytics records reset successfully.');
    }
}
