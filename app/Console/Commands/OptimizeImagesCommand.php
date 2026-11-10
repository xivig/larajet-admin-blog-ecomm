<?php

namespace App\Console\Commands;

use App\Models\Picture;
use App\Services\ImageOptimizationService;
use Illuminate\Console\Command;

class OptimizeImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'images:optimize {--force : Re-optimize already processed images}';

    /**
     * The console command description.
     */
    protected $description = 'Batch optimize uploaded images into modern WebP format with responsive breakpoints';

    /**
     * Execute the console command.
     */
    public function handle(ImageOptimizationService $optimizer): int
    {
        $this->info('Starting automated image optimization & WebP conversion...');

        $query = Picture::query();
        if (!$this->option('force')) {
            $query->whereNull('webp_path');
        }

        $pictures = $query->get();
        $total = $pictures->count();

        if ($total === 0) {
            $this->info('All images are already optimized with WebP and responsive assets.');
            return 0;
        }

        $this->output->progressStart($total);
        $optimized = 0;

        foreach ($pictures as $picture) {
            if ($optimizer->optimizeExistingPicture($picture)) {
                $optimized++;
            }
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->info("Successfully converted {$optimized}/{$total} images to WebP.");

        return 0;
    }
}
