<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Picture;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $columns = $request->has('cols') ? (int) $request->cols : 4;
        $perPage = $columns === 3 ? 9 : 12;

        $posts = $query->paginate($perPage)->withQueryString();
        $images = Picture::all();
        $categories = Category::withCount('posts')->get();
        $tags = Tag::withCount('posts')->orderByDesc('posts_count')->take(25)->get();
        $totalPostsCount = Post::published()->count();

        return view('blog.blog-list', compact('posts', 'images', 'categories', 'tags', 'columns', 'isBlogList', 'totalPostsCount'));
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

    /**
     * Continuous technical feed stream.
     */
    public function feed(Request $request): View
    {
        $featuredPosts = Post::published()
            ->where('is_featured', 1)
            ->with(['category', 'image', 'author.user'])
            ->latest('published_at')
            ->take(4)
            ->get();

        if ($featuredPosts->count() < 4) {
            $fallback = Post::published()
                ->whereNotIn('id', $featuredPosts->pluck('id'))
                ->with(['category', 'image', 'author.user'])
                ->latest('published_at')
                ->take(4 - $featuredPosts->count())
                ->get();
            $featuredPosts = $featuredPosts->concat($fallback);
        }

        $blockHero = Post::published()
            ->where('is_sticky', 1)
            ->with(['category', 'image', 'author.user'])
            ->first() ?? $featuredPosts->first();

        $blockSub = Post::published()
            ->where('id', '!=', $blockHero ? $blockHero->id : 0)
            ->with(['category', 'image', 'author.user'])
            ->latest('published_at')
            ->take(2)
            ->get();

        $feedPosts = Post::published()
            ->with(['category', 'image', 'author.user', 'tag'])
            ->latest('published_at')
            ->paginate(10)
            ->withQueryString();

        return view('blog.feed', compact('featuredPosts', 'blockHero', 'blockSub', 'feedPosts'));
    }

    /**
     * Author profile and publications archive.
     */
    public function author(Request $request, $id): View
    {
        $author = Author::with(['user'])->where('id', $id)->orWhere('nickname', $id)->first();
        if (!$author && is_numeric($id)) {
            $author = Author::with('user')->find($id);
        }
        if (!$author) {
            $author = Author::where('user_id', $id)->first();
        }
        if (!$author) {
            abort(404, 'Author profile not found.');
        }

        $posts = Post::published()
            ->where('author_id', $author->id)
            ->with(['category', 'image', 'author.user', 'tag'])
            ->latest('published_at')
            ->paginate(8)
            ->withQueryString();

        $totalViews = Post::published()->where('author_id', $author->id)->sum('views_count');

        return view('blog.author', compact('author', 'posts', 'totalViews'));
    }

    /**
     * Authors and engineering contributors directory.
     */
    public function authors(Request $request): View
    {
        $authors = Author::with(['user'])
            ->withCount(['posts' => function ($q) {
                $q->published();
            }])
            ->orderByDesc('posts_count')
            ->get();

        return view('blog.authors', compact('authors'));
    }

    /**
     * Tag topic archive.
     */
    public function tag(Request $request, $id): View
    {
        $tag = Tag::where('id', $id)->orWhere('name', $id)->firstOrFail();

        $posts = Post::published()
            ->where('tag_id', $tag->id)
            ->with(['category', 'image', 'author.user', 'tag'])
            ->latest('published_at')
            ->paginate(8)
            ->withQueryString();

        return view('blog.tag', compact('tag', 'posts'));
    }

    /**
     * Living image gallery.
     */
    public function gallery(Request $request): View
    {
        $postImages = PostImage::with('post')->latest()->take(30)->get();
        $galleryItems = [];

        foreach ($postImages as $pi) {
            if ($pi->url) {
                $galleryItems[] = [
                    'url' => $pi->url,
                    'caption' => $pi->caption ?: ($pi->post->title ?? 'Architectural Visual'),
                    'post_title' => $pi->post->title ?? null,
                    'post_slug' => $pi->post->slug ?? null,
                ];
            }
        }

        if (count($galleryItems) < 12) {
            $pictures = Picture::take(20)->get();
            foreach ($pictures as $pic) {
                $link = $pic->link;
                $url = Str::startsWith($link, ['http://', 'https://']) ? $link : asset($link);
                $galleryItems[] = [
                    'url' => $url,
                    'caption' => $pic->name ?? 'System Blueprint',
                    'post_title' => null,
                    'post_slug' => null,
                ];
            }
        }

        $categories = Category::withCount('posts')->get();

        return view('blog.gallery', compact('galleryItems', 'categories'));
    }

    /**
     * Video insights gallery & primary video player.
     */
    public function videos(Request $request): View
    {
        $mainVideo = Post::published()
            ->whereNotNull('video_url')
            ->with(['category', 'image', 'author.user'])
            ->first();

        if (!$mainVideo) {
            $mainVideo = Post::published()->with(['category', 'image', 'author.user'])->first();
        }

        $moreVideos = Post::published()
            ->where('id', '!=', $mainVideo ? $mainVideo->id : 0)
            ->with(['category', 'image', 'author.user'])
            ->latest('published_at')
            ->take(6)
            ->get();

        return view('blog.video', compact('mainVideo', 'moreVideos'));
    }

    /**
     * Single video insight post.
     */
    public function singleVideo(Request $request, string $slug): View
    {
        $mainVideo = Post::where('slug', $slug)
            ->with(['category', 'image', 'author.user'])
            ->firstOrFail();

        $moreVideos = Post::published()
            ->where('id', '!=', $mainVideo->id)
            ->with(['category', 'image', 'author.user'])
            ->latest('published_at')
            ->take(6)
            ->get();

        return view('blog.video', compact('mainVideo', 'moreVideos'));
    }

    /**
     * Magazine Plus / Elite News Portal Home.
     */
    public function magazine(Request $request): View
    {
        $tickerPosts = Post::published()->latest('published_at')->take(5)->get();

        // Hero carousel & stack items
        $heroFeatured = Post::published()
            ->where('is_featured', 1)
            ->with(['category', 'image', 'author.user'])
            ->latest('published_at')
            ->take(6)
            ->get();

        if ($heroFeatured->count() < 6) {
            $fallback = Post::published()
                ->whereNotIn('id', $heroFeatured->pluck('id'))
                ->with(['category', 'image', 'author.user'])
                ->latest('published_at')
                ->take(6 - $heroFeatured->count())
                ->get();
            $heroFeatured = $heroFeatured->concat($fallback);
        }

        $heroLeft = $heroFeatured->slice(0, 2);
        $heroCenter = $heroFeatured->slice(2, 2);
        $heroRight = $heroFeatured->slice(4, 2);

        // Popular Stories (4-column grid)
        $popularStories = Post::published()
            ->with(['category', 'image', 'author.user'])
            ->orderByDesc('views_count')
            ->take(4)
            ->get();

        // Recent Stories (4-column grid)
        $recentStories = Post::published()
            ->with(['category', 'image', 'author.user'])
            ->latest('published_at')
            ->take(4)
            ->get();

        // Block Posts (2x2)
        $blockPosts = Post::published()
            ->with(['category', 'image', 'author.user'])
            ->latest('published_at')
            ->skip(4)
            ->take(2)
            ->get();

        // Matrix & Grid Posts
        $matrixPosts = Post::published()
            ->with(['category', 'image'])
            ->latest('published_at')
            ->skip(6)
            ->take(2)
            ->get();

        $gridHero = Post::published()
            ->with(['category', 'image', 'author.user'])
            ->where('is_sticky', 1)
            ->first() ?? Post::published()->first();

        $gridStack = Post::published()
            ->with(['category', 'image'])
            ->where('id', '!=', $gridHero ? $gridHero->id : 0)
            ->latest('published_at')
            ->skip(8)
            ->take(4)
            ->get();

        // Category showcases
        $techPosts = Post::published()
            ->with(['category', 'image'])
            ->latest('published_at')
            ->take(6)
            ->get();

        $businessPosts = Post::published()
            ->with(['category', 'image'])
            ->latest('published_at')
            ->skip(2)
            ->take(6)
            ->get();

        $sidebarCategories = Category::withCount('posts')->orderByDesc('posts_count')->take(10)->get();
        $sidebarTags = Tag::withCount('posts')->orderByDesc('posts_count')->take(25)->get();

        return view('blog.magazine', compact(
            'tickerPosts',
            'heroLeft',
            'heroCenter',
            'heroRight',
            'popularStories',
            'recentStories',
            'blockPosts',
            'matrixPosts',
            'gridHero',
            'gridStack',
            'techPosts',
            'businessPosts',
            'sidebarCategories',
            'sidebarTags'
        ));
    }

    /**
     * Display a single blog post using the Magazine Single layout.
     */
    public function singleMagazine(Request $request, ?string $slug = null): View
    {
        if ($slug) {
            $post = Post::where('slug', $slug)
                ->with(['category', 'author.user', 'image', 'user', 'tag', 'images', 'comments.user', 'comments.replies.user'])
                ->firstOrFail();
        } else {
            $post = Post::published()
                ->with(['category', 'author.user', 'image', 'user', 'tag', 'images', 'comments.user', 'comments.replies.user'])
                ->latest('published_at')
                ->firstOrFail();
        }

        $user = auth()->user();
        $isPreview = $request->boolean('preview');
        $isPublished = ($post->post_status === 'published' || $post->status) && (!$post->published_at || $post->published_at <= now());
        if (!$isPublished && !$isPreview && (!$user || !$user->hasRole(['super-admin', 'admin', 'editor', 'author']))) {
            abort(404, 'Article is not available for public viewing.');
        }

        if ($isPublished && !$isPreview) {
            $post->increment('views_count');
        }

        $isLocked = $post->isLockedForUser($user);
        $tableOfContents = $post->table_of_contents;
        $contentWithAnchors = $post->content_with_anchors;
        $relatedPosts = $post->getRelatedPosts(8);
        $previousPost = $post->previousPost();
        $nextPost = $post->nextPost();
        $galleryImages = $post->images;

        $tickerPosts = Post::published()->latest('published_at')->take(5)->get();
        $sidebarCategories = Category::withCount('posts')->orderByDesc('posts_count')->take(10)->get();
        $sidebarTags = Tag::withCount('posts')->orderByDesc('posts_count')->take(25)->get();

        return view('blog.single-magazine', compact(
            'post',
            'isLocked',
            'isPreview',
            'tableOfContents',
            'contentWithAnchors',
            'relatedPosts',
            'previousPost',
            'nextPost',
            'galleryImages',
            'tickerPosts',
            'sidebarCategories',
            'sidebarTags'
        ));
    }

    /**
     * Full-screen Hero Image Post Format.
     */
    public function formatImage(Request $request, ?string $slug = null): View
    {
        if ($slug) {
            $post = Post::where('slug', $slug)
                ->with(['category', 'author.user', 'image', 'user', 'tag'])
                ->firstOrFail();
        } else {
            $post = Post::published()
                ->whereNotNull('image_id')
                ->with(['category', 'author.user', 'image', 'user', 'tag'])
                ->latest('published_at')
                ->first() ?? Post::published()->with(['category', 'author.user', 'image'])->firstOrFail();
        }

        $relatedPosts = $post->getRelatedPosts(8);
        $sidebarCategories = Category::withCount('posts')->orderByDesc('posts_count')->take(10)->get();
        $sidebarTags = Tag::withCount('posts')->orderByDesc('posts_count')->take(25)->get();

        return view('blog.formats.image', compact('post', 'relatedPosts', 'sidebarCategories', 'sidebarTags'));
    }

    /**
     * External Bookmark / Link Post Format.
     */
    public function formatLink(Request $request, ?string $slug = null): View
    {
        if ($slug) {
            $post = Post::where('slug', $slug)
                ->with(['category', 'author.user', 'image', 'user', 'tag'])
                ->firstOrFail();
        } else {
            $post = Post::published()
                ->with(['category', 'author.user', 'image', 'user', 'tag'])
                ->latest('published_at')
                ->firstOrFail();
        }

        $relatedPosts = $post->getRelatedPosts(6);
        $sidebarCategories = Category::withCount('posts')->orderByDesc('posts_count')->take(10)->get();
        $sidebarTags = Tag::withCount('posts')->orderByDesc('posts_count')->take(25)->get();

        return view('blog.formats.link', compact('post', 'relatedPosts', 'sidebarCategories', 'sidebarTags'));
    }

    /**
     * Category archive view.
     */
    public function category(Request $request, string $id): View
    {
        $category = Category::where('id', $id)
            ->orWhere('name', $id)
            ->first();

        if (!$category) {
            $category = Category::where('name', 'like', "%{$id}%")->first();
        }

        if (!$category) {
            // Pick first category as fallback
            $category = Category::firstOrFail();
        }

        $posts = Post::published()
            ->where('category_id', $category->id)
            ->with(['category', 'image', 'author.user', 'tag'])
            ->latest('published_at')
            ->paginate(8)
            ->withQueryString();

        $sidebarCategories = Category::withCount('posts')->orderByDesc('posts_count')->take(10)->get();
        $sidebarTags = Tag::withCount('posts')->orderByDesc('posts_count')->take(25)->get();

        return view('blog.category', compact('category', 'posts', 'sidebarCategories', 'sidebarTags'));
    }

    /**
     * Full tags directory and index cloud.
     */
    public function tags(Request $request): View
    {
        $allTags = Tag::withCount(['posts' => function ($q) {
                $q->published();
            }])
            ->orderBy('name')
            ->get();

        $topTags = Tag::withCount(['posts' => function ($q) {
                $q->published();
            }])
            ->orderByDesc('posts_count')
            ->take(16)
            ->get();

        $groupedTags = $allTags->groupBy(function ($tag) {
            return strtoupper(substr($tag->name, 0, 1));
        })->sortKeys();

        $totalTagsCount = $allTags->count();
        $sidebarCategories = Category::withCount('posts')->orderByDesc('posts_count')->take(10)->get();
        $sidebarTags = Tag::withCount('posts')->orderByDesc('posts_count')->take(25)->get();

        return view('blog.tags', compact('allTags', 'topTags', 'groupedTags', 'totalTagsCount', 'sidebarCategories', 'sidebarTags'));
    }
}
