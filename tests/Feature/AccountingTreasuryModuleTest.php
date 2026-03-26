<?php

namespace Tests\Feature;

use App\Models\AccountingCustomerAccount;
use App\Models\AccountingGeneralExpenseCategory;
use App\Models\AccountingTreasury;
use App\Models\AccountingTreasuryTransaction;
use App\Models\CrmStatus;
use App\Models\Inquiry;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingTreasuryModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_view_treasury(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.accounting.treasuries.store'), [
                'name' => 'Main InstaPay',
                'type' => AccountingTreasury::TYPE_INSTAPAY,
                'identifier' => '01000000000',
                'opening_balance' => 1500,
                'is_active' => 1,
            ])
            ->assertRedirect();

        $treasury = AccountingTreasury::query()->where('name', 'Main InstaPay')->firstOrFail();

        $this->assertSame('1500.00', number_format((float) $treasury->opening_balance, 2, '.', ''));

        $this->actingAs($admin)
            ->get(route('admin.accounting.treasuries.show', $treasury))
            ->assertOk()
            ->assertSee('Main InstaPay')
            ->assertSee('1,500.00', false);
    }

    public function test_customer_payment_and_general_expense_affect_treasury_balance(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $treasury = AccountingTreasury::query()->create([
            'name' => 'Cash Treasury',
            'type' => AccountingTreasury::TYPE_CASH,
            'opening_balance' => 1000,
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        $lead = Inquiry::query()->create([
            'type' => 'visa',
            'full_name' => 'Treasury Customer',
            'phone' => '01012345678',
            'crm_status_id' => CrmStatus::query()->firstOrFail()->id,
            'status' => 'new',
            'total_amount' => 5000,
            'paid_amount' => 0,
        ]);

        $account = AccountingCustomerAccount::query()->create([
            'inquiry_id' => $lead->id,
            'customer_name' => $lead->full_name,
            'phone' => $lead->phone,
            'total_amount' => 5000,
            'paid_amount' => 0,
            'remaining_amount' => 5000,
            'payment_status' => 'unpaid',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.accounting.customers.payments.store', $account), [
                'amount' => 1200,
                'payment_date' => now()->toDateString(),
                'accounting_treasury_id' => $treasury->id,
            ])
            ->assertRedirect();

        $category = AccountingGeneralExpenseCategory::query()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.accounting.general-expenses.store'), [
                'accounting_general_expense_category_id' => $category->id,
                'accounting_treasury_id' => $treasury->id,
                'amount' => 300,
                'expense_date' => now()->toDateString(),
            ])
            ->assertRedirect();

        $treasury->refresh();

        $this->assertSame(2, AccountingTreasuryTransaction::query()->where('accounting_treasury_id', $treasury->id)->count());
        $this->assertSame('1900.00', number_format($treasury->currentBalance(), 2, '.', ''));
    }
}
