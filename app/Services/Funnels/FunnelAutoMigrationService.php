<?php

namespace App\Services\Funnels;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Auto-Migration Service for Interactive Funnels Module.
 * Creates all required tables on first access without needing `php artisan migrate`.
 */
class FunnelAutoMigrationService
{
    public static function ensureTablesExist(): void
    {
        if (Schema::hasTable('funnels')) {
            return; // already migrated
        }

        // ── 1. funnel_templates ──────────────────────────────────────────────
        if (! Schema::hasTable('funnel_templates')) {
            Schema::create('funnel_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('category')->nullable();
                $table->text('description')->nullable();
                $table->string('thumbnail_url')->nullable();
                $table->json('schema_data')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // ── 2. funnels ───────────────────────────────────────────────────────
        if (! Schema::hasTable('funnels')) {
            Schema::create('funnels', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('template_id')->nullable();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('status')->default('draft'); // draft | published | archived
                $table->text('description')->nullable();
                $table->json('design_settings')->nullable();
                $table->json('crm_settings')->nullable();
                $table->json('tracking_settings')->nullable();
                $table->json('seo_settings')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // ── 3. funnel_steps ──────────────────────────────────────────────────
        if (! Schema::hasTable('funnel_steps')) {
            Schema::create('funnel_steps', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('funnel_id')->index();
                $table->string('title')->nullable();
                $table->string('subtitle')->nullable();
                $table->string('step_type')->default('question'); // welcome|question|lead_form|result
                $table->integer('sort_order')->default(1);
                $table->boolean('is_hidden')->default(false);
                $table->timestamps();
                $table->foreign('funnel_id')->references('id')->on('funnels')->onDelete('cascade');
            });
        }

        // ── 4. funnel_elements ───────────────────────────────────────────────
        if (! Schema::hasTable('funnel_elements')) {
            Schema::create('funnel_elements', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('step_id')->index();
                $table->string('element_type'); // single_choice|multi_choice|text_input|rating|slider|contact_form|heading|button
                $table->string('label')->nullable();
                $table->string('question_key')->nullable();
                $table->json('properties')->nullable();
                $table->integer('sort_order')->default(1);
                $table->timestamps();
                $table->foreign('step_id')->references('id')->on('funnel_steps')->onDelete('cascade');
            });
        }

        // ── 5. funnel_results ────────────────────────────────────────────────
        if (! Schema::hasTable('funnel_results')) {
            Schema::create('funnel_results', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('funnel_id')->index();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('image_url')->nullable();
                $table->integer('min_score')->nullable();
                $table->integer('max_score')->nullable();
                $table->string('cta_label')->nullable();
                $table->string('cta_type')->default('button'); // button|whatsapp|url
                $table->string('cta_url')->nullable();
                $table->string('cta_whatsapp_number')->nullable();
                $table->json('logic_conditions')->nullable();
                $table->integer('sort_order')->default(1);
                $table->timestamps();
                $table->foreign('funnel_id')->references('id')->on('funnels')->onDelete('cascade');
            });
        }

        // ── 6. funnel_conditions ─────────────────────────────────────────────
        if (! Schema::hasTable('funnel_conditions')) {
            Schema::create('funnel_conditions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('funnel_id')->index();
                $table->string('condition_type');
                $table->json('rules')->nullable();
                $table->string('action');
                $table->json('action_params')->nullable();
                $table->integer('sort_order')->default(1);
                $table->timestamps();
                $table->foreign('funnel_id')->references('id')->on('funnels')->onDelete('cascade');
            });
        }

        // ── 7. funnel_responses ──────────────────────────────────────────────
        if (! Schema::hasTable('funnel_responses')) {
            Schema::create('funnel_responses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('funnel_id')->index();
                $table->unsignedBigInteger('funnel_result_id')->nullable();
                $table->unsignedBigInteger('crm_inquiry_id')->nullable();
                $table->string('session_id')->nullable()->index();
                $table->string('visitor_ip', 45)->nullable();
                $table->string('user_agent')->nullable();
                $table->integer('score')->default(0);
                $table->boolean('is_completed')->default(false);
                $table->string('crm_sync_status')->default('pending'); // pending|synced|failed
                $table->timestamp('last_sync_attempt')->nullable();
                $table->text('sync_error')->nullable();
                $table->json('utm_data')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->foreign('funnel_id')->references('id')->on('funnels')->onDelete('cascade');
            });
        }

        // ── 8. funnel_response_answers ───────────────────────────────────────
        if (! Schema::hasTable('funnel_response_answers')) {
            Schema::create('funnel_response_answers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('response_id')->index();
                $table->unsignedBigInteger('element_id')->nullable();
                $table->string('question_label')->nullable();
                $table->text('answer_value')->nullable();
                $table->timestamps();
                $table->foreign('response_id')->references('id')->on('funnel_responses')->onDelete('cascade');
            });
        }

        // ── 9. funnel_webhooks ───────────────────────────────────────────────
        if (! Schema::hasTable('funnel_webhooks')) {
            Schema::create('funnel_webhooks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('funnel_id')->index();
                $table->string('name');
                $table->string('url');
                $table->string('method')->default('POST');
                $table->json('headers')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->foreign('funnel_id')->references('id')->on('funnels')->onDelete('cascade');
            });
        }

        // ── Seed permissions ─────────────────────────────────────────────────
        self::seedPermissions();

        // ── Seed templates ───────────────────────────────────────────────────
        self::seedTemplates();
    }

    private static function seedPermissions(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        $perms = [
            ['name' => 'View Funnels',      'slug' => 'funnels.view'],
            ['name' => 'Create Funnels',    'slug' => 'funnels.create'],
            ['name' => 'Edit Funnels',      'slug' => 'funnels.edit'],
            ['name' => 'Delete Funnels',    'slug' => 'funnels.delete'],
            ['name' => 'Publish Funnels',   'slug' => 'funnels.publish'],
            ['name' => 'Funnel Templates',  'slug' => 'funnels.templates'],
            ['name' => 'Funnel Analytics',  'slug' => 'funnels.analytics'],
        ];

        foreach ($perms as $perm) {
            DB::table('permissions')->insertOrIgnore(array_merge($perm, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    private static function seedTemplates(): void
    {
        // Run seeder if empty or if templates have fewer than 8 entries
        if (! DB::table('funnel_templates')->where('slug', 'whatsapp-qualification-funnel')->exists()) {
            (new \Database\Seeders\FunnelTemplateSeeder())->run();
        }
    }
}
