<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show admin profile edit form.
     */
    public function index(Request $request): View
    {
        return $this->edit($request);
    }

    /**
     * Show admin profile edit form.
     */
    public function edit(Request $request): View
    {
        $admin = Auth::guard('admin')->user() ?? Admin::first();

        $deviceTracking = app(\App\Services\DeviceTrackingService::class);
        $activeSessions = $deviceTracking->getActiveSessions($admin?->id, $request);
        $loginHistories = $deviceTracking->getRecentHistory('admin', $admin?->id, 10);

        // QR Code SVG & Secret for 2FA setup if in progress
        $qrCodeSvg = null;
        $twoFactorSecret = null;

        if ($admin && !empty($admin->two_factor_secret) && is_null($admin->two_factor_confirmed_at)) {
            try {
                $qrCodeSvg = $admin->twoFactorQrCodeSvg();
                $twoFactorSecret = decrypt($admin->two_factor_secret);
            } catch (\Throwable $e) {
                // Handle unencrypted or mismatched key gracefully
            }
        }

        return view('admin.profile', compact(
            'admin',
            'activeSessions',
            'loginHistories',
            'qrCodeSvg',
            'twoFactorSecret'
        ));
    }

    /**
     * Resend verification email to admin.
     */
    public function sendVerification(Request $request): RedirectResponse
    {
        /** @var Admin|null $admin */
        $admin = Auth::guard('admin')->user();
        if ($admin && !$admin->hasVerifiedEmail()) {
            $admin->sendEmailVerificationNotification();
            return redirect()->route('admin.profile')
                ->with('success', 'A fresh verification link has been sent to ' . $admin->email);
        }

        return redirect()->route('admin.profile')
            ->with('info', 'Your email address is already verified.');
    }

    /**
     * Update admin profile info including avatar image.
     */
    public function update(Request $request): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            $admin = Admin::first();
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . ($admin ? $admin->id : 0),
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;

        if ($request->hasFile('avatar')) {
            $avatarDir = public_path('uploads/avatars');
            if (!file_exists($avatarDir)) {
                mkdir($avatarDir, 0755, true);
            }

            $file = $request->file('avatar');
            $fileName = 'admin_' . $admin->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($avatarDir, $fileName);
            $admin->profile_photo_path = 'uploads/avatars/' . $fileName;
        }

        $admin->save();

        return redirect()->route('admin.profile')->with('success', 'Admin profile and avatar updated successfully!');
    }

    /**
     * Update admin password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            $admin = Admin::first();
        }

        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($admin) {
                if (!Hash::check($value, $admin->password)) {
                    $fail('The provided current password does not match our records.');
                }
            }],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $admin->password = Hash::make($request->password);
        $admin->save();

        return redirect()->route('admin.profile')->with('success', 'Password updated successfully!');
    }
}
