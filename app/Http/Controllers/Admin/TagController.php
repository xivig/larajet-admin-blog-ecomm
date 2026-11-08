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
     * Show the form for creating a new tag.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('admin.tags.index');
    }

    /**
     * Display the specified tag.
     */
    public function show(Tag $tag): RedirectResponse
    {
        return redirect()->route('admin.tags.index');
    }

    /**
     * Show the form for editing the specified tag.
     */
    public function edit(Tag $tag): RedirectResponse
    {
        return redirect()->route('admin.tags.index');
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

    /**
     * Bulk actions for tags.
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
            return back()->with('error', 'No tags selected for bulk action.');
        }

        if ($action === 'delete') {
            Tag::whereIn('id', $ids)->delete();
            return back()->with('success', count($ids) . ' tag(s) moved to trash!');
        } elseif ($action === 'restore') {
            Tag::onlyTrashed()->whereIn('id', $ids)->restore();
            return back()->with('success', count($ids) . ' tag(s) restored from trash!');
        } elseif ($action === 'force_delete') {
            Tag::onlyTrashed()->whereIn('id', $ids)->forceDelete();
            return back()->with('success', count($ids) . ' tag(s) permanently deleted.');
        }

        return back();
    }
}
