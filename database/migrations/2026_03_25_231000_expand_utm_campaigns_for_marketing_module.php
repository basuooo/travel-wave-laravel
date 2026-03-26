<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('utm_campaigns')) {
            return;
        }

        Schema::table('utm_campaigns', function (Blueprint $table) {
            if (! Schema::hasColumn('utm_campaigns', 'campaign_code')) {
                $table->string('campaign_code')->nullable()->after('display_name');
            }

            if (! Schema::hasColumn('utm_campaigns', 'campaign_type')) {
                $table->string('campaign_type')->nullable()->after('generated_url');
            }

            if (! Schema::hasColumn('utm_campaigns', 'objective')) {
                $table->string('objective')->nullable()->after('campaign_type');
            }

            if (! Schema::hasColumn('utm_campaigns', 'external_campaign_id')) {
                $table->string('external_campaign_id')->nullable()->after('platform');
            }

            if (! Schema::hasColumn('utm_campaigns', 'start_date')) {
                $table->date('start_date')->nullable()->after('external_campaign_id');
            }

            if (! Schema::hasColumn('utm_campaigns', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }

            if (! Schema::hasColumn('utm_campaigns', 'budget')) {
                $table->decimal('budget', 12, 2)->nullable()->after('end_date');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('utm_campaigns')) {
            return;
        }

        Schema::table('utm_campaigns', function (Blueprint $table) {
            foreach ([
                'campaign_code',
                'campaign_type',
                'objective',
                'external_campaign_id',
                'start_date',
                'end_date',
                'budget',
            ] as $column) {
                if (Schema::hasColumn('utm_campaigns', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
