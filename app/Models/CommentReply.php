<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentReply extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'comment_id',
        'user_id',
        'reply',
        'status',
        'guest_name',
        'guest_email',
        'likes_count',
        'reports_count',
    ];

    protected function casts(): array
    {
        return [
            'likes_count' => 'integer',
            'reports_count' => 'integer',
        ];
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
