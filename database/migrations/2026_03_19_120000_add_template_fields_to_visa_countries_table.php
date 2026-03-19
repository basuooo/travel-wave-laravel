<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('visa_countries', function (Blueprint $table) {
            $table->string('flag_image')->nullable()->after('hero_image');
            $table->string('hero_cta_text_en')->nullable()->after('hero_subtitle_ar');
            $table->string('hero_cta_text_ar')->nullable()->after('hero_cta_text_en');
            $table->string('hero_cta_url')->nullable()->after('hero_cta_text_ar');
            $table->decimal('hero_overlay_opacity', 3, 2)->default(0.45)->after('hero_cta_url');
            $table->string('visa_type_en')->nullable()->after('overview_ar');
            $table->string('visa_type_ar')->nullable()->after('visa_type_en');
            $table->string('stay_duration_en')->nullable()->after('visa_type_ar');
            $table->string('stay_duration_ar')->nullable()->after('stay_duration_en');
            $table->string('introduction_title_en')->nullable()->after('stay_duration_ar');
            $table->string('introduction_title_ar')->nullable()->after('introduction_title_en');
            $table->string('detailed_title_en')->nullable()->after('introduction_title_ar');
            $table->string('detailed_title_ar')->nullable()->after('detailed_title_en');
            $table->longText('detailed_description_en')->nullable()->after('detailed_title_ar');
            $table->longText('detailed_description_ar')->nullable()->after('detailed_description_en');
            $table->string('why_choose_title_en')->nullable()->after('services');
            $table->string('why_choose_title_ar')->nullable()->after('why_choose_title_en');
            $table->json('why_choose_items')->nullable()->after('why_choose_title_ar');
            $table->string('documents_title_en')->nullable()->after('required_documents');
            $table->string('documents_title_ar')->nullable()->after('documents_title_en');
            $table->json('document_items')->nullable()->after('documents_title_ar');
            $table->string('steps_title_en')->nullable()->after('application_steps');
            $table->string('steps_title_ar')->nullable()->after('steps_title_en');
            $table->json('step_items')->nullable()->after('steps_title_ar');
            $table->string('fees_title_en')->nullable()->after('fees_ar');
            $table->string('fees_title_ar')->nullable()->after('fees_title_en');
            $table->json('fee_items')->nullable()->after('fees_title_ar');
            $table->longText('fees_notes_en')->nullable()->after('fee_items');
            $table->longText('fees_notes_ar')->nullable()->after('fees_notes_en');
            $table->string('faq_title_en')->nullable()->after('faqs');
            $table->string('faq_title_ar')->nullable()->after('faq_title_en');
            $table->string('map_title_en')->nullable()->after('faq_title_ar');
            $table->string('map_title_ar')->nullable()->after('map_title_en');
            $table->text('map_description_en')->nullable()->after('map_title_ar');
            $table->text('map_description_ar')->nullable()->after('map_description_en');
            $table->longText('map_embed_code')->nullable()->after('map_description_ar');
            $table->boolean('map_is_active')->default(true)->after('map_embed_code');
            $table->string('inquiry_form_title_en')->nullable()->after('map_is_active');
            $table->string('inquiry_form_title_ar')->nullable()->after('inquiry_form_title_en');
            $table->text('inquiry_form_subtitle_en')->nullable()->after('inquiry_form_title_ar');
            $table->text('inquiry_form_subtitle_ar')->nullable()->after('inquiry_form_subtitle_en');
            $table->string('inquiry_form_button_en')->nullable()->after('inquiry_form_subtitle_ar');
            $table->string('inquiry_form_button_ar')->nullable()->after('inquiry_form_button_en');
            $table->text('inquiry_form_success_en')->nullable()->after('inquiry_form_button_ar');
            $table->text('inquiry_form_success_ar')->nullable()->after('inquiry_form_success_en');
            $table->string('inquiry_form_default_service_type')->nullable()->after('inquiry_form_success_ar');
            $table->json('inquiry_form_visible_fields')->nullable()->after('inquiry_form_default_service_type');
            $table->boolean('inquiry_form_is_active')->default(true)->after('inquiry_form_visible_fields');
            $table->string('final_cta_background_image')->nullable()->after('cta_url');
            $table->boolean('final_cta_is_active')->default(true)->after('final_cta_background_image');
            $table->string('og_image')->nullable()->after('meta_description_ar');
        });
    }

    public function down()
    {
        Schema::table('visa_countries', function (Blueprint $table) {
            $table->dropColumn([
                'flag_image',
                'hero_cta_text_en',
                'hero_cta_text_ar',
                'hero_cta_url',
                'hero_overlay_opacity',
                'visa_type_en',
                'visa_type_ar',
                'stay_duration_en',
                'stay_duration_ar',
                'introduction_title_en',
                'introduction_title_ar',
                'detailed_title_en',
                'detailed_title_ar',
                'detailed_description_en',
                'detailed_description_ar',
                'why_choose_title_en',
                'why_choose_title_ar',
                'why_choose_items',
                'documents_title_en',
                'documents_title_ar',
                'document_items',
                'steps_title_en',
                'steps_title_ar',
                'step_items',
                'fees_title_en',
                'fees_title_ar',
                'fee_items',
                'fees_notes_en',
                'fees_notes_ar',
                'faq_title_en',
                'faq_title_ar',
                'map_title_en',
                'map_title_ar',
                'map_description_en',
                'map_description_ar',
                'map_embed_code',
                'map_is_active',
                'inquiry_form_title_en',
                'inquiry_form_title_ar',
                'inquiry_form_subtitle_en',
                'inquiry_form_subtitle_ar',
                'inquiry_form_button_en',
                'inquiry_form_button_ar',
                'inquiry_form_success_en',
                'inquiry_form_success_ar',
                'inquiry_form_default_service_type',
                'inquiry_form_visible_fields',
                'inquiry_form_is_active',
                'final_cta_background_image',
                'final_cta_is_active',
                'og_image',
            ]);
        });
    }
};
