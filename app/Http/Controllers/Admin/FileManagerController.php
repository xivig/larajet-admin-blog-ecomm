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
     * Display the file manager.
     */
    public function index(Request $request): View
    {
        $allowedFolders = [
            'uploads/posts' => 'Post Images',
            'uploads/avatars' => 'User Avatars',
            'uploads/chat' => 'Chat Attachments',
            'admin-dist/img' => 'Template Visuals',
        ];

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

        // Stats across all upload directories
        $totalFiles = count($files);
        $totalBytes = array_sum(array_column($files, 'raw_size'));
        $totalSizeFormatted = round($totalBytes / (1024 * 1024), 2) . ' MB';

        return view('admin.file-manager.index', compact('files', 'allowedFolders', 'currentFolder', 'totalFiles', 'totalSizeFormatted'));
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
     * Delete a file.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $relPath = $request->path;
        // Security check against directory traversal
        if (str_contains($relPath, '..') || str_starts_with($relPath, '/') || str_starts_with($relPath, '\\')) {
            return back()->with('error', 'Invalid file path specified.');
        }

        $fullPath = public_path($relPath);
        if (File::exists($fullPath) && is_file($fullPath)) {
            File::delete($fullPath);
            return back()->with('success', 'File deleted successfully.');
        }

        return back()->with('error', 'File not found or already deleted.');
    }
}
