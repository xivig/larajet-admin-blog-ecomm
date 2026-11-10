<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Display the admin chat interface.
     */
    public function index(Request $request): View
    {
        $currentChannel = $request->input('channel', 'general');
        $receiverId = $request->input('user_id');

        $users = User::latest()->take(20)->get();

        $messagesQuery = ChatMessage::with(['sender', 'receiver'])->latest();

        if ($receiverId) {
            $currentReceiver = User::find($receiverId);
            $currentUserId = auth()->id() ?: 1;
            $messagesQuery->where(function ($q) use ($currentUserId, $receiverId) {
                $q->where('sender_id', $currentUserId)->where('receiver_id', $receiverId);
            })->orWhere(function ($q) use ($currentUserId, $receiverId) {
                $q->where('sender_id', $receiverId)->where('receiver_id', $currentUserId);
            });
        } else {
            $currentReceiver = null;
            $messagesQuery->where('channel', $currentChannel)->whereNull('receiver_id');
        }

        $messages = $messagesQuery->take(50)->get()->reverse();

        $channels = [
            'general' => ['name' => '# General Workspace', 'desc' => 'All-team announcements & discussions', 'icon' => 'bi-hash'],
            'engineering' => ['name' => '# Engineering & Architecture', 'desc' => 'Technical blueprints & stack discussions', 'icon' => 'bi-code-slash'],
            'editorial' => ['name' => '# Editorial Pipeline', 'desc' => 'Blog publications & content review', 'icon' => 'bi-newspaper'],
        ];

        return view('admin.chat.index', compact('users', 'messages', 'currentChannel', 'currentReceiver', 'channels'));
    }

    /**
     * Send a new message.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'message' => 'required|string|max:3000',
            'channel' => 'nullable|string',
            'receiver_id' => 'nullable|exists:users,id',
            'attachment' => ['nullable', 'file', 'max:10240', \App\Services\SecurityFileGuard::mediaMimeRule()],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            if (!$file->isValid() || !\App\Services\SecurityFileGuard::isSafeUploadedFile($file, \App\Services\SecurityFileGuard::MEDIA_EXTENSIONS)) {
                return back()->with('error', 'Attachment type is not permitted.');
            }
            $dir = public_path('uploads/chat');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $fileName = \App\Services\SecurityFileGuard::safeStoredFilename(
                (string) $file->getClientOriginalName(),
                (string) $file->getClientOriginalExtension()
            );
            $file->move($dir, $fileName);
            $attachmentPath = 'uploads/chat/' . $fileName;
        }

        ChatMessage::create([
            'sender_id' => auth()->id() ?: 1,
            'receiver_id' => $request->receiver_id ?: null,
            'channel' => $request->receiver_id ? ($request->channel ?: 'direct') : ($request->channel ?: 'general'),
            'message' => $request->message,
            'attachment' => $attachmentPath,
        ]);

        if ($request->receiver_id) {
            return redirect()->route('admin.chat.index', ['user_id' => $request->receiver_id])->with('success', 'Message sent!');
        }

        return redirect()->route('admin.chat.index', ['channel' => $request->channel ?: 'general']);
    }

    /**
     * API to fetch latest messages for polling.
     */
    public function apiMessages(Request $request): JsonResponse
    {
        $channel = $request->input('channel', 'general');
        $receiverId = $request->input('user_id');

        $query = ChatMessage::with('sender')->latest();

        if ($receiverId) {
            $currentUserId = auth()->id() ?: 1;
            $query->where(function ($q) use ($currentUserId, $receiverId) {
                $q->where('sender_id', $currentUserId)->where('receiver_id', $receiverId);
            })->orWhere(function ($q) use ($currentUserId, $receiverId) {
                $q->where('sender_id', $receiverId)->where('receiver_id', $currentUserId);
            });
        } else {
            $query->where('channel', $channel)->whereNull('receiver_id');
        }

        $messages = $query->take(30)->get()->reverse()->values();

        return response()->json([
            'status' => 'success',
            'messages' => $messages,
        ]);
    }

    /**
     * Alias for route admin.chat.messages
     */
    public function fetchMessages(Request $request): JsonResponse
    {
        return $this->apiMessages($request);
    }
}
