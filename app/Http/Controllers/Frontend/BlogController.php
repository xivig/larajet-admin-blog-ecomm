<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Category;
use App\Models\Comment;
use App\Models\GalleryItem;
use App\Models\Picture;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
            ->where('video_url', '!=', '')
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
     * Magazine Edition Home — every portal section populated dynamically with
     * non-overlapping story selection and cyclic fallback when datasets are small.
     */
    public function magazine(Request $request): View
    {
        $allPool = Post::published()
            ->with(['category', 'image', 'author.user', 'tag'])
            ->latest('published_at')
            ->latest('id')
            ->take(60)
            ->get();

        $popularPool = Post::published()
            ->with(['category', 'image', 'author.user', 'tag'])
            ->orderByDesc('views_count')
            ->latest('published_at')
            ->take(24)
            ->get();

        $usedIds = [];

        // 1. Ticker & Hero Showcase (Left 2, Center Carousel 3, Right 2)
        $tickerPosts = $allPool->take(6)->values();

        $heroPool = $this->selectMagazineSlice(
            $allPool,
            $usedIds,
            7,
            fn (Post $post) => (bool) ($post->is_featured || $post->is_sticky)
        );
        $heroLeft = $heroPool->slice(0, 2)->values();
        $heroCenter = $heroPool->slice(2, 3)->values();
        $heroRight = $heroPool->slice(5, 2)->values();

        // 2. Popular Stories (4-column views-ranked showcase)
        $popularStories = $popularPool->take(4)->values();
        foreach ($popularStories as $pop) {
            $usedIds[] = $pop->id;
        }
        $usedIds = array_values(array_unique($usedIds));

        // 3. Recent Stories (4-column fresh dispatches)
        $recentStories = $this->selectMagazineSlice($allPool, $usedIds, 4);

        // 4. Featured Block Posts (full 2x2 grid = 4 cards)
        $blockPosts = $this->selectMagazineSlice($allPool, $usedIds, 4);

        // 5. Analysis Matrix (4 items) & Dynamic Visual Briefs (6 DB gallery/media tiles)
        $matrixPosts = $this->selectMagazineSlice($allPool, $usedIds, 4);
        $visualBriefs = $this->buildVisualBriefs($allPool, 6);

        // 6. Editorial Spotlight Block Post (1 flagship 21:9 + 2 sub cards)
        $spotlightGroup = $this->selectMagazineSlice($allPool, $usedIds, 3);
        $spotlightMain = $spotlightGroup->first();
        $spotlightSub = $spotlightGroup->slice(1, 2)->values();

        // 7. Two-Column Dispatches (3 left + 3 right = 6 items)
        $columnPosts = $this->selectMagazineSlice($allPool, $usedIds, 6);
        $columnLeft = $columnPosts->slice(0, 3)->values();
        $columnRight = $columnPosts->slice(3, 3)->values();

        // 8. Deep-Dive Dispatch Grid (1 large left + 4 stacked right)
        $stickyCandidate = $allPool->firstWhere('is_sticky', true);
        if ($stickyCandidate && !in_array($stickyCandidate->id, $usedIds, true)) {
            $gridHero = $stickyCandidate;
            $usedIds[] = $stickyCandidate->id;
        } else {
            $gridHero = $this->selectMagazineSlice($allPool, $usedIds, 1)->first() ?? $allPool->first();
        }
        $gridStack = $this->selectMagazineSlice(
            $allPool,
            $usedIds,
            4,
            fn (Post $post) => !$gridHero || $post->id !== $gridHero->id
        );

        // 9. Business & Commerce Feature Showcase (1 large top + 6 3-col list items)
        $businessMatcher = fn (Post $post) => Str::contains(
            Str::lower($post->category?->name ?? ''),
            ['business', 'bank', 'retail', 'commerce', 'finance', 'real estate', 'websites', 'restaurant', 'service', 'memorabilia', 'collect']
        );
        $businessShowcase = $this->selectMagazineSlice($allPool, $usedIds, 7, $businessMatcher);
        $businessHero = $businessShowcase->first();
        $businessPosts = $businessShowcase->slice(1, 6)->values();

        // 10. Technology & Engineering Intelligence (2 blocks + 4 list items = 6)
        $techMatcher = fn (Post $post) => Str::contains(
            Str::lower($post->category?->name ?? ''),
            ['tech', 'computer', 'laptop', 'printer', 'network', 'monitor', 'software', 'robot', 'electronic', 'science', 'digital', 'laravel']
        );
        $techPosts = $this->selectMagazineSlice($allPool, $usedIds, 6, $techMatcher);

        // 11. Video & Multimedia Dispatches (3 cards, prioritizing native video URLs)
        $videoDirect = Post::published()
            ->whereNotNull('video_url')
            ->where('video_url', '!=', '')
            ->with(['category', 'image', 'author.user'])
            ->latest('published_at')
            ->take(3)
            ->get();

        $videoPosts = $videoDirect->values();
        if ($videoPosts->count() < 3) {
            $videoIds = $videoPosts->pluck('id')->all();
            $filler = $allPool->whereNotIn('id', $videoIds)->take(3 - $videoPosts->count());
            $videoPosts = $videoPosts->concat($filler)->values();
        }

        // 12. Editor's Picks ("You Missed" 4-col grid)
        $editorsPicks = $this->selectMagazineSlice($allPool, $usedIds, 4);

        // 13. Active Channels / Categories Strip
        $featuredCategories = Category::withCount(['posts' => fn ($q) => $q->published()])
            ->orderByDesc('posts_count')
            ->take(8)
            ->get();

        // Edition telemetry summary
        $magazineStats = [
            'published_posts' => Post::published()->count(),
            'active_categories' => Category::whereHas('posts', fn ($q) => $q->published())->count(),
            'gallery_assets' => max(GalleryItem::count(), Picture::count()),
            'video_dispatches' => max(
                Post::published()->whereNotNull('video_url')->where('video_url', '!=', '')->count(),
                $videoPosts->count()
            ),
        ];

        // Sidebar data — strictly capped at <= 10 categories & <= 25 tags
        $sidebarPopular = $popularPool->take(4)->values();
        $sidebarRecent = $allPool->take(4)->values();
        $sidebarComments = Comment::with(['user', 'post'])->latest()->take(4)->get();
        $sidebarCategories = Category::withCount(['posts' => fn ($q) => $q->published()])
            ->orderByDesc('posts_count')
            ->take(10)
            ->get();
        $sidebarTags = Tag::withCount(['posts' => fn ($q) => $q->published()])
            ->orderByDesc('posts_count')
            ->take(25)
            ->get();

        return view('blog.magazine', compact(
            'tickerPosts',
            'heroLeft',
            'heroCenter',
            'heroRight',
            'popularStories',
            'recentStories',
            'blockPosts',
            'matrixPosts',
            'visualBriefs',
            'spotlightMain',
            'spotlightSub',
            'columnLeft',
            'columnRight',
            'gridHero',
            'gridStack',
            'businessHero',
            'businessPosts',
            'techPosts',
            'videoPosts',
            'editorsPicks',
            'featuredCategories',
            'magazineStats',
            'sidebarPopular',
            'sidebarRecent',
            'sidebarComments',
            'sidebarCategories',
            'sidebarTags'
        ));
    }

    /**
     * Pick `$count` items for a magazine section, preferring unseen posts matching `$filter`,
     * then unseen pool items, then circular wrap-around so sections are never empty.
     */
    private function selectMagazineSlice(Collection $pool, array &$usedIds, int $count, ?callable $filter = null): Collection
    {
        if ($pool->isEmpty() || $count <= 0) {
            return collect();
        }

        $selected = collect();
        $pickedNow = [];

        if ($filter !== null) {
            foreach ($pool->filter($filter) as $post) {
                if (in_array($post->id, $usedIds, true)) {
                    continue;
                }
                $selected->push($post);
                $pickedNow[] = $post->id;
                if ($selected->count() >= $count) {
                    break;
                }
            }
        }

        if ($selected->count() < $count) {
            foreach ($pool as $post) {
                if (in_array($post->id, $usedIds, true) || in_array($post->id, $pickedNow, true)) {
                    continue;
                }
                $selected->push($post);
                $pickedNow[] = $post->id;
                if ($selected->count() >= $count) {
                    break;
                }
            }
        }

        $usedIds = array_values(array_unique(array_merge($usedIds, $pickedNow)));

        // Circular wrap-around when dataset has fewer remaining items than requested
        if ($selected->count() < $count) {
            $cursor = count($usedIds);
            $poolValues = $pool->values();
            $poolSize = $poolValues->count();
            $guard = 0;
            while ($selected->count() < $count && $guard < ($poolSize * 2 + $count)) {
                $candidate = $poolValues->get($cursor % $poolSize);
                if ($candidate && ($selected->count() >= $poolSize || !$selected->contains('id', $candidate->id))) {
                    $selected->push($candidate);
                }
                $cursor++;
                $guard++;
            }
        }

        return $selected->values();
    }

    /**
     * Assemble visual photography briefs from GalleryItem and Post cover imagery.
     */
    private function buildVisualBriefs(Collection $allPool, int $count = 6): Collection
    {
        $items = collect();

        $galleryItems = GalleryItem::with('gallery')
            ->orderBy('order')
            ->latest('id')
            ->take($count * 2)
            ->get();

        foreach ($galleryItems as $item) {
            if ($items->count() >= $count) {
                break;
            }
            $items->push([
                'url' => $item->url,
                'title' => $item->caption ?: ($item->gallery?->title ?? 'Visual Dispatch'),
                'subtitle' => $item->gallery?->title ?? 'Curated Gallery',
                'link' => route('blog.gallery'),
            ]);
        }

        if ($items->count() < $count) {
            foreach ($allPool as $post) {
                if ($items->count() >= $count) {
                    break;
                }
                $imgUrl = $post->image?->url;
                if (!$imgUrl) {
                    continue;
                }
                $items->push([
                    'url' => $imgUrl,
                    'title' => Str::limit($post->title, 42),
                    'subtitle' => $post->category?->name ?? 'Editorial Photo',
                    'link' => route('blog.single-magazine', $post->slug),
                ]);
            }
        }

        return $items->values();
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
     * Immersive Visual / Photography Feature Post Format (/blog/format/image/{slug?}).
     */
    public function formatImage(Request $request, ?string $slug = null): View
    {
        $relations = ['category', 'author.user', 'image', 'user', 'tag', 'images', 'comments.user', 'comments.replies.user'];

        if ($slug) {
            $post = Post::where('slug', $slug)->with($relations)->firstOrFail();
        } else {
            $post = Post::published()
                ->whereNotNull('image_id')
                ->with($relations)
                ->latest('published_at')
                ->latest('id')
                ->first() ?? Post::published()->with($relations)->firstOrFail();
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
        $previousPost = $post->previousPost();
        $nextPost = $post->nextPost();

        // Build real image metadata telemetry (falling back to file metrics on disk if needed)
        $visualTelemetry = $this->resolveVisualTelemetry($post);

        // Multi-frame plate collection for filmstrip + Cinema Lightbox
        $visualFrames = $this->buildVisualFormatFrames($post, $visualTelemetry, 6);

        // Other featured image editions for quick exploration bar
        $visualSwitcherPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->whereNotNull('image_id')
            ->with(['category', 'image', 'author.user'])
            ->latest('published_at')
            ->take(6)
            ->get();

        // Related stories grid (guarantee rich 8 cards when available)
        $relatedPosts = $post->getRelatedPosts(8);
        if ($relatedPosts->count() < 8) {
            $excludeIds = $relatedPosts->pluck('id')->push($post->id)->all();
            $filler = Post::published()
                ->whereNotIn('id', $excludeIds)
                ->with(['category', 'image', 'author.user'])
                ->latest('published_at')
                ->take(8 - $relatedPosts->count())
                ->get();
            $relatedPosts = $relatedPosts->concat($filler)->values();
        }

        $sidebarCategories = Category::withCount(['posts' => fn ($q) => $q->published()])
            ->orderByDesc('posts_count')
            ->take(10)
            ->get();
        $sidebarTags = Tag::withCount(['posts' => fn ($q) => $q->published()])
            ->orderByDesc('posts_count')
            ->take(25)
            ->get();

        return view('blog.formats.image', compact(
            'post',
            'isLocked',
            'isPreview',
            'tableOfContents',
            'contentWithAnchors',
            'previousPost',
            'nextPost',
            'visualTelemetry',
            'visualFrames',
            'visualSwitcherPosts',
            'relatedPosts',
            'sidebarCategories',
            'sidebarTags'
        ));
    }

    /**
     * Extract authentic resolution, MIME format, byte size, and WebP telemetry for a post's cover plate.
     */
    private function resolveVisualTelemetry(Post $post): array
    {
        $pic = $post->image;
        $src = $pic?->url ?? asset('admin-dist/img/blog-1-PTDVKJDt.webp');
        $webpUrl = $pic?->webp_url ?? $src;
        $alt = $pic?->display_alt ?? $post->title;
        $caption = $pic?->caption ?: ($pic?->title ?: $post->title);

        $dimensions = $pic?->dimensions;
        $mimeType = $pic?->mime_type;
        $fileSizeBytes = (int) ($pic?->file_size ?? 0);

        $relPath = $pic?->link ? ltrim((string) $pic->link, '/') : null;
        if ($relPath && !Str::startsWith($relPath, ['http://', 'https://'])) {
            $diskPath = public_path($relPath);
            if (is_file($diskPath)) {
                if ($fileSizeBytes <= 0) {
                    $fileSizeBytes = (int) @filesize($diskPath);
                }
                if (empty($dimensions)) {
                    $info = @getimagesize($diskPath);
                    if (is_array($info) && !empty($info[0]) && !empty($info[1])) {
                        $dimensions = $info[0] . 'x' . $info[1];
                    }
                    if (empty($mimeType) && is_array($info) && !empty($info['mime'])) {
                        $mimeType = $info['mime'];
                    }
                }
            }
        }

        $formattedSize = $fileSizeBytes >= 1048576
            ? number_format($fileSizeBytes / 1048576, 2) . ' MB'
            : ($fileSizeBytes >= 1024 ? number_format($fileSizeBytes / 1024, 1) . ' KB' : ($fileSizeBytes > 0 ? $fileSizeBytes . ' B' : 'HD Stream'));

        $formatExt = strtoupper(pathinfo(parse_url($src, PHP_URL_PATH) ?: 'image.jpg', PATHINFO_EXTENSION) ?: 'IMG');

        return [
            'src' => $src,
            'webp_url' => $webpUrl,
            'alt' => $alt,
            'caption' => $caption,
            'dimensions' => $dimensions ?: '1920x1080',
            'mime_type' => $mimeType ?: 'image/jpeg',
            'format_badge' => $formatExt === 'WEBP' ? 'WebP Optimized' : $formatExt . ' Master',
            'formatted_size' => $formattedSize,
        ];
    }

    /**
     * Build ordered image plates for the Visual Format lightbox & filmstrip.
     */
    private function buildVisualFormatFrames(Post $post, array $telemetry, int $limit = 6): Collection
    {
        $frames = collect([
            [
                'url' => $telemetry['src'],
                'title' => $telemetry['caption'],
                'alt' => $telemetry['alt'],
                'badge' => 'Primary Plate · ' . $telemetry['dimensions'],
            ],
        ]);

        foreach ($post->images ?? collect() as $pi) {
            if ($frames->count() >= $limit) {
                break;
            }
            if (!empty($pi->url) && !$frames->contains('url', $pi->url)) {
                $frames->push([
                    'url' => $pi->url,
                    'title' => $pi->caption ?: $post->title,
                    'alt' => $pi->caption ?: $post->title,
                    'badge' => 'Essay Plate #' . ($frames->count() + 1),
                ]);
            }
        }

        if ($frames->count() < $limit) {
            $galleryExtras = GalleryItem::orderBy('order')->latest('id')->take($limit * 2)->get();
            foreach ($galleryExtras as $gi) {
                if ($frames->count() >= $limit) {
                    break;
                }
                if (!$frames->contains('url', $gi->url)) {
                    $frames->push([
                        'url' => $gi->url,
                        'title' => $gi->caption ?: 'Curated Visual Study',
                        'alt' => $gi->caption ?: 'Curated Visual Study',
                        'badge' => 'Archive Plate #' . ($frames->count() + 1),
                    ]);
                }
            }
        }

        return $frames->values();
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
