<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Picture;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Display a grid/gallery of media files.
     */
    public function index(): View
    {
        $pictures = Picture::withCount('posts')->latest()->paginate(24);
        return view('admin.media.index', compact('pictures'));
    }

    /**
     * Store newly uploaded media images.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'media_file' => 'required|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
            'name' => 'nullable|string|max:255',
        ]);

        $file = $request->file('media_file');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $name = $request->filled('name') ? $request->name : $originalName;
        $fileName = time() . '_' . Str::slug($originalName) . '.' . $file->getClientOriginalExtension();

        $file->move(public_path('uploads/media'), $fileName);

        Picture::create([
            'name' => $name,
            'link' => 'uploads/media/' . $fileName,
        ]);

        return redirect()->route('admin.media.index')->with('success', 'Media asset "' . $name . '" uploaded successfully!');
    }

    /**
     * Delete media item.
     */
    public function destroy(Picture $picture): RedirectResponse
    {
        $name = $picture->name;
        // Optionally delete physical file if within uploads/
        if (Str::startsWith($picture->link, 'uploads/') && file_exists(public_path($picture->link))) {
            @unlink(public_path($picture->link));
        }

        $picture->delete();

        return redirect()->route('admin.media.index')->with('success', 'Media asset "' . $name . '" deleted successfully!');
    }
}
