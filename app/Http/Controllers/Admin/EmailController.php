<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminEmail;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    /**
     * Display the email inbox.
     */
    public function index(Request $request): View
    {
        $folder = $request->input('folder', 'inbox');
        $query = AdminEmail::latest();

        if ($request->has('starred') && $request->starred == '1') {
            $query->where('is_starred', true);
        } else {
            $query->where('folder', $folder);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('subject', 'like', "%{$s}%")
                  ->orWhere('body', 'like', "%{$s}%")
                  ->orWhere('sender_name', 'like', "%{$s}%")
                  ->orWhere('sender_email', 'like', "%{$s}%");
            });
        }

        $emails = $query->paginate(20)->withQueryString();

        $viewEmail = null;
        if ($request->filled('view')) {
            $viewEmail = AdminEmail::find($request->view);
            if ($viewEmail) {
                $viewEmail->update(['is_read' => true]);
            }
        }

        $counts = [
            'inbox' => AdminEmail::where('folder', 'inbox')->count(),
            'unread' => AdminEmail::where('folder', 'inbox')->where('is_read', false)->count(),
            'starred' => AdminEmail::where('is_starred', true)->count(),
            'sent' => AdminEmail::where('folder', 'sent')->count(),
            'draft' => AdminEmail::where('folder', 'draft')->count(),
            'trash' => AdminEmail::where('folder', 'trash')->count(),
        ];

        return view('admin.email.index', compact('emails', 'counts', 'folder', 'viewEmail'));
    }

    /**
     * Store and send a newly composed email.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'recipient_email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'is_draft' => 'nullable|boolean',
        ]);

        $folder = $request->input('is_draft') ? 'draft' : 'sent';

        AdminEmail::create([
            'sender_name' => auth()->user()->name ?? 'Executive Administrator',
            'sender_email' => auth()->user()->email ?? 'admin@xivig.com',
            'recipient_email' => $request->recipient_email,
            'subject' => $request->subject,
            'body' => $request->body,
            'folder' => $folder,
            'is_read' => true,
        ]);

        $msg = $folder === 'draft' ? 'Email saved to drafts.' : 'Email dispatched successfully!';
        return redirect()->route('admin.email.index', ['folder' => $folder])->with('success', $msg);
    }

    /**
     * Mark an email as read and view details.
     */
    public function show(Request $request, AdminEmail $email): RedirectResponse|JsonResponse
    {
        $email->update(['is_read' => true]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'email' => $email,
            ]);
        }

        return redirect()->route('admin.email.index', ['folder' => $email->folder, 'view' => $email->id]);
    }

    /**
     * Mark an email as read via AJAX.
     */
    public function markRead(AdminEmail $email): JsonResponse
    {
        $email->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'unread_count' => AdminEmail::where('folder', 'inbox')->where('is_read', false)->count(),
        ]);
    }

    /**
     * Toggle starred status.
     */
    public function toggleStar(AdminEmail $email): RedirectResponse
    {
        $email->update(['is_starred' => !$email->is_starred]);
        return back()->with('success', 'Email ' . ($email->is_starred ? 'starred.' : 'unstarred.'));
    }

    /**
     * Move to trash or restore.
     */
    public function moveToFolder(Request $request, AdminEmail $email): RedirectResponse
    {
        $folder = $request->input('folder', 'trash');
        $email->update(['folder' => $folder]);
        return back()->with('success', 'Email moved to ' . ucfirst($folder) . '.');
    }

    /**
     * Alias for moveFolder matching admin.email.move route.
     */
    public function moveFolder(Request $request, AdminEmail $email): RedirectResponse
    {
        return $this->moveToFolder($request, $email);
    }

    /**
     * Permanently delete an email.
     */
    public function destroy(AdminEmail $email): RedirectResponse
    {
        $email->delete();
        return back()->with('success', 'Email permanently purged.');
    }
}
