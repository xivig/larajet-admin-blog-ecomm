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
        $status = $request->input('status');

        if ($status === 'trash') {
            $query = Post::onlyTrashed()->with(['category', 'author.user', 'image', 'tag', 'comments'])->latest();
        } else {
            $query = Post::with(['category', 'author.user', 'image', 'tag', 'comments'])->latest();
            if ($status === 'published') {
                $query->where('status', true);
            } elseif ($status === 'draft') {
                $query->where('status', false);
            }
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $posts = $query->get();
        $categories = Category::all();

        $counts = [
            'all' => Post::count(),
            'published' => Post::where('status', true)->count(),
            'draft' => Post::where('status', false)->count(),
            'trash' => Post::onlyTrashed()->count(),
        ];

        return view('admin.posts.index', compact('posts', 'categories', 'counts'));
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
            'tag_id' => 'nullable',
            'image_id' => 'nullable',
            'author_id' => 'nullable',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'status' => 'nullable',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
        ]);

        // Handle image upload if provided
        $imageId = $request->filled('image_id') ? $request->input('image_id') : null;
        if ($request->hasFile('featured_image')) {
            $postsDir = public_path('uploads/posts');
            if (!file_exists($postsDir)) {
                mkdir($postsDir, 0755, true);
            }
            $file = $request->file('featured_image');
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move($postsDir, $fileName);
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
            'tag_id' => 'nullable',
            'image_id' => 'nullable',
            'author_id' => 'nullable',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'status' => 'nullable',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
        ]);

        $imageId = $request->filled('image_id') ? $request->input('image_id') : $post->image_id;
        if ($request->hasFile('featured_image')) {
            $postsDir = public_path('uploads/posts');
            if (!file_exists($postsDir)) {
                mkdir($postsDir, 0755, true);
            }
            $file = $request->file('featured_image');
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move($postsDir, $fileName);
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
     * Move the specified post to trash (Soft Delete).
     */
    public function destroy(Post $post): RedirectResponse
    {
        $title = $post->title;
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Article "' . $title . '" moved to trash successfully!');
    }

    /**
     * Restore a soft-deleted post from trash.
     */
    public function restore(int|string $id): RedirectResponse
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->restore();

        return redirect()->route('admin.posts.index', ['status' => 'trash'])->with('success', 'Article "' . $post->title . '" restored successfully!');
    }

    /**
     * Permanently remove a post from storage.
     */
    public function forceDelete(int|string $id): RedirectResponse
    {
        $post = Post::withTrashed()->findOrFail($id);
        $title = $post->title;

        // Permanently remove related comments
        $post->comments()->forceDelete();
        $post->forceDelete();

        return redirect()->route('admin.posts.index', ['status' => 'trash'])->with('success', 'Article "' . $title . '" permanently deleted.');
    }

    /**
     * Duplicate a post into a new draft.
     */
    public function duplicate(Post $post): RedirectResponse
    {
        $baseTitle = $post->title . ' (Copy)';
        $baseSlug = Str::slug($baseTitle);
        $slug = $baseSlug;
        $counter = 1;

        while (Post::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $newPost = Post::create([
            'user_id' => auth()->id() ?? $post->user_id,
            'category_id' => $post->category_id,
            'tag_id' => $post->tag_id,
            'image_id' => $post->image_id,
            'author_id' => $post->author_id,
            'title' => $baseTitle,
            'slug' => $slug,
            'content' => $post->content,
            'excerpt' => $post->excerpt,
            'status' => false, // draft by default
        ]);

        return redirect()->route('admin.posts.edit', $newPost->id)->with('success', 'Article duplicated successfully as draft! You are now editing the duplicate.');
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
