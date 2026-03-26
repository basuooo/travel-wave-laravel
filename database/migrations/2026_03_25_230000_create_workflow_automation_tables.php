<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_automations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('trigger_type');
            $table->string('entity_type')->nullable();
            $table->json('conditions')->nullable();
            $table->json('actions');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('run_once')->default(false);
            $table->unsignedInteger('cooldown_minutes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_executed_at')->nullable();
            $table->timestamps();

            $table->index(['trigger_type', 'is_active', 'priority']);
            $table->index(['entity_type', 'is_active']);
        });

        Schema::create('workflow_execution_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_automation_id')->constrained('workflow_automations')->cascadeOnDelete();
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('trigger_type');
            $table->string('execution_status');
            $table->string('target_label')->nullable();
            $table->text('result_summary')->nullable();
            $table->text('error_message')->nullable();
            $table->json('context')->nullable();
            $table->timestamp('executed_at');
            $table->timestamps();

            $table->index(['workflow_automation_id', 'executed_at']);
            $table->index(['trigger_type', 'execution_status']);
            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_execution_logs');
        Schema::dropIfExists('workflow_automations');
    }
};
