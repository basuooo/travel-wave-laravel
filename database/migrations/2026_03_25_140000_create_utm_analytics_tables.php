<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('utm_campaigns')) {
            Schema::create('utm_campaigns', function (Blueprint $table) {
                $table->id();
                $table->string('display_name');
                $table->string('base_url', 2048);
                $table->string('generated_url', 2048);
                $table->string('utm_source')->nullable();
                $table->string('utm_medium')->nullable();
                $table->string('utm_campaign')->nullable();
                $table->string('utm_id')->nullable();
                $table->string('utm_term')->nullable();
                $table->string('utm_content')->nullable();
                $table->string('platform')->nullable();
                $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status', 30)->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['utm_source', 'utm_medium', 'utm_campaign'], 'utm_campaigns_source_medium_campaign_idx');
                $table->index(['status', 'owner_user_id'], 'utm_campaigns_status_owner_idx');
            });
        }

        if (! Schema::hasTable('utm_visits')) {
            Schema::create('utm_visits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('utm_campaign_id')->nullable()->constrained('utm_campaigns')->nullOnDelete();
                $table->string('session_key')->nullable();
                $table->string('utm_source')->nullable();
                $table->string('utm_medium')->nullable();
                $table->string('utm_campaign')->nullable();
                $table->string('utm_id')->nullable();
                $table->string('utm_term')->nullable();
                $table->string('utm_content')->nullable();
                $table->string('landing_page', 2048)->nullable();
                $table->string('referrer', 2048)->nullable();
                $table->string('request_path', 2048)->nullable();
                $table->string('ip_address', 64)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('visited_at')->nullable();
                $table->timestamps();

                $table->index('visited_at');
                $table->index('session_key');
                $table->index(['utm_source', 'utm_medium'], 'utm_visits_source_medium_idx');
                $table->index('utm_campaign');
            });
        }

        Schema::table('inquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('inquiries', 'utm_campaign_id')) {
                $table->foreignId('utm_campaign_id')->nullable()->after('marketing_landing_page_id')->constrained('utm_campaigns')->nullOnDelete();
            }

            if (! Schema::hasColumn('inquiries', 'utm_medium')) {
                $table->text('utm_medium')->nullable()->after('utm_source');
            }

            if (! Schema::hasColumn('inquiries', 'utm_id')) {
                $table->text('utm_id')->nullable()->after('utm_campaign');
            }

            if (! Schema::hasColumn('inquiries', 'utm_term')) {
                $table->text('utm_term')->nullable()->after('utm_id');
            }

            if (! Schema::hasColumn('inquiries', 'utm_content')) {
                $table->text('utm_content')->nullable()->after('utm_term');
            }

            if (! Schema::hasColumn('inquiries', 'landing_page')) {
                $table->text('landing_page')->nullable()->after('utm_content');
            }

            if (! Schema::hasColumn('inquiries', 'referrer')) {
                $table->text('referrer')->nullable()->after('landing_page');
            }

            if (! Schema::hasColumn('inquiries', 'first_touch_at')) {
                $table->timestamp('first_touch_at')->nullable()->after('referrer');
            }

            if (! Schema::hasColumn('inquiries', 'last_touch_at')) {
                $table->timestamp('last_touch_at')->nullable()->after('first_touch_at');
            }

            if (! Schema::hasColumn('inquiries', 'first_utm_source')) {
                $table->text('first_utm_source')->nullable()->after('last_touch_at');
            }

            if (! Schema::hasColumn('inquiries', 'first_utm_medium')) {
                $table->text('first_utm_medium')->nullable()->after('first_utm_source');
            }

            if (! Schema::hasColumn('inquiries', 'first_utm_campaign')) {
                $table->text('first_utm_campaign')->nullable()->after('first_utm_medium');
            }

            if (! Schema::hasColumn('inquiries', 'first_utm_id')) {
                $table->text('first_utm_id')->nullable()->after('first_utm_campaign');
            }

            if (! Schema::hasColumn('inquiries', 'first_utm_term')) {
                $table->text('first_utm_term')->nullable()->after('first_utm_id');
            }

            if (! Schema::hasColumn('inquiries', 'first_utm_content')) {
                $table->text('first_utm_content')->nullable()->after('first_utm_term');
            }

            if (! Schema::hasColumn('inquiries', 'first_landing_page')) {
                $table->text('first_landing_page')->nullable()->after('first_utm_content');
            }

            if (! Schema::hasColumn('inquiries', 'first_referrer')) {
                $table->text('first_referrer')->nullable()->after('first_landing_page');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            if (Schema::hasColumn('inquiries', 'utm_campaign_id')) {
                $table->dropConstrainedForeignId('utm_campaign_id');
            }

            foreach ([
                'utm_medium',
                'utm_id',
                'utm_term',
                'utm_content',
                'landing_page',
                'referrer',
                'first_touch_at',
                'last_touch_at',
                'first_utm_source',
                'first_utm_medium',
                'first_utm_campaign',
                'first_utm_id',
                'first_utm_term',
                'first_utm_content',
                'first_landing_page',
                'first_referrer',
            ] as $column) {
                if (Schema::hasColumn('inquiries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::dropIfExists('utm_visits');
        Schema::dropIfExists('utm_campaigns');
    }
};
