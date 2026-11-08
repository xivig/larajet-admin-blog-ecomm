<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CommentController extends Controller
{
    /**
     * Display a listing of comments for moderation.
     */
    public function index(): View
    {
        $comments = Comment::with(['post', 'user', 'replies.user'])->latest()->get();
        return view('admin.comments.index', compact('comments'));
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->replies()->delete();
        $comment->delete();

        return redirect()->route('admin.comments.index')->with('success', 'Comment and its replies removed successfully!');
    }
}
