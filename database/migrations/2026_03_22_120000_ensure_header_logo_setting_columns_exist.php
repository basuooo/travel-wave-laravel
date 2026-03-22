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
            if (! Schema::hasColumn('settings', 'logo_width')) {
                $table->unsignedInteger('logo_width')->default(220)->nullable(false);
            }

            if (! Schema::hasColumn('settings', 'logo_height')) {
                $table->unsignedInteger('logo_height')->nullable();
            }

            if (! Schema::hasColumn('settings', 'logo_keep_aspect_ratio')) {
                $table->boolean('logo_keep_aspect_ratio')->default(true);
            }

            if (! Schema::hasColumn('settings', 'mobile_logo_width')) {
                $table->unsignedInteger('mobile_logo_width')->default(168)->nullable(false);
            }

            if (! Schema::hasColumn('settings', 'header_logo_enabled')) {
                $table->boolean('header_logo_enabled')->default(true);
            }

            if (! Schema::hasColumn('settings', 'header_is_sticky')) {
                $table->boolean('header_is_sticky')->default(true);
            }

            if (! Schema::hasColumn('settings', 'header_vertical_padding')) {
                $table->unsignedInteger('header_vertical_padding')->default(8)->nullable(false);
            }
        });
    }

    public function down(): void
    {
        // Keep backwards compatibility for older environments.
    }
};
