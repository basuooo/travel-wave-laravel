<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('goal_targets')) {
            Schema::create('goal_targets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('target_type', 100);
                $table->decimal('target_value', 14, 2);
                $table->string('period_type', 30)->default('monthly');
                $table->date('period_start');
                $table->date('period_end');
                $table->text('note')->nullable();
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['user_id', 'target_type'], 'goal_targets_user_type_idx');
                $table->index(['period_start', 'period_end'], 'goal_targets_period_idx');
            });
        }

        if (! Schema::hasTable('commission_statements')) {
            Schema::create('commission_statements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('basis_type', 100);
                $table->date('period_start');
                $table->date('period_end');
                $table->decimal('earned_amount', 14, 2)->default(0);
                $table->decimal('paid_amount', 14, 2)->default(0);
                $table->decimal('remaining_amount', 14, 2)->default(0);
                $table->string('payment_status', 30)->default('unpaid');
                $table->json('calculation_snapshot')->nullable();
                $table->text('note')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['user_id', 'basis_type', 'period_start', 'period_end'], 'commission_statements_unique_period');
                $table->index(['payment_status', 'period_start', 'period_end'], 'commission_statements_status_period_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_statements');
        Schema::dropIfExists('goal_targets');
    }
};
