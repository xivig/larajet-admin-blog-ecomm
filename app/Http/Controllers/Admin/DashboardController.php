<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Picture;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Display the executive blog admin dashboard with real-time stats and metrics.
     */
    public function index(): View
    {
        $totalPosts = Post::count();
        $publishedPosts = Post::where('status', true)->count();
        $draftPosts = Post::where('status', false)->count();
        $totalCategories = Category::count();
        $totalTags = Tag::count();
        $totalComments = Comment::count();
        $totalMedia = Picture::count();

        // Recent posts with relationships
        $recentPosts = Post::with(['category', 'author.user', 'image', 'tag', 'comments'])
            ->latest()
            ->take(7)
            ->get();

        // Recent comments
        $recentComments = Comment::with(['post', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Top categories with post count
        $topCategories = Category::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalPosts',
            'publishedPosts',
            'draftPosts',
            'totalCategories',
            'totalTags',
            'totalComments',
            'totalMedia',
            'recentPosts',
            'recentComments',
            'topCategories'
        ));
    }
}
