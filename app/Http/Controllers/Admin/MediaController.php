<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Picture;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Display a rich grid/list gallery of media files with filters and storage metrics.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = Picture::withCount('posts');

        // Search filter
        if ($request->filled('q')) {
            $searchTerm = '%' . trim($request->q) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('alt_text', 'like', $searchTerm)
                  ->orWhere('caption', 'like', $searchTerm)
                  ->orWhere('link', 'like', $searchTerm);
            });
        }

        // MIME / Type filter
        if ($request->filled('type') && $request->type !== 'all') {
            match ($request->type) {
                'image' => $query->where('mime_type', 'like', 'image/%')->where('mime_type', '!=', 'image/svg+xml'),
                'svg' => $query->where('mime_type', 'image/svg+xml'),
                'document' => $query->where(function ($q) {
                    $q->where('mime_type', 'like', '%pdf%')
                      ->orWhere('mime_type', 'like', '%document%')
                      ->orWhere('mime_type', 'like', '%text%');
                }),
                'video' => $query->where('mime_type', 'like', 'video/%'),
                'audio' => $query->where('mime_type', 'like', 'audio/%'),
                default => null,
            };
        }

        // Folder category filter
        if ($request->filled('folder') && $request->folder !== 'all') {
            $query->where('folder', $request->folder);
        }

        // Usage filter
        if ($request->filled('usage') && $request->usage !== 'all') {
            if ($request->usage === 'used') {
                $query->has('posts');
            } elseif ($request->usage === 'unused') {
                $query->doesntHave('posts');
            }
        }

        // Sorting
        $sort = $request->query('sort', 'newest');
        match ($sort) {
            'oldest' => $query->oldest(),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'size_desc' => $query->orderBy('file_size', 'desc'),
            'size_asc' => $query->orderBy('file_size', 'asc'),
            'most_used' => $query->orderBy('posts_count', 'desc'),
            default => $query->latest(),
        };

        $perPage = (int) $request->query('per_page', 24);
        $pictures = $query->paginate($perPage)->withQueryString();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $pictures,
            ]);
        }

        // Storage statistics
        $totalCount = Picture::count();
        $totalBytes = (int) Picture::sum('file_size');
        $formattedTotalSize = $this->formatBytes($totalBytes);
        $usedInPostsCount = Picture::has('posts')->count();
        $unusedCount = Picture::doesntHave('posts')->count();
        $availableFolders = Picture::select('folder')->distinct()->whereNotNull('folder')->pluck('folder');

        $activeView = $request->query('view', 'grid');

        return view('admin.media.index', compact(
            'pictures',
            'totalCount',
            'formattedTotalSize',
            'usedInPostsCount',
            'unusedCount',
            'availableFolders',
            'activeView'
        ));
    }

    /**
     * Store newly uploaded media files (single or batch).
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'media_files' => 'nullable|array',
            'media_files.*' => 'file|max:20480',
            'media_file' => 'nullable|file|max:20480',
            'name' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'folder' => 'nullable|string|max:100',
        ]);

        $files = [];
        if ($request->hasFile('media_files')) {
            $files = $request->file('media_files');
        } elseif ($request->hasFile('media_file')) {
            $files = [$request->file('media_file')];
        }

        if (empty($files)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'No files provided for upload.'], 422);
            }
            return back()->with('error', 'Please select at least one valid file to upload.');
        }

        $folder = $request->filled('folder') ? Str::slug($request->folder) : 'general';
        $subDir = 'uploads/media/' . date('Y/m');
        $targetDir = public_path($subDir);

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $createdAssets = [];

        foreach ($files as $file) {
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = strtolower($file->getClientOriginalExtension());
            $name = $request->filled('name') && count($files) === 1 ? $request->name : Str::title(str_replace(['-', '_'], ' ', $originalName));
            $altText = $request->filled('alt_text') && count($files) === 1 ? $request->alt_text : $name;

            $fileName = time() . '_' . Str::random(6) . '_' . Str::slug($originalName) . '.' . $extension;
            $file->move($targetDir, $fileName);

            $relativeLink = $subDir . '/' . $fileName;
            $fullPath = $targetDir . '/' . $fileName;

            $fileSize = file_exists($fullPath) ? filesize($fullPath) : $file->getSize();
            $mimeType = $file->getClientMimeType() ?: 'application/octet-stream';

            $dimensions = null;
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $imgSize = @getimagesize($fullPath);
                if ($imgSize) {
                    $dimensions = $imgSize[0] . 'x' . $imgSize[1];
                    if (!empty($imgSize['mime'])) {
                        $mimeType = $imgSize['mime'];
                    }
                }
            }

            $picture = Picture::create([
                'name' => $name,
                'alt_text' => $altText,
                'caption' => null,
                'description' => null,
                'link' => $relativeLink,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'dimensions' => $dimensions,
                'folder' => $folder,
            ]);

            $createdAssets[] = $picture;
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => count($createdAssets) . ' file(s) uploaded successfully!',
                'assets' => $createdAssets,
            ]);
        }

        $count = count($createdAssets);
        return redirect()->route('admin.media.index')->with('success', "{$count} media asset(s) uploaded successfully!");
    }

    /**
     * Inspect single media asset with relationships (JSON API for Inspector Modal).
     */
    public function show(Picture $picture): JsonResponse
    {
        $picture->load(['posts' => function ($q) {
            $q->select('id', 'title', 'slug', 'image_id', 'created_at');
        }]);

        return response()->json([
            'success' => true,
            'asset' => [
                'id' => $picture->id,
                'name' => $picture->name,
                'alt_text' => $picture->alt_text,
                'caption' => $picture->caption,
                'description' => $picture->description,
                'link' => $picture->link,
                'url' => $picture->url,
                'mime_type' => $picture->mime_type,
                'file_size' => $picture->file_size,
                'formatted_size' => $picture->formatted_size,
                'dimensions' => $picture->dimensions,
                'folder' => $picture->folder,
                'created_at' => $picture->created_at ? $picture->created_at->format('M d, Y h:i A') : 'N/A',
                'posts_count' => $picture->posts->count(),
                'posts' => $picture->posts,
                'is_image' => $picture->is_image,
            ],
        ]);
    }

    /**
     * Update asset metadata (Title, Alt Text, Caption, Description, Folder).
     */
    public function update(Request $request, Picture $picture): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:2000',
            'folder' => 'nullable|string|max:100',
        ]);

        $picture->update($validated);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Media metadata updated successfully!',
                'asset' => $picture,
            ]);
        }

        return redirect()->route('admin.media.index')->with('success', 'Media asset updated successfully!');
    }

    /**
     * Delete a single media item and its physical file.
     */
    public function destroy(Request $request, Picture $picture): RedirectResponse|JsonResponse
    {
        $name = $picture->name;

        // Safely unlink physical file if within uploads/
        if (Str::startsWith($picture->link, 'uploads/') && file_exists(public_path($picture->link))) {
            @unlink(public_path($picture->link));
        }

        $picture->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Media asset '{$name}' deleted successfully!",
            ]);
        }

        return redirect()->route('admin.media.index')->with('success', "Media asset '{$name}' deleted successfully!");
    }

    /**
     * Handle bulk actions (bulk delete, bulk move folder).
     */
    public function bulkAction(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'action' => 'required|string|in:delete,move',
            'ids' => 'required|array',
            'ids.*' => 'exists:pictures,id',
            'target_folder' => 'nullable|string|max:100',
        ]);

        $pictures = Picture::whereIn('id', $request->ids)->get();
        $count = $pictures->count();

        if ($request->action === 'delete') {
            foreach ($pictures as $pic) {
                if (Str::startsWith($pic->link, 'uploads/') && file_exists(public_path($pic->link))) {
                    @unlink(public_path($pic->link));
                }
                $pic->delete();
            }
            $msg = "{$count} media asset(s) deleted permanently.";
        } elseif ($request->action === 'move') {
            $folder = $request->filled('target_folder') ? Str::slug($request->target_folder) : 'general';
            Picture::whereIn('id', $request->ids)->update(['folder' => $folder]);
            $msg = "{$count} media asset(s) moved to '{$folder}' folder.";
        } else {
            $msg = 'Action processed.';
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.media.index')->with('success', $msg);
    }

    /**
     * Media picker API for modal in post creation/editing.
     */
    public function pickerApi(Request $request): JsonResponse
    {
        $query = Picture::latest();

        if ($request->filled('q')) {
            $term = '%' . trim($request->q) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('alt_text', 'like', $term);
            });
        }

        $pictures = $query->paginate(20);

        return response()->json([
            'success' => true,
            'items' => $pictures->items(),
            'pagination' => [
                'current_page' => $pictures->currentPage(),
                'last_page' => $pictures->lastPage(),
                'total' => $pictures->total(),
            ],
        ]);
    }

    /**
     * Helper to format bytes to human-readable size.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 MB';
        }
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }
}
