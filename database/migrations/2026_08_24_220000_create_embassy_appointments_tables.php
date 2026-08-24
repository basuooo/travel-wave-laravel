<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create embassy_appointments table
        if (! Schema::hasTable('embassy_appointments')) {
            Schema::create('embassy_appointments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('visa_country_id')->constrained('visa_countries')->cascadeOnDelete();
                $table->foreignId('visa_record_id')->nullable()->constrained('visa_records')->nullOnDelete();
                $table->string('visa_type')->default('سياحة');
                $table->string('appointment_center')->default('BLS');
                $table->string('appointment_type')->default('Regular');
                $table->string('status', 30)->default('unknown'); // available_now, available_later, no_availability, unknown
                $table->date('earliest_date')->nullable();
                $table->timestamp('last_updated_at')->nullable();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->text('booking_link')->nullable();
                $table->timestamps();

                $table->unique(['visa_country_id', 'visa_type', 'appointment_center', 'appointment_type'], 'embassy_appts_unique_combo');
            });
        }

        // 2. Create embassy_availability_events table
        if (! Schema::hasTable('embassy_availability_events')) {
            Schema::create('embassy_availability_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('embassy_appointment_id')->constrained('embassy_appointments')->cascadeOnDelete();
                $table->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status', 30)->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 3. Create embassy_appointment_notifications table
        if (! Schema::hasTable('embassy_appointment_notifications')) {
            Schema::create('embassy_appointment_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('embassy_availability_event_id')->constrained('embassy_availability_events')->cascadeOnDelete();
                $table->foreignId('embassy_appointment_id')->constrained('embassy_appointments')->cascadeOnDelete();
                $table->foreignId('inquiry_id')->constrained('inquiries')->cascadeOnDelete();
                $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
                $table->string('status', 30)->default('pending'); // pending, notified, snoozed, contacted, dismissed
                $table->timestamp('snoozed_until')->nullable();
                $table->timestamp('contacted_at')->nullable();
                $table->string('contact_result', 50)->nullable(); // agreed, no_answer, call_later, not_ready, refused, other
                $table->text('contact_notes')->nullable();
                $table->timestamps();

                $table->unique(['embassy_availability_event_id', 'inquiry_id'], 'embassy_notif_unique_event_lead');
            });
        }

        // 4. Create embassy_appointment_logs table
        if (! Schema::hasTable('embassy_appointment_logs')) {
            Schema::create('embassy_appointment_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('embassy_appointment_id')->constrained('embassy_appointments')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('user_name')->nullable();
                $table->string('action');
                $table->string('old_status', 30)->nullable();
                $table->string('new_status', 30)->nullable();
                $table->date('old_earliest_date')->nullable();
                $table->date('new_earliest_date')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 5. Add optional matching fields to inquiries table if not present
        Schema::table('inquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('inquiries', 'visa_country_id')) {
                $table->foreignId('visa_country_id')->nullable()->after('country')->constrained('visa_countries')->nullOnDelete();
            }
            if (! Schema::hasColumn('inquiries', 'appointment_center')) {
                $table->string('appointment_center')->nullable()->after('visa_country_id');
            }
            if (! Schema::hasColumn('inquiries', 'appointment_type')) {
                $table->string('appointment_type')->nullable()->after('appointment_center');
            }
        });

        // 6. Ensure CrmStatus exists for "انتظار فتح مواعيد السفارة"
        if (Schema::hasTable('crm_statuses')) {
            $existing = DB::table('crm_statuses')->where('slug', 'awaiting-embassy-appointment')->first();
            $now = now();
            if (! $existing) {
                DB::table('crm_statuses')->insert([
                    'slug' => 'awaiting-embassy-appointment',
                    'name_ar' => 'انتظار فتح مواعيد السفارة',
                    'name_en' => 'Awaiting Embassy Appointment',
                    'status_group' => 'secondary',
                    'color' => 'warning',
                    'sort_order' => 25,
                    'is_default' => false,
                    'is_system' => true,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('embassy_appointment_logs');
        Schema::dropIfExists('embassy_appointment_notifications');
        Schema::dropIfExists('embassy_availability_events');
        Schema::dropIfExists('embassy_appointments');

        Schema::table('inquiries', function (Blueprint $table) {
            if (Schema::hasColumn('inquiries', 'visa_country_id')) {
                $table->dropConstrainedForeignId('visa_country_id');
            }
            if (Schema::hasColumn('inquiries', 'appointment_center')) {
                $table->dropColumn('appointment_center');
            }
            if (Schema::hasColumn('inquiries', 'appointment_type')) {
                $table->dropColumn('appointment_type');
            }
        });
    }
};
