<?php

namespace App\Http\Controllers;

use App\Models\CommentReply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentReplyController extends Controller
{
    /**
     * Store a newly created reply in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'comment_id' => 'required|exists:comments,id',
            'reply' => 'required|string|max:1000',
        ]);

        $validated['user_id'] = auth()->id() ?? 1;

        CommentReply::create($validated);

        return back()->with('success', 'Reply posted successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CommentReply $commentReply): RedirectResponse
    {
        $commentReply->delete();
        return back()->with('success', 'Reply deleted successfully!');
    }
}
