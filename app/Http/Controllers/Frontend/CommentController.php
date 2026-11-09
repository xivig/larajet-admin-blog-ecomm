<?php

namespace App\Http\Controllers\Frontend;

use App\Events\CommentSubmitted;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\CommentStoreRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a freshly submitted comment.
     */
    public function store(CommentStoreRequest $request, int $postId): RedirectResponse
    {
        $post = Post::findOrFail($postId);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'name' => $request->input('name') ?? Auth::user()?->name ?? 'Guest Reader',
            'email' => $request->input('email') ?? Auth::user()?->email ?? 'guest@example.com',
            'comment' => $request->validated('comment'),
            'status' => 'pending',
        ]);

        event(new CommentSubmitted($comment));

        return back()->with('success', 'Your comment has been submitted for moderation.');
    }
}
