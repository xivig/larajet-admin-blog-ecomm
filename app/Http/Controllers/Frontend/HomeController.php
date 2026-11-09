<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the frontend home page.
     */
    public function index(Request $request): View
    {
        $featuredPosts = Post::where('status', true)
            ->latest()
            ->take(6)
            ->get();

        $categories = Category::withCount('posts')
            ->having('posts_count', '>', 0)
            ->take(8)
            ->get();

        return view('welcome', compact('featuredPosts', 'categories'));
    }
}
