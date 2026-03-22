<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'twitter_url')) {
                $table->string('twitter_url')->nullable()->after('instagram_url');
            }

            if (! Schema::hasColumn('settings', 'linkedin_url')) {
                $table->string('linkedin_url')->nullable()->after('youtube_url');
            }

            if (! Schema::hasColumn('settings', 'snapchat_url')) {
                $table->string('snapchat_url')->nullable()->after('linkedin_url');
            }

            if (! Schema::hasColumn('settings', 'telegram_url')) {
                $table->string('telegram_url')->nullable()->after('snapchat_url');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        Schema::table('settings', function (Blueprint $table) {
            $drop = [];

            foreach (['twitter_url', 'linkedin_url', 'snapchat_url', 'telegram_url'] as $column) {
                if (Schema::hasColumn('settings', $column)) {
                    $drop[] = $column;
                }
            }

            if ($drop !== []) {
                $table->dropColumn($drop);
            }
        });
    }
};
