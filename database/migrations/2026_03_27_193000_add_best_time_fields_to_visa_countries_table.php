<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('visa_countries', 'best_time_badge_en')) {
            Schema::table('visa_countries', function (Blueprint $table) {
                $table->string('best_time_badge_en')->nullable()->after('detailed_description_ar');
            });
        }

        if (! Schema::hasColumn('visa_countries', 'best_time_badge_ar')) {
            Schema::table('visa_countries', function (Blueprint $table) {
                $table->string('best_time_badge_ar')->nullable()->after('best_time_badge_en');
            });
        }

        if (! Schema::hasColumn('visa_countries', 'best_time_title_en')) {
            Schema::table('visa_countries', function (Blueprint $table) {
                $table->string('best_time_title_en')->nullable()->after('best_time_badge_ar');
            });
        }

        if (! Schema::hasColumn('visa_countries', 'best_time_title_ar')) {
            Schema::table('visa_countries', function (Blueprint $table) {
                $table->string('best_time_title_ar')->nullable()->after('best_time_title_en');
            });
        }

        if (! Schema::hasColumn('visa_countries', 'best_time_description_en')) {
            Schema::table('visa_countries', function (Blueprint $table) {
                $table->text('best_time_description_en')->nullable()->after('best_time_title_ar');
            });
        }

        if (! Schema::hasColumn('visa_countries', 'best_time_description_ar')) {
            Schema::table('visa_countries', function (Blueprint $table) {
                $table->text('best_time_description_ar')->nullable()->after('best_time_description_en');
            });
        }
    }

    public function down(): void
    {
        $columns = [
            'best_time_badge_en',
            'best_time_badge_ar',
            'best_time_title_en',
            'best_time_title_ar',
            'best_time_description_en',
            'best_time_description_ar',
        ];

        $existingColumns = array_values(array_filter($columns, fn (string $column) => Schema::hasColumn('visa_countries', $column)));

        if ($existingColumns !== []) {
            Schema::table('visa_countries', function (Blueprint $table) use ($existingColumns) {
                $table->dropColumn($existingColumns);
            });
        }
    }
};
