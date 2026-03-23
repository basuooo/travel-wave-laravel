<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $existing = DB::table('crm_statuses')->where('slug', 'merged')->first();

        if ($existing) {
            DB::table('crm_statuses')
                ->where('id', $existing->id)
                ->update([
                    'name_ar' => 'دمج',
                    'name_en' => 'Merged',
                    'status_group' => 'primary',
                    'is_active' => true,
                    'sort_order' => $existing->sort_order ?? 999,
                    'updated_at' => now(),
                ]);

            return;
        }

        DB::table('crm_statuses')->insert([
            'slug' => 'merged',
            'name_ar' => 'دمج',
            'name_en' => 'Merged',
            'status_group' => 'primary',
            'is_active' => true,
            'sort_order' => 999,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('crm_statuses')->where('slug', 'merged')->delete();
    }
};
