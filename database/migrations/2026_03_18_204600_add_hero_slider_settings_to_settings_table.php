<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('hero_slider_autoplay')->default(true)->after('global_cta_url');
            $table->unsignedInteger('hero_slider_interval')->default(5000)->after('hero_slider_autoplay');
            $table->decimal('hero_slider_overlay_opacity', 3, 2)->default(0.45)->after('hero_slider_interval');
            $table->boolean('hero_slider_show_dots')->default(true)->after('hero_slider_overlay_opacity');
            $table->boolean('hero_slider_show_arrows')->default(true)->after('hero_slider_show_dots');
            $table->string('hero_slider_content_alignment')->default('start')->after('hero_slider_show_arrows');
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_slider_autoplay',
                'hero_slider_interval',
                'hero_slider_overlay_opacity',
                'hero_slider_show_dots',
                'hero_slider_show_arrows',
                'hero_slider_content_alignment',
            ]);
        });
    }
};
