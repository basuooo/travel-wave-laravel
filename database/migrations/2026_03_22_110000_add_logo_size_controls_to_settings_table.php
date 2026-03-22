<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('logo_keep_aspect_ratio')->default(true)->after('logo_height');
            $table->unsignedInteger('footer_logo_width')->default(200)->after('footer_logo_path');
            $table->unsignedInteger('footer_logo_height')->nullable()->after('footer_logo_width');
            $table->boolean('footer_logo_keep_aspect_ratio')->default(true)->after('footer_logo_height');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'logo_keep_aspect_ratio',
                'footer_logo_width',
                'footer_logo_height',
                'footer_logo_keep_aspect_ratio',
            ]);
        });
    }
};
