<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Brands Table
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('primary_color', 50)->nullable()->default('#1e3a8a');
            $table->string('secondary_color', 50)->nullable()->default('#0284c7');
            $table->json('header_settings')->nullable();
            $table->json('footer_settings')->nullable();
            $table->json('default_tracking')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Brand Domains Table
        Schema::create('brand_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->string('domain')->unique();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(true);
            $table->string('ssl_status', 50)->default('active');
            $table->timestamps();
        });

        // 3. Template Categories Table
        Schema::create('lp_template_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('slug')->unique();
            $table->string('icon', 100)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 4. Templates Table
        Schema::create('lp_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignId('template_category_id')->nullable()->constrained('lp_template_categories')->nullOnDelete();
            $table->string('name_en');
            $table->string('name_ar');
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('slug')->unique();
            $table->string('preview_image')->nullable();
            $table->longText('structure')->nullable(); // JSON structure
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_global')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 5. Section Categories Table
        Schema::create('lp_section_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('slug')->unique();
            $table->string('icon', 100)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 6. Sections Table
        Schema::create('lp_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_category_id')->nullable()->constrained('lp_section_categories')->nullOnDelete();
            $table->string('name_en');
            $table->string('name_ar');
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('preview_image')->nullable();
            $table->longText('structure')->nullable(); // JSON section elements
            $table->boolean('is_active')->default(true);
            $table->boolean('is_global')->default(true);
            $table->timestamps();
        });

        // 7. Global Components Table
        Schema::create('lp_global_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('component_type', 100); // e.g. header, footer, cta, whatsapp
            $table->longText('structure')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        // 8. Landing Pages Table
        Schema::create('lp_landing_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignId('assigned_lead_form_id')->nullable()->constrained('lead_forms')->nullOnDelete();
            $table->string('header_mode', 50)->default('website'); // website, custom, none
            $table->longText('custom_header_structure')->nullable();
            $table->string('footer_mode', 50)->default('website'); // website, custom, none
            $table->longText('custom_footer_structure')->nullable();
            
            $table->string('status', 50)->default('draft'); // draft, published, archived
            $table->boolean('is_active')->default(true); // Toggle ON/OFF
            $table->string('slug')->unique();
            $table->string('internal_name');
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();
            
            $table->string('campaign_name')->nullable();
            $table->string('ad_platform', 100)->nullable();
            $table->string('campaign_type', 100)->nullable();
            $table->string('traffic_source')->nullable();

            $table->dateTime('publish_at')->nullable();
            $table->dateTime('unpublish_at')->nullable();

            $table->string('tracking_mode', 50)->default('brand'); // website, brand, custom
            $table->json('tracking_integration_ids')->nullable();
            $table->text('custom_tracking_code')->nullable();

            $table->string('seo_title_en')->nullable();
            $table->string('seo_title_ar')->nullable();
            $table->text('seo_description_en')->nullable();
            $table->text('seo_description_ar')->nullable();
            $table->string('og_image')->nullable();
            $table->string('canonical_url', 1000)->nullable();
            $table->string('robots_meta', 100)->default('index, follow');
            $table->json('schema_json')->nullable();

            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('utm_term')->nullable();

            $table->longText('custom_html_head')->nullable();
            $table->longText('custom_css')->nullable();
            $table->longText('custom_js')->nullable();

            $table->longText('structure')->nullable(); // Master Canvas JSON structure

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // 9. Page Translations Table
        Schema::create('lp_page_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_id')->constrained('lp_landing_pages')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('title')->nullable();
            $table->json('content_json')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();

            $table->unique(['landing_page_id', 'locale']);
        });

        // 10. Page Versions Table
        Schema::create('lp_page_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_id')->constrained('lp_landing_pages')->cascadeOnDelete();
            $table->integer('version_number');
            $table->string('label')->nullable();
            $table->longText('structure')->nullable();
            $table->json('settings')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 11. Experiments (A/B Testing) Table
        Schema::create('lp_experiments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_id')->constrained('lp_landing_pages')->cascadeOnDelete();
            $table->string('name');
            $table->string('status', 50)->default('draft'); // draft, running, paused, ended
            $table->json('traffic_split_json')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->timestamps();
        });

        // 12. Page Variants Table
        Schema::create('lp_page_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('experiment_id')->constrained('lp_experiments')->cascadeOnDelete();
            $table->foreignId('landing_page_id')->constrained('lp_landing_pages')->cascadeOnDelete();
            $table->string('variant_letter', 10); // A, B, C...
            $table->string('name');
            $table->integer('traffic_weight')->default(50); // e.g. 50%
            $table->longText('structure')->nullable();
            $table->boolean('is_control')->default(false);
            $table->timestamps();
        });

        // 13. Activity Logs Table
        Schema::create('lp_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('landing_page_id')->nullable()->constrained('lp_landing_pages')->cascadeOnDelete();
            $table->string('action', 100);
            $table->string('entity_type', 100)->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('before_state')->nullable();
            $table->json('after_state')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lp_activity_logs');
        Schema::dropIfExists('lp_page_variants');
        Schema::dropIfExists('lp_experiments');
        Schema::dropIfExists('lp_page_versions');
        Schema::dropIfExists('lp_page_translations');
        Schema::dropIfExists('lp_landing_pages');
        Schema::dropIfExists('lp_global_components');
        Schema::dropIfExists('lp_sections');
        Schema::dropIfExists('lp_section_categories');
        Schema::dropIfExists('lp_templates');
        Schema::dropIfExists('lp_template_categories');
        Schema::dropIfExists('brand_domains');
        Schema::dropIfExists('brands');
    }
};
