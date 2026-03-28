<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->string('best_time_badge_en')->nullable()->after('detailed_description_ar');
            $table->string('best_time_badge_ar')->nullable()->after('best_time_badge_en');
        });
    }

    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn([
                'best_time_badge_en',
                'best_time_badge_ar',
            ]);
        });
    }
};
