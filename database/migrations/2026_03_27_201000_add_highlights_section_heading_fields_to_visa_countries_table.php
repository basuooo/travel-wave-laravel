<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('visa_countries', 'highlights_section_label_en')) {
            Schema::table('visa_countries', function (Blueprint $table) {
                $table->string('highlights_section_label_en')->nullable()->after('best_time_description_ar');
            });
        }

        if (! Schema::hasColumn('visa_countries', 'highlights_section_label_ar')) {
            Schema::table('visa_countries', function (Blueprint $table) {
                $table->string('highlights_section_label_ar')->nullable()->after('highlights_section_label_en');
            });
        }

        if (! Schema::hasColumn('visa_countries', 'highlights_section_title_en')) {
            Schema::table('visa_countries', function (Blueprint $table) {
                $table->text('highlights_section_title_en')->nullable()->after('highlights_section_label_ar');
            });
        }

        if (! Schema::hasColumn('visa_countries', 'highlights_section_title_ar')) {
            Schema::table('visa_countries', function (Blueprint $table) {
                $table->text('highlights_section_title_ar')->nullable()->after('highlights_section_title_en');
            });
        }
    }

    public function down(): void
    {
        $columns = [
            'highlights_section_label_en',
            'highlights_section_label_ar',
            'highlights_section_title_en',
            'highlights_section_title_ar',
        ];

        $existing = array_values(array_filter($columns, fn (string $column) => Schema::hasColumn('visa_countries', $column)));

        if ($existing !== []) {
            Schema::table('visa_countries', function (Blueprint $table) use ($existing) {
                $table->dropColumn($existing);
            });
        }
    }
};
