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
            'spam' => AdminEmail::where('folder', 'spam')->count(),
            'trash' => AdminEmail::where('folder', 'trash')->count(),
        ];

        return view('admin.email.index', compact('emails', 'counts', 'folder', 'viewEmail'));
    }

    /**
     * Store and send a newly composed email with optional attachments.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'recipient_email' => 'required|email|max:255',
            'cc_email' => 'nullable|email|max:255',
            'bcc_email' => 'nullable|email|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'attachments.*' => 'nullable|file|max:20480',
            'is_draft' => 'nullable',
        ]);

        $folder = $request->filled('is_draft') && $request->is_draft ? 'draft' : 'sent';

        $storedAttachments = [];
        if ($request->hasFile('attachments')) {
            $uploadDir = public_path('uploads/email-attachments');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($request->file('attachments') as $file) {
                if ($file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $extension = strtolower($file->getClientOriginalExtension());
                    $rawSize = $file->getSize();

                    // Format human-readable size
                    $units = ['B', 'KB', 'MB', 'GB'];
                    $pow = floor(($rawSize ? log($rawSize) : 0) / log(1024));
                    $pow = min($pow, count($units) - 1);
                    $formattedSize = round($rawSize / pow(1024, $pow), 1) . ' ' . $units[$pow];

                    $safeFileName = time() . '_' . uniqid() . '.' . $extension;
                    $file->move($uploadDir, $safeFileName);

                    $storedAttachments[] = [
                        'name' => $originalName,
                        'path' => 'uploads/email-attachments/' . $safeFileName,
                        'size' => $formattedSize,
                        'extension' => $extension,
                    ];
                }
            }
        }

        AdminEmail::create([
            'sender_name' => auth()->user()->name ?? 'Executive Administrator',
            'sender_email' => auth()->user()->email ?? 'admin@xivig.com',
            'recipient_email' => $request->recipient_email,
            'cc_email' => $request->cc_email,
            'bcc_email' => $request->bcc_email,
            'subject' => $request->subject,
            'body' => $request->body,
            'attachments' => !empty($storedAttachments) ? $storedAttachments : null,
            'folder' => $folder,
            'is_read' => true,
        ]);

        $msg = $folder === 'draft' ? 'Email draft saved successfully.' : 'Email dispatched successfully!';
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
     * Mark an email as unread via AJAX or redirect.
     */
    public function markUnread(AdminEmail $email): RedirectResponse|JsonResponse
    {
        $email->update(['is_read' => false]);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => AdminEmail::where('folder', 'inbox')->where('is_read', false)->count(),
            ]);
        }

        return redirect()->route('admin.email.index', ['folder' => $email->folder])->with('success', 'Conversation marked as unread.');
    }

    /**
     * Report an email as spam.
     */
    public function reportSpam(AdminEmail $email): RedirectResponse|JsonResponse
    {
        $email->update(['folder' => 'spam']);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Conversation moved to Spam.',
            ]);
        }

        return redirect()->route('admin.email.index', ['folder' => 'spam'])->with('success', 'Conversation moved to Spam.');
    }

    /**
     * Toggle starred status.
     */
    public function toggleStar(AdminEmail $email): RedirectResponse|JsonResponse
    {
        $email->update(['is_starred' => !$email->is_starred]);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'is_starred' => $email->is_starred,
            ]);
        }

        return back()->with('success', 'Email ' . ($email->is_starred ? 'starred.' : 'unstarred.'));
    }

    /**
     * Move to trash or restore.
     */
    public function moveToFolder(Request $request, AdminEmail $email): RedirectResponse|JsonResponse
    {
        $folder = $request->input('folder', 'trash');
        $email->update(['folder' => $folder]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'folder' => $folder,
                'message' => 'Email moved to ' . ucfirst($folder) . '.',
            ]);
        }

        return back()->with('success', 'Email moved to ' . ucfirst($folder) . '.');
    }

    /**
     * Alias for moveFolder matching admin.email.move route.
     */
    public function moveFolder(Request $request, AdminEmail $email): RedirectResponse|JsonResponse
    {
        return $this->moveToFolder($request, $email);
    }

    /**
     * Restore an email from trash back to inbox.
     */
    public function restore(AdminEmail $email): RedirectResponse|JsonResponse
    {
        $email->update(['folder' => 'inbox']);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Conversation restored to inbox.',
            ]);
        }

        return back()->with('success', 'Email restored to inbox.');
    }

    /**
     * Empty all emails from trash folder.
     */
    public function emptyTrash(): RedirectResponse
    {
        $trashedEmails = AdminEmail::where('folder', 'trash')->get();
        foreach ($trashedEmails as $email) {
            if (!empty($email->attachments) && is_array($email->attachments)) {
                foreach ($email->attachments as $att) {
                    if (isset($att['path']) && file_exists(public_path($att['path']))) {
                        @unlink(public_path($att['path']));
                    }
                }
            }
            $email->delete();
        }
        return back()->with('success', 'Trash emptied successfully.');
    }

    /**
     * Mark all emails as read.
     */
    public function markAllRead(): RedirectResponse
    {
        AdminEmail::where('folder', 'inbox')->update(['is_read' => true]);
        return back()->with('success', 'All inbox messages marked as read.');
    }

    /**
     * Handle bulk operations for selected emails.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'No conversations were selected.');
        }

        switch ($action) {
            case 'trash':
                AdminEmail::whereIn('id', $ids)->update(['folder' => 'trash']);
                return back()->with('success', count($ids) . ' conversation(s) moved to trash.');
            case 'restore':
                AdminEmail::whereIn('id', $ids)->update(['folder' => 'inbox']);
                return back()->with('success', count($ids) . ' conversation(s) restored to inbox.');
            case 'spam':
                AdminEmail::whereIn('id', $ids)->update(['folder' => 'spam']);
                return back()->with('success', count($ids) . ' conversation(s) reported as spam.');
            case 'delete':
                $emails = AdminEmail::whereIn('id', $ids)->get();
                foreach ($emails as $email) {
                    if (!empty($email->attachments) && is_array($email->attachments)) {
                        foreach ($email->attachments as $att) {
                            if (isset($att['path']) && file_exists(public_path($att['path']))) {
                                @unlink(public_path($att['path']));
                            }
                        }
                    }
                    $email->delete();
                }
                return back()->with('success', count($ids) . ' conversation(s) permanently deleted.');
            case 'archive':
                AdminEmail::whereIn('id', $ids)->update(['folder' => 'sent']);
                return back()->with('success', count($ids) . ' conversation(s) archived.');
            case 'mark_read':
                AdminEmail::whereIn('id', $ids)->update(['is_read' => true]);
                return back()->with('success', count($ids) . ' conversation(s) marked as read.');
            case 'mark_unread':
                AdminEmail::whereIn('id', $ids)->update(['is_read' => false]);
                return back()->with('success', count($ids) . ' conversation(s) marked as unread.');
            case 'star':
                AdminEmail::whereIn('id', $ids)->update(['is_starred' => true]);
                return back()->with('success', count($ids) . ' conversation(s) starred.');
            case 'unstar':
                AdminEmail::whereIn('id', $ids)->update(['is_starred' => false]);
                return back()->with('success', count($ids) . ' conversation(s) unstarred.');
            default:
                return back()->with('error', 'Invalid bulk action specified.');
        }
    }

    /**
     * Delete an email: moves to trash if not in trash yet, permanently purges if already in trash.
     */
    public function destroy(AdminEmail $email): RedirectResponse|JsonResponse
    {
        if ($email->folder === 'trash') {
            if (!empty($email->attachments) && is_array($email->attachments)) {
                foreach ($email->attachments as $att) {
                    if (isset($att['path']) && file_exists(public_path($att['path']))) {
                        @unlink(public_path($att['path']));
                    }
                }
            }
            $email->delete();

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Conversation permanently deleted.',
                ]);
            }

            return back()->with('success', 'Conversation permanently deleted.');
        }

        $email->update(['folder' => 'trash']);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Conversation moved to trash.',
            ]);
        }

        return back()->with('success', 'Conversation moved to trash.');
    }
}
