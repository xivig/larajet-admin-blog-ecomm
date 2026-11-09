<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\DeviceTrackingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SessionController extends Controller
{
    public function __construct(
        protected DeviceTrackingService $deviceTrackingService
    ) {
    }

    /**
     * Terminate a specific browser session.
     */
    public function destroy(Request $request, string $sessionId): RedirectResponse
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        // Prevent accidentally destroying current session through this route
        if ($sessionId === $request->session()->getId()) {
            return redirect()->route('admin.profile', ['tab' => 'sessions'])
                ->withErrors(['session' => 'Cannot terminate your current active session from here. Please use Log Out instead.']);
        }

        $this->deviceTrackingService->terminateSession($sessionId, $admin->id);

        return redirect()->route('admin.profile', ['tab' => 'sessions'])
            ->with('success', 'Selected device session has been revoked.');
    }

    /**
     * Terminate all other browser sessions across all devices for the admin.
     */
    public function logoutOthers(Request $request): RedirectResponse
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (!Hash::check($request->password, $admin->password)) {
            return redirect()->route('admin.profile', ['tab' => 'sessions'])
                ->withErrors(['logout_password' => 'The provided password does not match our records.']);
        }

        $count = $this->deviceTrackingService->terminateOtherSessions($admin->id, $request->session()->getId());

        return redirect()->route('admin.profile', ['tab' => 'sessions'])
            ->with('success', "Successfully terminated {$count} other active device session(s).");
    }
}
