<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Category;
use App\Models\Picture;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of posts for admin.
     */
    public function index(): View
    {
        $posts = Post::with(['category', 'author', 'image', 'tag'])->latest()->paginate(15);
        return view('admin.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create(): View
    {
        $categories = Category::all();
        $tags = Tag::all();
        $authors = Author::all();
        $pictures = Picture::all();
        return view('admin.create', compact('categories', 'tags', 'authors', 'pictures'));
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'tag_id' => 'required|exists:tags,id',
            'image_id' => 'required|exists:pictures,id',
            'author_id' => 'required|exists:authors,id',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id() ?? 1;
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['excerpt'] = $validated['excerpt'] ?? Str::limit(strip_tags($validated['content']), 120);
        $validated['status'] = $request->boolean('status', true);

        Post::create($validated);

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully!');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post): View
    {
        return view('admin.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post): View
    {
        $categories = Category::all();
        $tags = Tag::all();
        $authors = Author::all();
        $pictures = Picture::all();
        return view('admin.edit', compact('post', 'categories', 'tags', 'authors', 'pictures'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'tag_id' => 'required|exists:tags,id',
            'image_id' => 'required|exists:pictures,id',
            'author_id' => 'required|exists:authors,id',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        $validated['status'] = $request->boolean('status', true);
        if ($post->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        }

        $post->update($validated);

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully!');
    }
}
