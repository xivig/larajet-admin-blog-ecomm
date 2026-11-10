<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a freshly submitted comment (Authenticated or Guest).
     */
    public function store(Request $request): RedirectResponse
    {
        // Honeypot spam check
        if ($request->filled('website_hp')) {
            return back()->with('error', 'Spam detection triggered.');
        }

        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'comment' => 'required|string|max:2000',
            'guest_name' => 'nullable|string|max:100',
            'guest_email' => 'nullable|email|max:255',
            'guest_website' => 'nullable|url|max:255',
        ]);

        $userId = auth()->id();
        $guestName = null;
        $guestEmail = null;
        $guestWebsite = null;

        if (!$userId) {
            $guestName = $request->input('guest_name') ?: 'Guest Reader';
            $guestEmail = $request->input('guest_email') ?: 'guest@example.com';
            $guestWebsite = $request->input('guest_website');
        }

        $status = $userId || filter_var(env('COMMENTS_AUTO_APPROVE', false), FILTER_VALIDATE_BOOLEAN)
            ? 'approved'
            : 'pending';

        Comment::create([
            'post_id' => $validated['post_id'],
            'user_id' => $userId,
            'guest_name' => $guestName,
            'guest_email' => $guestEmail,
            'guest_website' => $guestWebsite,
            'comment' => $validated['comment'],
            'status' => $status,
            'ip_address' => $request->ip(),
            'likes_count' => 0,
            'reports_count' => 0,
        ]);

        $flash = $status === 'approved'
            ? 'Your comment has been published successfully!'
            : 'Your comment has been submitted and is awaiting moderation.';

        return back()->with('success', $flash);
    }

    /**
     * Like / Upvote a comment.
     */
    public function like(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $comment = Comment::findOrFail($id);
        $comment->increment('likes_count');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'likes' => $comment->likes_count,
            ]);
        }

        return back()->with('success', 'Thank you for your feedback!');
    }

    /**
     * Report / Flag a comment for moderation.
     */
    public function report(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $comment = Comment::findOrFail($id);
        $comment->increment('reports_count');

        // Automatic quarantine if reports reach threshold
        if ($comment->reports_count >= 3) {
            $comment->update(['status' => 'pending']);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Comment has been reported for moderation review.',
            ]);
        }

        return back()->with('info', 'Comment has been reported for moderation review.');
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted successfully!');
    }
}
