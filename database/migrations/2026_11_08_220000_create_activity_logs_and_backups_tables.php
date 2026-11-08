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
        // 1. Activity Logs Table
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action', 50)->default('info'); // create, update, delete, restore, login, backup, settings, reassign
                $table->string('module', 50)->default('System'); // Publications, Taxonomies, Comments, Users, Roles, Media, Galleries, Settings, System
                $table->string('description');
                $table->json('properties')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                $table->index(['module', 'action']);
                $table->index('created_at');
            });
        }

        // 2. System Backups Table
        if (!Schema::hasTable('system_backups')) {
            Schema::create('system_backups', function (Blueprint $table) {
                $table->id();
                $table->string('filename')->unique();
                $table->string('disk_path');
                $table->string('type', 30)->default('full'); // full, database, media
                $table->unsignedBigInteger('size_bytes')->default(0);
                $table->string('formatted_size', 50)->default('0 KB');
                $table->text('notes')->nullable();
                $table->string('status', 30)->default('completed');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('system_backups');
    }
};
