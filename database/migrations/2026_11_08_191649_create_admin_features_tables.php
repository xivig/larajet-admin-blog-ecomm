<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add status to comments if not present
        if (!Schema::hasColumn('comments', 'status')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->string('status', 30)->default('approved')->after('comment');
            });
        }

        // 2. Admin Notifications
        if (!Schema::hasTable('admin_notifications')) {
            Schema::create('admin_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('type', 50)->default('info'); // info, comment, post, user, alert
                $table->string('title');
                $table->text('message');
                $table->string('link')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamps();
            });
        }

        // 3. Roles and Permissions
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('description')->nullable();
                $table->string('color', 30)->default('primary');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('module', 50)->default('general');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('role_permission')) {
            Schema::create('role_permission', function (Blueprint $table) {
                $table->foreignId('role_id')->constrained()->cascadeOnDelete();
                $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
                $table->primary(['role_id', 'permission_id']);
            });
        }

        if (!Schema::hasTable('role_user')) {
            Schema::create('role_user', function (Blueprint $table) {
                $table->foreignId('role_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->primary(['role_id', 'user_id']);
            });
        }

        // 4. Live Chat Messages
        if (!Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('receiver_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('channel', 50)->default('general');
                $table->text('message');
                $table->string('attachment')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamps();
            });
        }

        // 5. Admin Emails / Inbox
        if (!Schema::hasTable('admin_emails')) {
            Schema::create('admin_emails', function (Blueprint $table) {
                $table->id();
                $table->string('sender_name')->nullable();
                $table->string('sender_email');
                $table->string('recipient_email')->default('admin@xivig.com');
                $table->string('subject');
                $table->longText('body');
                $table->string('folder', 30)->default('inbox'); // inbox, sent, draft, trash
                $table->boolean('is_starred')->default(false);
                $table->boolean('is_read')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_emails');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('admin_notifications');
        if (Schema::hasColumn('comments', 'status')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
