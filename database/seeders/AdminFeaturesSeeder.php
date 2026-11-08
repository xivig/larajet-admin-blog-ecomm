<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $roles = [
            [
                'name' => 'Super Administrator',
                'slug' => 'super-admin',
                'description' => 'Unrestricted access to all system entities, database records, and server settings.',
                'color' => 'primary',
            ],
            [
                'name' => 'Managing Editor',
                'slug' => 'editor',
                'description' => 'Full editorial control over publications, categories, tags, and media library.',
                'color' => 'success',
            ],
            [
                'name' => 'Technical Author',
                'slug' => 'author',
                'description' => 'Can compose, edit, and draft architectural publications for editorial review.',
                'color' => 'info',
            ],
            [
                'name' => 'Community Moderator',
                'slug' => 'moderator',
                'description' => 'Moderate reader discussions, approve pending comments, and flag spam.',
                'color' => 'warning',
            ],
        ];

        foreach ($roles as $r) {
            DB::table('roles')->updateOrInsert(['slug' => $r['slug']], array_merge($r, ['created_at' => now(), 'updated_at' => now()]));
        }

        // 2. Permissions
        $permissions = [
            ['name' => 'View Publications', 'slug' => 'posts.view', 'module' => 'Publications'],
            ['name' => 'Create Publications', 'slug' => 'posts.create', 'module' => 'Publications'],
            ['name' => 'Edit Publications', 'slug' => 'posts.edit', 'module' => 'Publications'],
            ['name' => 'Delete Publications', 'slug' => 'posts.delete', 'module' => 'Publications'],
            ['name' => 'Moderate Comments', 'slug' => 'comments.moderate', 'module' => 'Comments'],
            ['name' => 'Delete Comments', 'slug' => 'comments.delete', 'module' => 'Comments'],
            ['name' => 'Manage Taxonomies', 'slug' => 'taxonomy.manage', 'module' => 'Taxonomies'],
            ['name' => 'Manage User Accounts', 'slug' => 'users.manage', 'module' => 'Users'],
            ['name' => 'Manage Roles & Access', 'slug' => 'roles.manage', 'module' => 'Security'],
            ['name' => 'Media Assets Management', 'slug' => 'media.manage', 'module' => 'Media'],
        ];

        foreach ($permissions as $p) {
            DB::table('permissions')->updateOrInsert(['slug' => $p['slug']], array_merge($p, ['created_at' => now(), 'updated_at' => now()]));
        }

        // Link Super Admin to all permissions
        $superAdminRole = DB::table('roles')->where('slug', 'super-admin')->first();
        $allPermissions = DB::table('permissions')->get();
        if ($superAdminRole) {
            foreach ($allPermissions as $perm) {
                DB::table('role_permission')->updateOrInsert([
                    'role_id' => $superAdminRole->id,
                    'permission_id' => $perm->id,
                ]);
            }
        }

        // Assign first user as Super Admin
        $firstUser = User::first();
        if ($firstUser && $superAdminRole) {
            DB::table('role_user')->updateOrInsert([
                'role_id' => $superAdminRole->id,
                'user_id' => $firstUser->id,
            ]);
        }

        // 3. Admin Notifications
        $notifications = [
            [
                'type' => 'comment',
                'title' => 'New Discussion Comment',
                'message' => 'Sarah Jenkins commented on "High-Performance Distributed Cache Architecture".',
                'link' => '/admin/comments',
                'is_read' => false,
                'created_at' => now()->subMinutes(12),
                'updated_at' => now()->subMinutes(12),
            ],
            [
                'type' => 'user',
                'title' => 'New Enterprise Registration',
                'message' => 'David Miller registered as an Enterprise Architect.',
                'link' => '/admin/users',
                'is_read' => false,
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'type' => 'alert',
                'title' => 'Security Audit Clean',
                'message' => 'Automated vulnerability audit passed with 0 security advisories.',
                'link' => '/admin/dashboard',
                'is_read' => true,
                'created_at' => now()->subHours(5),
                'updated_at' => now()->subHours(5),
            ],
            [
                'type' => 'info',
                'title' => 'Scheduled Backup Completed',
                'message' => 'Database snapshot archive completed (18.4 MB).',
                'link' => '/admin/file-manager',
                'is_read' => true,
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
        ];

        foreach ($notifications as $n) {
            DB::table('admin_notifications')->insert($n);
        }

        // 4. Initial Emails in Inbox
        $emails = [
            [
                'sender_name' => 'Alex Rivera (VP Eng)',
                'sender_email' => 'arivera@cloudscale.io',
                'recipient_email' => 'admin@xivig.com',
                'subject' => 'Partnership Inquiry: Microservice Orchestration Blueprint',
                'body' => "Hi Xivig Team,\n\nWe reviewed your recent editorial on distributed caching and would love to license your enterprise boilerplate for our 40-engineer engineering team.\n\nCould we schedule a 20-minute architecture review this Thursday?\n\nBest regards,\nAlex Rivera",
                'folder' => 'inbox',
                'is_starred' => true,
                'is_read' => false,
                'created_at' => now()->subHours(1),
                'updated_at' => now()->subHours(1),
            ],
            [
                'sender_name' => 'GitHub Security Bot',
                'sender_email' => 'alerts@github.com',
                'recipient_email' => 'admin@xivig.com',
                'subject' => '[Security] Dependabot weekly scan report: 0 alerts',
                'body' => "Hello,\n\nNo vulnerable dependencies found for repository xivig/larajet-admin-blog-ecomm.\n\nAll Composer and NPM packages conform to strict LTS guidelines.",
                'folder' => 'inbox',
                'is_starred' => false,
                'is_read' => true,
                'created_at' => now()->subHours(8),
                'updated_at' => now()->subHours(8),
            ],
            [
                'sender_name' => 'DevOps Automation',
                'sender_email' => 'cron@xivig.internal',
                'recipient_email' => 'admin@xivig.com',
                'subject' => 'Weekly Performance Digest: 99.98% Uptime',
                'body' => "Weekly Summary:\n- Average response latency: 24ms\n- Memory consumption: 42MB peak\n- Cache hit ratio: 94.2%\n- Database transactions: 184,200",
                'folder' => 'inbox',
                'is_starred' => false,
                'is_read' => true,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
        ];

        foreach ($emails as $em) {
            DB::table('admin_emails')->insert($em);
        }

        // 5. Initial Chat Messages
        if ($firstUser) {
            $chatMessages = [
                [
                    'sender_id' => $firstUser->id,
                    'channel' => 'general',
                    'message' => 'Welcome to the Xivig Enterprise engineering workspace!',
                    'created_at' => now()->subHours(3),
                    'updated_at' => now()->subHours(3),
                ],
                [
                    'sender_id' => $firstUser->id,
                    'channel' => 'general',
                    'message' => 'Laravel 13 compatibility verified and admin dashboard fully configured.',
                    'created_at' => now()->subHours(2),
                    'updated_at' => now()->subHours(2),
                ],
            ];
            foreach ($chatMessages as $cm) {
                DB::table('chat_messages')->insert($cm);
            }
        }
    }
}
