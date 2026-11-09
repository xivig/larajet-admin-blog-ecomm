<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 0. Clean legacy roles and permissions outside the 5 roles & 10 modules
        Role::whereNotIn('slug', ['super-admin', 'admin', 'editor', 'author', 'subscriber'])->delete();

        // 1. Define the 5 Roles
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Unrestricted access to every module, database record, server settings, and system configuration.',
                'color' => 'danger',
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Complete administrative authority over content, pages, taxonomy, users, comments, settings, menu, and newsletter.',
                'color' => 'primary',
            ],
            [
                'name' => 'Editor',
                'slug' => 'editor',
                'description' => 'Full editorial control over articles, static pages, categories, media assets, comment moderation, and newsletter campaigns.',
                'color' => 'success',
            ],
            [
                'name' => 'Author',
                'slug' => 'author',
                'description' => 'Can compose, edit, and draft own articles and pages, upload media, and participate in community moderation.',
                'color' => 'info',
            ],
            [
                'name' => 'Subscriber',
                'slug' => 'subscriber',
                'description' => 'Standard community member with read privileges, discussion commenting, and newsletter subscription access.',
                'color' => 'secondary',
            ],
        ];

        $roleModels = [];
        foreach ($roles as $roleData) {
            $roleModels[$roleData['slug']] = Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }

        // 2. Define the 10 Permission Modules with Granular Permissions
        $permissions = [
            // Module 1: Posts
            ['name' => 'View Posts', 'slug' => 'posts.view', 'module' => 'Posts'],
            ['name' => 'Create Posts', 'slug' => 'posts.create', 'module' => 'Posts'],
            ['name' => 'Edit Posts', 'slug' => 'posts.edit', 'module' => 'Posts'],
            ['name' => 'Delete Posts', 'slug' => 'posts.delete', 'module' => 'Posts'],
            ['name' => 'Publish Posts', 'slug' => 'posts.publish', 'module' => 'Posts'],

            // Module 2: Pages
            ['name' => 'View Pages', 'slug' => 'pages.view', 'module' => 'Pages'],
            ['name' => 'Create Pages', 'slug' => 'pages.create', 'module' => 'Pages'],
            ['name' => 'Edit Pages', 'slug' => 'pages.edit', 'module' => 'Pages'],
            ['name' => 'Delete Pages', 'slug' => 'pages.delete', 'module' => 'Pages'],

            // Module 3: Categories
            ['name' => 'View Categories', 'slug' => 'categories.view', 'module' => 'Categories'],
            ['name' => 'Create Categories', 'slug' => 'categories.create', 'module' => 'Categories'],
            ['name' => 'Edit Categories', 'slug' => 'categories.edit', 'module' => 'Categories'],
            ['name' => 'Delete Categories', 'slug' => 'categories.delete', 'module' => 'Categories'],

            // Module 4: Media
            ['name' => 'View Media Library', 'slug' => 'media.view', 'module' => 'Media'],
            ['name' => 'Upload Media Files', 'slug' => 'media.upload', 'module' => 'Media'],
            ['name' => 'Edit Media Details', 'slug' => 'media.edit', 'module' => 'Media'],
            ['name' => 'Delete Media Files', 'slug' => 'media.delete', 'module' => 'Media'],

            // Module 5: SEO
            ['name' => 'View SEO Intelligence', 'slug' => 'seo.view', 'module' => 'SEO'],
            ['name' => 'Edit SEO Meta & Robots', 'slug' => 'seo.edit', 'module' => 'SEO'],

            // Module 6: Users
            ['name' => 'View User Directory', 'slug' => 'users.view', 'module' => 'Users'],
            ['name' => 'Create User Accounts', 'slug' => 'users.create', 'module' => 'Users'],
            ['name' => 'Edit User Profiles', 'slug' => 'users.edit', 'module' => 'Users'],
            ['name' => 'Delete User Accounts', 'slug' => 'users.delete', 'module' => 'Users'],
            ['name' => 'Manage User Roles & Authority', 'slug' => 'users.roles', 'module' => 'Users'],

            // Module 7: Comments
            ['name' => 'View Comments', 'slug' => 'comments.view', 'module' => 'Comments'],
            ['name' => 'Moderate & Approve Comments', 'slug' => 'comments.moderate', 'module' => 'Comments'],
            ['name' => 'Reply to Comments', 'slug' => 'comments.reply', 'module' => 'Comments'],
            ['name' => 'Delete Comments', 'slug' => 'comments.delete', 'module' => 'Comments'],

            // Module 8: Settings
            ['name' => 'View Site Settings', 'slug' => 'settings.view', 'module' => 'Settings'],
            ['name' => 'Update Settings & Maintenance', 'slug' => 'settings.edit', 'module' => 'Settings'],

            // Module 9: Menu
            ['name' => 'View Navigation Menus', 'slug' => 'menu.view', 'module' => 'Menu'],
            ['name' => 'Manage & Reorder Menus', 'slug' => 'menu.manage', 'module' => 'Menu'],

            // Module 10: Newsletter
            ['name' => 'View Subscribers & Campaigns', 'slug' => 'newsletter.view', 'module' => 'Newsletter'],
            ['name' => 'Send Newsletter Broadcasts', 'slug' => 'newsletter.send', 'module' => 'Newsletter'],
            ['name' => 'Export Subscriber Lists', 'slug' => 'newsletter.export', 'module' => 'Newsletter'],
            ['name' => 'Delete Subscribers', 'slug' => 'newsletter.delete', 'module' => 'Newsletter'],
        ];

        Permission::whereNotIn('slug', array_column($permissions, 'slug'))->delete();

        $allPermissionIds = [];
        $permissionIdMap = [];
        foreach ($permissions as $p) {
            $permModel = Permission::updateOrCreate(
                ['slug' => $p['slug']],
                $p
            );
            $allPermissionIds[] = $permModel->id;
            $permissionIdMap[$p['slug']] = $permModel->id;
        }

        // Helper to resolve slugs to IDs
        $resolveSlugs = function (array $slugs) use ($permissionIdMap): array {
            return array_values(array_filter(array_map(fn($s) => $permissionIdMap[$s] ?? null, $slugs)));
        };

        // 3. Map Default Permissions to Roles

        // Super Admin gets 100% of permissions
        $roleModels['super-admin']->permissions()->sync($allPermissionIds);

        // Admin gets all operational permissions
        $adminPermissions = $allPermissionIds;
        $roleModels['admin']->permissions()->sync($adminPermissions);

        // Editor permissions
        $editorSlugs = [
            'posts.view', 'posts.create', 'posts.edit', 'posts.delete', 'posts.publish',
            'pages.view', 'pages.create', 'pages.edit', 'pages.delete',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'media.view', 'media.upload', 'media.edit', 'media.delete',
            'seo.view',
            'comments.view', 'comments.moderate', 'comments.reply', 'comments.delete',
            'menu.view', 'menu.manage',
            'newsletter.view', 'newsletter.send',
        ];
        $roleModels['editor']->permissions()->sync($resolveSlugs($editorSlugs));

        // Author permissions
        $authorSlugs = [
            'posts.view', 'posts.create', 'posts.edit',
            'pages.view', 'pages.create',
            'categories.view',
            'media.view', 'media.upload',
            'comments.view', 'comments.reply',
        ];
        $roleModels['author']->permissions()->sync($resolveSlugs($authorSlugs));

        // Subscriber permissions
        $subscriberSlugs = [
            'posts.view',
            'pages.view',
            'comments.view',
            'newsletter.view',
        ];
        $roleModels['subscriber']->permissions()->sync($resolveSlugs($subscriberSlugs));

        // 4. Assign default Super Admin role to the first User
        $superAdminRole = $roleModels['super-admin'];
        $firstUser = User::first();
        if ($firstUser) {
            $firstUser->roles()->syncWithoutDetaching([$superAdminRole->id]);
        }
    }
}
