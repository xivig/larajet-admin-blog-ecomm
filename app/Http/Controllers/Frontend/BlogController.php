<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Picture;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of blog posts.
     */
    public function index(Request $request): View
    {
        $query = Post::query()
            ->with(['category', 'author.user', 'image', 'user', 'tag', 'comments']);

        // Default to published articles for public readers
        if (!auth()->check() || !auth()->user()->hasRole(['super-admin', 'admin', 'editor'])) {
            $query->published();
        }

        // Filter: Category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category)
                  ->orWhere('id', $request->category);
            });
        }

        // Filter: Tag
        if ($request->filled('tag')) {
            $query->whereHas('tag', function ($q) use ($request) {
                $q->where('name', $request->tag)
                  ->orWhere('id', $request->tag);
            });
        }

        // Filter: Keyword search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Sticky posts pinned at the very top, followed by published date
        $query->orderByDesc('is_sticky')
              ->orderByDesc('published_at')
              ->latest('id');

        $isBlogList = $request->routeIs('blog.list') || $request->is('blog-list*');
        $columns = $isBlogList ? 3 : 4;
        $perPage = $isBlogList ? 9 : 12;

        $posts = $query->paginate($perPage)->withQueryString();
        $images = Picture::all();
        $categories = Category::withCount('posts')->get();
        $tags = Tag::withCount('posts')->orderByDesc('posts_count')->take(25)->get();

        return view('blog.blog-list', compact('posts', 'images', 'categories', 'tags', 'columns', 'isBlogList'));
    }

    /**
     * Display a single blog post.
     */
    public function singlePost(Request $request, string $slug): View
    {
        $post = Post::where('slug', $slug)
            ->with(['category', 'author.user', 'image', 'user', 'tag', 'images', 'comments.user', 'comments.replies.user'])
            ->firstOrFail();

        $isPreview = $request->boolean('preview');
        $user = auth()->user();

        // If not published and not authorized previewer -> abort 404
        $isPublished = ($post->post_status === 'published' || $post->status) && (!$post->published_at || $post->published_at <= now());
        if (!$isPublished && !$isPreview && (!$user || !$user->hasRole(['super-admin', 'admin', 'editor', 'author']))) {
            abort(404, 'Article is not available for public viewing.');
        }

        // Track view counter
        if ($isPublished && !$isPreview) {
            $post->increment('views_count');
        }

        // Check if article is locked by Password Protection or Private visibility
        $isLocked = $post->isLockedForUser($user);

        // Precompute rich features
        $tableOfContents = $post->table_of_contents;
        $contentWithAnchors = $post->content_with_anchors;
        $relatedPosts = $post->getRelatedPosts(3);
        $previousPost = $post->previousPost();
        $nextPost = $post->nextPost();
        $galleryImages = $post->images;

        $recentPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(5)
            ->get();

        $categories = Category::withCount('posts')->get();
        $tags = Tag::all();
        $images = Picture::all();

        return view('blog.blog-post', compact(
            'post',
            'isLocked',
            'isPreview',
            'tableOfContents',
            'contentWithAnchors',
            'relatedPosts',
            'previousPost',
            'nextPost',
            'galleryImages',
            'recentPosts',
            'categories',
            'tags',
            'images'
        ));
    }

    /**
     * Unlock a password-protected article.
     */
    public function unlock(Request $request, string $slug): RedirectResponse
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $post = Post::where('slug', $slug)->firstOrFail();

        if ($post->verifyPassword($request->password)) {
            return redirect()->route('single-post', $slug)->with('success', 'Article unlocked successfully!');
        }

        return redirect()->route('single-post', $slug)->with('error', 'Incorrect password. Access denied.');
    }
}
