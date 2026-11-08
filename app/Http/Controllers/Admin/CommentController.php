<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentReply;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of comments for moderation.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status');

        if ($status === 'trash') {
            $query = Comment::onlyTrashed()->with(['post', 'user', 'replies.user'])->latest();
        } else {
            $query = Comment::with(['post', 'user', 'replies.user'])->latest();

            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('post', function ($pq) use ($search) {
                      $pq->where('title', 'like', "%{$search}%");
                  });
            });
        }

        $comments = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => Comment::count(),
            'approved' => Comment::where('status', 'approved')->count(),
            'pending' => Comment::where('status', 'pending')->count(),
            'spam' => Comment::where('status', 'spam')->count(),
            'trash' => Comment::onlyTrashed()->count(),
        ];

        return view('admin.comments.index', compact('comments', 'counts'));
    }

    /**
     * Approve a comment.
     */
    public function approve(Comment $comment): RedirectResponse
    {
        $comment->update(['status' => 'approved']);
        return back()->with('success', 'Comment marked as approved.');
    }

    /**
     * Mark a comment as spam or pending.
     */
    public function setStatus(Request $request, Comment $comment): RedirectResponse
    {
        $status = $request->input('status', 'pending');
        if (in_array($status, ['approved', 'pending', 'spam'])) {
            $comment->update(['status' => $status]);
        }
        return back()->with('success', 'Comment status updated to ' . ucfirst($status) . '.');
    }

    /**
     * Update comment content.
     */
    public function update(Request $request, Comment $comment): RedirectResponse
    {
        $request->validate([
            'comment' => 'required|string|max:2000',
            'status' => 'nullable|string|in:approved,pending,spam',
        ]);

        $comment->update([
            'comment' => $request->comment,
            'status' => $request->status ?: $comment->status,
        ]);

        return back()->with('success', 'Comment updated successfully.');
    }

    /**
     * Reply to a comment as administrator.
     */
    public function reply(Request $request, Comment $comment): RedirectResponse
    {
        $request->validate([
            'reply' => 'required|string|max:1000',
        ]);

        CommentReply::create([
            'user_id' => auth()->id() ?: 1,
            'comment_id' => $comment->id,
            'reply' => $request->reply,
        ]);

        return back()->with('success', 'Reply submitted successfully.');
    }

    /**
     * Move the specified comment to trash (Soft Delete).
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('success', 'Comment moved to trash.');
    }

    /**
     * Restore a soft-deleted comment from trash.
     */
    public function restore(int|string $id): RedirectResponse
    {
        $comment = Comment::onlyTrashed()->findOrFail($id);
        $comment->restore();

        return back()->with('success', 'Comment restored successfully.');
    }

    /**
     * Permanently remove a comment from storage.
     */
    public function forceDelete(int|string $id): RedirectResponse
    {
        $comment = Comment::withTrashed()->findOrFail($id);
        $comment->replies()->forceDelete();
        $comment->forceDelete();

        return back()->with('success', 'Comment permanently deleted.');
    }

    /**
     * Bulk actions for multiple comments.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:approve,pending,spam,delete,restore,force_delete',
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $ids = $request->ids;
        $action = $request->action;

        if ($action === 'delete') {
            Comment::whereIn('id', $ids)->delete();
            $msg = count($ids) . ' comments moved to trash.';
        } elseif ($action === 'restore') {
            Comment::onlyTrashed()->whereIn('id', $ids)->restore();
            $msg = count($ids) . ' comments restored from trash.';
        } elseif ($action === 'force_delete') {
            CommentReply::whereIn('comment_id', $ids)->forceDelete();
            Comment::onlyTrashed()->whereIn('id', $ids)->forceDelete();
            $msg = count($ids) . ' comments permanently deleted.';
        } else {
            $statusMap = ['approve' => 'approved', 'pending' => 'pending', 'spam' => 'spam'];
            Comment::whereIn('id', $ids)->update(['status' => $statusMap[$action]]);
            $msg = count($ids) . ' comments marked as ' . $statusMap[$action] . '.';
        }

        return back()->with('success', $msg);
    }
}
