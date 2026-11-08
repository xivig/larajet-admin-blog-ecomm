<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Display the form to request a password reset link.
     */
    public function showLinkRequestForm(): View
    {
        return view('admin.auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
        ], [
            'email.exists' => 'No administrator account found with that email address.',
        ]);

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        $resetUrl = route('admin.password.reset', ['token' => $token, 'email' => $request->email]);

        // Provide easy reset action in development/session flash
        return back()->with('status', 'A password reset token has been generated! For instant testing, you can use: ' . $resetUrl)
                     ->with('reset_url', $resetUrl);
    }

    /**
     * Display the password reset view for the given token.
     */
    public function showResetForm(Request $request, ?string $token = null): View
    {
        return view('admin.auth.reset-password', [
            'token' => $token ?? $request->route('token'),
            'email' => $request->email,
        ]);
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:admins,email',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'Invalid or expired password reset token.']);
        }

        // Verify token
        if (!Hash::check($request->token, $record->token) && $request->token !== $record->token) {
            return back()->withErrors(['token' => 'Invalid password reset token.']);
        }

        // Update admin password
        $admin = Admin::where('email', $request->email)->first();
        if ($admin) {
            $admin->password = Hash::make($request->password);
            $admin->save();
        }

        // Delete reset token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('admin.login')->with('success', 'Your password has been successfully reset! Please sign in with your new credentials.');
    }
}
