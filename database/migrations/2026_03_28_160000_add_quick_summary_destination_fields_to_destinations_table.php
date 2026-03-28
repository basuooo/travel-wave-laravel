<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            if (! Schema::hasColumn('destinations', 'quick_summary_destination_label_en')) {
                $table->string('quick_summary_destination_label_en')->nullable()->after('quick_info_title_ar');
            }

            if (! Schema::hasColumn('destinations', 'quick_summary_destination_label_ar')) {
                $table->string('quick_summary_destination_label_ar')->nullable()->after('quick_summary_destination_label_en');
            }

            if (! Schema::hasColumn('destinations', 'quick_summary_destination_icon')) {
                $table->string('quick_summary_destination_icon')->nullable()->after('quick_summary_destination_label_ar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            foreach ([
                'quick_summary_destination_icon',
                'quick_summary_destination_label_ar',
                'quick_summary_destination_label_en',
            ] as $column) {
                if (Schema::hasColumn('destinations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
