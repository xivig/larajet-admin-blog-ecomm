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
    public function index(Request $request): View
    {
        $query = Post::with(['category', 'author.user', 'image', 'tag', 'comments'])->latest();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'published');
        }

        $posts = $query->get();
        $categories = Category::all();

        return view('admin.posts.index', compact('posts', 'categories'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create(): View
    {
        $categories = Category::all();
        $tags = Tag::all();
        $authors = Author::with('user')->get();
        $pictures = Picture::latest()->take(24)->get();

        return view('admin.posts.create', compact('categories', 'tags', 'authors', 'pictures'));
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'tag_id' => 'nullable|exists:tags,id',
            'image_id' => 'nullable|exists:pictures,id',
            'author_id' => 'nullable|exists:authors,id',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'status' => 'nullable',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        // Handle image upload if provided
        $imageId = $request->input('image_id');
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/posts'), $fileName);
            $picture = Picture::create([
                'name' => $request->title,
                'link' => 'uploads/posts/' . $fileName,
            ]);
            $imageId = $picture->id;
        }

        // Fallback image if none selected
        if (!$imageId) {
            $defaultPic = Picture::first();
            $imageId = $defaultPic ? $defaultPic->id : 1;
        }

        // Author assignment
        $authorId = $request->input('author_id');
        if (!$authorId) {
            $firstAuthor = Author::first();
            $authorId = $firstAuthor ? $firstAuthor->id : 1;
        }

        // Tag fallback
        $tagId = $request->input('tag_id');
        if (!$tagId) {
            $firstTag = Tag::first();
            $tagId = $firstTag ? $firstTag->id : 1;
        }

        // Unique slug
        $baseSlug = Str::slug($request->title);
        $slug = $baseSlug;
        $counter = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $excerpt = $request->filled('excerpt')
            ? $request->excerpt
            : Str::limit(strip_tags($request->content), 150);

        $status = $request->has('status') && ($request->status == '1' || $request->status == 'published' || $request->status == 'on');

        Post::create([
            'user_id' => auth()->id() ?? 1,
            'category_id' => $request->category_id,
            'tag_id' => $tagId,
            'image_id' => $imageId,
            'author_id' => $authorId,
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'excerpt' => $excerpt,
            'status' => $status,
        ]);

        return redirect()->route('admin.posts.index')->with('success', 'Article "' . $request->title . '" published successfully!');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post): View
    {
        $post->load(['category', 'author.user', 'image', 'tag', 'comments.user']);
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post): View
    {
        $categories = Category::all();
        $tags = Tag::all();
        $authors = Author::with('user')->get();
        $pictures = Picture::latest()->take(24)->get();

        return view('admin.posts.edit', compact('post', 'categories', 'tags', 'authors', 'pictures'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, Post $post): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'tag_id' => 'nullable|exists:tags,id',
            'image_id' => 'nullable|exists:pictures,id',
            'author_id' => 'nullable|exists:authors,id',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'status' => 'nullable',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $imageId = $request->input('image_id', $post->image_id);
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/posts'), $fileName);
            $picture = Picture::create([
                'name' => $request->title,
                'link' => 'uploads/posts/' . $fileName,
            ]);
            $imageId = $picture->id;
        }

        // Slug update if title changed
        $slug = $post->slug;
        if ($post->title !== $request->title) {
            $baseSlug = Str::slug($request->title);
            $slug = $baseSlug;
            $counter = 1;
            while (Post::where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
        }

        $excerpt = $request->filled('excerpt')
            ? $request->excerpt
            : Str::limit(strip_tags($request->content), 150);

        $status = $request->has('status') && ($request->status == '1' || $request->status == 'published' || $request->status == 'on');

        $post->update([
            'category_id' => $request->category_id,
            'tag_id' => $request->tag_id ?? $post->tag_id,
            'image_id' => $imageId,
            'author_id' => $request->author_id ?? $post->author_id,
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'excerpt' => $excerpt,
            'status' => $status,
        ]);

        return redirect()->route('admin.posts.index')->with('success', 'Article updated successfully!');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $title = $post->title;
        $post->comments()->delete();
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Article "' . $title . '" deleted successfully!');
    }

    /**
     * Quick Draft submission from Dashboard.
     */
    public function quickDraft(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $baseSlug = Str::slug($request->title);
        $slug = $baseSlug . '-draft-' . Str::random(4);

        $firstCat = Category::first();
        $firstTag = Tag::first();
        $firstPic = Picture::first();
        $firstAuthor = Author::first();

        Post::create([
            'user_id' => auth()->id() ?? 1,
            'category_id' => $firstCat ? $firstCat->id : 1,
            'tag_id' => $firstTag ? $firstTag->id : 1,
            'image_id' => $firstPic ? $firstPic->id : 1,
            'author_id' => $firstAuthor ? $firstAuthor->id : 1,
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'excerpt' => Str::limit(strip_tags($request->content), 120),
            'status' => false, // draft
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Quick draft saved successfully!');
    }
}
