<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SecurityFileGuard
{
    public const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public const MEDIA_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'gif', 'webp',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'zip',
        'mp3', 'mp4', 'webm', 'ogg',
    ];

    public const SAFE_RESTORE_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'ico', 'svg',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv',
        'mp3', 'mp4', 'webm', 'ogg',
    ];

    /**
     * Strictly blocked executable or web-server control extensions.
     */
    public const BLOCKED_EXTENSIONS = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'php8', 'phar',
        'pl', 'py', 'rb', 'cgi', 'sh', 'bash', 'exe', 'bat', 'cmd', 'com',
        'msi', 'dll', 'so', 'js', 'mjs', 'html', 'htm', 'xhtml', 'svg',
        'htaccess', 'htpasswd', 'ini', 'env',
    ];

    public static function imageMimeRule(): string
    {
        return 'mimes:' . implode(',', self::IMAGE_EXTENSIONS);
    }

    public static function mediaMimeRule(): string
    {
        return 'mimes:' . implode(',', self::MEDIA_EXTENSIONS);
    }

    /**
     * Validate extension, reject double-extension handler tricks (e.g., evil.php.jpg),
     * and verify actual image headers for raster image extensions.
     */
    public static function isSafeUploadedFile(UploadedFile $file, array $allowedExtensions): bool
    {
        $clientName = (string) $file->getClientOriginalName();
        $ext = strtolower((string) $file->getClientOriginalExtension());

        if ($ext === '' || !in_array($ext, $allowedExtensions, true) || in_array($ext, self::BLOCKED_EXTENSIONS, true)) {
            return false;
        }

        // Prevent Apache mod_mime multi-extension execution (e.g., payload.php.jpg)
        if (preg_match('/\.(php\d*|phtml|phar|cgi|pl|py|rb|sh|exe|htaccess|htpasswd)(\.|$)/i', $clientName)) {
            return false;
        }

        if (in_array($ext, self::IMAGE_EXTENSIONS, true)) {
            $realPath = $file->getRealPath();
            if (!$realPath || @getimagesize($realPath) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Build a randomized, traversal-free filename for safe storage in public/uploads.
     */
    public static function safeStoredFilename(string $originalName, string $extension): string
    {
        $base = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
        $ext = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $extension) ?: 'bin');

        return time() . '_' . Str::random(8) . '_' . ($base !== '' ? $base : 'asset') . '.' . $ext;
    }

    /**
     * Resolve an existing relative path under public_path() and confine it to allowed folder prefixes.
     */
    public static function resolveAllowedPublicPath(string $relPath, array $allowedPrefixes = ['uploads/']): ?string
    {
        $normalized = str_replace('\\', '/', ltrim(trim($relPath), '/'));
        if ($normalized === '' || str_contains($normalized, "\0") || str_contains($normalized, '..')) {
            return null;
        }

        $matchesPrefix = false;
        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($normalized, rtrim($prefix, '/') . '/')) {
                $matchesPrefix = true;
                break;
            }
        }

        if (!$matchesPrefix) {
            return null;
        }

        $publicRoot = realpath(public_path());
        $resolved = realpath(public_path($normalized));
        if (!$publicRoot || !$resolved) {
            return null;
        }

        $rootNorm = rtrim(str_replace('\\', '/', $publicRoot), '/') . '/';
        $targetNorm = str_replace('\\', '/', $resolved);
        if (!str_starts_with($targetNorm, $rootNorm) || !is_file($resolved)) {
            return null;
        }

        return $resolved;
    }

    /**
     * Confine a destination folder slug strictly under an allowed upload subfolder.
     */
    public static function resolveSafeUploadFolder(string $folder): ?string
    {
        $normalized = trim(str_replace('\\', '/', $folder), '/');
        if ($normalized === '' || str_contains($normalized, '..') || str_contains($normalized, "\0")) {
            return null;
        }

        if (!str_starts_with($normalized, 'uploads/')) {
            return null;
        }

        $parts = explode('/', $normalized);
        $safeParts = ['uploads'];
        foreach (array_slice($parts, 1) as $segment) {
            $clean = Str::slug($segment);
            if ($clean === '' || $clean === 'trash') {
                return null;
            }
            $safeParts[] = $clean;
        }

        $relative = implode('/', $safeParts);
        $targetDir = public_path($relative);
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $publicUploads = realpath(public_path('uploads'));
        $resolvedDir = realpath($targetDir);
        if (!$publicUploads || !$resolvedDir) {
            return null;
        }

        $baseNorm = rtrim(str_replace('\\', '/', $publicUploads), '/') . '/';
        $destNorm = rtrim(str_replace('\\', '/', $resolvedDir), '/') . '/';

        return str_starts_with($destNorm, $baseNorm) ? $relative : null;
    }

    /**
     * Validate Zip archive entries against Zip-Slip traversal and unsafe executable extensions.
     */
    public static function isSafeZipEntry(string $entry, array $allowedPrefixes, array $allowedExtensions): bool
    {
        if ($entry === '' || str_ends_with($entry, '/') || str_contains($entry, "\0") || str_contains($entry, '\\')) {
            return false;
        }

        if (str_starts_with($entry, '/') || preg_match('#(^|/)\.\.(/|$)#', $entry)) {
            return false;
        }

        $matchesPrefix = false;
        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($entry, $prefix)) {
                $matchesPrefix = true;
                break;
            }
        }
        if (!$matchesPrefix) {
            return false;
        }

        $base = basename($entry);
        if (str_starts_with($base, '.')) {
            return false;
        }

        $ext = strtolower((string) pathinfo($base, PATHINFO_EXTENSION));
        if ($ext === '' || !in_array($ext, $allowedExtensions, true) || in_array($ext, self::BLOCKED_EXTENSIONS, true)) {
            return false;
        }

        return !preg_match('/\.(php\d*|phtml|phar|cgi|pl|py|sh|exe|htaccess|htpasswd)(\.|$)/i', $base);
    }
}
