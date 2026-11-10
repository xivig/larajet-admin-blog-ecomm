<?php

namespace App\Models;

use Database\Factories\PictureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Picture extends Model
{
    /** @use HasFactory<PictureFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'title',
        'alt_text',
        'caption',
        'description',
        'link',
        'webp_path',
        'responsive_urls',
        'mime_type',
        'file_size',
        'dimensions',
        'folder',
    ];

    protected $appends = [
        'url',
        'webp_url',
        'formatted_size',
        'is_image',
    ];

    protected function casts(): array
    {
        return [
            'responsive_urls' => 'array',
        ];
    }

    /**
     * Relationship to Posts.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'image_id', 'id');
    }

    /**
     * Get the public asset URL.
     */
    public function getUrlAttribute(): string
    {
        if (empty($this->link)) {
            return asset('admin-dist/img/blog-1-PTDVKJDt.webp');
        }

        if (Str::startsWith($this->link, ['http://', 'https://'])) {
            return $this->link;
        }

        if (Str::startsWith($this->link, ['uploads/', 'admin-dist/', 'xivig-blog/'])) {
            return asset($this->link);
        }

        // Default legacy fallback
        return asset('xivig-blog/images/blog/' . $this->link);
    }

    /**
     * Get the WebP asset URL if available.
     */
    public function getWebpUrlAttribute(): string
    {
        if (!empty($this->webp_path)) {
            if (Str::startsWith($this->webp_path, ['http://', 'https://'])) {
                return $this->webp_path;
            }
            return asset($this->webp_path);
        }

        // Fallback to regular url if it's already webp
        if (Str::endsWith(strtolower($this->link ?? ''), '.webp')) {
            return $this->url;
        }

        return $this->url;
    }

    /**
     * Generate responsive srcset string.
     */
    public function getSrcsetAttribute(): string
    {
        if (!empty($this->responsive_urls) && is_array($this->responsive_urls)) {
            $srcsetParts = [];
            foreach ($this->responsive_urls as $width => $path) {
                $itemUrl = Str::startsWith($path, ['http://', 'https://']) ? $path : asset($path);
                $srcsetParts[] = "{$itemUrl} {$width}w";
            }
            if (!empty($srcsetParts)) {
                return implode(', ', $srcsetParts);
            }
        }

        return $this->webp_url . ' 1200w';
    }

    /**
     * Clean Alt Text fallback generator.
     */
    public function getDisplayAltAttribute(): string
    {
        if (!empty($this->alt_text)) {
            return $this->alt_text;
        }
        if (!empty($this->title)) {
            return $this->title;
        }
        if (!empty($this->name)) {
            return ucwords(str_replace(['-', '_'], ' ', pathinfo($this->name, PATHINFO_FILENAME)));
        }
        return 'Article Editorial Visual';
    }

    /**
     * Clean Title / Caption fallback generator.
     */
    public function getDisplayTitleAttribute(): string
    {
        return $this->title ?: ($this->caption ?: $this->display_alt);
    }

    /**
     * Get human-readable file size.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes <= 0) {
            return 'Unknown size';
        }

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }

    /**
     * Determine if asset is an image.
     */
    public function getIsImageAttribute(): bool
    {
        if (empty($this->mime_type)) {
            return true;
        }

        return Str::startsWith($this->mime_type, 'image/');
    }
}
