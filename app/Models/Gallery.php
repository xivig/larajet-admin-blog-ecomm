<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gallery extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'cover_image',
        'status',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(GalleryItem::class)->orderBy('order', 'asc')->orderBy('id', 'asc');
    }

    public function getCoverUrlAttribute(): string
    {
        if ($this->cover_image && file_exists(public_path($this->cover_image))) {
            return asset($this->cover_image);
        }

        $firstItem = $this->items()->first();
        if ($firstItem && file_exists(public_path($firstItem->image_path))) {
            return asset($firstItem->image_path);
        }

        return asset('xivig-admin/img/1-DZLDOIIi.jpg');
    }
}
