<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\DeviceTrackingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;

class TwoFactorChallengeController extends Controller
{
    public function __construct(
        protected TwoFactorAuthenticationProvider $provider,
        protected DeviceTrackingService $deviceTrackingService
    ) {
    }

    /**
     * Show the Two-Factor Challenge view for Admin.
     */
    public function create(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('admin.2fa.id')) {
            return redirect()->route('admin.login');
        }

        return view('admin.auth.two-factor-challenge');
    }

    /**
     * Verify the 6-digit TOTP code or recovery code.
     */
    public function store(Request $request): RedirectResponse
    {
        $adminId = $request->session()->get('admin.2fa.id');
        if (!$adminId) {
            return redirect()->route('admin.login');
        }

        /** @var Admin|null $admin */
        $admin = Admin::find($adminId);
        if (!$admin) {
            $request->session()->forget(['admin.2fa.id', 'admin.2fa.remember']);
            return redirect()->route('admin.login');
        }

        // Validate 6-digit code or recovery code
        if ($code = $request->input('code')) {
            $code = str_replace(' ', '', $code);
            $secret = decrypt($admin->two_factor_secret);

            if (!$this->provider->verify($secret, $code)) {
                throw ValidationException::withMessages([
                    'code' => ['The provided authentication code is invalid.'],
                ]);
            }
        } elseif ($recoveryCode = $request->input('recovery_code')) {
            $recoveryCode = str_replace(' ', '', $recoveryCode);
            $validCode = collect($admin->recoveryCodes())->first(function ($code) use ($recoveryCode) {
                return hash_equals($code, $recoveryCode);
            });

            if (!$validCode) {
                throw ValidationException::withMessages([
                    'recovery_code' => ['The provided emergency recovery code is invalid.'],
                ]);
            }

            // Consume the recovery code so it cannot be reused
            $admin->replaceRecoveryCode($validCode);
        } else {
            throw ValidationException::withMessages([
                'code' => ['Please enter your 6-digit Google Authenticator code or an emergency recovery code.'],
            ]);
        }

        $remember = $request->session()->pull('admin.2fa.remember', false);
        $request->session()->forget('admin.2fa.id');

        Auth::guard('admin')->login($admin, $remember);
        $request->session()->regenerate();

        $this->deviceTrackingService->recordLogin(
            'admin',
            $admin->id,
            $admin->email,
            $request,
            'successful'
        );

        return redirect()->intended(route('admin.dashboard'));
    }
}
