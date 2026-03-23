<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('crm_statuses')) {
            return;
        }

        $now = now();
        $existing = DB::table('crm_statuses')->where('slug', 'call-later')->first();

        $payload = [
            'name_en' => 'Call Later',
            'name_ar' => 'اتصل لاحقًا',
            'slug' => 'call-later',
            'status_group' => 'lead',
            'color' => 'info',
            'sort_order' => 11,
            'is_default' => false,
            'is_system' => true,
            'is_active' => true,
        ];

        if ($existing) {
            DB::table('crm_statuses')
                ->where('id', $existing->id)
                ->update($payload + ['updated_at' => $now]);

            return;
        }

        DB::table('crm_statuses')->insert($payload + [
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('crm_statuses')) {
            return;
        }

        DB::table('crm_statuses')->where('slug', 'call-later')->delete();
    }
};
