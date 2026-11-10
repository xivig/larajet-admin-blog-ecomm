<?php

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'post_id',
        'comment',
        'status',
        'guest_name',
        'guest_email',
        'guest_website',
        'likes_count',
        'reports_count',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'likes_count' => 'integer',
            'reports_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(CommentReply::class);
    }

    public function approvedReplies(): HasMany
    {
        return $this->hasMany(CommentReply::class)->where('status', 'approved');
    }

    public function getAuthorNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->name;
        }
        return $this->guest_name ?: 'Guest Reader';
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
