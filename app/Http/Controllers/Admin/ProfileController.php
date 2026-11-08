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
    public function edit(): View
    {
        $admin = Auth::guard('admin')->user() ?? Admin::first();
        return view('admin.profile', compact('admin'));
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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
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
