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
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_ar');
            $table->string('slug')->unique();
            $table->text('excerpt_en')->nullable();
            $table->text('excerpt_ar')->nullable();
            $table->string('hero_badge_en')->nullable();
            $table->string('hero_badge_ar')->nullable();
            $table->string('hero_title_en')->nullable();
            $table->string('hero_title_ar')->nullable();
            $table->text('hero_subtitle_en')->nullable();
            $table->text('hero_subtitle_ar')->nullable();
            $table->string('hero_image')->nullable();
            $table->longText('overview_en')->nullable();
            $table->longText('overview_ar')->nullable();
            $table->json('highlights')->nullable();
            $table->json('packages')->nullable();
            $table->json('included_items')->nullable();
            $table->json('excluded_items')->nullable();
            $table->json('itinerary')->nullable();
            $table->json('gallery')->nullable();
            $table->json('faqs')->nullable();
            $table->string('cta_title_en')->nullable();
            $table->string('cta_title_ar')->nullable();
            $table->text('cta_text_en')->nullable();
            $table->text('cta_text_ar')->nullable();
            $table->string('cta_button_en')->nullable();
            $table->string('cta_button_ar')->nullable();
            $table->string('cta_url')->nullable();
            $table->string('meta_title_en')->nullable();
            $table->string('meta_title_ar')->nullable();
            $table->text('meta_description_en')->nullable();
            $table->text('meta_description_ar')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
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
        Schema::dropIfExists('destinations');
    }
};
