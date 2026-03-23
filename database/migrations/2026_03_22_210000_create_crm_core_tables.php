<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_ar');
            $table->string('slug')->unique();
            $table->string('status_group', 20)->default('primary');
            $table->string('color', 30)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_system')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('inquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('inquiries', 'whatsapp_number')) {
                $table->text('whatsapp_number')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('inquiries', 'country')) {
                $table->text('country')->nullable()->after('whatsapp_number');
            }
            if (! Schema::hasColumn('inquiries', 'crm_status_id')) {
                $table->foreignId('crm_status_id')->nullable()->after('status')->constrained('crm_statuses')->nullOnDelete();
            }
            if (! Schema::hasColumn('inquiries', 'crm_status2_id')) {
                $table->foreignId('crm_status2_id')->nullable()->after('crm_status_id')->constrained('crm_statuses')->nullOnDelete();
            }
            if (! Schema::hasColumn('inquiries', 'assigned_user_id')) {
                $table->foreignId('assigned_user_id')->nullable()->after('crm_status2_id')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('inquiries', 'lead_source')) {
                $table->text('lead_source')->nullable()->after('assigned_user_id');
            }
            if (! Schema::hasColumn('inquiries', 'campaign_name')) {
                $table->text('campaign_name')->nullable()->after('lead_source');
            }
            if (! Schema::hasColumn('inquiries', 'utm_source')) {
                $table->text('utm_source')->nullable()->after('campaign_name');
            }
            if (! Schema::hasColumn('inquiries', 'utm_campaign')) {
                $table->text('utm_campaign')->nullable()->after('utm_source');
            }
            if (! Schema::hasColumn('inquiries', 'priority')) {
                $table->string('priority', 20)->nullable()->after('utm_campaign');
            }
            if (! Schema::hasColumn('inquiries', 'last_follow_up_at')) {
                $table->timestamp('last_follow_up_at')->nullable()->after('priority');
            }
            if (! Schema::hasColumn('inquiries', 'next_follow_up_at')) {
                $table->timestamp('next_follow_up_at')->nullable()->after('last_follow_up_at');
            }
            if (! Schema::hasColumn('inquiries', 'follow_up_result')) {
                $table->text('follow_up_result')->nullable()->after('next_follow_up_at');
            }
        });

        Schema::create('crm_lead_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->constrained('inquiries')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->longText('body');
            $table->timestamps();
        });

        Schema::create('crm_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->constrained('inquiries')->cascadeOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 20)->default('open');
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        $now = now();
        DB::table('crm_statuses')->insert([
            ['name_en' => 'New Lead', 'name_ar' => 'عميل جديد', 'slug' => 'new-lead', 'status_group' => 'primary', 'color' => 'warning', 'sort_order' => 1, 'is_default' => true, 'is_system' => true, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name_en' => 'No Answer', 'name_ar' => 'لا يرد', 'slug' => 'no-answer', 'status_group' => 'primary', 'color' => 'secondary', 'sort_order' => 2, 'is_default' => false, 'is_system' => true, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name_en' => 'Not Interested', 'name_ar' => 'غير مهتم', 'slug' => 'not-interested', 'status_group' => 'primary', 'color' => 'danger', 'sort_order' => 3, 'is_default' => false, 'is_system' => true, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name_en' => 'Duplicate', 'name_ar' => 'مكرر', 'slug' => 'duplicate', 'status_group' => 'primary', 'color' => 'dark', 'sort_order' => 4, 'is_default' => false, 'is_system' => true, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name_en' => 'Qualified', 'name_ar' => 'مؤهل', 'slug' => 'qualified', 'status_group' => 'primary', 'color' => 'info', 'sort_order' => 5, 'is_default' => false, 'is_system' => true, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name_en' => 'Complete Lead', 'name_ar' => 'مكتمل', 'slug' => 'complete-lead', 'status_group' => 'primary', 'color' => 'success', 'sort_order' => 6, 'is_default' => false, 'is_system' => true, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name_en' => 'Pending Callback', 'name_ar' => 'بانتظار إعادة الاتصال', 'slug' => 'pending-callback', 'status_group' => 'secondary', 'color' => 'secondary', 'sort_order' => 1, 'is_default' => true, 'is_system' => true, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name_en' => 'WhatsApp Follow-up', 'name_ar' => 'متابعة واتساب', 'slug' => 'whatsapp-follow-up', 'status_group' => 'secondary', 'color' => 'success', 'sort_order' => 2, 'is_default' => false, 'is_system' => true, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name_en' => 'Awaiting Documents', 'name_ar' => 'بانتظار المستندات', 'slug' => 'awaiting-documents', 'status_group' => 'secondary', 'color' => 'warning', 'sort_order' => 3, 'is_default' => false, 'is_system' => true, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name_en' => 'Appointment Booked', 'name_ar' => 'تم حجز الموعد', 'slug' => 'appointment-booked', 'status_group' => 'secondary', 'color' => 'info', 'sort_order' => 4, 'is_default' => false, 'is_system' => true, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $defaultPrimaryId = DB::table('crm_statuses')->where('slug', 'new-lead')->value('id');

        DB::table('inquiries')
            ->whereNull('crm_status_id')
            ->update([
                'crm_status_id' => $defaultPrimaryId,
                'whatsapp_number' => DB::raw('COALESCE(whatsapp_number, phone)'),
                'country' => DB::raw('COALESCE(country, nationality)'),
                'lead_source' => DB::raw("COALESCE(lead_source, 'website')"),
                'priority' => DB::raw("COALESCE(priority, 'normal')"),
            ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_tasks');
        Schema::dropIfExists('crm_lead_notes');

        Schema::table('inquiries', function (Blueprint $table) {
            foreach ([
                'crm_status_id',
                'crm_status2_id',
                'assigned_user_id',
            ] as $foreignKey) {
                if (Schema::hasColumn('inquiries', $foreignKey)) {
                    $table->dropConstrainedForeignId($foreignKey);
                }
            }

            foreach ([
                'whatsapp_number',
                'country',
                'lead_source',
                'campaign_name',
                'utm_source',
                'utm_campaign',
                'priority',
                'last_follow_up_at',
                'next_follow_up_at',
                'follow_up_result',
            ] as $column) {
                if (Schema::hasColumn('inquiries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::dropIfExists('crm_statuses');
    }
};
