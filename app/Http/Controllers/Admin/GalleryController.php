<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Models\Picture;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    /**
     * Display a listing of galleries with active & trash tabs.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'active');

        if ($status === 'trash') {
            $galleries = Gallery::onlyTrashed()->withCount('items')->latest('deleted_at')->get();
        } else {
            $galleries = Gallery::withCount('items')->latest()->get();
        }

        $counts = [
            'active' => Gallery::count(),
            'trash' => Gallery::onlyTrashed()->count(),
        ];

        return view('admin.galleries.index', compact('galleries', 'status', 'counts'));
    }

    /**
     * Show the form for creating a new gallery (modal on index).
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('admin.galleries.index');
    }

    /**
     * Show the form for editing the specified gallery.
     */
    public function edit(Gallery $gallery): View|RedirectResponse
    {
        return redirect()->route('admin.galleries.show', $gallery);
    }

    /**
     * Store a newly created gallery.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:galleries,slug',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $slug = $request->filled('slug')
            ? Str::slug($request->slug)
            : Str::slug($request->title) . '-' . Str::random(5);

        // Ensure unique slug
        $baseSlug = $slug;
        $counter = 1;
        while (Gallery::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $fileName = 'gallery_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $dir = public_path('uploads/galleries');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $file->move($dir, $fileName);
            $coverImagePath = 'uploads/galleries/' . $fileName;
        }

        $gallery = Gallery::create([
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'cover_image' => $coverImagePath,
            'status' => $request->input('status', 'active'),
        ]);

        return redirect()->route('admin.galleries.index')->with('success', 'Gallery "' . $gallery->title . '" created successfully!');
    }

    /**
     * Display photos and asset manager for the specified gallery.
     */
    public function show(Gallery $gallery): View
    {
        $gallery->load('items');
        $availablePictures = Picture::latest()->take(30)->get();

        return view('admin.galleries.show', compact('gallery', 'availablePictures'));
    }

    /**
     * Update the specified gallery.
     */
    public function update(Request $request, Gallery $gallery): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:galleries,slug,' . $gallery->id,
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'status' => 'required|string|in:active,inactive',
        ]);

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->slug),
            'description' => $request->description,
            'status' => $request->status,
        ];

        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $fileName = 'gallery_' . time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $dir = public_path('uploads/galleries');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $file->move($dir, $fileName);
            $data['cover_image'] = 'uploads/galleries/' . $fileName;
        }

        $gallery->update($data);

        return redirect()->route('admin.galleries.index')->with('success', 'Gallery "' . $gallery->title . '" updated successfully!');
    }

    /**
     * Soft delete the specified gallery to Trash.
     */
    public function destroy(Gallery $gallery): RedirectResponse
    {
        $title = $gallery->title;
        $gallery->delete();

        return redirect()->route('admin.galleries.index')->with('success', 'Gallery "' . $title . '" moved to trash.');
    }

    /**
     * Restore a trashed gallery.
     */
    public function restore($id): RedirectResponse
    {
        $gallery = Gallery::onlyTrashed()->findOrFail($id);
        $gallery->restore();

        return redirect()->route('admin.galleries.index', ['status' => 'trash'])->with('success', 'Gallery "' . $gallery->title . '" restored successfully.');
    }

    /**
     * Permanently purge a gallery and its photos from database.
     */
    public function forceDelete($id): RedirectResponse
    {
        $gallery = Gallery::withTrashed()->findOrFail($id);
        $gallery->items()->delete();
        $gallery->forceDelete();

        return redirect()->route('admin.galleries.index', ['status' => 'trash'])->with('success', 'Gallery permanently deleted.');
    }

    /**
     * Upload photos directly into gallery.
     */
    public function uploadPhotos(Request $request, Gallery $gallery): JsonResponse|RedirectResponse
    {
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp,gif|max:10240',
        ]);

        $dir = public_path('uploads/galleries/' . $gallery->id);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $uploadedCount = 0;
        foreach ($request->file('photos') as $file) {
            $fileName = 'asset_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $path = 'uploads/galleries/' . $gallery->id . '/' . $fileName;

            GalleryItem::create([
                'gallery_id' => $gallery->id,
                'image_path' => $path,
                'caption' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'order' => $gallery->items()->count() + 1,
            ]);

            // Set as cover if none exists
            if (!$gallery->cover_image) {
                $gallery->update(['cover_image' => $path]);
            }

            $uploadedCount++;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Successfully uploaded {$uploadedCount} photo(s).",
            ]);
        }

        return redirect()->route('admin.galleries.show', $gallery->id)->with('success', "Added {$uploadedCount} photo(s) to gallery.");
    }

    /**
     * Attach existing media items to this gallery.
     */
    public function attachMedia(Request $request, Gallery $gallery): RedirectResponse
    {
        $request->validate([
            'picture_ids' => 'required|array',
            'picture_ids.*' => 'exists:pictures,id',
        ]);

        $pictures = Picture::whereIn('id', $request->picture_ids)->get();
        foreach ($pictures as $pic) {
            GalleryItem::create([
                'gallery_id' => $gallery->id,
                'image_path' => $pic->link,
                'caption' => $pic->name,
                'order' => $gallery->items()->count() + 1,
            ]);
            if (!$gallery->cover_image) {
                $gallery->update(['cover_image' => $pic->link]);
            }
        }

        return redirect()->route('admin.galleries.show', $gallery->id)->with('success', 'Selected media assets attached to gallery.');
    }

    /**
     * Delete an individual photo from gallery.
     */
    public function deletePhoto(GalleryItem $item): RedirectResponse
    {
        $galleryId = $item->gallery_id;
        $gallery = $item->gallery;

        // If this item was cover, reset cover
        if ($gallery && $gallery->cover_image === $item->image_path) {
            $nextItem = $gallery->items()->where('id', '!=', $item->id)->first();
            $gallery->update(['cover_image' => $nextItem ? $nextItem->image_path : null]);
        }

        $item->delete();

        return redirect()->route('admin.galleries.show', $galleryId)->with('success', 'Photo removed from gallery.');
    }

    /**
     * Set a photo as gallery cover.
     */
    public function setCover(Gallery $gallery, GalleryItem $item): RedirectResponse
    {
        $gallery->update(['cover_image' => $item->image_path]);

        return redirect()->route('admin.galleries.show', $gallery->id)->with('success', 'Gallery cover updated.');
    }

    /**
     * Bulk actions for galleries.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:delete,restore,force_delete',
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', 'No galleries selected for bulk action.');
        }

        if ($action === 'delete') {
            Gallery::whereIn('id', $ids)->delete();
            return back()->with('success', count($ids) . ' gallery(s) moved to trash!');
        } elseif ($action === 'restore') {
            Gallery::onlyTrashed()->whereIn('id', $ids)->restore();
            return back()->with('success', count($ids) . ' gallery(s) restored from trash!');
        } elseif ($action === 'force_delete') {
            foreach ($ids as $id) {
                $g = Gallery::onlyTrashed()->find($id);
                if ($g) {
                    $g->items()->delete();
                    $g->forceDelete();
                }
            }
            return back()->with('success', count($ids) . ' gallery(s) permanently deleted.');
        }

        return back();
    }
}
