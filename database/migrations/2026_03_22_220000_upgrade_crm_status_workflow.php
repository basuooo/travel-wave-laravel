<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            if (! Schema::hasColumn('inquiries', 'status_1_updated_at')) {
                $table->timestamp('status_1_updated_at')->nullable()->after('crm_status_id');
            }
            if (! Schema::hasColumn('inquiries', 'status_1_updated_by')) {
                $table->foreignId('status_1_updated_by')->nullable()->after('status_1_updated_at')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('inquiries', 'status_2_updated_at')) {
                $table->timestamp('status_2_updated_at')->nullable()->after('crm_status2_id');
            }
            if (! Schema::hasColumn('inquiries', 'status_2_updated_by')) {
                $table->foreignId('status_2_updated_by')->nullable()->after('status_2_updated_at')->constrained('users')->nullOnDelete();
            }
        });

        if (! Schema::hasTable('crm_status_updates')) {
            Schema::create('crm_status_updates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inquiry_id')->constrained('inquiries')->cascadeOnDelete();
                $table->string('status_level', 20);
                $table->foreignId('old_status_id')->nullable()->constrained('crm_statuses')->nullOnDelete();
                $table->foreignId('new_status_id')->nullable()->constrained('crm_statuses')->nullOnDelete();
                $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('changed_at')->nullable();
                $table->timestamps();
            });
        }

        $statuses = [
            ['slug' => 'new-lead', 'name_en' => 'New Lead', 'name_ar' => 'عميل جديد', 'status_group' => 'primary', 'color' => 'warning', 'sort_order' => 1, 'is_default' => true, 'is_system' => true, 'is_active' => true],
            ['slug' => 'no-answer', 'name_en' => 'No Answer', 'name_ar' => 'لا يوجد رد', 'status_group' => 'primary', 'color' => 'secondary', 'sort_order' => 2, 'is_default' => false, 'is_system' => true, 'is_active' => true],
            ['slug' => 'closed', 'name_en' => 'Closed', 'name_ar' => 'مغلق', 'status_group' => 'primary', 'color' => 'dark', 'sort_order' => 3, 'is_default' => false, 'is_system' => true, 'is_active' => true],
            ['slug' => 'not-interested', 'name_en' => 'Not Interested', 'name_ar' => 'غير مهتم', 'status_group' => 'primary', 'color' => 'danger', 'sort_order' => 4, 'is_default' => false, 'is_system' => true, 'is_active' => true],
            ['slug' => 'duplicate', 'name_en' => 'Duplicate', 'name_ar' => 'مكرر', 'status_group' => 'primary', 'color' => 'dark', 'sort_order' => 5, 'is_default' => false, 'is_system' => true, 'is_active' => true],
            ['slug' => 'complete-lead', 'name_en' => 'Complete Customer', 'name_ar' => 'عميل مكتمل', 'status_group' => 'primary', 'color' => 'success', 'sort_order' => 6, 'is_default' => false, 'is_system' => true, 'is_active' => true],
            ['slug' => 'qualified', 'name_en' => 'Qualified', 'name_ar' => 'مؤهل', 'status_group' => 'primary', 'color' => 'info', 'sort_order' => 7, 'is_default' => false, 'is_system' => true, 'is_active' => true],
            ['slug' => 'unavailable', 'name_en' => 'Unavailable', 'name_ar' => 'غير متاح', 'status_group' => 'secondary', 'color' => 'secondary', 'sort_order' => 10, 'is_default' => false, 'is_system' => true, 'is_active' => true],
            ['slug' => 'busy', 'name_en' => 'Busy', 'name_ar' => 'مشغول', 'status_group' => 'secondary', 'color' => 'secondary', 'sort_order' => 11, 'is_default' => false, 'is_system' => true, 'is_active' => true],
            ['slug' => 'cancelled', 'name_en' => 'Cancelled', 'name_ar' => 'إلغاء', 'status_group' => 'secondary', 'color' => 'danger', 'sort_order' => 12, 'is_default' => false, 'is_system' => true, 'is_active' => true],
            ['slug' => 'no-bank-account', 'name_en' => 'No Bank Account', 'name_ar' => 'لا يوجد حساب بنكي', 'status_group' => 'secondary', 'color' => 'warning', 'sort_order' => 13, 'is_default' => false, 'is_system' => true, 'is_active' => true],
            ['slug' => 'bank-account-less-than-6-months', 'name_en' => 'Bank Account أقل من 6 Months', 'name_ar' => 'حساب بنكي أقل من 6 أشهر', 'status_group' => 'secondary', 'color' => 'warning', 'sort_order' => 14, 'is_default' => false, 'is_system' => true, 'is_active' => true],
            ['slug' => 'bank-account-less-than-80k', 'name_en' => 'Bank Account أقل من 80K', 'name_ar' => 'حساب بنكي أقل من 80 ألف', 'status_group' => 'secondary', 'color' => 'warning', 'sort_order' => 15, 'is_default' => false, 'is_system' => true, 'is_active' => true],
            ['slug' => 'work-contract-not-tourism', 'name_en' => 'Work Contract, Not Tourism', 'name_ar' => 'عقد عمل وليس سياحة', 'status_group' => 'secondary', 'color' => 'danger', 'sort_order' => 16, 'is_default' => false, 'is_system' => true, 'is_active' => true],
            ['slug' => 'wrong-number', 'name_en' => 'Wrong Number', 'name_ar' => 'رقم خاطئ', 'status_group' => 'secondary', 'color' => 'danger', 'sort_order' => 17, 'is_default' => false, 'is_system' => true, 'is_active' => true],
            ['slug' => 'far-location', 'name_en' => 'Far Location', 'name_ar' => 'المكان بعيد', 'status_group' => 'secondary', 'color' => 'secondary', 'sort_order' => 18, 'is_default' => false, 'is_system' => true, 'is_active' => true],
            ['slug' => 'international-number', 'name_en' => 'International Number', 'name_ar' => 'رقم دولي', 'status_group' => 'secondary', 'color' => 'secondary', 'sort_order' => 19, 'is_default' => false, 'is_system' => true, 'is_active' => true],
            ['slug' => 'documents-complete', 'name_en' => 'Documents Complete', 'name_ar' => 'الأوراق مكتملة', 'status_group' => 'secondary', 'color' => 'success', 'sort_order' => 20, 'is_default' => false, 'is_system' => true, 'is_active' => true],
            ['slug' => 'missing-documents', 'name_en' => 'Missing Documents', 'name_ar' => 'أوراق ناقصة مستندات', 'status_group' => 'secondary', 'color' => 'warning', 'sort_order' => 21, 'is_default' => false, 'is_system' => true, 'is_active' => true],
            ['slug' => 'call-later', 'name_en' => 'Call Later', 'name_ar' => 'اتصل لاحقًا', 'status_group' => 'secondary', 'color' => 'info', 'sort_order' => 22, 'is_default' => false, 'is_system' => true, 'is_active' => true],
        ];

        $now = now();

        foreach ($statuses as $status) {
            $existing = DB::table('crm_statuses')->where('slug', $status['slug'])->first();

            if ($existing) {
                DB::table('crm_statuses')
                    ->where('slug', $status['slug'])
                    ->update($status + ['updated_at' => $now]);
            } else {
                DB::table('crm_statuses')->insert($status + ['created_at' => $now, 'updated_at' => $now]);
            }
        }

        $defaultPrimaryId = DB::table('crm_statuses')->where('slug', 'new-lead')->value('id');

        DB::table('inquiries')
            ->whereNull('crm_status_id')
            ->update(['crm_status_id' => $defaultPrimaryId]);

        DB::table('inquiries')
            ->whereNull('status_1_updated_at')
            ->update(['status_1_updated_at' => $now]);
    }

    public function down(): void
    {
        if (Schema::hasTable('crm_status_updates')) {
            Schema::dropIfExists('crm_status_updates');
        }

        Schema::table('inquiries', function (Blueprint $table) {
            foreach (['status_1_updated_by', 'status_2_updated_by'] as $column) {
                if (Schema::hasColumn('inquiries', $column)) {
                    $table->dropConstrainedForeignId($column);
                }
            }

            foreach (['status_1_updated_at', 'status_2_updated_at'] as $column) {
                if (Schema::hasColumn('inquiries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
