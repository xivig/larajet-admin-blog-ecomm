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
        return [
            'uploads/posts' => 'Post Images',
            'uploads/avatars' => 'User Avatars',
            'uploads/chat' => 'Chat Attachments',
            'admin-dist/img' => 'Template Visuals',
            'uploads/trash' => 'Recycle Bin / Trash',
        ];
    }

    /**
     * Display the file manager.
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
                'extension' => $ext,
                'is_image' => $isImg,
                'modified' => date('M d, Y h:i A', $file->getMTime()),
            ];
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
            'trashCount'
        ));
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
        $allowedFolders = ['uploads/posts', 'uploads/avatars', 'uploads/chat', 'admin-dist/img'];
        if (!in_array($folder, $allowedFolders)) {
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

        return back()->with('success', 'File duplicated successfully as "' . basename($targetPath) . '"!');
    }

    /**
     * Soft delete a file (Move to Recycle Bin) or permanently delete if already in trash.
     */
    public function destroy(Request $request): RedirectResponse
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
            return back()->with('error', 'File not found or already deleted.');
        }

        $fileName = basename($fullPath);
        $dirName = dirname($relPath);

        // If already in trash, permanently delete
        if ($dirName === 'uploads/trash') {
            File::delete($fullPath);
            return back()->with('success', 'File permanently deleted from Recycle Bin.');
        }

        // Move to uploads/trash with origin folder encoded
        $trashDir = public_path('uploads/trash');
        if (!File::exists($trashDir)) {
            File::makeDirectory($trashDir, 0755, true);
        }

        // Prefix with origin folder identifier: e.g. "origin_uploads_posts___filename.ext"
        $originSlug = str_replace(['/', '\\'], '_', $dirName);
        $trashFileName = $originSlug . '___' . $fileName;
        $trashPath = $trashDir . DIRECTORY_SEPARATOR . $trashFileName;

        // If file with same trash name exists, prepend timestamp
        if (File::exists($trashPath)) {
            $trashFileName = $originSlug . '___' . time() . '_' . $fileName;
            $trashPath = $trashDir . DIRECTORY_SEPARATOR . $trashFileName;
        }

        File::move($fullPath, $trashPath);

        return back()->with('success', 'File "' . $fileName . '" moved to Recycle Bin / Trash.');
    }

    /**
     * Restore a file from Recycle Bin back to its original directory.
     */
    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $relPath = $request->path;
        if (str_contains($relPath, '..')) {
            return back()->with('error', 'Invalid file path.');
        }

        $fullPath = public_path($relPath);
        if (!File::exists($fullPath) || !is_file($fullPath)) {
            return back()->with('error', 'Trashed file not found.');
        }

        $fileName = basename($fullPath);

        // Extract original folder from prefix: "origin_uploads_posts___filename.ext"
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
}
