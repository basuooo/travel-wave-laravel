<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('crm_follow_ups')) {
            Schema::create('crm_follow_ups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inquiry_id')->constrained('inquiries')->cascadeOnDelete();
                $table->foreignId('crm_status_id')->nullable()->constrained('crm_statuses')->nullOnDelete();
                $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status', 20)->default('pending');
                $table->timestamp('scheduled_at');
                $table->unsignedInteger('reminder_offset_minutes')->default(30);
                $table->timestamp('remind_at')->nullable();
                $table->timestamp('reminder_sent_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->text('note')->nullable();
                $table->text('completion_note')->nullable();
                $table->timestamps();
            });
        }

        $now = now();
        $callLater = DB::table('crm_statuses')->where('slug', 'call-later')->first();
        if (! $callLater) {
            DB::table('crm_statuses')->insert([
                'name_en' => 'Call Later',
                'name_ar' => 'اتصل لاحقًا',
                'slug' => 'call-later',
                'status_group' => 'lead',
                'color' => 'info',
                'sort_order' => 11,
                'is_default' => false,
                'is_system' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_follow_ups');
        Schema::dropIfExists('notifications');
    }
};
