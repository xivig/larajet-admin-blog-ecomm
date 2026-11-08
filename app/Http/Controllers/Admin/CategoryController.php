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
    public function index(): View
    {
        $categories = Category::withCount('posts')->latest()->get();
        return view('admin.categories.index', compact('categories'));
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
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->posts()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Cannot delete category that still contains published posts. Reassign them first.');
        }

        $name = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category "' . $name . '" deleted successfully!');
    }
}
