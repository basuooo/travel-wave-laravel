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
            if (! Schema::hasColumn('settings', 'header_logo_position_en')) {
                $table->string('header_logo_position_en', 10)->default('left')->after('header_vertical_padding');
            }

            if (! Schema::hasColumn('settings', 'header_logo_position_ar')) {
                $table->string('header_logo_position_ar', 10)->default('right')->after('header_logo_position_en');
            }

            if (! Schema::hasColumn('settings', 'header_menu_position_en')) {
                $table->string('header_menu_position_en', 10)->default('left')->after('header_logo_position_ar');
            }

            if (! Schema::hasColumn('settings', 'header_menu_position_ar')) {
                $table->string('header_menu_position_ar', 10)->default('right')->after('header_menu_position_en');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        Schema::table('settings', function (Blueprint $table) {
            foreach ([
                'header_logo_position_en',
                'header_logo_position_ar',
                'header_menu_position_en',
                'header_menu_position_ar',
            ] as $column) {
                if (Schema::hasColumn('settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
