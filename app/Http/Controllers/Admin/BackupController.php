<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemBackup;
use App\Services\BackupService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    protected BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Display the system backups dashboard.
     */
    public function index(): View
    {
        $backups = SystemBackup::latest()->get();

        $totalSizeBytes = $backups->sum('size_bytes');
        $formattedTotalSize = SystemBackup::formatBytes($totalSizeBytes);

        $latestBackup = $backups->first();

        // Disk space metrics
        $diskFree = disk_free_space(base_path());
        $diskTotal = disk_total_space(base_path());
        $formattedDiskFree = $diskFree ? SystemBackup::formatBytes($diskFree) : 'N/A';
        $formattedDiskTotal = $diskTotal ? SystemBackup::formatBytes($diskTotal) : 'N/A';
        $diskUsagePercent = ($diskTotal && $diskFree) ? round((($diskTotal - $diskFree) / $diskTotal) * 100, 1) : 0;

        return view('admin.backups.index', compact(
            'backups',
            'formattedTotalSize',
            'latestBackup',
            'formattedDiskFree',
            'formattedDiskTotal',
            'diskUsagePercent'
        ));
    }

    /**
     * Trigger a new system backup.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'type' => 'required|string|in:full,database,media',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $backup = $this->backupService->createBackup($request->type, $request->notes);
            return redirect()->route('admin.backups.index')->with(
                'success',
                "New {$backup->type} backup ({$backup->filename}) created successfully ({$backup->formatted_size})!"
            );
        } catch (\Throwable $e) {
            return redirect()->route('admin.backups.index')->with('error', 'Backup generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Download a generated backup archive.
     */
    public function download(SystemBackup $backup): BinaryFileResponse|RedirectResponse
    {
        if (!file_exists($backup->absolute_path)) {
            return redirect()->route('admin.backups.index')->with('error', 'Backup archive file does not exist on disk.');
        }

        return response()->download($backup->absolute_path, $backup->filename);
    }

    /**
     * Restore system from an existing backup archive.
     */
    public function restore(SystemBackup $backup): RedirectResponse
    {
        try {
            $this->backupService->restoreBackup($backup);
            return redirect()->route('admin.backups.index')->with(
                'success',
                "System successfully restored from backup archive ({$backup->filename})!"
            );
        } catch (\Throwable $e) {
            return redirect()->route('admin.backups.index')->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    /**
     * Remove a backup archive and record.
     */
    public function destroy(SystemBackup $backup): RedirectResponse
    {
        try {
            $this->backupService->deleteBackup($backup);
            return redirect()->route('admin.backups.index')->with('success', 'Backup archive deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.backups.index')->with('error', 'Failed to delete backup: ' . $e->getMessage());
        }
    }
}
