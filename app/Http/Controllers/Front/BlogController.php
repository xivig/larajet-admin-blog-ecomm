<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Picture;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of blog posts.
     */
    public function index(Request $request): View
    {
        $query = Post::query()
            ->with(['category', 'author.user', 'image', 'user', 'tag', 'comments'])
            ->latest();

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        if ($request->filled('tag')) {
            $query->whereHas('tag', function ($q) use ($request) {
                $q->where('name', $request->tag);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

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
    public function singlePost(string $slug): View
    {
        $post = Post::with(['category', 'author.user', 'image', 'user', 'tag', 'comments.user', 'comments.replies.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        $recentPosts = Post::with(['image', 'category', 'author.user'])
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(5)
            ->get();

        $categories = Category::withCount('posts')->get();
        $tags = Tag::withCount('posts')->orderByDesc('posts_count')->take(25)->get();

        return view('blog.blog-post', compact('post', 'recentPosts', 'categories', 'tags'));
    }
}
