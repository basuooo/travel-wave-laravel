<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Funnel Templates
        if (! Schema::hasTable('funnel_templates')) {
            Schema::create('funnel_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('category')->default('Lead Generation');
                $table->text('description')->nullable();
                $table->string('thumbnail')->nullable();
                $table->json('schema_data')->nullable(); // Complete step/element/logic structure
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 2. Funnels
        if (! Schema::hasTable('funnels')) {
            Schema::create('funnels', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('workspace_id')->nullable()->default(1);
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('template_id')->nullable();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('status')->default('draft'); // draft, published, unpublished
                $table->json('design_settings')->nullable(); // primary_color, font, logo, button_style, background, etc.
                $table->json('tracking_settings')->nullable(); // meta_pixel_id, ga4_id, custom_events, etc.
                $table->json('crm_settings')->nullable(); // enabled, field_mapping, source_id, service_type_id
                $table->json('seo_settings')->nullable(); // meta_title, meta_description, og_image, index
                $table->timestamp('published_at')->nullable();
                $table->softDeletes();
                $table->timestamps();

                $table->foreign('template_id')->references('id')->on('funnel_templates')->onDelete('set null');
            });
        }

        // 3. Funnel Steps
        if (! Schema::hasTable('funnel_steps')) {
            Schema::create('funnel_steps', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('funnel_id');
                $table->string('title');
                $table->string('subtitle')->nullable();
                $table->string('step_type')->default('question'); // welcome, question, lead_form, result, custom
                $table->integer('sort_order')->default(0);
                $table->boolean('is_hidden')->default(false);
                $table->timestamps();

                $table->foreign('funnel_id')->references('id')->on('funnels')->onDelete('cascade');
            });
        }

        // 4. Funnel Elements (Questions, Text, Images, Forms, Buttons)
        if (! Schema::hasTable('funnel_elements')) {
            Schema::create('funnel_elements', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('step_id');
                $table->string('element_type'); // single_choice, multiple_choice, text, image, rating, slider, contact_form, calculator, button, etc.
                $table->string('label')->nullable();
                $table->string('question_key')->nullable();
                $table->json('properties')->nullable(); // options, placeholder, is_required, font_size, alignments, scoring options
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('step_id')->references('id')->on('funnel_steps')->onDelete('cascade');
            });
        }

        // 5. Funnel Results / Outcomes
        if (! Schema::hasTable('funnel_results')) {
            Schema::create('funnel_results', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('funnel_id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('image_url')->nullable();
                $table->integer('min_score')->nullable();
                $table->integer('max_score')->nullable();
                $table->string('cta_label')->nullable();
                $table->string('cta_type')->default('button'); // button, url, whatsapp
                $table->string('cta_url')->nullable();
                $table->string('cta_whatsapp_number')->nullable();
                $table->json('logic_conditions')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('funnel_id')->references('id')->on('funnels')->onDelete('cascade');
            });
        }

        // 6. Funnel Conditional Logic Rules
        if (! Schema::hasTable('funnel_conditions')) {
            Schema::create('funnel_conditions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('funnel_id');
                $table->unsignedBigInteger('step_id')->nullable();
                $table->unsignedBigInteger('element_id')->nullable();
                $table->string('operator')->default('='); // =, !=, >, <, >=, <=, contains, not_contains
                $table->text('compare_value')->nullable();
                $table->string('target_type')->default('step'); // step, result, end
                $table->unsignedBigInteger('target_id')->nullable();
                $table->string('logical_operator')->default('AND'); // AND, OR
                $table->timestamps();

                $table->foreign('funnel_id')->references('id')->on('funnels')->onDelete('cascade');
            });
        }

        // 7. Funnel Responses (Visitor submissions)
        if (! Schema::hasTable('funnel_responses')) {
            Schema::create('funnel_responses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('funnel_id');
                $table->unsignedBigInteger('funnel_result_id')->nullable();
                $table->unsignedBigInteger('crm_inquiry_id')->nullable();
                $table->string('session_id')->index();
                $table->string('visitor_ip', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->integer('score')->default(0);
                $table->boolean('is_completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->string('crm_sync_status')->default('pending'); // pending, synced, failed
                $table->timestamp('last_sync_attempt')->nullable();
                $table->text('sync_error')->nullable();
                $table->json('utm_data')->nullable();
                $table->timestamps();

                $table->foreign('funnel_id')->references('id')->on('funnels')->onDelete('cascade');
                $table->foreign('funnel_result_id')->references('id')->on('funnel_results')->onDelete('set null');
                $table->foreign('crm_inquiry_id')->references('id')->on('inquiries')->onDelete('set null');
            });
        }

        // 8. Funnel Response Answers
        if (! Schema::hasTable('funnel_response_answers')) {
            Schema::create('funnel_response_answers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('response_id');
                $table->unsignedBigInteger('element_id')->nullable();
                $table->string('question_label')->nullable();
                $table->text('answer_value')->nullable();
                $table->integer('score_given')->default(0);
                $table->timestamps();

                $table->foreign('response_id')->references('id')->on('funnel_responses')->onDelete('cascade');
                $table->foreign('element_id')->references('id')->on('funnel_elements')->onDelete('set null');
            });
        }

        // 9. Funnel Webhooks
        if (! Schema::hasTable('funnel_webhooks')) {
            Schema::create('funnel_webhooks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('funnel_id');
                $table->string('name');
                $table->string('event_trigger')->default('lead_submitted'); // lead_submitted, funnel_completed, qualified_lead
                $table->string('target_url');
                $table->string('secret')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('funnel_id')->references('id')->on('funnels')->onDelete('cascade');
            });
        }

        // Add Interactive Funnels permissions if roles/permissions tables exist
        if (Schema::hasTable('permissions')) {
            $funnelPermissions = [
                ['name' => 'View Funnels', 'slug' => 'funnels.view', 'module' => 'funnels', 'description' => 'View interactive funnels and dashboard'],
                ['name' => 'Create Funnels', 'slug' => 'funnels.create', 'module' => 'funnels', 'description' => 'Create new funnels'],
                ['name' => 'Edit Funnels', 'slug' => 'funnels.edit', 'module' => 'funnels', 'description' => 'Edit funnel builder and settings'],
                ['name' => 'Delete Funnels', 'slug' => 'funnels.delete', 'module' => 'funnels', 'description' => 'Delete funnels'],
                ['name' => 'Publish Funnels', 'slug' => 'funnels.publish', 'module' => 'funnels', 'description' => 'Publish and unpublish funnels'],
                ['name' => 'Manage Templates', 'slug' => 'funnels.templates', 'module' => 'funnels', 'description' => 'Manage funnel templates'],
                ['name' => 'View Funnel Analytics', 'slug' => 'funnels.analytics', 'module' => 'funnels', 'description' => 'View funnel conversion analytics'],
            ];

            foreach ($funnelPermissions as $perm) {
                DB::table('permissions')->updateOrInsert(
                    ['slug' => $perm['slug']],
                    $perm + ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funnel_webhooks');
        Schema::dropIfExists('funnel_response_answers');
        Schema::dropIfExists('funnel_responses');
        Schema::dropIfExists('funnel_conditions');
        Schema::dropIfExists('funnel_results');
        Schema::dropIfExists('funnel_elements');
        Schema::dropIfExists('funnel_steps');
        Schema::dropIfExists('funnels');
        Schema::dropIfExists('funnel_templates');
    }
};
