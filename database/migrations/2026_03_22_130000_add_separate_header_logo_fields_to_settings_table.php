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
            if (! Schema::hasColumn('settings', 'header_logo_path')) {
                $table->string('header_logo_path')->nullable()->after('logo_path');
            }

            if (! Schema::hasColumn('settings', 'header_logo_width')) {
                $table->unsignedInteger('header_logo_width')->default(220)->after('mobile_logo_width');
            }

            if (! Schema::hasColumn('settings', 'header_logo_height')) {
                $table->unsignedInteger('header_logo_height')->nullable()->after('header_logo_width');
            }

            if (! Schema::hasColumn('settings', 'header_logo_keep_aspect_ratio')) {
                $table->boolean('header_logo_keep_aspect_ratio')->default(true)->after('header_logo_height');
            }

            if (! Schema::hasColumn('settings', 'header_mobile_logo_width')) {
                $table->unsignedInteger('header_mobile_logo_width')->default(168)->after('header_logo_keep_aspect_ratio');
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

            foreach ([
                'header_logo_path',
                'header_logo_width',
                'header_logo_height',
                'header_logo_keep_aspect_ratio',
                'header_mobile_logo_width',
            ] as $column) {
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
