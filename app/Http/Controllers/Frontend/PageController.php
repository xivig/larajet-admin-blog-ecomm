<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a published static/dynamic page by slug.
     */
    public function show(Request $request, string $slug): View
    {
        $page = Page::where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        $categories = Category::withCount('posts')->take(10)->get();
        $tags = Tag::take(25)->get();

        $template = $page->template ?: 'default';
        $viewName = "blog.pages.{$template}";

        if (!view()->exists($viewName)) {
            $viewName = 'blog.pages.default';
        }

        return view($viewName, compact('page', 'categories', 'tags'));
    }
}
