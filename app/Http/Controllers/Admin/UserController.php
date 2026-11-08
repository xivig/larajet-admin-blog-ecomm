<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of registered users.
     */
    public function index(): View
    {
        $users = User::withCount(['posts', 'comments'])->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'is_author' => 'nullable',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $profilePhotoPath = null;
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = 'user_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $fileName);
            $profilePhotoPath = 'uploads/avatars/' . $fileName;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_photo_path' => $profilePhotoPath,
        ]);

        if ($request->has('is_author')) {
            Author::create([
                'user_id' => $user->id,
                'nickname' => $user->name,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User "' . $user->name . '" created successfully!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        $user->load(['posts' => function ($q) {
            $q->latest()->take(5);
        }, 'comments' => function ($q) {
            $q->latest()->take(5);
        }]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = 'user_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $fileName);
            $data['profile_photo_path'] = 'uploads/avatars/' . $fileName;
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $name = $user->name;
        $user->comments()->delete();
        $user->author()->delete();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User "' . $name . '" deleted successfully!');
    }
}
