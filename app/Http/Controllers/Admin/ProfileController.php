<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
    public function edit(): View
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('admin'));
    }

    /**
     * Update admin profile info.
     */
    public function update(Request $request): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . $admin->id,
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('admin.profile')->with('success', 'Admin profile updated successfully!');
    }

    /**
     * Update admin password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'current_password' => ['required', 'current_password:admin'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.profile')->with('success', 'Password updated successfully!');
    }
}
