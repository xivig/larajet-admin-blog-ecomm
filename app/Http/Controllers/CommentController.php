<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Comment::with(['user', 'post', 'replies'])->latest()->paginate(20);
    }

    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'comment' => 'required|string|max:1000',
        ]);

        $validated['user_id'] = auth()->id() ?? 1;

        Comment::create($validated);

        return back()->with('success', 'Comment posted successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted successfully!');
    }
}
