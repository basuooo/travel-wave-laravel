<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_country_strip_items', function (Blueprint $table) {
            if (! Schema::hasColumn('home_country_strip_items', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }

            if (! Schema::hasColumn('home_country_strip_items', 'deleted_by')) {
                $table->foreignId('deleted_by')->nullable()->after('deleted_at')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('home_country_strip_items', function (Blueprint $table) {
            if (Schema::hasColumn('home_country_strip_items', 'deleted_by')) {
                $table->dropConstrainedForeignId('deleted_by');
            }

            if (Schema::hasColumn('home_country_strip_items', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
