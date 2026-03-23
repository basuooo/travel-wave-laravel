<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tracking_integrations', function (Blueprint $table) {
            if (! Schema::hasColumn('tracking_integrations', 'settings')) {
                $table->json('settings')->nullable()->after('script_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tracking_integrations', function (Blueprint $table) {
            if (Schema::hasColumn('tracking_integrations', 'settings')) {
                $table->dropColumn('settings');
            }
        });
    }
};
