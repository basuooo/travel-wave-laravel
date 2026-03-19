<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_country_strip_items', function (Blueprint $table) {
            $table->string('subtitle_en')->nullable()->after('name_ar');
            $table->string('subtitle_ar')->nullable()->after('subtitle_en');
            $table->string('flag_image_path')->nullable()->after('image_path');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->string('home_country_strip_subtitle_en')->nullable()->after('home_country_strip_title_ar');
            $table->string('home_country_strip_subtitle_ar')->nullable()->after('home_country_strip_subtitle_en');
        });
    }

    public function down(): void
    {
        Schema::table('home_country_strip_items', function (Blueprint $table) {
            $table->dropColumn([
                'subtitle_en',
                'subtitle_ar',
                'flag_image_path',
            ]);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'home_country_strip_subtitle_en',
                'home_country_strip_subtitle_ar',
            ]);
        });
    }
};
