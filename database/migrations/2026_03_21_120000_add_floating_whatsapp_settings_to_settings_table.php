<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('floating_whatsapp_enabled')->default(true)->after('hero_slider_layout_mode');
            $table->string('floating_whatsapp_number')->nullable()->after('floating_whatsapp_enabled');
            $table->text('floating_whatsapp_message_en')->nullable()->after('floating_whatsapp_number');
            $table->text('floating_whatsapp_message_ar')->nullable()->after('floating_whatsapp_message_en');
            $table->string('floating_whatsapp_button_text_en')->nullable()->after('floating_whatsapp_message_ar');
            $table->string('floating_whatsapp_button_text_ar')->nullable()->after('floating_whatsapp_button_text_en');
            $table->boolean('floating_whatsapp_show_icon')->default(true)->after('floating_whatsapp_button_text_ar');
            $table->string('floating_whatsapp_position')->default('bottom_right')->after('floating_whatsapp_show_icon');
            $table->string('floating_whatsapp_animation_style')->default('pulse')->after('floating_whatsapp_position');
            $table->unsignedInteger('floating_whatsapp_animation_speed')->default(3200)->after('floating_whatsapp_animation_style');
            $table->boolean('floating_whatsapp_show_desktop')->default(true)->after('floating_whatsapp_animation_speed');
            $table->boolean('floating_whatsapp_show_mobile')->default(true)->after('floating_whatsapp_show_desktop');
            $table->string('floating_whatsapp_background_color')->nullable()->after('floating_whatsapp_show_mobile');
            $table->string('floating_whatsapp_visibility_mode')->default('all')->after('floating_whatsapp_background_color');
            $table->json('floating_whatsapp_visibility_targets')->nullable()->after('floating_whatsapp_visibility_mode');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'floating_whatsapp_enabled',
                'floating_whatsapp_number',
                'floating_whatsapp_message_en',
                'floating_whatsapp_message_ar',
                'floating_whatsapp_button_text_en',
                'floating_whatsapp_button_text_ar',
                'floating_whatsapp_show_icon',
                'floating_whatsapp_position',
                'floating_whatsapp_animation_style',
                'floating_whatsapp_animation_speed',
                'floating_whatsapp_show_desktop',
                'floating_whatsapp_show_mobile',
                'floating_whatsapp_background_color',
                'floating_whatsapp_visibility_mode',
                'floating_whatsapp_visibility_targets',
            ]);
        });
    }
};
