<?php

namespace Tests\Feature;

use App\Models\AccountingCustomerAccount;
use App\Models\AccountingCustomerPayment;
use App\Models\AccountingEmployeeTransaction;
use App\Models\CommissionStatement;
use App\Models\GoalTarget;
use App\Models\Inquiry;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoalsCommissionsModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_goal_and_view_progress(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $salesRole = Role::query()->where('slug', 'sales-leads-manager')->firstOrFail();
        $seller = User::query()->create([
            'name' => 'Goal Seller',
            'email' => 'goal-seller@travelwave.test',
            'password' => bcrypt('password123'),
            'is_admin' => true,
            'is_active' => true,
        ]);
        $seller->roles()->sync([$salesRole->id]);

        Inquiry::query()->create([
            'type' => 'visa',
            'full_name' => 'Lead One',
            'phone' => '01000000001',
            'assigned_user_id' => $seller->id,
            'crm_status_id' => \App\Models\CrmStatus::query()->first()->id,
            'status' => 'new',
            'created_at' => now()->startOfMonth()->addDay(),
            'updated_at' => now()->startOfMonth()->addDay(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.goals-commissions.targets.store'), [
                'user_id' => $seller->id,
                'target_type' => 'lead_count',
                'target_value' => 5,
                'period_type' => 'monthly',
                'period_start' => now()->startOfMonth()->toDateString(),
                'period_end' => now()->endOfMonth()->toDateString(),
                'is_active' => 1,
            ])
            ->assertRedirect();

        $goal = GoalTarget::query()->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.goals-commissions.targets.show', $goal))
            ->assertOk()
            ->assertSee('20.0%', false);
    }

    public function test_admin_can_generate_commission_statement_from_existing_accounting_logic(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $salesRole = Role::query()->where('slug', 'sales-leads-manager')->firstOrFail();
        $seller = User::query()->create([
            'name' => 'Commission Seller',
            'email' => 'commission-seller@travelwave.test',
            'password' => bcrypt('password123'),
            'is_admin' => true,
            'is_active' => true,
        ]);
        $seller->roles()->sync([$salesRole->id]);

        $lead = Inquiry::query()->create([
            'type' => 'visa',
            'full_name' => 'Commission Lead',
            'phone' => '01000000002',
            'assigned_user_id' => $seller->id,
            'crm_status_id' => \App\Models\CrmStatus::query()->first()->id,
            'status' => 'new',
            'total_amount' => 10000,
            'paid_amount' => 6000,
            'created_at' => now()->startOfMonth()->addDay(),
            'updated_at' => now()->startOfMonth()->addDay(),
        ]);

        $account = AccountingCustomerAccount::query()->create([
            'inquiry_id' => $lead->id,
            'assigned_user_id' => $seller->id,
            'customer_name' => $lead->full_name,
            'total_amount' => 10000,
            'paid_amount' => 6000,
            'remaining_amount' => 4000,
            'total_customer_expenses' => 1000,
            'company_profit_before_seller' => 5000,
            'seller_profit' => 500,
            'final_company_profit' => 4500,
            'payment_status' => 'partially_paid',
        ]);

        AccountingCustomerPayment::query()->create([
            'accounting_customer_account_id' => $account->id,
            'created_by' => $admin->id,
            'amount' => 6000,
            'payment_date' => now()->toDateString(),
            'payment_type' => 'payment',
        ]);

        AccountingEmployeeTransaction::query()->create([
            'user_id' => $seller->id,
            'created_by' => $admin->id,
            'transaction_type' => AccountingEmployeeTransaction::TYPE_COMMISSION,
            'amount' => 200,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.goals-commissions.commissions.store'), [
                'user_id' => $seller->id,
                'basis_type' => CommissionStatement::BASIS_SELLER_PROFIT_SHARE,
                'period_start' => now()->startOfMonth()->toDateString(),
                'period_end' => now()->endOfMonth()->toDateString(),
            ])
            ->assertRedirect();

        $statement = CommissionStatement::query()->firstOrFail();

        $this->assertSame('500.00', number_format((float) $statement->earned_amount, 2, '.', ''));
        $this->assertSame('200.00', number_format((float) $statement->paid_amount, 2, '.', ''));
        $this->assertSame('300.00', number_format((float) $statement->remaining_amount, 2, '.', ''));
        $this->assertSame('partially_paid', $statement->payment_status);
    }
}
