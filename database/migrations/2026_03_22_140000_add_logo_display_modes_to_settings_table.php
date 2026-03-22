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
            if (! Schema::hasColumn('settings', 'header_logo_display_mode')) {
                $table->string('header_logo_display_mode', 20)->nullable()->after('header_logo_keep_aspect_ratio');
            }

            if (! Schema::hasColumn('settings', 'footer_logo_display_mode')) {
                $table->string('footer_logo_display_mode', 20)->nullable()->after('footer_logo_keep_aspect_ratio');
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

            foreach (['header_logo_display_mode', 'footer_logo_display_mode'] as $column) {
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
