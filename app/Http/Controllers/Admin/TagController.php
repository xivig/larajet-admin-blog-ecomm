<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of tags with their post counts.
     */
    public function index(): View
    {
        $tags = Tag::withCount('posts')->latest()->get();
        return view('admin.tags.index', compact('tags'));
    }

    /**
     * Store a newly created tag.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:tags,name',
        ]);

        Tag::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.tags.index')->with('success', 'Tag "#' . $request->name . '" created successfully!');
    }

    /**
     * Update the specified tag in storage.
     */
    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:tags,name,' . $tag->id,
        ]);

        $tag->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.tags.index')->with('success', 'Tag updated successfully!');
    }

    /**
     * Remove the specified tag from storage (Soft Delete).
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $name = $tag->name;
        $tag->delete();

        return redirect()->route('admin.tags.index')->with('success', 'Tag "#' . $name . '" moved to trash.');
    }

    /**
     * Restore a soft-deleted tag from trash.
     */
    public function restore(int|string $id): RedirectResponse
    {
        $tag = Tag::onlyTrashed()->findOrFail($id);
        $tag->restore();

        return redirect()->route('admin.tags.index')->with('success', 'Tag "#' . $tag->name . '" restored successfully.');
    }

    /**
     * Permanently remove a tag from storage.
     */
    public function forceDelete(int|string $id): RedirectResponse
    {
        $tag = Tag::withTrashed()->findOrFail($id);
        $name = $tag->name;
        $tag->forceDelete();

        return redirect()->route('admin.tags.index')->with('success', 'Tag "#' . $name . '" permanently deleted.');
    }
}
