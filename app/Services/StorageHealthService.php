<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class StorageHealthService
{
    /**
     * Test whether a directory is writable safely across both Windows and Unix platforms.
     */
    public function isWritable(string $dir): bool
    {
        if (!File::exists($dir)) {
            return false;
        }

        $testFile = rtrim($dir, '/\\') . '/.write_check_' . uniqid();
        $written = @file_put_contents($testFile, 'ok');

        if ($written !== false) {
            @unlink($testFile);
            return true;
        }

        return false;
    }

    /**
     * Inspect all key CMS storage targets and return detailed metrics.
     */
    public function getHealthReport(): array
    {
        $storagePublic = storage_path('app/public');
        $publicStorage = public_path('storage');
        $uploadsDir = public_path('uploads');

        return [
            'storage_app_public' => [
                'path' => $storagePublic,
                'exists' => File::exists($storagePublic),
                'writable' => $this->isWritable($storagePublic),
            ],
            'public_storage_symlink' => [
                'path' => $publicStorage,
                'exists' => File::exists($publicStorage) || is_link($publicStorage),
                'is_link' => is_link($publicStorage),
                'writable' => $this->isWritable($publicStorage),
            ],
            'public_uploads' => [
                'path' => $uploadsDir,
                'exists' => File::exists($uploadsDir),
                'writable' => $this->isWritable($uploadsDir),
            ],
            'disk_free_space' => disk_free_space(base_path()) ?: 0,
            'disk_total_space' => disk_total_space(base_path()) ?: 0,
        ];
    }
}
