<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            if (! Schema::hasColumn('destinations', 'highlights_section_label_en')) {
                $table->string('highlights_section_label_en')->nullable()->after('best_time_description_ar');
            }

            if (! Schema::hasColumn('destinations', 'highlights_section_label_ar')) {
                $table->string('highlights_section_label_ar')->nullable()->after('highlights_section_label_en');
            }
        });
    }

    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('destinations', 'highlights_section_label_en')) {
                $dropColumns[] = 'highlights_section_label_en';
            }

            if (Schema::hasColumn('destinations', 'highlights_section_label_ar')) {
                $dropColumns[] = 'highlights_section_label_ar';
            }

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
