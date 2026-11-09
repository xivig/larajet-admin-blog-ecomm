<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\RecoveryCode;

class TwoFactorController extends Controller
{
    public function __construct(
        protected TwoFactorAuthenticationProvider $provider
    ) {
    }

    /**
     * Enable Two-Factor Authentication (Generate TOTP secret & temporary recovery codes).
     */
    public function enable(Request $request): RedirectResponse
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        // Generate Base32 secret key
        $secret = $this->provider->generateSecretKey();

        // Generate 8 emergency recovery codes
        $recoveryCodes = Collection::times(8, fn () => RecoveryCode::generate())->all();

        // Store encrypted in admin record as unconfirmed
        $admin->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
            'two_factor_confirmed_at' => null,
        ])->save();

        return redirect()->route('admin.profile', ['tab' => 'security'])
            ->with('status', 'two-factor-authentication-enabling')
            ->with('success', 'Two-Factor Authentication initiated. Scan the QR code with Google Authenticator and enter your 6-digit verification code.');
    }

    /**
     * Confirm Two-Factor Authentication with 6-digit TOTP code.
     */
    public function confirm(Request $request): RedirectResponse
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();
        if (!$admin || empty($admin->two_factor_secret)) {
            return redirect()->route('admin.profile');
        }

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $decryptedSecret = decrypt($admin->two_factor_secret);

        if (!$this->provider->verify($decryptedSecret, $request->code)) {
            return redirect()->route('admin.profile', ['tab' => 'security'])
                ->with('status', 'two-factor-authentication-enabling')
                ->withErrors(['code' => 'The provided Two-Factor Authentication code is invalid. Please ensure your authenticator app time is synchronized.']);
        }

        $admin->forceFill([
            'two_factor_confirmed_at' => now(),
        ])->save();

        return redirect()->route('admin.profile', ['tab' => 'security'])
            ->with('success', 'Google Authenticator Two-Factor Authentication confirmed and activated successfully! Store your recovery codes in a safe location.');
    }

    /**
     * Regenerate new Two-Factor emergency recovery codes.
     */
    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();
        if (!$admin || !$admin->hasEnabledTwoFactorAuthentication()) {
            return redirect()->route('admin.profile');
        }

        $recoveryCodes = Collection::times(8, fn () => RecoveryCode::generate())->all();

        $admin->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ])->save();

        return redirect()->route('admin.profile', ['tab' => 'security'])
            ->with('success', 'New emergency recovery codes have been generated successfully.');
    }

    /**
     * Download emergency recovery codes as a text file.
     */
    public function downloadRecoveryCodes(Request $request): Response
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();
        if (!$admin || !$admin->hasEnabledTwoFactorAuthentication()) {
            abort(403);
        }

        $codes = $admin->recoveryCodes();
        $content = "XIVIGADMIN ENTERPRISE - TWO-FACTOR EMERGENCY RECOVERY CODES\n";
        $content .= "Generated: " . now()->toDateTimeString() . "\n";
        $content .= "Account: " . $admin->email . "\n";
        $content .= "--------------------------------------------------------\n";
        $content .= "Each code can only be used once. Keep them in a secure place.\n\n";

        foreach ($codes as $index => $code) {
            $content .= sprintf("[%d] %s\n", $index + 1, $code);
        }

        return response($content, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="xivig-admin-recovery-codes.txt"',
        ]);
    }

    /**
     * Disable Two-Factor Authentication.
     */
    public function disable(Request $request): RedirectResponse
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'current_password' => ['required', 'string'],
        ]);

        if (!Hash::check($request->current_password, $admin->password)) {
            return redirect()->route('admin.profile', ['tab' => 'security'])
                ->withErrors(['current_password' => 'The provided password does not match our records.']);
        }

        $admin->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return redirect()->route('admin.profile', ['tab' => 'security'])
            ->with('success', 'Two-Factor Authentication has been successfully disabled.');
    }
}
