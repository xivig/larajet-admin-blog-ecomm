<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of registered users with role and authority footprints.
     */
    public function index(): View
    {
        $users = User::with(['roles', 'author'])->withCount(['posts', 'comments'])->latest()->get();
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user with initial authority.
     */
    public function create(): View
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage with assigned authority.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'is_author' => 'nullable',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
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

        $roleIds = $request->input('roles', []);
        if (empty($roleIds)) {
            $subscriberRole = Role::firstOrCreate(
                ['slug' => 'subscriber'],
                [
                    'name' => 'Subscriber',
                    'description' => 'Standard community member and subscriber with basic read and comment access.',
                    'color' => 'secondary',
                ]
            );
            $roleIds = [$subscriberRole->id];
        }
        $user->roles()->sync($roleIds);

        if ($request->has('is_author')) {
            Author::create([
                'user_id' => $user->id,
                'nickname' => $user->name,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User "' . $user->name . '" created successfully with configured authority!');
    }

    /**
     * Display detailed profile, authority audit, and publication footprint for the specified user.
     */
    public function show(User $user): View
    {
        $user->load([
            'roles.permissions',
            'author',
            'posts' => function ($q) {
                $q->withTrashed()->with('category')->latest()->take(10);
            },
            'comments' => function ($q) {
                $q->withTrashed()->with('post')->latest()->take(10);
            }
        ])->loadCount(['posts', 'comments']);

        $roles = Role::all();
        $otherUsers = User::where('id', '!=', $user->id)->orderBy('name')->get();

        return view('admin.users.show', compact('user', 'roles', 'otherUsers'));
    }

    /**
     * Show the form for editing the specified user and their security authority.
     */
    public function edit(User $user): View
    {
        $user->load(['roles', 'author'])->loadCount(['posts', 'comments']);
        $roles = Role::all();
        $otherUsers = User::where('id', '!=', $user->id)->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles', 'otherUsers'));
    }

    /**
     * Update the specified user in storage, including security roles and author status.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'is_author' => 'nullable',
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

        // Update security roles
        $user->roles()->sync($request->input('roles', []));

        // Manage Author profile
        $isAuthor = $request->boolean('is_author');
        if ($isAuthor) {
            if (!$user->author) {
                // Reconnect previously detached author if available, or create new
                $existingAuthor = Author::where('former_user_id', $user->id)->whereNull('user_id')->first();
                if (!$existingAuthor) {
                    $prevAuthorId = Post::withTrashed()->where('user_id', $user->id)->value('author_id');
                    if ($prevAuthorId) {
                        $existingAuthor = Author::where('id', $prevAuthorId)->whereNull('user_id')->first();
                    }
                }
                if (!$existingAuthor) {
                    $existingAuthor = Author::where('nickname', $user->name)->whereNull('user_id')->first();
                }

                if ($existingAuthor) {
                    $existingAuthor->update([
                        'user_id' => $user->id,
                        'former_user_id' => null,
                    ]);
                } else {
                    Author::create([
                        'user_id' => $user->id,
                        'nickname' => $user->name,
                    ]);
                }
            }
        } else {
            if ($user->author) {
                $hasPosts = Post::withTrashed()->where('author_id', $user->author->id)->exists();
                if ($hasPosts) {
                    // Detach author from user to revoke author status, while preserving historical post attribution
                    $user->author->update([
                        'former_user_id' => $user->id,
                        'user_id' => null,
                    ]);
                } else {
                    $user->author->delete();
                }
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'User account and authority updated successfully!');
    }

    /**
     * Reassign authority (roles, permissions, authored publications, and discussions) from or to a user.
     */
    public function reassignAuthority(Request $request, User $user): RedirectResponse
    {
        $action = $request->input('action_type', 'transfer');

        if ($action === 'roles_only') {
            $request->validate([
                'roles' => 'nullable|array',
                'roles.*' => 'exists:roles,id',
                'is_author' => 'nullable',
            ]);

            $user->roles()->sync($request->input('roles', []));

            $isAuthor = $request->boolean('is_author');
            if ($isAuthor) {
                if (!$user->author) {
                    $existingAuthor = Author::where('former_user_id', $user->id)->whereNull('user_id')->first();
                    if (!$existingAuthor) {
                        $prevAuthorId = Post::withTrashed()->where('user_id', $user->id)->value('author_id');
                        if ($prevAuthorId) {
                            $existingAuthor = Author::where('id', $prevAuthorId)->whereNull('user_id')->first();
                        }
                    }
                    if (!$existingAuthor) {
                        $existingAuthor = Author::where('nickname', $user->name)->whereNull('user_id')->first();
                    }

                    if ($existingAuthor) {
                        $existingAuthor->update([
                            'user_id' => $user->id,
                            'former_user_id' => null,
                        ]);
                    } else {
                        Author::create([
                            'user_id' => $user->id,
                            'nickname' => $user->name,
                        ]);
                    }
                }
            } else {
                if ($user->author) {
                    $hasPosts = Post::withTrashed()->where('author_id', $user->author->id)->exists();
                    if ($hasPosts) {
                        $user->author->update([
                            'former_user_id' => $user->id,
                            'user_id' => null,
                        ]);
                    } else {
                        $user->author->delete();
                    }
                }
            }

            return back()->with('success', "Authority roles and contributor access updated for \"{$user->name}\".");
        }

        // Full Authority & Content Transfer
        $request->validate([
            'target_user_id' => 'required|exists:users,id',
            'transfer_posts' => 'nullable|boolean',
            'transfer_comments' => 'nullable|boolean',
            'copy_roles' => 'nullable|boolean',
            'delete_source_user' => 'nullable|boolean',
        ]);

        $targetUser = User::findOrFail($request->target_user_id);

        if ($targetUser->id === $user->id) {
            return back()->with('error', 'Cannot reassign authority to the exact same user account.');
        }

        $transferredPosts = 0;
        $transferredComments = 0;

        DB::transaction(function () use ($request, $user, $targetUser, &$transferredPosts, &$transferredComments) {
            // 1. Ensure target user has an Author profile
            $targetAuthor = $targetUser->author;
            if (!$targetAuthor) {
                $targetAuthor = Author::create([
                    'user_id' => $targetUser->id,
                    'nickname' => $targetUser->name,
                ]);
            }

            // 2. Transfer Authored Publications
            if ($request->input('transfer_posts', true)) {
                $sourceAuthorId = $user->author?->id;

                $postQuery = Post::withTrashed()->where(function ($q) use ($user, $sourceAuthorId) {
                    $q->where('user_id', $user->id);
                    if ($sourceAuthorId) {
                        $q->orWhere('author_id', $sourceAuthorId);
                    }
                });

                $transferredPosts = $postQuery->count();

                $postQuery->update([
                    'user_id' => $targetUser->id,
                    'author_id' => $targetAuthor->id,
                ]);
            }

            // 3. Transfer Discussions / Comments
            if ($request->input('transfer_comments', true)) {
                $transferredComments = Comment::withTrashed()->where('user_id', $user->id)->count();
                Comment::withTrashed()->where('user_id', $user->id)->update([
                    'user_id' => $targetUser->id,
                ]);
            }

            // 4. Optionally copy security roles
            if ($request->input('copy_roles', false) && $user->roles()->exists()) {
                $targetUser->roles()->syncWithoutDetaching($user->roles->pluck('id')->toArray());
            }

            // 5. Optionally delete source user
            if ($request->input('delete_source_user', false)) {
                $user->roles()->detach();
                if ($user->author) {
                    $user->author->delete();
                }
                $user->delete();
            }
        });

        if ($request->input('delete_source_user', false)) {
            return redirect()->route('admin.users.index')->with(
                'success',
                "Authority and publications ({$transferredPosts} article(s)" . ($transferredComments ? ", {$transferredComments} comment(s)" : "") . ") successfully reassigned to \"{$targetUser->name}\", and account for \"{$user->name}\" was safely decommissioned."
            );
        }

        return back()->with(
            'success',
            "Authority and {$transferredPosts} article(s)" . ($transferredComments ? " and {$transferredComments} comment(s)" : "") . " successfully reassigned from \"{$user->name}\" to \"{$targetUser->name}\"."
        );
    }

    /**
     * Remove the specified user from storage with safe authority reassignment verification.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        $name = $user->name;

        DB::transaction(function () use ($request, $user) {
            // Find a recipient user to prevent orphan database foreign key issues
            $targetUser = null;
            if ($request->filled('reassign_to_user_id')) {
                $targetUser = User::find($request->reassign_to_user_id);
            }
            if (!$targetUser) {
                $targetUser = User::where('id', '!=', $user->id)->first();
            }

            if ($targetUser) {
                $targetAuthor = $targetUser->author ?: Author::create([
                    'user_id' => $targetUser->id,
                    'nickname' => $targetUser->name,
                ]);

                $sourceAuthorId = $user->author?->id;
                Post::withTrashed()
                    ->where('user_id', $user->id)
                    ->orWhere(function ($q) use ($sourceAuthorId) {
                        if ($sourceAuthorId) {
                            $q->where('author_id', $sourceAuthorId);
                        }
                    })->update([
                        'user_id' => $targetUser->id,
                        'author_id' => $targetAuthor->id,
                    ]);

                Comment::withTrashed()->where('user_id', $user->id)->update([
                    'user_id' => $targetUser->id,
                ]);
            } else {
                $user->comments()->delete();
            }

            $user->roles()->detach();
            if ($user->author) {
                $user->author()->delete();
            }
            $user->delete();
        });

        return redirect()->route('admin.users.index')->with('success', 'User "' . $name . '" deleted successfully!');
    }
}
