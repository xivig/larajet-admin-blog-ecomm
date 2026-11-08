<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\BackupService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ActivityLogAndBackupSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin ? $admin->id : 1;

        // 1. Initial Activity Logs
        $sampleLogs = [
            [
                'user_id' => $adminId,
                'action' => 'login',
                'module' => 'System',
                'description' => 'Administrator successfully authenticated into XivigAdmin PRO console.',
                'ip_address' => '127.0.0.1',
                'created_at' => Carbon::now()->subMinutes(85),
            ],
            [
                'user_id' => $adminId,
                'action' => 'update',
                'module' => 'Settings',
                'description' => 'Updated Editorial Gazette Title and Timezone parameters.',
                'ip_address' => '127.0.0.1',
                'created_at' => Carbon::now()->subMinutes(70),
            ],
            [
                'user_id' => $adminId,
                'action' => 'create',
                'module' => 'Galleries',
                'description' => 'Created new visual lookbook gallery "abc" (/abc-lpeiY).',
                'ip_address' => '127.0.0.1',
                'created_at' => Carbon::now()->subMinutes(55),
            ],
            [
                'user_id' => $adminId,
                'action' => 'reassign',
                'module' => 'Users',
                'description' => 'Configured user authority and synced security roles for editorial contributors.',
                'ip_address' => '127.0.0.1',
                'created_at' => Carbon::now()->subMinutes(35),
            ],
            [
                'user_id' => $adminId,
                'action' => 'create',
                'module' => 'Publications',
                'description' => 'Published new editorial article "Architectural Mastery & Horology".',
                'ip_address' => '127.0.0.1',
                'created_at' => Carbon::now()->subMinutes(20),
            ],
            [
                'user_id' => $adminId,
                'action' => 'backup',
                'module' => 'System',
                'description' => 'Generated full baseline system snapshot and media assets archive.',
                'ip_address' => '127.0.0.1',
                'created_at' => Carbon::now()->subMinutes(5),
            ],
        ];

        foreach ($sampleLogs as $log) {
            ActivityLog::create(array_merge($log, [
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/130.0.0.0 Safari/537.36',
                'updated_at' => $log['created_at'],
            ]));
        }

        // 2. Generate initial real backup via BackupService
        try {
            $backupService = new BackupService();
            $backupService->createBackup('database', 'Initial baseline database snapshot');
        } catch (\Throwable $e) {
            // Backup service warning handled gracefully
        }
    }
}
