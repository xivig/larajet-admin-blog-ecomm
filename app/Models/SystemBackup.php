<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemBackup extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'disk_path',
        'type',
        'size_bytes',
        'formatted_size',
        'notes',
        'status',
    ];

    public function getAbsolutePathAttribute(): string
    {
        return storage_path('app/backups/' . $this->filename);
    }

    public function existsOnDisk(): bool
    {
        return file_exists($this->absolute_path);
    }

    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
