<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'site_status')) {
                $table->string('site_status')->default('active')->after('site_tagline_ar');
            }
            if (!Schema::hasColumn('settings', 'site_redirect_url')) {
                $table->string('site_redirect_url')->nullable()->after('site_status');
            }
            if (!Schema::hasColumn('settings', 'maintenance_title_ar')) {
                $table->string('maintenance_title_ar')->nullable()->after('site_redirect_url');
            }
            if (!Schema::hasColumn('settings', 'maintenance_title_en')) {
                $table->string('maintenance_title_en')->nullable()->after('maintenance_title_ar');
            }
            if (!Schema::hasColumn('settings', 'maintenance_message_ar')) {
                $table->text('maintenance_message_ar')->nullable()->after('maintenance_title_en');
            }
            if (!Schema::hasColumn('settings', 'maintenance_message_en')) {
                $table->text('maintenance_message_en')->nullable()->after('maintenance_message_ar');
            }
            if (!Schema::hasColumn('settings', 'maintenance_bypass_admin')) {
                $table->boolean('maintenance_bypass_admin')->default(true)->after('maintenance_message_en');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'site_status',
                'site_redirect_url',
                'maintenance_title_ar',
                'maintenance_title_en',
                'maintenance_message_ar',
                'maintenance_message_en',
                'maintenance_bypass_admin',
            ]);
        });
    }
};
