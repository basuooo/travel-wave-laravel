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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title_en');
            $table->string('title_ar');
            $table->string('slug')->nullable()->unique();
            $table->string('hero_badge_en')->nullable();
            $table->string('hero_badge_ar')->nullable();
            $table->string('hero_title_en')->nullable();
            $table->string('hero_title_ar')->nullable();
            $table->text('hero_subtitle_en')->nullable();
            $table->text('hero_subtitle_ar')->nullable();
            $table->string('hero_primary_cta_text_en')->nullable();
            $table->string('hero_primary_cta_text_ar')->nullable();
            $table->string('hero_primary_cta_url')->nullable();
            $table->string('hero_secondary_cta_text_en')->nullable();
            $table->string('hero_secondary_cta_text_ar')->nullable();
            $table->string('hero_secondary_cta_url')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('intro_title_en')->nullable();
            $table->string('intro_title_ar')->nullable();
            $table->text('intro_body_en')->nullable();
            $table->text('intro_body_ar')->nullable();
            $table->json('sections')->nullable();
            $table->string('meta_title_en')->nullable();
            $table->string('meta_title_ar')->nullable();
            $table->text('meta_description_en')->nullable();
            $table->text('meta_description_ar')->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('pages');
    }
};
