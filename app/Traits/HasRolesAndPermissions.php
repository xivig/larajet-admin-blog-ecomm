<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRolesAndPermissions
{
    /**
     * The roles assigned to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * Check if the model has a specific role or any of the given roles.
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }

        $roles = array_map(fn ($r) => strtolower(trim($r)), $roles);

        // Check model's direct role attribute if present
        if (!empty($this->role) && in_array(strtolower($this->role), $roles, true)) {
            return true;
        }

        // If this model is an Admin guard instance, default treat as admin or super-admin
        if ($this instanceof \App\Models\Admin) {
            if (in_array('admin', $roles, true) || in_array('super-admin', $roles, true)) {
                return true;
            }
        }

        // Check assigned roles relationship
        return $this->roles->contains(function (Role $role) use ($roles) {
            return in_array(strtolower($role->slug), $roles, true) || in_array(strtolower($role->name), $roles, true);
        });
    }

    /**
     * Check if the user is a Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(['super-admin', 'super administrator']);
    }

    /**
     * Check if the user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        // Super Admin always bypasses all permission checks
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Admins have access to all modules unless explicitly restricted
        if ($this instanceof \App\Models\Admin || $this->hasRole('admin')) {
            return true;
        }

        return $this->allPermissions()->contains($permission);
    }

    /**
     * Check if the user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isSuperAdmin() || $this instanceof \App\Models\Admin || $this->hasRole('admin')) {
            return true;
        }

        $userPerms = $this->allPermissions();
        foreach ($permissions as $perm) {
            if ($userPerms->contains($perm)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all unique permission slugs assigned to the user through their roles.
     */
    public function allPermissions(): Collection
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->flatMap(function (Role $role) {
                return $role->permissions->pluck('slug');
            })->unique();
        }

        return $this->roles()->with('permissions')->get()->flatMap(function (Role $role) {
            return $role->permissions->pluck('slug');
        })->unique();
    }
}
