<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to log an activity seamlessly from anywhere.
     */
    public static function record(string $action, string $module, string $description, ?array $properties = null, ?User $user = null): self
    {
        $userId = $user?->id ?? auth()->id();
        if (!$userId) {
            $firstUser = User::first();
            $userId = $firstUser ? $firstUser->id : null;
        }

        return static::create([
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()?->ip() ?? '127.0.0.1',
            'user_agent' => request()?->userAgent() ?? 'XivigAdmin Browser Agent',
        ]);
    }
}
