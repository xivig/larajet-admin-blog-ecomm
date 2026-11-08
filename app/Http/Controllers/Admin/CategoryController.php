<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories with their post counts.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status');

        if ($status === 'trash') {
            $categories = Category::onlyTrashed()->withCount('posts')->latest()->get();
        } else {
            $categories = Category::withCount('posts')->latest()->get();
        }

        $counts = [
            'active' => Category::count(),
            'trash' => Category::onlyTrashed()->count(),
        ];

        return view('admin.categories.index', compact('categories', 'counts'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable',
        ]);

        $status = $request->has('status');

        Category::create([
            'name' => $request->name,
            'description' => $request->description ?? '',
            'status' => $status,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category "' . $request->name . '" created successfully!');
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description ?? '',
            'status' => $request->has('status'),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    /**
     * Move the category to trash (Soft Delete).
     */
    public function destroy(Category $category): RedirectResponse
    {
        $name = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category "' . $name . '" moved to trash successfully!');
    }

    /**
     * Restore a soft-deleted category from trash.
     */
    public function restore(int|string $id): RedirectResponse
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();

        return redirect()->route('admin.categories.index', ['status' => 'trash'])->with('success', 'Category "' . $category->name . '" restored successfully!');
    }

    /**
     * Permanently remove a category from storage.
     */
    public function forceDelete(int|string $id): RedirectResponse
    {
        $category = Category::withTrashed()->findOrFail($id);

        if ($category->posts()->count() > 0) {
            $fallbackCategory = Category::where('id', '!=', $category->id)->first();
            if ($fallbackCategory) {
                Post::withTrashed()->where('category_id', $category->id)->update([
                    'category_id' => $fallbackCategory->id,
                ]);
            }
        }

        $name = $category->name;
        $category->forceDelete();

        return redirect()->route('admin.categories.index', ['status' => 'trash'])->with('success', 'Category "' . $name . '" permanently deleted.');
    }

    /**
     * Duplicate a category.
     */
    public function duplicate(Category $category): RedirectResponse
    {
        $baseName = $category->name . ' (Copy)';
        $name = $baseName;
        $i = 2;

        while (Category::withTrashed()->where('name', $name)->exists()) {
            $name = $category->name . ' (Copy ' . $i++ . ')';
        }

        Category::create([
            'name' => $name,
            'description' => $category->description,
            'status' => $category->status,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category "' . $category->name . '" duplicated successfully as "' . $name . '"!');
    }

    /**
     * Bulk actions for categories.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete,restore,force_delete',
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', 'No categories selected for bulk action.');
        }

        if ($action === 'activate') {
            Category::whereIn('id', $ids)->update(['status' => true]);
            return back()->with('success', count($ids) . ' category(s) activated successfully!');
        } elseif ($action === 'deactivate') {
            Category::whereIn('id', $ids)->update(['status' => false]);
            return back()->with('success', count($ids) . ' category(s) deactivated successfully!');
        } elseif ($action === 'delete') {
            Category::whereIn('id', $ids)->delete();
            return back()->with('success', count($ids) . ' category(s) moved to trash!');
        } elseif ($action === 'restore') {
            Category::onlyTrashed()->whereIn('id', $ids)->restore();
            return back()->with('success', count($ids) . ' category(s) restored from trash!');
        } elseif ($action === 'force_delete') {
            $fallback = Category::whereNotIn('id', $ids)->first();
            foreach ($ids as $id) {
                $c = Category::onlyTrashed()->find($id);
                if ($c) {
                    if ($fallback) {
                        Post::withTrashed()->where('category_id', $c->id)->update(['category_id' => $fallback->id]);
                    }
                    $c->forceDelete();
                }
            }
            return back()->with('success', count($ids) . ' category(s) permanently deleted.');
        }

        return back();
    }
}
