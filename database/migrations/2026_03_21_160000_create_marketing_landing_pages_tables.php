<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('internal_name');
            $table->string('title_en');
            $table->string('title_ar');
            $table->string('slug')->unique();
            $table->string('campaign_name')->nullable();
            $table->string('ad_platform')->nullable();
            $table->string('campaign_type')->nullable();
            $table->string('traffic_source')->nullable();
            $table->text('target_audience_note')->nullable();
            $table->string('status')->default('draft');
            $table->foreignId('assigned_lead_form_id')->nullable()->constrained('lead_forms')->nullOnDelete();
            $table->json('tracking_integration_ids')->nullable();
            $table->json('sections')->nullable();
            $table->string('seo_title_en')->nullable();
            $table->string('seo_title_ar')->nullable();
            $table->text('seo_description_en')->nullable();
            $table->text('seo_description_ar')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('utm_term')->nullable();
            $table->text('final_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('marketing_landing_page_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketing_landing_page_id')->constrained()->cascadeOnDelete();
            $table->string('event_type', 50);
            $table->string('session_key')->nullable();
            $table->string('source')->nullable();
            $table->string('medium')->nullable();
            $table->string('campaign')->nullable();
            $table->string('content')->nullable();
            $table->string('term')->nullable();
            $table->text('referrer')->nullable();
            $table->string('path')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->foreignId('marketing_landing_page_id')->nullable()->after('lead_form_assignment_id')->constrained('marketing_landing_pages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('marketing_landing_page_id');
        });

        Schema::dropIfExists('marketing_landing_page_events');
        Schema::dropIfExists('marketing_landing_pages');
    }
};
