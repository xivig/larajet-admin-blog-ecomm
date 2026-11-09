<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_type',
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'status',
    ];

    /**
     * Get the badge color for login status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'successful' => 'bg-success-subtle text-success border border-success-subtle',
            'failed' => 'bg-danger-subtle text-danger border border-danger-subtle',
            '2fa_prompted' => 'bg-warning-subtle text-warning border border-warning-subtle',
            default => 'bg-secondary-subtle text-secondary',
        };
    }
}
