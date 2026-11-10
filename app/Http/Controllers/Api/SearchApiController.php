<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\SearchLog;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchApiController extends Controller
{
    /**
     * Live search autocomplete endpoint.
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $term = trim($request->input('q', ''));

        if (empty($term) || strlen($term) < 2) {
            $popular = SearchLog::where('results_count', '>', 0)
                ->orderBy('hits_count', 'desc')
                ->take(8)
                ->pluck('query');

            return response()->json([
                'success' => true,
                'popular' => $popular,
                'posts' => [],
                'categories' => [],
                'tags' => [],
            ]);
        }

        // Search published posts
        $posts = Post::published()
            ->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('excerpt', 'like', "%{$term}%")
                  ->orWhere('content', 'like', "%{$term}%");
            })
            ->with(['category', 'image'])
            ->latest('published_at')
            ->take(6)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'title' => $p->title,
                    'slug' => $p->slug,
                    'url' => url('/blog/' . $p->slug),
                    'category' => $p->category?->name ?? 'Article',
                    'reading_time' => $p->reading_time . ' min read',
                    'thumbnail' => $p->image?->webp_url ?? $p->image?->url ?? asset('xivig-admin/img/blog-1-PTDVKJDt.webp'),
                    'date' => $p->published_at ? $p->published_at->format('M d, Y') : '',
                ];
            });

        // Search categories
        $categories = Category::where('name', 'like', "%{$term}%")
            ->take(4)
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'url' => url('/category/' . $c->slug),
                ];
            });

        // Search tags
        $tags = Tag::where('name', 'like', "%{$term}%")
            ->take(5)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'name' => $t->name,
                    'url' => url('/tag/' . $t->id),
                ];
            });

        $totalResults = $posts->count() + $categories->count() + $tags->count();

        // Track query in Search Intelligence Logs
        $searchLog = SearchLog::where('query', strtolower($term))->first();
        if ($searchLog) {
            $searchLog->increment('hits_count');
            $searchLog->update([
                'results_count' => $totalResults,
                'ip' => $request->ip(),
                'last_searched_at' => now(),
            ]);
        } else {
            SearchLog::create([
                'query' => strtolower($term),
                'results_count' => $totalResults,
                'hits_count' => 1,
                'ip' => $request->ip(),
                'last_searched_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'query' => $term,
            'total' => $totalResults,
            'posts' => $posts,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }
}
