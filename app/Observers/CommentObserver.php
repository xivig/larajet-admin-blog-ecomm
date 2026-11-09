<?php

namespace App\Observers;

use App\Models\Comment;
use App\Notifications\NewCommentNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

class CommentObserver
{
    /**
     * Handle the Comment "created" event.
     */
    public function created(Comment $comment): void
    {
        // Notify post author if exists
        $authorUser = $comment->post?->user ?? User::where('role', 'admin')->first();

        if ($authorUser) {
            $authorUser->notify(new NewCommentNotification($comment));
        }
    }
}
