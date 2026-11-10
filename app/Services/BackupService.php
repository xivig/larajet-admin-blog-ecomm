<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\SystemBackup;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZipArchive;

class BackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!file_exists($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    /**
     * Create a backup archive based on type: full, database, or media.
     */
    public function createBackup(string $type = 'full', ?string $notes = null): SystemBackup
    {
        $timestamp = Carbon::now()->format('Y-m-d_His');
        $filename = "xivig_backup_{$type}_{$timestamp}.zip";
        $zipPath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Cannot create zip archive at {$zipPath}");
        }

        $manifest = [
            'app' => 'XivigAdmin PRO',
            'version' => '2.5.0',
            'type' => $type,
            'created_at' => Carbon::now()->toIso8601String(),
            'notes' => $notes,
            'db_driver' => config('database.default'),
        ];

        // 1. Database backup
        if ($type === 'full' || $type === 'database') {
            $this->addDatabaseToZip($zip, $manifest);
        }

        // 2. Media / Uploads backup
        if ($type === 'full' || $type === 'media') {
            $uploadsPath = public_path('uploads');
            if (file_exists($uploadsPath)) {
                $this->addDirectoryToZip($zip, $uploadsPath, 'uploads');
                $manifest['uploads_included'] = true;
            }
        }

        // 3. Manifest file
        $zip->addFromString('manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));
        $zip->close();

        $sizeBytes = file_exists($zipPath) ? filesize($zipPath) : 0;
        $formattedSize = SystemBackup::formatBytes($sizeBytes);

        $backup = SystemBackup::create([
            'filename' => $filename,
            'disk_path' => 'backups/' . $filename,
            'type' => $type,
            'size_bytes' => $sizeBytes,
            'formatted_size' => $formattedSize,
            'notes' => $notes,
            'status' => 'completed',
        ]);

        ActivityLog::record(
            'backup',
            'System',
            "Generated new {$type} system backup ({$filename}, {$formattedSize})",
            ['backup_id' => $backup->id, 'type' => $type, 'size' => $formattedSize]
        );

        return $backup;
    }

    /**
     * Add database dump to ZIP.
     */
    protected function addDatabaseToZip(ZipArchive $zip, array &$manifest): void
    {
        $dbConnection = config('database.default');

        if ($dbConnection === 'sqlite') {
            $sqlitePath = config('database.connections.sqlite.database');
            if (file_exists($sqlitePath)) {
                $zip->addFile($sqlitePath, 'database/database.sqlite');
                $manifest['database_file'] = 'database/database.sqlite';
            }
        } else {
            // Generic SQL Dump of all tables for MySQL/PgSQL
            $tables = DB::select('SHOW TABLES');
            $sqlDump = "-- XivigAdmin SQL Backup\n-- Generated: " . Carbon::now()->toDateTimeString() . "\n\n";

            $dbNameKey = 'Tables_in_' . config('database.connections.' . $dbConnection . '.database');
            foreach ($tables as $t) {
                $tableName = $t->$dbNameKey ?? current((array)$t);
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sqlDump .= "\nDROP TABLE IF EXISTS `{$tableName}`;\n";
                $sqlDump .= ($createTable[0]->{'Create Table'} ?? '') . ";\n\n";

                $rows = DB::table($tableName)->get();
                foreach ($rows as $row) {
                    $values = array_map(function ($val) {
                        return is_null($val) ? 'NULL' : "'" . addslashes($val) . "'";
                    }, (array)$row);

                    $sqlDump .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
                }
            }

            $zip->addFromString('database/database_dump.sql', $sqlDump);
            $manifest['database_file'] = 'database/database_dump.sql';
        }
    }

    /**
     * Recursively add a folder to ZIP archive.
     */
    protected function addDirectoryToZip(ZipArchive $zip, string $dir, string $zipSubdir = ''): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($dir) + 1);
                $zipPath = $zipSubdir ? ($zipSubdir . '/' . str_replace('\\', '/', $relativePath)) : str_replace('\\', '/', $relativePath);
                $zip->addFile($filePath, $zipPath);
            }
        }
    }

    /**
     * Restore from a backup archive.
     */
    public function restoreBackup(SystemBackup $backup): bool
    {
        $zipPath = $backup->absolute_path;
        if (!file_exists($zipPath)) {
            throw new \RuntimeException("Backup archive file not found on disk: {$backup->filename}");
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException("Unable to open backup archive {$backup->filename}");
        }

        // Restore Database safely via direct stream write (no raw extractTo Zip-Slip)
        $sqliteIndex = $zip->locateName('database/database.sqlite');
        if ($sqliteIndex !== false) {
            $sqliteTarget = config('database.connections.sqlite.database');
            if ($sqliteTarget && file_exists($sqliteTarget)) {
                copy($sqliteTarget, $sqliteTarget . '.pre_restore.bak');
            }
            if ($sqliteTarget && ($sqliteBytes = $zip->getFromIndex($sqliteIndex)) !== false) {
                $dbDir = dirname($sqliteTarget);
                if (!is_dir($dbDir)) {
                    mkdir($dbDir, 0755, true);
                }
                file_put_contents($sqliteTarget, $sqliteBytes);
            }
        }

        // Restore Uploads safely via Zip-Slip and extension allowlist verification
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = (string) $zip->getNameIndex($i);
            if (!SecurityFileGuard::isSafeZipEntry($entry, ['uploads/'], SecurityFileGuard::SAFE_RESTORE_EXTENSIONS)) {
                continue;
            }

            $destPath = public_path($entry);
            $destDir = dirname($destPath);
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            $bytes = $zip->getFromIndex($i);
            if ($bytes !== false) {
                file_put_contents($destPath, $bytes);
            }
        }

        $zip->close();

        ActivityLog::record(
            'restore',
            'System',
            "Restored system from backup archive ({$backup->filename})",
            ['backup_id' => $backup->id, 'filename' => $backup->filename]
        );

        return true;
    }

    /**
     * Delete a backup file and record.
     */
    public function deleteBackup(SystemBackup $backup): bool
    {
        $filename = $backup->filename;
        if (file_exists($backup->absolute_path)) {
            unlink($backup->absolute_path);
        }

        $backup->delete();

        ActivityLog::record(
            'delete',
            'System',
            "Deleted system backup archive ({$filename})",
            ['filename' => $filename]
        );

        return true;
    }
}
