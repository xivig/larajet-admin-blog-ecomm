<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('admin.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(AdminLoginRequest $request): RedirectResponse
    {
        $admin = $request->authenticate();

        if ($admin->hasEnabledTwoFactorAuthentication()) {
            $request->session()->put([
                'admin.2fa.id' => $admin->id,
                'admin.2fa.remember' => $request->boolean('remember'),
            ]);

            return redirect()->route('admin.two-factor.challenge');
        }

        Auth::guard('admin')->login($admin, $request->boolean('remember'));

        $request->session()->regenerate();

        // Forget any stale intended URL that might point back to login
        $request->session()->forget('url.intended');

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
