<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            'admin-dist/img' => 'Template Visuals',
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
        if (empty($folderSlug)) {
            return back()->with('error', 'Invalid folder name specified.');
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
     * Upload a new file.
     */
    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20 MB max
            'folder' => 'required|string',
        ]);

        $folder = $request->folder;
        $allowedFolders = array_keys($this->getAllowedFolders());
        if (!in_array($folder, $allowedFolders) || $folder === 'uploads/trash') {
            $folder = 'uploads/posts';
        }

        $destDir = public_path($folder);
        if (!File::exists($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $file = $request->file('file');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();
        $safeName = time() . '_' . Str::slug($originalName) . '.' . $ext;

        $file->move($destDir, $safeName);

        return redirect()->route('admin.file-manager.index', ['folder' => $folder])
            ->with('success', 'File "' . $safeName . '" uploaded successfully!');
    }

    /**
     * Duplicate an existing file.
     */
    public function duplicate(Request $request): RedirectResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $relPath = $request->path;
        if (str_contains($relPath, '..') || str_starts_with($relPath, '/') || str_starts_with($relPath, '\\')) {
            return back()->with('error', 'Invalid file path specified.');
        }

        $fullPath = public_path($relPath);
        if (!File::exists($fullPath) || !is_file($fullPath)) {
            return back()->with('error', 'File not found to duplicate.');
        }

        $dir = dirname($fullPath);
        $baseName = pathinfo($fullPath, PATHINFO_FILENAME);
        $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
        $dotExt = $ext ? ('.' . $ext) : '';

        $copyName = $baseName . '-copy' . $dotExt;
        $targetPath = $dir . DIRECTORY_SEPARATOR . $copyName;
        $counter = 2;

        while (File::exists($targetPath)) {
            $targetPath = $dir . DIRECTORY_SEPARATOR . $baseName . '-copy-' . $counter++ . $dotExt;
        }

        File::copy($fullPath, $targetPath);

        return back()->with('success', 'File duplicated as "' . basename($targetPath) . '"!');
    }

    /**
     * Soft delete / move a file to Recycle Bin.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $relPath = $request->path;
        if (str_contains($relPath, '..') || str_starts_with($relPath, '/') || str_starts_with($relPath, '\\')) {
            return back()->with('error', 'Invalid file path.');
        }

        $fullPath = public_path($relPath);
        if (!File::exists($fullPath) || !is_file($fullPath)) {
            return back()->with('error', 'File does not exist.');
        }

        $trashDir = public_path('uploads/trash');
        if (!File::exists($trashDir)) {
            File::makeDirectory($trashDir, 0755, true);
        }

        $dirSlug = str_replace(['/', '\\'], '_', dirname($relPath));
        $trashName = $dirSlug . '___' . basename($fullPath);
        $trashPath = $trashDir . DIRECTORY_SEPARATOR . $trashName;

        File::move($fullPath, $trashPath);

        return back()->with('success', 'File moved to Recycle Bin.');
    }

    /**
     * Restore a file from Recycle Bin.
     */
    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $relPath = $request->path;
        if (!str_starts_with($relPath, 'uploads/trash/')) {
            return back()->with('error', 'Only trashed files can be restored.');
        }

        $fullPath = public_path($relPath);
        if (!File::exists($fullPath) || !is_file($fullPath)) {
            return back()->with('error', 'Trashed file not found.');
        }

        $fileName = basename($fullPath);

        $destFolder = 'uploads/posts';
        $realName = $fileName;

        if (str_contains($fileName, '___')) {
            [$originSlug, $extractedName] = explode('___', $fileName, 2);
            $realName = $extractedName;
            $destFolder = str_replace('_', '/', $originSlug);
        }

        $targetDir = public_path($destFolder);
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $realName;
        if (File::exists($targetPath)) {
            $ext = pathinfo($realName, PATHINFO_EXTENSION);
            $base = pathinfo($realName, PATHINFO_FILENAME);
            $targetPath = $targetDir . DIRECTORY_SEPARATOR . $base . '_restored_' . time() . '.' . $ext;
        }

        File::move($fullPath, $targetPath);

        return redirect()->route('admin.file-manager.index', ['folder' => $destFolder])
            ->with('success', 'File "' . basename($targetPath) . '" restored to ' . $destFolder . '!');
    }

    /**
     * Force delete a file permanently from trash.
     */
    public function forceDelete(Request $request): RedirectResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $relPath = $request->path;
        if (str_contains($relPath, '..')) {
            return back()->with('error', 'Invalid file path.');
        }

        $fullPath = public_path($relPath);
        if (File::exists($fullPath) && is_file($fullPath)) {
            File::delete($fullPath);
            return back()->with('success', 'File permanently deleted.');
        }

        return back()->with('error', 'File not found.');
    }

    /**
     * Empty entire Recycle Bin.
     */
    public function emptyTrash(): RedirectResponse
    {
        $trashDir = public_path('uploads/trash');
        if (File::exists($trashDir)) {
            $files = File::files($trashDir);
            foreach ($files as $file) {
                File::delete($file->getPathname());
            }
        }

        return redirect()->route('admin.file-manager.index', ['folder' => 'uploads/trash'])
            ->with('success', 'Recycle Bin has been completely emptied.');
    }

    /**
     * Bulk actions for file manager files (delete, restore, force_delete).
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

        $trashDir = public_path('uploads/trash');
        if (!File::exists($trashDir)) {
            File::makeDirectory($trashDir, 0755, true);
        }

        foreach ($paths as $relPath) {
            if (str_contains($relPath, '..')) continue;
            $fullPath = public_path($relPath);
            if (!File::exists($fullPath) || !is_file($fullPath)) continue;

            if ($action === 'delete') {
                $dirSlug = str_replace(['/', '\\'], '_', dirname($relPath));
                $trashName = $dirSlug . '___' . basename($fullPath);
                $dest = $trashDir . DIRECTORY_SEPARATOR . $trashName;
                File::move($fullPath, $dest);
                $count++;
            } elseif ($action === 'restore') {
                $fileName = basename($fullPath);
                $destFolder = 'uploads/posts';
                $realName = $fileName;
                if (str_contains($fileName, '___')) {
                    [$originSlug, $extractedName] = explode('___', $fileName, 2);
                    $realName = $extractedName;
                    $destFolder = str_replace('_', '/', $originSlug);
                }
                $targetDir = public_path($destFolder);
                if (!File::exists($targetDir)) {
                    File::makeDirectory($targetDir, 0755, true);
                }
                $targetPath = $targetDir . DIRECTORY_SEPARATOR . $realName;
                if (File::exists($targetPath)) {
                    $ext = pathinfo($realName, PATHINFO_EXTENSION);
                    $base = pathinfo($realName, PATHINFO_FILENAME);
                    $targetPath = $targetDir . DIRECTORY_SEPARATOR . $base . '_restored_' . time() . '.' . $ext;
                }
                File::move($fullPath, $targetPath);
                $count++;
            } elseif ($action === 'force_delete') {
                File::delete($fullPath);
                $count++;
            }
        }

        return back()->with('success', "Bulk action '{$action}' successfully executed on {$count} file(s).");
    }
}
