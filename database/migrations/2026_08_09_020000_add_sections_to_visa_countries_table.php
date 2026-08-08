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
        if (Schema::hasTable('visa_countries') && ! Schema::hasColumn('visa_countries', 'sections')) {
            Schema::table('visa_countries', function (Blueprint $table) {
                $table->json('sections')->nullable()->after('overview_ar');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('visa_countries') && Schema::hasColumn('visa_countries', 'sections')) {
            Schema::table('visa_countries', function (Blueprint $table) {
                $table->dropColumn('sections');
            });
        }
    }
};
