<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('crm_lead_assignments')) {
            Schema::create('crm_lead_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inquiry_id')->constrained('inquiries')->cascadeOnDelete();
                $table->foreignId('old_assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('new_assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('changed_at')->nullable();
                $table->text('note')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_assignments');
    }
};
