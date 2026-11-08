<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
    public function markAllAsRead(): RedirectResponse
    {
        AdminNotification::where('is_read', false)->update(['is_read' => true]);
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
