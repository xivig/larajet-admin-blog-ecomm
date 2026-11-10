<?php

namespace App\Http\Controllers;

use App\Models\CommentReply;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentReplyController extends Controller
{
    /**
     * Store a freshly submitted reply to a parent comment.
     */
    public function store(Request $request): RedirectResponse
    {
        // Honeypot spam check
        if ($request->filled('website_hp')) {
            return back()->with('error', 'Spam detection triggered.');
        }

        $validated = $request->validate([
            'comment_id' => 'required|exists:comments,id',
            'reply' => 'required|string|max:1000',
            'guest_name' => 'nullable|string|max:100',
            'guest_email' => 'nullable|email|max:255',
        ]);

        $userId = auth()->id();
        $guestName = null;
        $guestEmail = null;

        if (!$userId) {
            $guestName = $request->input('guest_name') ?: 'Guest Contributor';
            $guestEmail = $request->input('guest_email') ?: 'guest@example.com';
        }

        $status = $userId || filter_var(env('COMMENTS_AUTO_APPROVE', false), FILTER_VALIDATE_BOOLEAN)
            ? 'approved'
            : 'pending';

        CommentReply::create([
            'comment_id' => $validated['comment_id'],
            'user_id' => $userId,
            'guest_name' => $guestName,
            'guest_email' => $guestEmail,
            'reply' => $validated['reply'],
            'status' => $status,
            'likes_count' => 0,
            'reports_count' => 0,
        ]);

        $flash = $status === 'approved'
            ? 'Your reply has been posted successfully!'
            : 'Your reply has been submitted and is awaiting moderation.';

        return back()->with('success', $flash);
    }

    /**
     * Like / Upvote a comment reply.
     */
    public function like(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $reply = CommentReply::findOrFail($id);
        $reply->increment('likes_count');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'likes' => $reply->likes_count,
            ]);
        }

        return back()->with('success', 'Liked reply!');
    }

    /**
     * Report / Flag a reply for moderation.
     */
    public function report(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $reply = CommentReply::findOrFail($id);
        $reply->increment('reports_count');

        if ($reply->reports_count >= 3) {
            $reply->update(['status' => 'pending']);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reply flagged for review.',
            ]);
        }

        return back()->with('info', 'Reply flagged for review.');
    }

    /**
     * Remove the specified reply.
     */
    public function destroy(CommentReply $commentReply): RedirectResponse
    {
        $commentReply->delete();
        return back()->with('success', 'Reply deleted successfully!');
    }
}
