<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingCustomerAccount;
use App\Models\AccountingCustomerExpense;
use App\Models\AccountingCustomerPayment;
use App\Models\AccountingEmployeeTransaction;
use App\Models\AccountingExpenseCategory;
use App\Models\AccountingExpenseSubcategory;
use App\Models\AccountingGeneralExpense;
use App\Models\AccountingGeneralExpenseCategory;
use App\Models\AccountingTreasury;
use App\Models\Inquiry;
use App\Models\User;
use App\Support\AdminNotificationCenterService;
use App\Support\AccountingCalculatorService;
use App\Support\AccountingReportService;
use App\Support\AccountingTreasuryService;
use App\Support\AuditLogService;
use App\Support\WorkflowAutomationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AccountingController extends Controller
{
    public function dashboard(Request $request, AccountingReportService $reportService)
    {
        return view('admin.accounting.dashboard', $reportService->build($request) + [
            'sellers' => $this->salesUsers(),
        ]);
    }

    public function customers(Request $request)
    {
        $query = AccountingCustomerAccount::query()
            ->with(['inquiry.crmSource', 'assignedUser'])
            ->latest();

        if ($request->filled('seller_id')) {
            $query->where('assigned_user_id', $request->integer('seller_id'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->string('payment_status'));
        }

        if ($request->filled('from')) {
            $query->whereHas('inquiry', fn ($leadQuery) => $leadQuery->whereDate('created_at', '>=', $request->date('from')));
        }

        if ($request->filled('to')) {
            $query->whereHas('inquiry', fn ($leadQuery) => $leadQuery->whereDate('created_at', '<=', $request->date('to')));
        }

        return view('admin.accounting.customers.index', [
            'items' => $query->paginate(20)->withQueryString(),
            'sellers' => $this->salesUsers(),
        ]);
    }

    public function customer(AccountingCustomerAccount $account)
    {
        $account->load([
            'inquiry.crmSource',
            'assignedUser',
            'payments.creator',
            'payments.treasury',
            'expenses.category',
            'expenses.subcategory',
            'expenses.creator',
            'expenses.treasury',
        ]);

        return view('admin.accounting.customers.show', [
            'account' => $account,
            'expenseCategories' => $this->expenseCategories(),
            'treasuries' => $this->activeTreasuries(),
        ]);
    }

    public function storePayment(
        Request $request,
        AccountingCustomerAccount $account,
        AccountingCalculatorService $calculator,
        AdminNotificationCenterService $notificationCenterService,
        AccountingTreasuryService $treasuryService
    )
    {
        $auditLogService = app(AuditLogService::class);
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
            'payment_date' => ['required', 'date'],
            'accounting_treasury_id' => ['required', Rule::exists('accounting_treasuries', 'id')->where('is_active', 1)],
            'note' => ['nullable', 'string'],
        ]);

        $nextPaid = round((float) ($account->paid_amount ?? 0) + (float) $data['amount'], 2);
        if ($nextPaid > (float) ($account->total_amount ?? 0)) {
            return back()->withErrors([
                'amount' => __('admin.accounting_paid_amount_exceeds_total'),
            ]);
        }

        $payment = $account->payments()->create([
            'accounting_treasury_id' => $data['accounting_treasury_id'],
            'created_by' => auth()->id(),
            'amount' => $data['amount'],
            'payment_date' => $data['payment_date'],
            'note' => $data['note'] ?? null,
            'payment_type' => 'payment',
        ]);
        $treasuryService->syncForCustomerPayment($payment);

        $calculator->syncLeadPaymentSummary($account->inquiry()->firstOrFail(), $nextPaid, auth()->id());
        $notificationCenterService->createAccountingPaymentNotification($account->fresh(['assignedUser', 'inquiry']), $payment->fresh(), $request->user());
        $auditLogService->log(
            $request->user(),
            'accounting',
            'payment_added',
            $payment->fresh(),
            [
                'title' => __('admin.accounting_payment_added'),
                'description' => $account->customer_name,
                'new_values' => [
                    'amount' => $payment->amount,
                    'payment_date' => $payment->payment_date,
                    'account' => $account->customer_name,
                ],
                'changed_fields' => ['amount', 'payment_date'],
            ]
        );
        app(WorkflowAutomationService::class)->dispatch(WorkflowAutomationService::TRIGGER_PAYMENT_ADDED, $payment->fresh('account.assignedUser'), [
            'actor' => $request->user(),
            'amount' => (float) $payment->amount,
            'payment_status' => $account->fresh()->payment_status,
        ]);

        return back()->with('success', __('admin.accounting_payment_added'));
    }

    public function storeCustomerExpense(
        Request $request,
        AccountingCustomerAccount $account,
        AccountingCalculatorService $calculator,
        AccountingTreasuryService $treasuryService
    )
    {
        $auditLogService = app(AuditLogService::class);
        $data = $request->validate([
            'accounting_expense_category_id' => ['required', 'exists:accounting_expense_categories,id'],
            'accounting_expense_subcategory_id' => ['nullable', 'exists:accounting_expense_subcategories,id'],
            'accounting_treasury_id' => ['required', Rule::exists('accounting_treasuries', 'id')->where('is_active', 1)],
            'amount' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $expense = $account->expenses()->create($data + [
            'created_by' => auth()->id(),
        ]);
        $treasuryService->syncForCustomerExpense($expense);

        $calculator->syncLeadAccount($account->inquiry()->firstOrFail(), auth()->id());
        $auditLogService->log(
            $request->user(),
            'accounting',
            'expense_added',
            $expense->fresh(),
            [
                'title' => __('admin.accounting_customer_expense_added'),
                'description' => $account->customer_name,
                'new_values' => [
                    'amount' => $expense->amount,
                    'expense_date' => $expense->expense_date,
                    'note' => $expense->note,
                ],
                'changed_fields' => ['amount', 'expense_date', 'note'],
            ]
        );

        return back()->with('success', __('admin.accounting_customer_expense_added'));
    }

    public function destroyCustomerExpense(
        AccountingCustomerExpense $expense,
        AccountingCalculatorService $calculator,
        AccountingTreasuryService $treasuryService
    )
    {
        $auditLogService = app(AuditLogService::class);
        $account = $expense->account()->firstOrFail();
        $inquiry = $account->inquiry()->firstOrFail();
        $auditLogService->log(
            auth()->user(),
            'accounting',
            'expense_deleted',
            $expense,
            [
                'title' => __('admin.accounting_customer_expense_deleted'),
                'description' => $account->customer_name,
                'old_values' => [
                    'amount' => $expense->amount,
                    'expense_date' => $expense->expense_date,
                    'note' => $expense->note,
                ],
                'changed_fields' => ['amount', 'expense_date', 'note'],
            ]
        );
        $treasuryService->deleteForRelated($expense);
        $expense->delete();
        $calculator->syncLeadAccount($inquiry, auth()->id());

        return back()->with('success', __('admin.accounting_customer_expense_deleted'));
    }

    public function generalExpenses(Request $request)
    {
        $query = AccountingGeneralExpense::query()->with(['category', 'creator', 'treasury'])->latest('expense_date');

        if ($request->filled('category_id')) {
            $query->where('accounting_general_expense_category_id', $request->integer('category_id'));
        }

        if ($request->filled('from')) {
            $query->whereDate('expense_date', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('expense_date', '<=', $request->date('to'));
        }

        return view('admin.accounting.general-expenses.index', [
            'items' => $query->paginate(20)->withQueryString(),
            'categories' => $this->generalExpenseCategories(),
            'treasuries' => $this->activeTreasuries(),
        ]);
    }

    public function storeGeneralExpense(Request $request, AccountingTreasuryService $treasuryService)
    {
        $auditLogService = app(AuditLogService::class);
        $data = $request->validate([
            'accounting_general_expense_category_id' => ['required', 'exists:accounting_general_expense_categories,id'],
            'accounting_treasury_id' => ['required', Rule::exists('accounting_treasuries', 'id')->where('is_active', 1)],
            'amount' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $expense = AccountingGeneralExpense::query()->create($data + [
            'created_by' => auth()->id(),
        ]);
        $treasuryService->syncForGeneralExpense($expense);

        $auditLogService->log(
            $request->user(),
            'accounting',
            'expense_added',
            $expense,
            [
                'title' => __('admin.accounting_general_expense_added'),
                'new_values' => [
                    'amount' => $expense->amount,
                    'expense_date' => $expense->expense_date,
                    'note' => $expense->note,
                ],
                'changed_fields' => ['amount', 'expense_date', 'note'],
            ]
        );

        return back()->with('success', __('admin.accounting_general_expense_added'));
    }

    public function updateGeneralExpense(Request $request, AccountingGeneralExpense $expense, AccountingTreasuryService $treasuryService)
    {
        $auditLogService = app(AuditLogService::class);
        $before = [
            'accounting_treasury_id' => $expense->accounting_treasury_id,
            'amount' => $expense->amount,
            'expense_date' => $expense->expense_date,
            'note' => $expense->note,
        ];
        $data = $request->validate([
            'accounting_general_expense_category_id' => ['required', 'exists:accounting_general_expense_categories,id'],
            'accounting_treasury_id' => ['required', Rule::exists('accounting_treasuries', 'id')->where('is_active', 1)],
            'amount' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $expense->update($data);
        $treasuryService->syncForGeneralExpense($expense);
        $diff = $auditLogService->diff($before, [
            'accounting_treasury_id' => $expense->accounting_treasury_id,
            'amount' => $expense->amount,
            'expense_date' => $expense->expense_date,
            'note' => $expense->note,
        ]);

        if ($diff['changed_fields'] !== []) {
            $auditLogService->log(
                $request->user(),
                'accounting',
                'expense_updated',
                $expense,
                [
                    'title' => __('admin.accounting_general_expense_updated'),
                    'old_values' => $diff['old_values'],
                    'new_values' => $diff['new_values'],
                    'changed_fields' => $diff['changed_fields'],
                ]
            );
        }

        return back()->with('success', __('admin.accounting_general_expense_updated'));
    }

    public function destroyGeneralExpense(AccountingGeneralExpense $expense, AccountingTreasuryService $treasuryService)
    {
        app(AuditLogService::class)->log(
            auth()->user(),
            'accounting',
            'expense_deleted',
            $expense,
            [
                'title' => __('admin.accounting_general_expense_deleted'),
                'old_values' => [
                    'amount' => $expense->amount,
                    'expense_date' => $expense->expense_date,
                    'note' => $expense->note,
                ],
                'changed_fields' => ['amount', 'expense_date', 'note'],
            ]
        );
        $treasuryService->deleteForRelated($expense);
        $expense->delete();

        return back()->with('success', __('admin.accounting_general_expense_deleted'));
    }

    public function employees(Request $request)
    {
        $query = AccountingEmployeeTransaction::query()->with(['user', 'creator', 'treasury'])->latest('transaction_date');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->string('transaction_type'));
        }

        if ($request->filled('from')) {
            $query->whereDate('transaction_date', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('transaction_date', '<=', $request->date('to'));
        }

        $employeeSummary = AccountingEmployeeTransaction::query()
            ->select('user_id')
            ->selectRaw("SUM(CASE WHEN transaction_type = 'salary' THEN amount ELSE 0 END) as salary_total")
            ->selectRaw("SUM(CASE WHEN transaction_type = 'advance' THEN amount ELSE 0 END) as advance_total")
            ->selectRaw("SUM(CASE WHEN transaction_type = 'commission' THEN amount ELSE 0 END) as commission_total")
            ->selectRaw("SUM(CASE WHEN transaction_type = 'bonus' THEN amount ELSE 0 END) as bonus_total")
            ->selectRaw("SUM(CASE WHEN transaction_type = 'deduction' THEN amount ELSE 0 END) as deduction_total")
            ->when($request->filled('from'), fn ($q) => $q->whereDate('transaction_date', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('transaction_date', '<=', $request->date('to')))
            ->groupBy('user_id')
            ->with('user')
            ->get();

        return view('admin.accounting.employees.index', [
            'items' => $query->paginate(20)->withQueryString(),
            'employees' => $this->salesUsers(true),
            'typeOptions' => AccountingEmployeeTransaction::typeOptions(),
            'employeeSummary' => $employeeSummary,
            'treasuries' => $this->activeTreasuries(),
        ]);
    }

    public function storeEmployeeTransaction(Request $request, AccountingTreasuryService $treasuryService)
    {
        $auditLogService = app(AuditLogService::class);
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'transaction_type' => ['required', 'in:salary,advance,commission,bonus,deduction'],
            'accounting_treasury_id' => ['nullable', Rule::exists('accounting_treasuries', 'id')->where('is_active', 1)],
            'amount' => ['required', 'numeric', 'min:0'],
            'transaction_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ]);
        $data = $this->normalizeEmployeeTransactionTreasury($data);

        $transaction = AccountingEmployeeTransaction::query()->create($data + [
            'created_by' => auth()->id(),
        ]);
        $treasuryService->syncForEmployeeTransaction($transaction);

        $auditLogService->log(
            $request->user(),
            'accounting',
            'transaction_added',
            $transaction,
            [
                'title' => __('admin.accounting_employee_transaction_added'),
                'new_values' => [
                    'user_id' => $transaction->user_id,
                    'transaction_type' => $transaction->transaction_type,
                    'amount' => $transaction->amount,
                    'transaction_date' => $transaction->transaction_date,
                ],
                'changed_fields' => ['user_id', 'transaction_type', 'amount', 'transaction_date'],
            ]
        );

        return back()->with('success', __('admin.accounting_employee_transaction_added'));
    }

    public function updateEmployeeTransaction(
        Request $request,
        AccountingEmployeeTransaction $transaction,
        AccountingTreasuryService $treasuryService
    )
    {
        $auditLogService = app(AuditLogService::class);
        $before = [
            'user_id' => $transaction->user_id,
            'accounting_treasury_id' => $transaction->accounting_treasury_id,
            'transaction_type' => $transaction->transaction_type,
            'amount' => $transaction->amount,
            'transaction_date' => $transaction->transaction_date,
            'note' => $transaction->note,
        ];
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'transaction_type' => ['required', 'in:salary,advance,commission,bonus,deduction'],
            'accounting_treasury_id' => ['nullable', Rule::exists('accounting_treasuries', 'id')->where('is_active', 1)],
            'amount' => ['required', 'numeric', 'min:0'],
            'transaction_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ]);
        $data = $this->normalizeEmployeeTransactionTreasury($data);

        $transaction->update($data);
        $treasuryService->syncForEmployeeTransaction($transaction);
        $diff = $auditLogService->diff($before, [
            'user_id' => $transaction->user_id,
            'accounting_treasury_id' => $transaction->accounting_treasury_id,
            'transaction_type' => $transaction->transaction_type,
            'amount' => $transaction->amount,
            'transaction_date' => $transaction->transaction_date,
            'note' => $transaction->note,
        ]);

        if ($diff['changed_fields'] !== []) {
            $auditLogService->log(
                $request->user(),
                'accounting',
                'transaction_updated',
                $transaction,
                [
                    'title' => __('admin.accounting_employee_transaction_updated'),
                    'old_values' => $diff['old_values'],
                    'new_values' => $diff['new_values'],
                    'changed_fields' => $diff['changed_fields'],
                ]
            );
        }

        return back()->with('success', __('admin.accounting_employee_transaction_updated'));
    }

    public function destroyEmployeeTransaction(AccountingEmployeeTransaction $transaction, AccountingTreasuryService $treasuryService)
    {
        app(AuditLogService::class)->log(
            auth()->user(),
            'accounting',
            'transaction_deleted',
            $transaction,
            [
                'title' => __('admin.accounting_employee_transaction_deleted'),
                'old_values' => [
                    'user_id' => $transaction->user_id,
                    'transaction_type' => $transaction->transaction_type,
                    'amount' => $transaction->amount,
                    'transaction_date' => $transaction->transaction_date,
                    'note' => $transaction->note,
                ],
                'changed_fields' => ['user_id', 'transaction_type', 'amount', 'transaction_date', 'note'],
            ]
        );
        $treasuryService->deleteForRelated($transaction);
        $transaction->delete();

        return back()->with('success', __('admin.accounting_employee_transaction_deleted'));
    }

    public function settings()
    {
        return view('admin.accounting.settings', [
            'customerCategories' => $this->expenseCategories(),
            'generalCategories' => $this->generalExpenseCategories(),
        ]);
    }

    public function storeExpenseCategory(Request $request)
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        AccountingExpenseCategory::query()->create($data + [
            'slug' => Str::slug($data['name_en'] ?: $data['name_ar']),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', __('admin.accounting_expense_category_added'));
    }

    public function updateExpenseCategory(Request $request, AccountingExpenseCategory $category)
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category->update($data + [
            'sort_order' => $data['sort_order'] ?? $category->sort_order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', __('admin.accounting_expense_category_updated'));
    }

    public function destroyExpenseCategory(AccountingExpenseCategory $category)
    {
        if ($category->subcategories()->exists() || AccountingCustomerExpense::query()->where('accounting_expense_category_id', $category->id)->exists()) {
            return back()->withErrors(['expense_category' => __('admin.accounting_expense_category_in_use')]);
        }

        $category->delete();

        return back()->with('success', __('admin.accounting_expense_category_deleted'));
    }

    public function storeExpenseSubcategory(Request $request)
    {
        $data = $request->validate([
            'accounting_expense_category_id' => ['required', 'exists:accounting_expense_categories,id'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        AccountingExpenseSubcategory::query()->create($data + [
            'slug' => Str::slug($data['name_en'] ?: $data['name_ar']),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', __('admin.accounting_expense_subcategory_added'));
    }

    public function updateExpenseSubcategory(Request $request, AccountingExpenseSubcategory $subcategory)
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $subcategory->update($data + [
            'sort_order' => $data['sort_order'] ?? $subcategory->sort_order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', __('admin.accounting_expense_subcategory_updated'));
    }

    public function destroyExpenseSubcategory(AccountingExpenseSubcategory $subcategory)
    {
        if (AccountingCustomerExpense::query()->where('accounting_expense_subcategory_id', $subcategory->id)->exists()) {
            return back()->withErrors(['expense_subcategory' => __('admin.accounting_expense_subcategory_in_use')]);
        }

        $subcategory->delete();

        return back()->with('success', __('admin.accounting_expense_subcategory_deleted'));
    }

    public function storeGeneralExpenseCategory(Request $request)
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        AccountingGeneralExpenseCategory::query()->create($data + [
            'slug' => Str::slug($data['name_en'] ?: $data['name_ar']),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', __('admin.accounting_general_expense_category_added'));
    }

    public function updateGeneralExpenseCategory(Request $request, AccountingGeneralExpenseCategory $category)
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category->update($data + [
            'sort_order' => $data['sort_order'] ?? $category->sort_order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', __('admin.accounting_general_expense_category_updated'));
    }

    public function destroyGeneralExpenseCategory(AccountingGeneralExpenseCategory $category)
    {
        if (AccountingGeneralExpense::query()->where('accounting_general_expense_category_id', $category->id)->exists()) {
            return back()->withErrors(['general_expense_category' => __('admin.accounting_general_expense_category_in_use')]);
        }

        $category->delete();

        return back()->with('success', __('admin.accounting_general_expense_category_deleted'));
    }

    public function reports(Request $request, AccountingReportService $reportService)
    {
        return view('admin.accounting.reports', $reportService->build($request) + [
            'sellers' => $this->salesUsers(),
            'paymentStatusOptions' => $this->paymentStatusOptions(),
        ]);
    }

    protected function salesUsers(bool $includeAdmins = false)
    {
        $users = User::query()->where('is_active', true)->with('roles')->orderBy('name')->get();

        return $users->filter(function (User $user) use ($includeAdmins) {
            if ($includeAdmins && ($user->hasPermission('accounting.manage') || $user->hasPermission('leads.edit'))) {
                return true;
            }

            return $user->roles->contains(fn ($role) => $role->slug === 'sales-leads-manager');
        })->values();
    }

    protected function expenseCategories()
    {
        return AccountingExpenseCategory::query()
            ->with(['subcategories' => fn ($query) => $query->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
    }

    protected function generalExpenseCategories()
    {
        return AccountingGeneralExpenseCategory::query()->orderBy('sort_order')->get();
    }

    protected function activeTreasuries()
    {
        return AccountingTreasury::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    protected function normalizeEmployeeTransactionTreasury(array $data): array
    {
        $payoutTypes = [
            AccountingEmployeeTransaction::TYPE_SALARY,
            AccountingEmployeeTransaction::TYPE_ADVANCE,
            AccountingEmployeeTransaction::TYPE_COMMISSION,
            AccountingEmployeeTransaction::TYPE_BONUS,
        ];

        if (in_array($data['transaction_type'], $payoutTypes, true) && empty($data['accounting_treasury_id'])) {
            throw ValidationException::withMessages([
                'accounting_treasury_id' => __('admin.accounting_treasury_required_for_employee_payout'),
            ]);
        }

        if (! in_array($data['transaction_type'], $payoutTypes, true)) {
            $data['accounting_treasury_id'] = null;
        }

        return $data;
    }

    protected function paymentStatusOptions(): array
    {
        return [
            'unpaid' => __('admin.accounting_unpaid'),
            'partially_paid' => __('admin.accounting_partially_paid'),
            'fully_paid' => __('admin.accounting_fully_paid'),
        ];
    }
}
