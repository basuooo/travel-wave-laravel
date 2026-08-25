<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('public_catalog_settings')) {
            Schema::create('public_catalog_settings', function (Blueprint $table) {
                $table->id();
                $table->boolean('show_price')->default(true);
                $table->boolean('show_embassy_fee')->default(true);
                $table->boolean('show_working_days')->default(true);
                $table->boolean('show_biometrics')->default(true);
                $table->boolean('show_interview')->default(true);
                $table->boolean('show_notes')->default(true);
                $table->boolean('show_preview_button')->default(true);
                $table->string('logo_path')->nullable();
                $table->integer('logo_width')->default(180);
                $table->integer('logo_height')->default(50);
                $table->boolean('logo_keep_aspect_ratio')->default(true);
                $table->string('whatsapp_phone')->nullable();
                $table->text('whatsapp_message_template')->nullable();
                $table->boolean('floating_whatsapp_enabled')->default(true);
                $table->json('custom_buttons')->nullable();
                $table->foreignId('selected_lead_form_id')->nullable()->constrained('lead_forms')->nullOnDelete();
                $table->foreignId('selected_map_section_id')->nullable()->constrained('map_sections')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('public_catalog_settings');
    }
};
