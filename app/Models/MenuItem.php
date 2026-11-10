<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'parent_id',
        'title',
        'url',
        'target',
        'icon',
        'order',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Resolve relative paths to full site URLs while preserving anchors and external links.
     */
    public function resolvedUrl(): string
    {
        $raw = trim((string) $this->url);

        if ($raw === '' || str_starts_with($raw, '#') || Str::startsWith($raw, ['http://', 'https://', 'mailto:', 'tel:'])) {
            return $raw === '' ? '#' : $raw;
        }

        return url($raw);
    }

    /**
     * Determine whether this top-level item should render the dynamic Site Pages dropdown.
     */
    public function isPagesPlaceholder(): bool
    {
        return in_array(trim((string) $this->url), ['#pages', '/pages'], true);
    }

    /**
     * Determine whether this top-level item should render the rich Blog mega-menu.
     */
    public function isBlogMegaTrigger(): bool
    {
        $hasChildren = $this->relationLoaded('children')
            ? $this->children->isNotEmpty()
            : $this->children()->exists();

        return !$hasChildren && in_array(trim((string) $this->url), ['/blog', '/blog-list'], true);
    }

    /**
     * Determine whether the current request matches this item or any of its children.
     */
    public function isActive(): bool
    {
        if ($this->isPagesPlaceholder()) {
            return request()->is('page*');
        }

        $raw = trim((string) $this->url);
        if ($raw === '' || str_starts_with($raw, '#')) {
            return false;
        }

        if (Str::startsWith($raw, ['http://', 'https://'])) {
            $appHost = parse_url(url('/'), PHP_URL_HOST);
            $linkHost = parse_url($raw, PHP_URL_HOST);
            if ($appHost && $linkHost && strcasecmp($appHost, $linkHost) !== 0) {
                return false;
            }
        }

        $path = trim((string) parse_url($raw, PHP_URL_PATH), '/');
        $matchesSelf = $path === ''
            ? request()->is('/')
            : ($path === 'blog' ? request()->is('blog*') : request()->is($path));

        if ($matchesSelf) {
            return true;
        }

        if ($this->relationLoaded('children')) {
            foreach ($this->children as $child) {
                if ($child->isActive()) {
                    return true;
                }
            }
        }

        return false;
    }
}
