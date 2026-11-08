<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of system activity logs with filtering and analytics.
     */
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('module') && $request->module !== 'all') {
            $query->where('module', $request->module);
        }

        if ($request->filled('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('description', 'like', "%{$term}%")
                  ->orWhere('ip_address', 'like', "%{$term}%")
                  ->orWhereHas('user', function ($uq) use ($term) {
                      $uq->where('name', 'like', "%{$term}%")
                         ->orWhere('email', 'like', "%{$term}%");
                  });
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        $metrics = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'operators' => ActivityLog::distinct('user_id')->count('user_id'),
            'security' => ActivityLog::whereIn('module', ['System', 'Settings', 'Roles'])->count(),
        ];

        $modules = ActivityLog::distinct()->pluck('module')->filter()->values();
        $actions = ActivityLog::distinct()->pluck('action')->filter()->values();

        return view('admin.activity-logs.index', compact('logs', 'metrics', 'modules', 'actions'));
    }

    /**
     * Purge activity logs.
     */
    public function clear(Request $request): RedirectResponse
    {
        $period = $request->input('period', 'all');

        if ($period === '30') {
            $deleted = ActivityLog::where('created_at', '<', now()->subDays(30))->delete();
            $msg = "Purged {$deleted} activity log records older than 30 days.";
        } elseif ($period === '60') {
            $deleted = ActivityLog::where('created_at', '<', now()->subDays(60))->delete();
            $msg = "Purged {$deleted} activity log records older than 60 days.";
        } else {
            $deleted = ActivityLog::truncate();
            $msg = "All historical activity log records have been cleared.";
        }

        return redirect()->route('admin.activity-logs.index')->with('success', $msg);
    }

    /**
     * Export activity logs as CSV.
     */
    public function export(): StreamedResponse
    {
        $logs = ActivityLog::with('user')->latest()->get();
        $fileName = 'activity_logs_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Date & Time', 'Operator', 'Email', 'Module', 'Action', 'Description', 'IP Address']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->id,
                    $log->created_at ? $log->created_at->toDateTimeString() : '',
                    $log->user ? $log->user->name : 'System Automation',
                    $log->user ? $log->user->email : 'system@xivig.com',
                    $log->module,
                    $log->action,
                    $log->description,
                    $log->ip_address,
                ]);
            }

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }
}
