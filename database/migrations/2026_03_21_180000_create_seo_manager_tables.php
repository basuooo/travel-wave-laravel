<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('sitemap_include_pages')->default(true);
            $table->boolean('sitemap_include_visa_destinations')->default(true);
            $table->boolean('sitemap_include_destinations')->default(true);
            $table->boolean('sitemap_include_blog_posts')->default(true);
            $table->boolean('sitemap_include_marketing_pages')->default(true);
            $table->boolean('sitemap_include_images')->default(true);
            $table->timestamp('sitemap_last_generated_at')->nullable();
            $table->longText('robots_txt_content')->nullable();
            $table->string('search_console_property')->nullable();
            $table->text('search_console_notes')->nullable();
            $table->boolean('schema_organization_enabled')->default(true);
            $table->string('schema_organization_name')->nullable();
            $table->string('schema_organization_logo')->nullable();
            $table->boolean('schema_local_business_enabled')->default(false);
            $table->boolean('schema_breadcrumb_enabled')->default(true);
            $table->boolean('schema_faq_enabled')->default(true);
            $table->boolean('schema_article_enabled')->default(true);
            $table->boolean('schema_destination_enabled')->default(true);
            $table->boolean('merchant_center_enabled')->default(false);
            $table->string('merchant_center_verification_code')->nullable();
            $table->text('merchant_center_notes')->nullable();
            $table->string('default_robots_meta')->nullable();
            $table->timestamps();
        });

        Schema::create('seo_meta_entries', function (Blueprint $table) {
            $table->id();
            $table->string('target_type');
            $table->unsignedBigInteger('target_id');
            $table->string('meta_title_en')->nullable();
            $table->string('meta_title_ar')->nullable();
            $table->text('meta_description_en')->nullable();
            $table->text('meta_description_ar')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('robots_meta')->nullable();
            $table->string('og_title_en')->nullable();
            $table->string('og_title_ar')->nullable();
            $table->text('og_description_en')->nullable();
            $table->text('og_description_ar')->nullable();
            $table->string('og_image')->nullable();
            $table->string('twitter_title_en')->nullable();
            $table->string('twitter_title_ar')->nullable();
            $table->text('twitter_description_en')->nullable();
            $table->text('twitter_description_ar')->nullable();
            $table->string('twitter_image')->nullable();
            $table->boolean('schema_enabled')->default(true);
            $table->string('schema_type')->nullable();
            $table->string('hreflang_en_url')->nullable();
            $table->string('hreflang_ar_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['target_type', 'target_id']);
        });

        Schema::create('seo_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('source_path')->unique();
            $table->string('destination_url');
            $table->unsignedSmallInteger('redirect_type')->default(301);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('hit_count')->default(0);
            $table->timestamp('last_hit_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_redirects');
        Schema::dropIfExists('seo_meta_entries');
        Schema::dropIfExists('seo_settings');
    }
};
