<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'tag_id',
        'image_id',
        'author_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'status',
        'post_status',
        'published_at',
        'scheduled_at',
        'is_sticky',
        'is_featured',
        'visibility',
        'password',
        'reading_time',
        'views_count',
        // SEO Fields
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'meta_author',
        'meta_robots',
        'meta_language',
        'geo_region',
        'geo_placename',
        'geo_position',
        'meta_rating',
        'meta_copyright',
        // Social Open Graph
        'og_title',
        'og_description',
        'og_image',
        'twitter_card',
        // Schema.org
        'schema_type',
        'faq_schema',
        'video_schema',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    /**
     * Retrieve the model for a bound value (supports both numeric ID and slug).
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if ($field) {
            return $this->where($field, $value)->first();
        }

        if (is_numeric($value)) {
            $post = $this->where('id', $value)->first();
            if ($post) {
                return $post;
            }
        }

        return $this->where('slug', $value)->first();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'is_sticky' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'scheduled_at' => 'datetime',
            'reading_time' => 'integer',
            'views_count' => 'integer',
            'faq_schema' => 'array',
            'video_schema' => 'array',
        ];
    }

    // =========================================================================
    // Eloquent Relationships
    // =========================================================================

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Picture::class, 'image_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Unlimited Images / Photo Gallery.
     */
    public function images(): HasMany
    {
        return $this->hasMany(PostImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function gallery(): HasMany
    {
        return $this->images();
    }

    // =========================================================================
    // Query Scopes
    // =========================================================================

    public function scopePublished(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('post_status', 'published')
              ->orWhere(function ($sub) {
                  $sub->where('status', true)
                      ->where('post_status', '!=', 'draft')
                      ->where('post_status', '!=', 'scheduled');
              });
        })->where(function ($q) {
            $q->whereNull('published_at')
              ->orWhere('published_at', '<=', Carbon::now());
        });
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('post_status', 'draft')
            ->orWhere(function ($q) {
                $q->where('status', false)
                  ->where('post_status', '!=', 'scheduled');
            });
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('post_status', 'scheduled')
            ->orWhere(function ($q) {
                $q->where('status', false)
                  ->whereNotNull('scheduled_at')
                  ->where('scheduled_at', '>', Carbon::now());
            });
    }

    public function scopeSticky(Builder $query): Builder
    {
        return $query->where('is_sticky', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopePublicVisibility(Builder $query): Builder
    {
        return $query->where('visibility', 'public');
    }

    // =========================================================================
    // Accessors & Enterprise Calculations
    // =========================================================================

    /**
     * Auto-calculated reading time in minutes (~200 wpm).
     */
    public function getReadingTimeAttribute($value): int
    {
        if (!empty($value) && $value > 0) {
            return (int) $value;
        }

        $wordCount = str_word_count(strip_tags($this->content ?? $this->excerpt ?? ''));
        return max(1, (int) ceil($wordCount / 200));
    }

    /**
     * Extract structured Table of Contents from <h2> and <h3> tags.
     *
     * @return array<int, array{id: string, text: string, level: int}>
     */
    public function getTableOfContentsAttribute(): array
    {
        $content = $this->content ?? '';
        if (empty($content)) {
            return [];
        }

        $toc = [];
        $pattern = '/<(h[23])(?:\s+[^>]*)?>(.*?)<\/\1>/i';

        if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $index => $match) {
                $tag = strtolower($match[1]);
                $headingText = trim(strip_tags($match[2]));
                $slug = Str::slug($headingText) ?: 'section-' . ($index + 1);

                $toc[] = [
                    'id' => $slug,
                    'text' => $headingText,
                    'level' => ($tag === 'h2') ? 2 : 3,
                ];
            }
        }

        return $toc;
    }

    /**
     * Injects matching id="..." anchors into all <h2> and <h3> tags for TOC jump links.
     */
    public function getContentWithAnchorsAttribute(): string
    {
        $content = $this->content ?? '';
        if (empty($content)) {
            return '';
        }

        $index = 0;
        return preg_replace_callback('/<(h[23])(?:\s+[^>]*)?>(.*?)<\/\1>/i', function ($match) use (&$index) {
            $index++;
            $tag = strtolower($match[1]);
            $headingText = trim(strip_tags($match[2]));
            $slug = Str::slug($headingText) ?: 'section-' . $index;

            return "<{$tag} id=\"{$slug}\" class=\"content-heading content-{$tag}\">{$match[2]}</{$tag}>";
        }, $content);
    }

    /**
     * Determine if content is locked (Password Protected or Private).
     */
    public function isLockedForUser(?User $user = null): bool
    {
        // 1. Author and Super Admins always bypass lock
        if ($user && ($user->id === $this->user_id || $user->hasRole(['super-admin', 'admin', 'editor']))) {
            return false;
        }

        // 2. Private: accessible only by logged in members
        if ($this->visibility === 'private') {
            return !$user;
        }

        // 3. Password Protected: check session unlock
        if ($this->visibility === 'password_protected' && !empty($this->password)) {
            return session('unlocked_post_' . $this->id) !== true;
        }

        return false;
    }

    /**
     * Verify submitted password and record in session.
     */
    public function verifyPassword(string $input): bool
    {
        if (empty($this->password)) {
            return true;
        }

        $valid = ($input === $this->password);
        if (! $valid && (str_starts_with($this->password, '$2y$') || str_starts_with($this->password, '$2a$') || str_starts_with($this->password, '$argon2'))) {
            try {
                $valid = Hash::check($input, $this->password);
            } catch (\Throwable $e) {
                $valid = false;
            }
        }

        if ($valid) {
            session(['unlocked_post_' . $this->id => true]);
            return true;
        }

        return false;
    }

    /**
     * Query related posts based on shared category and tags.
     */
    public function getRelatedPosts(int $limit = 3)
    {
        return self::published()
            ->where('id', '!=', $this->id)
            ->where(function ($q) {
                if ($this->category_id) {
                    $q->where('category_id', $this->category_id);
                }
                if ($this->tag_id) {
                    $q->orWhere('tag_id', $this->tag_id);
                }
            })
            ->with(['category', 'image', 'author.user'])
            ->latest('published_at')
            ->take($limit)
            ->get();
    }

    /**
     * Get the published post immediately preceding this post.
     */
    public function previousPost(): ?self
    {
        $refDate = $this->published_at ?? $this->created_at;

        return self::published()
            ->where('id', '!=', $this->id)
            ->where('published_at', '<', $refDate)
            ->orderByDesc('published_at')
            ->first();
    }

    /**
     * Get the published post immediately following this post.
     */
    public function nextPost(): ?self
    {
        $refDate = $this->published_at ?? $this->created_at;

        return self::published()
            ->where('id', '!=', $this->id)
            ->where('published_at', '>', $refDate)
            ->orderBy('published_at')
            ->first();
    }

    /**
     * Deep-clone this post including category, tags, images, and SEO configuration.
     */
    public function clonePost(?string $newTitle = null): self
    {
        $baseTitle = $newTitle ?: ($this->title . ' (Copy)');
        $baseSlug = Str::slug($baseTitle);
        $slug = $baseSlug;
        $counter = 1;

        while (self::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $clone = $this->replicate([
            'slug',
            'created_at',
            'updated_at',
            'deleted_at',
            'views_count',
        ]);

        $clone->title = $baseTitle;
        $clone->slug = $slug;
        $clone->status = false;
        $clone->post_status = 'draft';
        $clone->published_at = null;
        $clone->scheduled_at = null;
        $clone->user_id = auth()->id() ?? $this->user_id;
        $clone->save();

        // Replicate unlimited gallery images
        foreach ($this->images as $img) {
            $clone->images()->create([
                'picture_id' => $img->picture_id,
                'image_path' => $img->image_path,
                'caption' => $img->caption,
                'alt_text' => $img->alt_text,
                'sort_order' => $img->sort_order,
            ]);
        }

        return $clone;
    }
}
