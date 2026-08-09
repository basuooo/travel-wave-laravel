<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('visa_countries')) {
            return;
        }

        Schema::table('visa_countries', function (Blueprint $table) {
            if (! Schema::hasColumn('visa_countries', 'content_mode')) {
                $table->string('content_mode', 20)->default('normal')->after('is_active');
            }
            if (! Schema::hasColumn('visa_countries', 'html_content_en')) {
                $table->longText('html_content_en')->nullable()->after('content_mode');
            }
            if (! Schema::hasColumn('visa_countries', 'html_content_ar')) {
                $table->longText('html_content_ar')->nullable()->after('html_content_en');
            }
            if (! Schema::hasColumn('visa_countries', 'sections')) {
                $table->json('sections')->nullable()->after('html_content_ar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('visa_countries')) {
            return;
        }

        Schema::table('visa_countries', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['content_mode', 'html_content_en', 'html_content_ar', 'sections'] as $col) {
                if (Schema::hasColumn('visa_countries', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
