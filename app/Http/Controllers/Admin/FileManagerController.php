<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SecurityFileGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FileManagerController extends Controller
{
    /**
     * Allowed storage directories.
     */
    protected function getAllowedFolders(): array
    {
        $folders = [
            'uploads/posts' => 'Post Images',
            'uploads/avatars' => 'User Avatars',
            'uploads/chat' => 'Chat Attachments',
            'uploads/email-attachments' => 'Email Attachments',
            'xivig-admin/img' => 'Template Visuals',
            'uploads/trash' => 'Recycle Bin / Trash',
        ];

        // Dynamically discover custom subfolders inside public/uploads
        $uploadsDir = public_path('uploads');
        if (File::exists($uploadsDir)) {
            foreach (File::directories($uploadsDir) as $dir) {
                $base = basename($dir);
                $rel = 'uploads/' . $base;
                if (!isset($folders[$rel]) && $base !== 'trash') {
                    $folders[$rel] = ucwords(str_replace(['-', '_'], ' ', $base));
                }
            }
        }

        return $folders;
    }

    /**
     * Display the file manager with sorting and view support.
     */
    public function index(Request $request): View
    {
        $allowedFolders = $this->getAllowedFolders();

        $currentFolder = $request->input('folder', 'uploads/posts');
        if (!array_key_exists($currentFolder, $allowedFolders)) {
            $currentFolder = 'uploads/posts';
        }

        $fullPath = public_path($currentFolder);
        if (!File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true);
        }

        $rawFiles = File::files($fullPath);
        $files = [];

        foreach ($rawFiles as $file) {
            $ext = strtolower($file->getExtension());
            $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
            $files[] = [
                'name' => $file->getFilename(),
                'path' => $currentFolder . '/' . $file->getFilename(),
                'size' => round($file->getSize() / 1024, 1) . ' KB',
                'raw_size' => $file->getSize(),
                'raw_modified' => $file->getMTime(),
                'extension' => $ext,
                'is_image' => $isImg,
                'modified' => date('M d, Y h:i A', $file->getMTime()),
            ];
        }

        // Apply Sorting
        $sort = $request->input('sort', 'name');
        switch ($sort) {
            case 'date':
                usort($files, fn($a, $b) => $b['raw_modified'] <=> $a['raw_modified']);
                break;
            case 'size':
                usort($files, fn($a, $b) => $b['raw_size'] <=> $a['raw_size']);
                break;
            case 'type':
                usort($files, fn($a, $b) => strcasecmp($a['extension'], $b['extension']));
                break;
            case 'name':
            default:
                usort($files, fn($a, $b) => strcasecmp($a['name'], $b['name']));
                break;
        }

        // Stats across all files in current directory
        $totalFiles = count($files);
        $totalBytes = array_sum(array_column($files, 'raw_size'));
        $totalSizeFormatted = round($totalBytes / (1024 * 1024), 2) . ' MB';

        // Count trashed files
        $trashPath = public_path('uploads/trash');
        $trashCount = File::exists($trashPath) ? count(File::files($trashPath)) : 0;

        return view('admin.file-manager.index', compact(
            'files',
            'allowedFolders',
            'currentFolder',
            'totalFiles',
            'totalSizeFormatted',
            'trashCount',
            'sort'
        ));
    }

    /**
     * Create a new folder inside uploads directory.
     */
    public function createFolder(Request $request): RedirectResponse
    {
        $request->validate([
            'folder_name' => 'required|string|max:50|regex:/^[a-zA-Z0-9_\-\s]+$/',
        ]);

        $folderSlug = Str::slug($request->folder_name);
        if (empty($folderSlug) || $folderSlug === 'trash') {
            return back()->with('error', 'Invalid or reserved folder name specified.');
        }

        $newRelPath = 'uploads/' . $folderSlug;
        $targetDir = public_path($newRelPath);

        if (File::exists($targetDir)) {
            return back()->with('error', 'A folder with this name already exists.');
        }

        File::makeDirectory($targetDir, 0755, true);

        return redirect()->route('admin.file-manager.index', ['folder' => $newRelPath])
            ->with('success', 'Folder "' . $request->folder_name . '" created successfully!');
    }

    /**
     * Upload a new file safely inside allowed upload directories.
     */
    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480', SecurityFileGuard::mediaMimeRule()],
            'folder' => 'required|string',
        ]);

        $file = $request->file('file');
        if (!$file || !SecurityFileGuard::isSafeUploadedFile($file, SecurityFileGuard::MEDIA_EXTENSIONS)) {
            return back()->with('error', 'File rejected by security policy (disallowed type or invalid image header).');
        }

        $folder = SecurityFileGuard::resolveSafeUploadFolder((string) $request->folder) ?? 'uploads/posts';
        $destDir = public_path($folder);
        if (!File::exists($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $safeName = SecurityFileGuard::safeStoredFilename(
            (string) $file->getClientOriginalName(),
            (string) $file->getClientOriginalExtension()
        );

        $file->move($destDir, $safeName);

        try {
            \App\Jobs\OptimizeMediaImageJob::dispatch($folder . '/' . $safeName);
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.file-manager.index', ['folder' => $folder])
            ->with('success', 'File "' . $safeName . '" uploaded successfully!');
    }

    /**
     * Duplicate an existing file inside allowed asset folders.
     */
    public function duplicate(Request $request): RedirectResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $fullPath = SecurityFileGuard::resolveAllowedPublicPath((string) $request->path, ['uploads/', 'xivig-admin/img/']);
        if (!$fullPath) {
            return back()->with('error', 'Invalid or unauthorized file path specified.');
        }

        $dir = dirname($fullPath);
        $baseName = pathinfo($fullPath, PATHINFO_FILENAME);
        $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
        $dotExt = $ext ? ('.' . $ext) : '';

        $targetPath = $dir . DIRECTORY_SEPARATOR . $baseName . '-copy' . $dotExt;
        $counter = 2;

        while (File::exists($targetPath)) {
            $targetPath = $dir . DIRECTORY_SEPARATOR . $baseName . '-copy-' . $counter++ . $dotExt;
        }

        File::copy($fullPath, $targetPath);

        return back()->with('success', 'File duplicated as "' . basename($targetPath) . '"!');
    }

    /**
     * Soft delete / move an uploaded file to Recycle Bin.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $relPath = ltrim(str_replace('\\', '/', (string) $request->path), '/');
        $fullPath = SecurityFileGuard::resolveAllowedPublicPath($relPath, ['uploads/']);
        if (!$fullPath || str_starts_with($relPath, 'uploads/trash/')) {
            return back()->with('error', 'Invalid or protected file path.');
        }

        $this->moveFileToTrash($fullPath, $relPath);

        return back()->with('success', 'File moved to Recycle Bin.');
    }

    /**
     * Restore a file from Recycle Bin back to a confined uploads folder.
     */
    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $fullPath = SecurityFileGuard::resolveAllowedPublicPath((string) $request->path, ['uploads/trash/']);
        if (!$fullPath) {
            return back()->with('error', 'Trashed file not found or unauthorized.');
        }

        $destFolder = $this->restoreFileFromTrash($fullPath);

        return redirect()->route('admin.file-manager.index', ['folder' => $destFolder])
            ->with('success', 'File restored safely to ' . $destFolder . '!');
    }

    /**
     * Force delete a file permanently from uploads or trash.
     */
    public function forceDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $fullPath = SecurityFileGuard::resolveAllowedPublicPath((string) $request->path, ['uploads/']);
        if ($fullPath) {
            File::delete($fullPath);
            return back()->with('success', 'File permanently deleted.');
        }

        return back()->with('error', 'File not found or protected.');
    }

    /**
     * Empty entire Recycle Bin.
     */
    public function emptyTrash(): RedirectResponse
    {
        $trashDir = public_path('uploads/trash');
        if (File::exists($trashDir)) {
            foreach (File::files($trashDir) as $file) {
                File::delete($file->getPathname());
            }
        }

        return redirect()->route('admin.file-manager.index', ['folder' => 'uploads/trash'])
            ->with('success', 'Recycle Bin has been completely emptied.');
    }

    /**
     * Bulk actions for uploaded files (delete, restore, force_delete).
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:delete,restore,force_delete',
            'paths' => 'required|array',
            'paths.*' => 'string',
        ]);

        $action = $request->input('action');
        $paths = $request->input('paths', []);
        $count = 0;

        foreach ($paths as $rawPath) {
            $relPath = ltrim(str_replace('\\', '/', (string) $rawPath), '/');
            $allowed = $action === 'restore' ? ['uploads/trash/'] : ['uploads/'];
            $fullPath = SecurityFileGuard::resolveAllowedPublicPath($relPath, $allowed);
            if (!$fullPath) {
                continue;
            }

            if ($action === 'delete' && !str_starts_with($relPath, 'uploads/trash/')) {
                $this->moveFileToTrash($fullPath, $relPath);
                $count++;
            } elseif ($action === 'restore') {
                $this->restoreFileFromTrash($fullPath);
                $count++;
            } elseif ($action === 'force_delete') {
                File::delete($fullPath);
                $count++;
            }
        }

        return back()->with('success', "Bulk action '{$action}' successfully executed on {$count} file(s).");
    }

    protected function moveFileToTrash(string $fullPath, string $relPath): void
    {
        $trashDir = public_path('uploads/trash');
        if (!File::exists($trashDir)) {
            File::makeDirectory($trashDir, 0755, true);
        }

        $dirSlug = str_replace(['/', '\\'], '_', dirname($relPath));
        $trashPath = $trashDir . DIRECTORY_SEPARATOR . $dirSlug . '___' . basename($fullPath);
        File::move($fullPath, $trashPath);
    }

    protected function restoreFileFromTrash(string $fullPath): string
    {
        $fileName = basename($fullPath);
        $destFolder = 'uploads/posts';
        $realName = $fileName;

        if (str_contains($fileName, '___')) {
            [$originSlug, $extractedName] = explode('___', $fileName, 2);
            $realName = basename($extractedName);
            $candidateFolder = str_replace('_', '/', $originSlug);
            $destFolder = SecurityFileGuard::resolveSafeUploadFolder($candidateFolder) ?? 'uploads/posts';
        }

        $targetDir = public_path($destFolder);
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $realName;
        if (File::exists($targetPath)) {
            $ext = pathinfo($realName, PATHINFO_EXTENSION);
            $base = pathinfo($realName, PATHINFO_FILENAME);
            $targetPath = $targetDir . DIRECTORY_SEPARATOR . $base . '_restored_' . time() . ($ext ? '.' . $ext : '');
        }

        File::move($fullPath, $targetPath);

        return $destFolder;
    }
}
