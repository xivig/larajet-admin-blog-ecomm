<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display all roles and permissions.
     */
    public function index(Request $request): View
    {
        $roles = Role::with(['permissions', 'users'])->get();
        $selectedRoleId = $request->query('role', $roles->first()?->id);
        $currentRole = $roles->firstWhere('id', $selectedRoleId) ?? $roles->first();
        $permissions = Permission::all()->groupBy('module');
        $users = User::all();

        return view('admin.roles.index', compact('roles', 'currentRole', 'permissions', 'users'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'color' => 'required|string|in:primary,success,info,warning,danger,secondary',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'color' => $request->color,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role "' . $role->name . '" created successfully!');
    }

    /**
     * Update an existing role and its permissions.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'color' => 'required|string|in:primary,success,info,warning,danger,secondary',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
        ]);

        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('admin.roles.index')->with('success', 'Role "' . $role->name . '" updated successfully!');
    }

    /**
     * Assign role to user.
     */
    public function assignUser(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $role = Role::findOrFail($request->role_id);

        $user->roles()->syncWithoutDetaching([$role->id]);

        return redirect()->route('admin.roles.index', ['role' => $role->id])->with('success', 'User ' . $user->name . ' assigned to ' . $role->name . '.');
    }

    /**
     * Remove role from user.
     */
    public function removeUser(Request $request, Role $role): RedirectResponse
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $role->users()->detach($request->user_id);
        return redirect()->route('admin.roles.index', ['role' => $role->id])->with('success', 'User removed from ' . $role->name . '.');
    }

    /**
     * Delete a role.
     */
    public function destroy(Role $role): RedirectResponse
    {
        if ($role->slug === 'super-admin') {
            return back()->with('error', 'Super Administrator role cannot be deleted.');
        }

        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
