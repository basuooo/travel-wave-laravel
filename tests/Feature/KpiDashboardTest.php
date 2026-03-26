<?php

namespace Tests\Feature;

use App\Models\AccountingCustomerAccount;
use App\Models\AccountingCustomerPayment;
use App\Models\AccountingGeneralExpense;
use App\Models\AccountingGeneralExpenseCategory;
use App\Models\CrmTask;
use App\Models\Inquiry;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KpiDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_kpi_dashboard_and_see_core_sections(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin-kpi@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_active' => true,
        ]);

        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Important Lead',
            'phone' => '01000011111',
            'assigned_user_id' => $admin->id,
            'total_amount' => 1000,
            'paid_amount' => 400,
            'remaining_amount' => 600,
            'payment_status' => 'partially_paid',
        ]);

        DB::table('inquiries')->where('id', $lead->id)->update([
            'updated_at' => now()->subDays(7),
        ]);

        CrmTask::query()->create([
            'inquiry_id' => $lead->id,
            'assigned_user_id' => $admin->id,
            'created_by' => $admin->id,
            'title' => 'Overdue Task',
            'task_type' => CrmTask::TYPE_LEAD,
            'category' => CrmTask::CATEGORY_DOCUMENTS,
            'priority' => CrmTask::PRIORITY_HIGH,
            'status' => CrmTask::STATUS_IN_PROGRESS,
            'due_at' => now()->subDay(),
            'last_activity_at' => now()->subDay(),
        ]);

        $account = AccountingCustomerAccount::query()->create([
            'inquiry_id' => $lead->id,
            'assigned_user_id' => $admin->id,
            'created_by' => $admin->id,
            'customer_name' => 'Important Lead',
            'total_amount' => 1000,
            'paid_amount' => 400,
            'remaining_amount' => 600,
            'total_customer_expenses' => 100,
            'company_profit_before_seller' => 300,
            'seller_profit' => 30,
            'final_company_profit' => 270,
            'payment_status' => 'partially_paid',
        ]);

        AccountingCustomerPayment::query()->create([
            'accounting_customer_account_id' => $account->id,
            'created_by' => $admin->id,
            'amount' => 400,
            'payment_date' => today(),
            'payment_type' => 'payment',
        ]);

        $generalCategory = AccountingGeneralExpenseCategory::query()->firstOrCreate([
            'slug' => 'operations',
        ], [
            'name_ar' => 'تشغيل',
            'name_en' => 'Operations',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        AccountingGeneralExpense::query()->create([
            'accounting_general_expense_category_id' => $generalCategory->id,
            'created_by' => $admin->id,
            'amount' => 50,
            'expense_date' => today(),
            'note' => 'Internet',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.kpi.dashboard'))
            ->assertOk()
            ->assertSee('KPI Dashboard')
            ->assertSee('Important Lead')
            ->assertSee('Overdue Task')
            ->assertSee('Finance Overview')
            ->assertSee('Team Performance');
    }

    public function test_non_admin_sales_user_sees_only_own_kpi_scope(): void
    {
        $seller = User::query()->create([
            'name' => 'Seller One',
            'email' => 'seller-one@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'is_active' => true,
        ]);

        $otherSeller = User::query()->create([
            'name' => 'Seller Two',
            'email' => 'seller-two@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'is_active' => true,
        ]);

        $dashboardAccess = Permission::query()->firstOrCreate([
            'slug' => 'dashboard.access',
        ], [
            'name' => 'Dashboard Access',
            'module' => 'dashboard',
        ]);
        $reportsView = Permission::query()->firstOrCreate([
            'slug' => 'reports.view',
        ], [
            'name' => 'View Reports',
            'module' => 'dashboard',
        ]);
        $leadsView = Permission::query()->firstOrCreate([
            'slug' => 'leads.view',
        ], [
            'name' => 'View Leads',
            'module' => 'leads',
        ]);

        $role = Role::query()->create([
            'name' => 'Sales',
            'slug' => 'sales-test',
            'is_system' => false,
        ]);
        $role->permissions()->sync([$dashboardAccess->id, $reportsView->id, $leadsView->id]);
        $seller->roles()->sync([$role->id]);

        $myLead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'My Delayed Lead',
            'phone' => '01011111111',
            'assigned_user_id' => $seller->id,
        ]);

        $otherLead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Other Delayed Lead',
            'phone' => '01022222222',
            'assigned_user_id' => $otherSeller->id,
        ]);

        DB::table('inquiries')->whereIn('id', [$myLead->id, $otherLead->id])->update([
            'updated_at' => now()->subDays(7),
        ]);

        $this->actingAs($seller)
            ->get(route('admin.kpi.dashboard'))
            ->assertOk()
            ->assertSee('My Delayed Lead')
            ->assertDontSee('Other Delayed Lead')
            ->assertDontSee('Finance Overview');
    }
}
