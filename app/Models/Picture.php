<?php

namespace App\Models;

use Database\Factories\PictureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Picture extends Model
{
    /** @use HasFactory<PictureFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'link',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'image_id', 'id');
    }
}
