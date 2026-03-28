<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('visa_countries', 'quick_summary_destination_label_en')) {
            Schema::table('visa_countries', function (Blueprint $table) {
                $table->text('quick_summary_destination_label_en')->nullable()->after('stay_duration_ar');
            });
        }

        if (! Schema::hasColumn('visa_countries', 'quick_summary_destination_label_ar')) {
            Schema::table('visa_countries', function (Blueprint $table) {
                $table->text('quick_summary_destination_label_ar')->nullable()->after('quick_summary_destination_label_en');
            });
        }

        if (! Schema::hasColumn('visa_countries', 'quick_summary_destination_icon')) {
            Schema::table('visa_countries', function (Blueprint $table) {
                $table->text('quick_summary_destination_icon')->nullable()->after('quick_summary_destination_label_ar');
            });
        }
    }

    public function down(): void
    {
        $columns = array_values(array_filter([
            'quick_summary_destination_label_en',
            'quick_summary_destination_label_ar',
            'quick_summary_destination_icon',
        ], fn (string $column) => Schema::hasColumn('visa_countries', $column)));

        if ($columns !== []) {
            Schema::table('visa_countries', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
