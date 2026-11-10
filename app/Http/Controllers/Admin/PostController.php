<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Category;
use App\Models\Picture;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\SlugHistory;
use App\Models\Tag;
use App\Services\ImageOptimizationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of posts for admin with granular status filters.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status', 'all');

        if ($status === 'trash') {
            $query = Post::onlyTrashed()->with(['category', 'author.user', 'image', 'tag', 'comments', 'images'])->latest();
        } else {
            $query = Post::with(['category', 'author.user', 'image', 'tag', 'comments', 'images'])->latest();

            if ($status === 'published') {
                $query->where(function ($q) {
                    $q->where('post_status', 'published')
                      ->orWhere(function ($sub) {
                          $sub->where('status', true)->where('post_status', '!=', 'draft')->where('post_status', '!=', 'scheduled');
                      });
                });
            } elseif ($status === 'scheduled') {
                $query->where('post_status', 'scheduled');
            } elseif ($status === 'draft') {
                $query->where(function ($q) {
                    $q->where('post_status', 'draft')
                      ->orWhere(function ($sub) {
                          $sub->where('status', false)->where('post_status', '!=', 'scheduled');
                      });
                });
            } elseif ($status === 'sticky') {
                $query->where('is_sticky', true);
            } elseif ($status === 'featured') {
                $query->where('is_featured', true);
            }
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('slug', 'like', "%{$s}%")
                  ->orWhere('excerpt', 'like', "%{$s}%");
            });
        }

        $posts = $query->paginate(15)->withQueryString();
        $categories = Category::all();

        $counts = [
            'all' => Post::count(),
            'published' => Post::where(fn($q) => $q->where('post_status', 'published')->orWhere('status', true))->count(),
            'scheduled' => Post::where('post_status', 'scheduled')->count(),
            'draft' => Post::where(fn($q) => $q->where('post_status', 'draft')->orWhere(fn($s) => $s->where('status', false)->where('post_status', '!=', 'scheduled')))->count(),
            'sticky' => Post::where('is_sticky', true)->count(),
            'featured' => Post::where('is_featured', true)->count(),
            'trash' => Post::onlyTrashed()->count(),
        ];

        return view('admin.posts.index', compact('posts', 'categories', 'counts', 'status'));
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
     * Store a newly created post with complete blog & SEO suite.
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
            'post_status' => 'nullable|in:draft,scheduled,published',
            'scheduled_at' => 'nullable|date',
            'visibility' => 'nullable|in:public,password_protected,private',
            'password' => 'nullable|string|max:255',
            'is_sticky' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            // Gallery files
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            // SEO Meta Validation
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:500',
            'meta_author' => 'nullable|string|max:255',
            'meta_robots' => 'nullable|string|max:100',
            'meta_language' => 'nullable|string|max:20',
            'geo_region' => 'nullable|string|max:50',
            'geo_placename' => 'nullable|string|max:100',
            'geo_position' => 'nullable|string|max:100',
            'meta_rating' => 'nullable|string|max:50',
            'meta_copyright' => 'nullable|string|max:255',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'twitter_card' => 'nullable|string|max:50',
            'schema_type' => 'nullable|string|max:50',
        ]);

        // Featured Hero Image with automated WebP & Responsive optimization
        $imageId = $request->filled('image_id') ? $request->input('image_id') : null;
        if ($request->hasFile('featured_image')) {
            $optimizer = app(ImageOptimizationService::class);
            $picture = $optimizer->processUpload($request->file('featured_image'), 'uploads/posts', $request->title, $request->title);
            $imageId = $picture->id;
        }

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

        // Unique Slug
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

        // Status & Publishing Calculation
        $postStatus = $request->input('post_status', 'draft');
        $publishedAt = null;
        $scheduledAt = null;

        if ($postStatus === 'published') {
            $publishedAt = Carbon::now();
            $statusBool = true;
        } elseif ($postStatus === 'scheduled') {
            $scheduledAt = $request->filled('scheduled_at') ? Carbon::parse($request->scheduled_at) : Carbon::now()->addHour();
            $statusBool = false;
        } else {
            $postStatus = 'draft';
            $statusBool = false;
        }

        // Reading Time Calculation
        $readingTime = max(1, (int) ceil(str_word_count(strip_tags($request->content)) / 200));

        // FAQs parsing if submitted
        $faqSchema = null;
        if ($request->has('faq_questions') && is_array($request->faq_questions)) {
            $faqs = [];
            foreach ($request->faq_questions as $idx => $question) {
                $answer = $request->faq_answers[$idx] ?? '';
                if (!empty(trim($question)) && !empty(trim($answer))) {
                    $faqs[] = ['question' => trim($question), 'answer' => trim($answer)];
                }
            }
            if (!empty($faqs)) {
                $faqSchema = $faqs;
            }
        }

        $post = Post::create([
            'user_id' => auth()->id() ?? 1,
            'category_id' => $request->category_id,
            'tag_id' => $tagId,
            'image_id' => $imageId,
            'author_id' => $authorId,
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'excerpt' => $excerpt,
            'status' => $statusBool,
            'post_status' => $postStatus,
            'published_at' => $publishedAt,
            'scheduled_at' => $scheduledAt,
            'is_sticky' => $request->boolean('is_sticky'),
            'is_featured' => $request->boolean('is_featured'),
            'visibility' => $request->input('visibility', 'public'),
            'password' => $request->filled('password') ? $request->password : null,
            'reading_time' => $readingTime,
            // SEO Meta
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'canonical_url' => $request->canonical_url,
            'meta_author' => $request->meta_author,
            'meta_robots' => $request->input('meta_robots', 'index, follow'),
            'meta_language' => $request->input('meta_language', 'en'),
            'geo_region' => $request->geo_region,
            'geo_placename' => $request->geo_placename,
            'geo_position' => $request->geo_position,
            'meta_rating' => $request->input('meta_rating', 'general'),
            'meta_copyright' => $request->meta_copyright,
            'og_title' => $request->og_title,
            'og_description' => $request->og_description,
            'og_image' => $request->og_image,
            'twitter_card' => $request->input('twitter_card', 'summary_large_image'),
            'schema_type' => $request->input('schema_type', 'Article'),
            'faq_schema' => $faqSchema,
        ]);

        // Process Unlimited Gallery Images
        if ($request->hasFile('gallery_images')) {
            $galleryDir = public_path('uploads/posts/gallery');
            if (!file_exists($galleryDir)) {
                mkdir($galleryDir, 0755, true);
            }

            foreach ($request->file('gallery_images') as $idx => $galleryFile) {
                $galleryName = time() . '_' . $idx . '_' . Str::slug(pathinfo($galleryFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $galleryFile->getClientOriginalExtension();
                $galleryFile->move($galleryDir, $galleryName);

                $post->images()->create([
                    'image_path' => 'uploads/posts/gallery/' . $galleryName,
                    'caption' => pathinfo($galleryFile->getClientOriginalName(), PATHINFO_FILENAME),
                    'alt_text' => $post->title,
                    'sort_order' => $idx + 1,
                ]);
            }
        }

        return redirect()->route('admin.posts.index')->with('success', 'Article "' . $request->title . '" created successfully!');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post): View
    {
        $post->load(['category', 'author.user', 'image', 'tag', 'comments.user', 'images']);
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post): View
    {
        $post->load(['images']);
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
            'post_status' => 'nullable|in:draft,scheduled,published',
            'scheduled_at' => 'nullable|date',
            'visibility' => 'nullable|in:public,password_protected,private',
            'password' => 'nullable|string|max:255',
            'is_sticky' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            // Gallery files
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            // SEO Meta Validation
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:500',
            'meta_author' => 'nullable|string|max:255',
            'meta_robots' => 'nullable|string|max:100',
            'meta_language' => 'nullable|string|max:20',
            'geo_region' => 'nullable|string|max:50',
            'geo_placename' => 'nullable|string|max:100',
            'geo_position' => 'nullable|string|max:100',
            'meta_rating' => 'nullable|string|max:50',
            'meta_copyright' => 'nullable|string|max:255',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'twitter_card' => 'nullable|string|max:50',
            'schema_type' => 'nullable|string|max:50',
        ]);

        $imageId = $request->filled('image_id') ? $request->input('image_id') : $post->image_id;
        if ($request->hasFile('featured_image')) {
            $optimizer = app(ImageOptimizationService::class);
            $picture = $optimizer->processUpload($request->file('featured_image'), 'uploads/posts', $request->title, $request->title);
            $imageId = $picture->id;
        }

        // Custom URL or Automatic Slug with Duplicate Detection & Slug History
        $slug = $post->slug;
        $candidateSlug = null;
        if ($request->filled('slug')) {
            $candidateSlug = Str::slug($request->slug);
        } elseif ($post->title !== $request->title) {
            $candidateSlug = Str::slug($request->title);
        }

        if ($candidateSlug && $candidateSlug !== $post->slug) {
            $slug = $candidateSlug;
            $counter = 1;
            while (Post::where('slug', $slug)->where('id', '!=', $post->id)->exists()) {
                $slug = $candidateSlug . '-' . $counter;
                $counter++;
            }

            // Automatic 301 redirection equity: store previous slug in history
            SlugHistory::create([
                'model_type' => 'Post',
                'model_id' => $post->id,
                'old_slug' => $post->slug,
                'new_slug' => $slug,
            ]);
        }

        $excerpt = $request->filled('excerpt')
            ? $request->excerpt
            : Str::limit(strip_tags($request->content), 150);

        // Status calculation
        $postStatus = $request->input('post_status', $post->post_status ?? 'draft');
        $publishedAt = $post->published_at;
        $scheduledAt = $post->scheduled_at;

        if ($postStatus === 'published') {
            if (!$publishedAt) {
                $publishedAt = Carbon::now();
            }
            $statusBool = true;
        } elseif ($postStatus === 'scheduled') {
            $scheduledAt = $request->filled('scheduled_at') ? Carbon::parse($request->scheduled_at) : ($scheduledAt ?? Carbon::now()->addHour());
            $statusBool = false;
        } else {
            $postStatus = 'draft';
            $statusBool = false;
        }

        $readingTime = max(1, (int) ceil(str_word_count(strip_tags($request->content)) / 200));

        // FAQs parsing
        $faqSchema = $post->faq_schema;
        if ($request->has('faq_questions') && is_array($request->faq_questions)) {
            $faqs = [];
            foreach ($request->faq_questions as $idx => $question) {
                $answer = $request->faq_answers[$idx] ?? '';
                if (!empty(trim($question)) && !empty(trim($answer))) {
                    $faqs[] = ['question' => trim($question), 'answer' => trim($answer)];
                }
            }
            $faqSchema = !empty($faqs) ? $faqs : null;
        }

        $post->update([
            'category_id' => $request->category_id,
            'tag_id' => $request->tag_id ?? $post->tag_id,
            'image_id' => $imageId,
            'author_id' => $request->author_id ?? $post->author_id,
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'excerpt' => $excerpt,
            'status' => $statusBool,
            'post_status' => $postStatus,
            'published_at' => $publishedAt,
            'scheduled_at' => $scheduledAt,
            'is_sticky' => $request->boolean('is_sticky'),
            'is_featured' => $request->boolean('is_featured'),
            'visibility' => $request->input('visibility', 'public'),
            'password' => $request->filled('password') ? $request->password : ($request->input('visibility') === 'password_protected' ? $post->password : null),
            'reading_time' => $readingTime,
            // SEO Meta
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'canonical_url' => $request->canonical_url,
            'meta_author' => $request->meta_author,
            'meta_robots' => $request->input('meta_robots', 'index, follow'),
            'meta_language' => $request->input('meta_language', 'en'),
            'geo_region' => $request->geo_region,
            'geo_placename' => $request->geo_placename,
            'geo_position' => $request->geo_position,
            'meta_rating' => $request->input('meta_rating', 'general'),
            'meta_copyright' => $request->meta_copyright,
            'og_title' => $request->og_title,
            'og_description' => $request->og_description,
            'og_image' => $request->og_image,
            'twitter_card' => $request->input('twitter_card', 'summary_large_image'),
            'schema_type' => $request->input('schema_type', 'Article'),
            'faq_schema' => $faqSchema,
        ]);

        // Process new Gallery Images
        if ($request->hasFile('gallery_images')) {
            $galleryDir = public_path('uploads/posts/gallery');
            if (!file_exists($galleryDir)) {
                mkdir($galleryDir, 0755, true);
            }

            $currentMaxSort = $post->images()->max('sort_order') ?? 0;
            foreach ($request->file('gallery_images') as $idx => $galleryFile) {
                $galleryName = time() . '_' . $idx . '_' . Str::slug(pathinfo($galleryFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $galleryFile->getClientOriginalExtension();
                $galleryFile->move($galleryDir, $galleryName);

                $post->images()->create([
                    'image_path' => 'uploads/posts/gallery/' . $galleryName,
                    'caption' => pathinfo($galleryFile->getClientOriginalName(), PATHINFO_FILENAME),
                    'alt_text' => $post->title,
                    'sort_order' => $currentMaxSort + $idx + 1,
                ]);
            }
        }

        // Update existing captions if present
        if ($request->has('gallery_captions') && is_array($request->gallery_captions)) {
            foreach ($request->gallery_captions as $imgId => $caption) {
                $post->images()->where('id', $imgId)->update(['caption' => $caption]);
            }
        }

        return redirect()->route('admin.posts.index')->with('success', 'Article updated successfully!');
    }

    /**
     * Deep-Clone a post.
     */
    public function clone(Post $post): RedirectResponse
    {
        $clone = $post->clonePost();

        return redirect()->route('admin.posts.edit', $clone->id)
            ->with('success', 'Article cloned successfully as a draft! You are now editing the clone.');
    }

    /**
     * Preview a post.
     */
    public function preview(Post $post): RedirectResponse
    {
        return redirect()->to(url('/blog/' . $post->slug . '?preview=1'));
    }

    /**
     * Delete an individual gallery image.
     */
    public function deleteGalleryImage(Post $post, PostImage $image): JsonResponse|RedirectResponse
    {
        if ($image->post_id === $post->id) {
            $filePath = public_path($image->image_path);
            if (File::exists($filePath)) {
                @unlink($filePath);
            }
            $image->delete();
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Gallery image removed successfully.');
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

        // Permanently remove related comments and gallery images
        $post->comments()->forceDelete();
        foreach ($post->images as $img) {
            $filePath = public_path($img->image_path);
            if (File::exists($filePath)) {
                @unlink($filePath);
            }
        }
        $post->images()->delete();
        $post->forceDelete();

        return redirect()->route('admin.posts.index', ['status' => 'trash'])->with('success', 'Article "' . $title . '" permanently deleted.');
    }

    /**
     * Duplicate a post into a new draft (alias for clone).
     */
    public function duplicate(Post $post): RedirectResponse
    {
        return $this->clone($post);
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
            'status' => false,
            'post_status' => 'draft',
            'visibility' => 'public',
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Quick draft saved successfully!');
    }

    /**
     * Bulk actions for posts.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:publish,draft,delete,restore,force_delete,sticky,featured',
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', 'No articles selected for bulk action.');
        }

        if ($action === 'publish') {
            Post::whereIn('id', $ids)->update([
                'status' => true,
                'post_status' => 'published',
                'published_at' => Carbon::now(),
            ]);
            return back()->with('success', count($ids) . ' article(s) published successfully!');
        } elseif ($action === 'draft') {
            Post::whereIn('id', $ids)->update([
                'status' => false,
                'post_status' => 'draft',
            ]);
            return back()->with('success', count($ids) . ' article(s) set to draft!');
        } elseif ($action === 'sticky') {
            Post::whereIn('id', $ids)->update(['is_sticky' => true]);
            return back()->with('success', count($ids) . ' article(s) marked as sticky!');
        } elseif ($action === 'featured') {
            Post::whereIn('id', $ids)->update(['is_featured' => true]);
            return back()->with('success', count($ids) . ' article(s) marked as featured!');
        } elseif ($action === 'delete') {
            Post::whereIn('id', $ids)->delete();
            return back()->with('success', count($ids) . ' article(s) moved to trash!');
        } elseif ($action === 'restore') {
            Post::onlyTrashed()->whereIn('id', $ids)->restore();
            return back()->with('success', count($ids) . ' article(s) restored from trash!');
        } elseif ($action === 'force_delete') {
            foreach ($ids as $id) {
                $p = Post::onlyTrashed()->find($id);
                if ($p) {
                    $p->comments()->forceDelete();
                    $p->images()->delete();
                    $p->forceDelete();
                }
            }
            return back()->with('success', count($ids) . ' article(s) permanently deleted.');
        }

        return back();
    }
}
