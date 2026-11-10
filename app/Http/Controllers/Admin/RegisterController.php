<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Allow initial bootstrap registration or explicit opt-in via ALLOW_ADMIN_REGISTRATION.
     */
    protected function registrationEnabled(): bool
    {
        if (Admin::count() === 0) {
            return true;
        }

        return filter_var(env('ALLOW_ADMIN_REGISTRATION', false), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Display the registration view.
     */
    public function create(): View|RedirectResponse
    {
        if (!$this->registrationEnabled()) {
            return redirect()->route('admin.login')
                ->with('error', 'Public administrator registration is disabled.');
        }

        return view('admin.auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if (!$this->registrationEnabled()) {
            abort(403, 'Public administrator registration is disabled.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.Admin::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($admin));

        Auth::guard('admin')->login($admin);

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }
}
