<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('embassy_appointments')) {
            Schema::table('embassy_appointments', function (Blueprint $table) {
                if (! Schema::hasColumn('embassy_appointments', 'deleted_at')) {
                    $table->softDeletes();
                }
                if (! Schema::hasColumn('embassy_appointments', 'deleted_by')) {
                    $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('embassy_appointments')) {
            Schema::table('embassy_appointments', function (Blueprint $table) {
                if (Schema::hasColumn('embassy_appointments', 'deleted_at')) {
                    $table->dropSoftDeletes();
                }
                if (Schema::hasColumn('embassy_appointments', 'deleted_by')) {
                    $table->dropColumn('deleted_by');
                }
            });
        }
    }
};
