<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class OptimizeMediaImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(public string $relativePath)
    {
        $this->onQueue(config('queue.connections.database.queue', 'default'));
    }

    /**
     * Execute the media processing job.
     */
    public function handle(): void
    {
        $fullPath = public_path($this->relativePath);

        if (!File::exists($fullPath) || !is_file($fullPath)) {
            Log::warning("OptimizeMediaImageJob: File not found: {$this->relativePath}");
            return;
        }

        $size = File::size($fullPath);
        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        // If it's an image, verify image integrity
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            try {
                $imageInfo = @getimagesize($fullPath);
                if ($imageInfo) {
                    Log::info("OptimizeMediaImageJob: Verified image {$this->relativePath} ({$imageInfo[0]}x{$imageInfo[1]}px, " . round($size / 1024, 1) . " KB)");
                }
            } catch (\Throwable $e) {
                Log::warning("OptimizeMediaImageJob: Could not inspect image {$this->relativePath}: " . $e->getMessage());
            }
        }
    }
}
