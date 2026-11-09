<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class StorageCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:storage-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify local storage paths, symlinks, and write permissions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $storagePublic = storage_path('app/public');
        $publicLink = public_path('storage');
        $uploadsDir = public_path('uploads');

        $isWritable = function ($dir) {
            if (!File::exists($dir)) return false;
            $testFile = rtrim($dir, '/\\') . '/.write_test_' . uniqid();
            $written = @file_put_contents($testFile, 'test');
            if ($written !== false) {
                @unlink($testFile);
                return true;
            }
            return false;
        };

        $rows = [
            [
                'Path' => 'storage/app/public',
                'Exists' => File::exists($storagePublic) ? 'YES' : 'NO',
                'Writable' => $isWritable($storagePublic) ? 'YES' : 'NO',
                'Notes' => 'Core framework private media disk'
            ],
            [
                'Path' => 'public/storage (symlink)',
                'Exists' => (is_link($publicLink) || File::exists($publicLink)) ? 'YES' : 'NO',
                'Writable' => $isWritable($publicLink) ? 'YES' : 'NO',
                'Notes' => is_link($publicLink) ? 'Symlink connected' : 'Directory or missing'
            ],
            [
                'Path' => 'public/uploads',
                'Exists' => File::exists($uploadsDir) ? 'YES' : 'NO',
                'Writable' => $isWritable($uploadsDir) ? 'YES' : 'NO',
                'Notes' => 'CMS direct upload repository'
            ],
        ];

        $this->table(['Target Path', 'Exists', 'Writable', 'Details'], $rows);

        return self::SUCCESS;
    }
}
