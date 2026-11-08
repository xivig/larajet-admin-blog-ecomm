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
        'alt_text',
        'caption',
        'description',
        'link',
        'mime_type',
        'file_size',
        'dimensions',
        'folder',
    ];

    protected $appends = [
        'url',
        'formatted_size',
        'is_image',
    ];

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
