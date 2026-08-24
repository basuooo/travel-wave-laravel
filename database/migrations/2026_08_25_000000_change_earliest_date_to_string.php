<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            if (Schema::hasTable('embassy_appointments')) {
                Schema::table('embassy_appointments', function (Blueprint $table) {
                    $table->string('earliest_date', 255)->nullable()->change();
                });
            }
            if (Schema::hasTable('embassy_appointment_logs')) {
                Schema::table('embassy_appointment_logs', function (Blueprint $table) {
                    $table->string('old_earliest_date', 255)->nullable()->change();
                    $table->string('new_earliest_date', 255)->nullable()->change();
                });
            }
        } catch (\Throwable $e) {
            try {
                DB::statement("ALTER TABLE embassy_appointments MODIFY earliest_date VARCHAR(255) NULL");
                DB::statement("ALTER TABLE embassy_appointment_logs MODIFY old_earliest_date VARCHAR(255) NULL, MODIFY new_earliest_date VARCHAR(255) NULL");
            } catch (\Throwable $ex) {}
        }
    }

    public function down(): void
    {
    }
};
