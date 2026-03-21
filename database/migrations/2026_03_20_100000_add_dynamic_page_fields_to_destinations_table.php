<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->string('destination_type')->default('domestic')->after('slug');
            $table->string('subtitle_en')->nullable()->after('excerpt_ar');
            $table->string('subtitle_ar')->nullable()->after('subtitle_en');
            $table->string('featured_image')->nullable()->after('hero_image');
            $table->string('hero_mobile_image')->nullable()->after('featured_image');
            $table->string('flag_image')->nullable()->after('hero_mobile_image');
            $table->string('hero_cta_text_en')->nullable()->after('hero_subtitle_ar');
            $table->string('hero_cta_text_ar')->nullable()->after('hero_cta_text_en');
            $table->string('hero_cta_url')->nullable()->after('hero_cta_text_ar');
            $table->string('hero_secondary_cta_text_en')->nullable()->after('hero_cta_url');
            $table->string('hero_secondary_cta_text_ar')->nullable()->after('hero_secondary_cta_text_en');
            $table->string('hero_secondary_cta_url')->nullable()->after('hero_secondary_cta_text_ar');
            $table->decimal('hero_overlay_opacity', 3, 2)->default(0.45)->after('hero_secondary_cta_url');

            $table->string('quick_info_title_en')->nullable()->after('overview_ar');
            $table->string('quick_info_title_ar')->nullable()->after('quick_info_title_en');
            $table->json('quick_info_items')->nullable()->after('quick_info_title_ar');

            $table->string('about_title_en')->nullable()->after('quick_info_items');
            $table->string('about_title_ar')->nullable()->after('about_title_en');
            $table->longText('about_description_en')->nullable()->after('about_title_ar');
            $table->longText('about_description_ar')->nullable()->after('about_description_en');
            $table->string('about_image')->nullable()->after('about_description_ar');
            $table->json('about_points')->nullable()->after('about_image');

            $table->string('detailed_title_en')->nullable()->after('about_points');
            $table->string('detailed_title_ar')->nullable()->after('detailed_title_en');
            $table->longText('detailed_description_en')->nullable()->after('detailed_title_ar');
            $table->longText('detailed_description_ar')->nullable()->after('detailed_description_en');

            $table->string('best_time_title_en')->nullable()->after('detailed_description_ar');
            $table->string('best_time_title_ar')->nullable()->after('best_time_title_en');
            $table->longText('best_time_description_en')->nullable()->after('best_time_title_ar');
            $table->longText('best_time_description_ar')->nullable()->after('best_time_description_en');

            $table->string('highlights_title_en')->nullable()->after('best_time_description_ar');
            $table->string('highlights_title_ar')->nullable()->after('highlights_title_en');
            $table->json('highlight_items')->nullable()->after('highlights_title_ar');

            $table->string('services_title_en')->nullable()->after('highlight_items');
            $table->string('services_title_ar')->nullable()->after('services_title_en');
            $table->text('services_intro_en')->nullable()->after('services_title_ar');
            $table->text('services_intro_ar')->nullable()->after('services_intro_en');
            $table->json('service_items')->nullable()->after('services_intro_ar');

            $table->string('documents_title_en')->nullable()->after('service_items');
            $table->string('documents_title_ar')->nullable()->after('documents_title_en');
            $table->text('documents_subtitle_en')->nullable()->after('documents_title_ar');
            $table->text('documents_subtitle_ar')->nullable()->after('documents_subtitle_en');
            $table->json('document_items')->nullable()->after('documents_subtitle_ar');

            $table->string('steps_title_en')->nullable()->after('document_items');
            $table->string('steps_title_ar')->nullable()->after('steps_title_en');
            $table->json('step_items')->nullable()->after('steps_title_ar');

            $table->string('pricing_title_en')->nullable()->after('step_items');
            $table->string('pricing_title_ar')->nullable()->after('pricing_title_en');
            $table->text('pricing_notes_en')->nullable()->after('pricing_title_ar');
            $table->text('pricing_notes_ar')->nullable()->after('pricing_notes_en');
            $table->json('pricing_items')->nullable()->after('pricing_notes_ar');

            $table->string('faq_title_en')->nullable()->after('pricing_items');
            $table->string('faq_title_ar')->nullable()->after('faq_title_en');

            $table->string('cta_secondary_button_en')->nullable()->after('cta_button_ar');
            $table->string('cta_secondary_button_ar')->nullable()->after('cta_secondary_button_en');
            $table->string('cta_secondary_url')->nullable()->after('cta_secondary_button_ar');
            $table->string('cta_background_image')->nullable()->after('cta_secondary_url');

            $table->string('form_title_en')->nullable()->after('cta_background_image');
            $table->string('form_title_ar')->nullable()->after('form_title_en');
            $table->text('form_subtitle_en')->nullable()->after('form_title_ar');
            $table->text('form_subtitle_ar')->nullable()->after('form_subtitle_en');
            $table->string('form_submit_text_en')->nullable()->after('form_subtitle_ar');
            $table->string('form_submit_text_ar')->nullable()->after('form_submit_text_en');
            $table->json('form_visible_fields')->nullable()->after('form_submit_text_ar');

            $table->boolean('show_hero')->default(true)->after('meta_description_ar');
            $table->boolean('show_quick_info')->default(true)->after('show_hero');
            $table->boolean('show_about')->default(true)->after('show_quick_info');
            $table->boolean('show_detailed')->default(true)->after('show_about');
            $table->boolean('show_best_time')->default(true)->after('show_detailed');
            $table->boolean('show_highlights')->default(true)->after('show_best_time');
            $table->boolean('show_services')->default(true)->after('show_highlights');
            $table->boolean('show_documents')->default(true)->after('show_services');
            $table->boolean('show_steps')->default(true)->after('show_documents');
            $table->boolean('show_pricing')->default(true)->after('show_steps');
            $table->boolean('show_faq')->default(true)->after('show_pricing');
            $table->boolean('show_cta')->default(true)->after('show_faq');
            $table->boolean('show_form')->default(true)->after('show_cta');
        });
    }

    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn([
                'destination_type',
                'subtitle_en',
                'subtitle_ar',
                'featured_image',
                'hero_mobile_image',
                'flag_image',
                'hero_cta_text_en',
                'hero_cta_text_ar',
                'hero_cta_url',
                'hero_secondary_cta_text_en',
                'hero_secondary_cta_text_ar',
                'hero_secondary_cta_url',
                'hero_overlay_opacity',
                'quick_info_title_en',
                'quick_info_title_ar',
                'quick_info_items',
                'about_title_en',
                'about_title_ar',
                'about_description_en',
                'about_description_ar',
                'about_image',
                'about_points',
                'detailed_title_en',
                'detailed_title_ar',
                'detailed_description_en',
                'detailed_description_ar',
                'best_time_title_en',
                'best_time_title_ar',
                'best_time_description_en',
                'best_time_description_ar',
                'highlights_title_en',
                'highlights_title_ar',
                'highlight_items',
                'services_title_en',
                'services_title_ar',
                'services_intro_en',
                'services_intro_ar',
                'service_items',
                'documents_title_en',
                'documents_title_ar',
                'documents_subtitle_en',
                'documents_subtitle_ar',
                'document_items',
                'steps_title_en',
                'steps_title_ar',
                'step_items',
                'pricing_title_en',
                'pricing_title_ar',
                'pricing_notes_en',
                'pricing_notes_ar',
                'pricing_items',
                'faq_title_en',
                'faq_title_ar',
                'cta_secondary_button_en',
                'cta_secondary_button_ar',
                'cta_secondary_url',
                'cta_background_image',
                'form_title_en',
                'form_title_ar',
                'form_subtitle_en',
                'form_subtitle_ar',
                'form_submit_text_en',
                'form_submit_text_ar',
                'form_visible_fields',
                'show_hero',
                'show_quick_info',
                'show_about',
                'show_detailed',
                'show_best_time',
                'show_highlights',
                'show_services',
                'show_documents',
                'show_steps',
                'show_pricing',
                'show_faq',
                'show_cta',
                'show_form',
            ]);
        });
    }
};
