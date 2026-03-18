<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name_en')->nullable();
            $table->string('site_name_ar')->nullable();
            $table->string('site_tagline_en')->nullable();
            $table->string('site_tagline_ar')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('phone')->nullable();
            $table->string('secondary_phone')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->text('address_en')->nullable();
            $table->text('address_ar')->nullable();
            $table->text('working_hours_en')->nullable();
            $table->text('working_hours_ar')->nullable();
            $table->longText('map_iframe')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->text('footer_text_en')->nullable();
            $table->text('footer_text_ar')->nullable();
            $table->text('copyright_text_en')->nullable();
            $table->text('copyright_text_ar')->nullable();
            $table->string('default_meta_title_en')->nullable();
            $table->string('default_meta_title_ar')->nullable();
            $table->text('default_meta_description_en')->nullable();
            $table->text('default_meta_description_ar')->nullable();
            $table->string('primary_color')->default('#12395b');
            $table->string('secondary_color')->default('#ff8c32');
            $table->string('global_cta_title_en')->nullable();
            $table->string('global_cta_title_ar')->nullable();
            $table->text('global_cta_text_en')->nullable();
            $table->text('global_cta_text_ar')->nullable();
            $table->string('global_cta_button_en')->nullable();
            $table->string('global_cta_button_ar')->nullable();
            $table->string('global_cta_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
