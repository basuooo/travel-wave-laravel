<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('visa_countries', function (Blueprint $table) {
            $table->string('hero_mobile_image')->nullable()->after('hero_image');
            $table->json('quick_summary_items')->nullable()->after('stay_duration_ar');
            $table->string('intro_image')->nullable()->after('quick_summary_items');
            $table->string('introduction_badge_en')->nullable()->after('introduction_title_ar');
            $table->string('introduction_badge_ar')->nullable()->after('introduction_badge_en');
            $table->json('introduction_points')->nullable()->after('introduction_badge_ar');
            $table->text('why_choose_intro_en')->nullable()->after('why_choose_title_ar');
            $table->text('why_choose_intro_ar')->nullable()->after('why_choose_intro_en');
            $table->text('documents_subtitle_en')->nullable()->after('documents_title_ar');
            $table->text('documents_subtitle_ar')->nullable()->after('documents_subtitle_en');
            $table->string('support_title_en')->nullable()->after('faq_title_ar');
            $table->string('support_title_ar')->nullable()->after('support_title_en');
            $table->text('support_subtitle_en')->nullable()->after('support_title_ar');
            $table->text('support_subtitle_ar')->nullable()->after('support_subtitle_en');
            $table->string('support_button_en')->nullable()->after('support_subtitle_ar');
            $table->string('support_button_ar')->nullable()->after('support_button_en');
            $table->string('support_button_link')->nullable()->after('support_button_ar');
            $table->boolean('support_is_active')->default(true)->after('support_button_link');
        });
    }

    public function down()
    {
        Schema::table('visa_countries', function (Blueprint $table) {
            $table->dropColumn([
                'hero_mobile_image',
                'quick_summary_items',
                'intro_image',
                'introduction_badge_en',
                'introduction_badge_ar',
                'introduction_points',
                'why_choose_intro_en',
                'why_choose_intro_ar',
                'documents_subtitle_en',
                'documents_subtitle_ar',
                'support_title_en',
                'support_title_ar',
                'support_subtitle_en',
                'support_subtitle_ar',
                'support_button_en',
                'support_button_ar',
                'support_button_link',
                'support_is_active',
            ]);
        });
    }
};
