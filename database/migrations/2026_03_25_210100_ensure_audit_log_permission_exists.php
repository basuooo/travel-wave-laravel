<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        Permission::query()->firstOrCreate(
            ['slug' => 'audit_logs.view'],
            [
                'module' => 'audit',
                'name' => 'View Audit Logs',
                'description' => 'Review system audit logs, accountability records, and traceability events.',
            ]
        );
    }

    public function down(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        Permission::query()->where('slug', 'audit_logs.view')->delete();
    }
};
