<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    /**
     * Display the notification center.
     */
    public function index(Request $request): View
    {
        $query = AdminNotification::latest();

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('filter') && $request->filter === 'unread') {
            $query->where('is_read', false);
        }

        $notifications = $query->paginate(15)->withQueryString();

        $unreadCount = AdminNotification::where('is_read', false)->count();
        $totalCount = AdminNotification::count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount', 'totalCount'));
    }

    /**
     * Live JSON feed for the admin header notification bell badge & dropdown.
     */
    public function feed(): JsonResponse
    {
        $unreadCount = AdminNotification::where('is_read', false)->count();
        $notifications = AdminNotification::latest()->take(5)->get()->map(function (AdminNotification $notif) {
            return [
                'id' => $notif->id,
                'type' => $notif->type,
                'title' => $notif->title,
                'message' => Str::limit($notif->message ?? '', 60),
                'is_read' => (bool) $notif->is_read,
                'read_url' => route('admin.notifications.read', $notif->id),
                'time_ago' => $notif->created_at ? $notif->created_at->diffForHumans() : 'Just now',
            ];
        });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a notification as read and redirect if link exists.
     */
    public function markAsRead(AdminNotification $notification): RedirectResponse
    {
        $notification->update(['is_read' => true]);

        if ($notification->link) {
            return redirect($notification->link);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): RedirectResponse|JsonResponse
    {
        AdminNotification::where('is_read', false)->update(['is_read' => true]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'ok',
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Remove a notification.
     */
    public function destroy(AdminNotification $notification): RedirectResponse
    {
        $notification->delete();
        return back()->with('success', 'Notification dismissed.');
    }

    /**
     * Clear all read notifications.
     */
    public function clearRead(): RedirectResponse
    {
        AdminNotification::where('is_read', true)->delete();
        return back()->with('success', 'Read notifications cleared.');
    }
}
