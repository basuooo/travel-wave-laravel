<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('header_background_color')->default('#12395b')->after('link_hover_color');
            $table->string('header_text_color')->default('#ffffff')->after('header_background_color');
            $table->string('header_link_color')->default('#ffffff')->after('header_text_color');
            $table->string('header_hover_color')->default('#ff8c32')->after('header_link_color');
            $table->string('header_active_link_color')->default('#ff8c32')->after('header_hover_color');
            $table->string('header_button_color')->default('#ff8c32')->after('header_active_link_color');
            $table->string('header_button_text_color')->default('#ffffff')->after('header_button_color');
            $table->boolean('header_logo_enabled')->default(true)->after('header_button_text_color');
            $table->boolean('header_is_sticky')->default(true)->after('header_logo_enabled');
            $table->unsignedInteger('header_vertical_padding')->default(8)->after('header_is_sticky');

            $table->string('footer_background_color')->default('#0d2438')->after('header_vertical_padding');
            $table->string('footer_text_color')->default('#d9e3ed')->after('footer_background_color');
            $table->string('footer_link_color')->default('#ffffff')->after('footer_text_color');
            $table->string('footer_hover_color')->default('#ff8c32')->after('footer_link_color');
            $table->string('footer_heading_color')->default('#ffffff')->after('footer_hover_color');
            $table->string('footer_button_color')->default('#ff8c32')->after('footer_heading_color');
            $table->string('footer_button_text_color')->default('#ffffff')->after('footer_button_color');
            $table->unsignedInteger('footer_vertical_padding')->default(80)->after('footer_button_text_color');
            $table->json('footer_quick_links')->nullable()->after('footer_vertical_padding');

            $table->string('home_country_strip_title_en')->nullable()->after('footer_quick_links');
            $table->string('home_country_strip_title_ar')->nullable()->after('home_country_strip_title_en');
            $table->boolean('home_country_strip_autoplay')->default(true)->after('home_country_strip_title_ar');
            $table->unsignedInteger('home_country_strip_speed')->default(32)->after('home_country_strip_autoplay');
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'header_background_color',
                'header_text_color',
                'header_link_color',
                'header_hover_color',
                'header_active_link_color',
                'header_button_color',
                'header_button_text_color',
                'header_logo_enabled',
                'header_is_sticky',
                'header_vertical_padding',
                'footer_background_color',
                'footer_text_color',
                'footer_link_color',
                'footer_hover_color',
                'footer_heading_color',
                'footer_button_color',
                'footer_button_text_color',
                'footer_vertical_padding',
                'footer_quick_links',
                'home_country_strip_title_en',
                'home_country_strip_title_ar',
                'home_country_strip_autoplay',
                'home_country_strip_speed',
            ]);
        });
    }
};
