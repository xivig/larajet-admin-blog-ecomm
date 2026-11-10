<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    public const TEMPLATES = [
        'default' => 'Default Content Layout',
        'landing' => 'Full-Width Landing Page',
        'about' => 'About Us Profile',
        'contact' => 'Contact & Inquiry Form',
        'privacy' => 'Privacy Policy Standard',
        'terms' => 'Terms & Conditions Standard',
        'custom' => 'Custom Raw Blade Template',
    ];

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'template',
        'status',
        'meta_title',
        'meta_description',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTemplateNameAttribute(): string
    {
        return self::TEMPLATES[$this->template] ?? 'Default Content Layout';
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
