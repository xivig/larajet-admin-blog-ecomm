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
        Schema::table('admin_emails', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('body');
            $table->string('cc_email')->nullable()->after('recipient_email');
            $table->string('bcc_email')->nullable()->after('cc_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_emails', function (Blueprint $table) {
            $table->dropColumn(['attachments', 'cc_email', 'bcc_email']);
        });
    }
};
