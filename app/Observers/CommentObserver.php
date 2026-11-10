<?php

namespace App\Observers;

use App\Models\AdminNotification;
use App\Models\Comment;
use App\Notifications\NewCommentNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class CommentObserver
{
    /**
     * Handle the Comment "created" event.
     */
    public function created(Comment $comment): void
    {
        try {
            $commenter = $comment->user?->name ?? $comment->guest_name ?? 'Reader';
            $postTitle = $comment->post?->title ?? 'an article';

            AdminNotification::create([
                'user_id' => $comment->user_id,
                'type' => 'comment',
                'title' => 'New Reader Comment',
                'message' => "{$commenter} commented on \"" . Str::limit($postTitle, 45) . "\".",
                'link' => '/admin/comments',
                'is_read' => false,
            ]);
        } catch (\Throwable $e) {
            // Never block comment creation if notification storage fails
        }

        // Notify post author if exists
        try {
            $authorUser = $comment->post?->user ?? User::where('role', 'admin')->first();

            if ($authorUser) {
                $authorUser->notify(new NewCommentNotification($comment));
            }
        } catch (\Throwable $e) {
            // Ignore queue/mailer errors during comment creation
        }
    }
}
