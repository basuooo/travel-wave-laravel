<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('crm_tasks') || Schema::hasColumn('crm_tasks', 'category')) {
            return;
        }

        Schema::table('crm_tasks', function (Blueprint $table) {
            $table->string('category', 100)->nullable()->after('task_type');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('crm_tasks') || ! Schema::hasColumn('crm_tasks', 'category')) {
            return;
        }

        Schema::table('crm_tasks', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
