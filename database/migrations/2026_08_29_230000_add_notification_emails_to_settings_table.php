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
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'notification_emails')) {
                $table->text('notification_emails')->nullable()->after('contact_email');
            }
            if (! Schema::hasColumn('settings', 'notification_email_mode')) {
                $table->string('notification_email_mode')->default('assigned_and_custom')->after('notification_emails');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'notification_emails')) {
                $table->dropColumn('notification_emails');
            }
            if (Schema::hasColumn('settings', 'notification_email_mode')) {
                $table->dropColumn('notification_email_mode');
            }
        });
    }
};
