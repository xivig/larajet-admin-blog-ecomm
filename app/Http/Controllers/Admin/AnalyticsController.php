<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(protected AnalyticsService $analyticsService)
    {
    }

    /**
     * Display the Real-Time Visitor & Audience Analytics Dashboard.
     */
    public function index(Request $request): View
    {
        $metrics = $this->analyticsService->getDashboardMetrics();

        return view('admin.analytics.index', $metrics);
    }
}
