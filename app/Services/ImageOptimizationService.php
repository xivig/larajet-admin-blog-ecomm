<?php

namespace App\Services;

use App\Models\Picture;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImageOptimizationService
{
    /**
     * Default compression quality for WebP.
     */
    protected int $quality = 82;

    /**
     * Responsive target widths.
     */
    protected array $breakpoints = [
        300 => 'thumb',
        600 => 'medium',
        1200 => 'large',
    ];

    /**
     * Process and optimize an uploaded image file.
     *
     * @param UploadedFile $file
     * @param string $directory (relative to public_path)
     * @param string|null $title
     * @param string|null $altText
     * @return Picture
     */
    public function processUpload(UploadedFile $file, string $directory = 'uploads/media', ?string $title = null, ?string $altText = null): Picture
    {
        $targetDir = public_path($directory);
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $baseName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
        $uniqueStamp = time() . '_' . Str::random(5);
        $savedFileName = "{$uniqueStamp}_{$baseName}.{$extension}";
        $fullPath = "{$targetDir}/{$savedFileName}";

        // Move uploaded file to public destination
        $file->move($targetDir, $savedFileName);
        $relativeLink = "{$directory}/{$savedFileName}";

        // Generate clean metadata
        $cleanTitle = $title ?: ucwords(str_replace(['-', '_'], ' ', pathinfo($originalName, PATHINFO_FILENAME)));
        $cleanAlt = $altText ?: $cleanTitle;

        // Perform WebP conversion and responsive variants
        $webpRelativePath = null;
        $responsiveUrls = [];
        $dimensions = null;

        if ($this->isImageSupported($extension) && function_exists('imagecreatefromstring')) {
            $imageDetails = $this->createOptimizedVariants($fullPath, $targetDir, $uniqueStamp . '_' . $baseName, $directory);
            $webpRelativePath = $imageDetails['webp_path'] ?? null;
            $responsiveUrls = $imageDetails['responsive_urls'] ?? [];
            $dimensions = $imageDetails['dimensions'] ?? null;
        }

        $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
        $mimeType = $file->getClientMimeType() ?: ('image/' . $extension);

        return Picture::create([
            'name' => $cleanTitle,
            'title' => $cleanTitle,
            'alt_text' => $cleanAlt,
            'caption' => $cleanTitle,
            'link' => $relativeLink,
            'webp_path' => $webpRelativePath,
            'responsive_urls' => $responsiveUrls,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'dimensions' => $dimensions,
            'folder' => $directory,
        ]);
    }

    /**
     * Create WebP and responsive resized variants.
     */
    public function createOptimizedVariants(string $sourceFile, string $targetDir, string $baseSlug, string $relPrefix): array
    {
        if (!file_exists($sourceFile)) {
            return [];
        }

        $imageContent = file_get_contents($sourceFile);
        $img = @imagecreatefromstring($imageContent);
        if (!$img) {
            return [];
        }

        $origWidth = imagesx($img);
        $origHeight = imagesy($img);
        $dimensions = "{$origWidth}x{$origHeight}";

        $webpFileName = "{$baseSlug}.webp";
        $webpFullPath = "{$targetDir}/{$webpFileName}";
        $webpRelativePath = "{$relPrefix}/{$webpFileName}";

        // Preserve alpha transparency for PNGs
        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);

        // Save WebP fullsize
        if (function_exists('imagewebp')) {
            imagewebp($img, $webpFullPath, $this->quality);
        }

        // Generate Responsive Breakpoints
        $responsiveUrls = [];
        foreach ($this->breakpoints as $targetWidth => $label) {
            if ($origWidth >= $targetWidth) {
                $targetHeight = (int) round(($origHeight / $origWidth) * $targetWidth);
                $resizedImg = imagecreatetruecolor($targetWidth, $targetHeight);

                // Preserve alpha channel
                imagealphablending($resizedImg, false);
                imagesavealpha($resizedImg, true);

                imagecopyresampled($resizedImg, $img, 0, 0, 0, 0, $targetWidth, $targetHeight, $origWidth, $origHeight);

                $variantName = "{$baseSlug}_{$targetWidth}w.webp";
                $variantFullPath = "{$targetDir}/{$variantName}";
                if (function_exists('imagewebp')) {
                    imagewebp($resizedImg, $variantFullPath, $this->quality);
                    $responsiveUrls[$targetWidth] = "{$relPrefix}/{$variantName}";
                }
                imagedestroy($resizedImg);
            }
        }

        imagedestroy($img);

        return [
            'webp_path' => file_exists($webpFullPath) ? $webpRelativePath : null,
            'responsive_urls' => $responsiveUrls,
            'dimensions' => $dimensions,
        ];
    }

    /**
     * Batch optimize an existing Picture record in the database.
     */
    public function optimizeExistingPicture(Picture $picture): bool
    {
        $rawPath = public_path($picture->link);
        if (!file_exists($rawPath)) {
            // Check legacy folder
            $altLegacy = public_path('xivig-blog/images/blog/' . $picture->link);
            if (file_exists($altLegacy)) {
                $rawPath = $altLegacy;
            } else {
                return false;
            }
        }

        $dir = dirname($rawPath);
        $relPrefix = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $dir);
        $relPrefix = str_replace('\\', '/', $relPrefix);

        $baseName = pathinfo($rawPath, PATHINFO_FILENAME);
        $variants = $this->createOptimizedVariants($rawPath, $dir, $baseName, $relPrefix);

        if (!empty($variants)) {
            $picture->update([
                'webp_path' => $variants['webp_path'] ?? $picture->webp_path,
                'responsive_urls' => $variants['responsive_urls'] ?? $picture->responsive_urls,
                'dimensions' => $variants['dimensions'] ?? $picture->dimensions,
                'title' => $picture->title ?: $picture->name,
                'alt_text' => $picture->alt_text ?: $picture->name,
            ]);
            return true;
        }

        return false;
    }

    protected function isImageSupported(string $extension): bool
    {
        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'bmp']);
    }
}
