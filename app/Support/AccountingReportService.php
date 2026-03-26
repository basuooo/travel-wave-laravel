<?php

namespace App\Support;

use App\Models\AccountingCustomerAccount;
use App\Models\AccountingCustomerExpense;
use App\Models\AccountingEmployeeTransaction;
use App\Models\AccountingGeneralExpense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AccountingReportService
{
    public function build(Request $request): array
    {
        $filters = $this->filters($request);
        $accountsQuery = $this->accountsQuery($filters);
        $accounts = (clone $accountsQuery)->with(['assignedUser', 'inquiry.crmSource'])->get();

        $generalExpensesQuery = AccountingGeneralExpense::query()->with('category');
        if ($filters['from']) {
            $generalExpensesQuery->whereDate('expense_date', '>=', $filters['from']);
        }
        if ($filters['to']) {
            $generalExpensesQuery->whereDate('expense_date', '<=', $filters['to']);
        }
        $generalExpenses = $generalExpensesQuery->get();

        $employeeTransactionsQuery = AccountingEmployeeTransaction::query()->with('user');
        if ($filters['from']) {
            $employeeTransactionsQuery->whereDate('transaction_date', '>=', $filters['from']);
        }
        if ($filters['to']) {
            $employeeTransactionsQuery->whereDate('transaction_date', '<=', $filters['to']);
        }
        if ($filters['seller_id']) {
            $employeeTransactionsQuery->where('user_id', $filters['seller_id']);
        }
        $employeeTransactions = $employeeTransactionsQuery->get();

        $customerExpenseRows = AccountingCustomerExpense::query()
            ->select('accounting_expense_category_id', DB::raw('SUM(amount) as total'))
            ->when($filters['from'] || $filters['to'] || $filters['seller_id'], function ($query) use ($filters) {
                $query->whereHas('account', function ($accountQuery) use ($filters) {
                    if ($filters['seller_id']) {
                        $accountQuery->where('assigned_user_id', $filters['seller_id']);
                    }
                    $accountQuery->whereHas('inquiry', function ($leadQuery) use ($filters) {
                        if ($filters['from']) {
                            $leadQuery->whereDate('created_at', '>=', $filters['from']);
                        }
                        if ($filters['to']) {
                            $leadQuery->whereDate('created_at', '<=', $filters['to']);
                        }
                    });
                });
            })
            ->groupBy('accounting_expense_category_id')
            ->get()
            ->keyBy('accounting_expense_category_id');

        $sellerReport = $accounts
            ->groupBy('assigned_user_id')
            ->map(function (Collection $rows, $sellerId) {
                $user = $rows->first()?->assignedUser;
                $totalAmount = (float) $rows->sum('total_amount');
                $totalExpenses = (float) $rows->sum('total_customer_expenses');
                $sellerProfit = (float) $rows->sum('seller_profit');
                $finalProfit = (float) $rows->sum('final_company_profit');

                return [
                    'user' => $user,
                    'customers' => $rows->count(),
                    'total_amount' => round($totalAmount, 2),
                    'total_expenses' => round($totalExpenses, 2),
                    'seller_profit' => round($sellerProfit, 2),
                    'final_profit' => round($finalProfit, 2),
                    'total_paid' => round((float) $rows->sum('paid_amount'), 2),
                    'total_remaining' => round((float) $rows->sum('remaining_amount'), 2),
                ];
            })
            ->filter(fn (array $row) => $row['user'])
            ->sortByDesc('final_profit')
            ->values();

        $employeeFinance = $employeeTransactions
            ->groupBy('user_id')
            ->map(function (Collection $rows) {
                $user = $rows->first()?->user;

                return [
                    'user' => $user,
                    'salary' => round((float) $rows->where('transaction_type', AccountingEmployeeTransaction::TYPE_SALARY)->sum('amount'), 2),
                    'advance' => round((float) $rows->where('transaction_type', AccountingEmployeeTransaction::TYPE_ADVANCE)->sum('amount'), 2),
                    'commission' => round((float) $rows->where('transaction_type', AccountingEmployeeTransaction::TYPE_COMMISSION)->sum('amount'), 2),
                    'bonus' => round((float) $rows->where('transaction_type', AccountingEmployeeTransaction::TYPE_BONUS)->sum('amount'), 2),
                    'deduction' => round((float) $rows->where('transaction_type', AccountingEmployeeTransaction::TYPE_DEDUCTION)->sum('amount'), 2),
                    'net_paid' => round(
                        (float) $rows->whereIn('transaction_type', [
                            AccountingEmployeeTransaction::TYPE_SALARY,
                            AccountingEmployeeTransaction::TYPE_ADVANCE,
                            AccountingEmployeeTransaction::TYPE_COMMISSION,
                            AccountingEmployeeTransaction::TYPE_BONUS,
                        ])->sum('amount') - (float) $rows->where('transaction_type', AccountingEmployeeTransaction::TYPE_DEDUCTION)->sum('amount'),
                        2
                    ),
                ];
            })
            ->filter(fn (array $row) => $row['user'])
            ->values();

        $dailyProfit = $accounts
            ->groupBy(fn (AccountingCustomerAccount $account) => optional($account->inquiry?->created_at)->format('Y-m-d') ?: optional($account->created_at)->format('Y-m-d'))
            ->map(fn (Collection $rows, string $day) => [
                'day' => $day,
                'total_amount' => round((float) $rows->sum('total_amount'), 2),
                'total_paid' => round((float) $rows->sum('paid_amount'), 2),
                'customer_expenses' => round((float) $rows->sum('total_customer_expenses'), 2),
                'final_profit' => round((float) $rows->sum('final_company_profit'), 2),
            ])
            ->sortBy('day')
            ->values();

        $summary = [
            'total_revenue' => round((float) $accounts->sum('total_amount'), 2),
            'total_collected' => round((float) $accounts->sum('paid_amount'), 2),
            'total_remaining' => round((float) $accounts->sum('remaining_amount'), 2),
            'total_customer_expenses' => round((float) $accounts->sum('total_customer_expenses'), 2),
            'total_general_expenses' => round((float) $generalExpenses->sum('amount'), 2),
            'company_profit_before_seller' => round((float) $accounts->sum('company_profit_before_seller'), 2),
            'seller_profit_total' => round((float) $accounts->sum('seller_profit'), 2),
            'final_company_profit' => round((float) $accounts->sum('final_company_profit'), 2),
            'net_after_general_expenses' => round((float) $accounts->sum('final_company_profit') - (float) $generalExpenses->sum('amount'), 2),
            'fully_paid' => $accounts->where('payment_status', 'fully_paid')->count(),
            'partially_paid' => $accounts->where('payment_status', 'partially_paid')->count(),
            'unpaid' => $accounts->where('payment_status', 'unpaid')->count(),
            'customer_count' => $accounts->count(),
            'average_expense_per_customer' => $accounts->isEmpty() ? 0 : round((float) $accounts->sum('total_customer_expenses') / max(1, $accounts->count()), 2),
            'average_profit_per_customer' => $accounts->isEmpty() ? 0 : round((float) $accounts->sum('final_company_profit') / max(1, $accounts->count()), 2),
            'collection_efficiency' => (float) $accounts->sum('total_amount') > 0
                ? round(((float) $accounts->sum('paid_amount') / (float) $accounts->sum('total_amount')) * 100, 1)
                : 0,
        ];

        return [
            'filters' => $filters,
            'summary' => $summary,
            'sellerReport' => $sellerReport,
            'customerAccounts' => $accounts->sortByDesc('created_at')->values(),
            'dailyProfit' => $dailyProfit,
            'generalExpenses' => $generalExpenses,
            'employeeFinance' => $employeeFinance,
            'customerExpenseBreakdown' => $customerExpenseRows,
        ];
    }

    protected function filters(Request $request): array
    {
        return [
            'from' => $request->filled('from') ? $request->date('from')->toDateString() : null,
            'to' => $request->filled('to') ? $request->date('to')->toDateString() : null,
            'seller_id' => $request->filled('seller_id') ? $request->integer('seller_id') : null,
            'payment_status' => $request->string('payment_status')->toString() ?: null,
        ];
    }

    protected function accountsQuery(array $filters)
    {
        return AccountingCustomerAccount::query()
            ->with(['assignedUser', 'inquiry.crmSource'])
            ->when($filters['seller_id'], fn ($query, $sellerId) => $query->where('assigned_user_id', $sellerId))
            ->when($filters['payment_status'], fn ($query, $status) => $query->where('payment_status', $status))
            ->when($filters['from'] || $filters['to'], function ($query) use ($filters) {
                $query->whereHas('inquiry', function ($leadQuery) use ($filters) {
                    if ($filters['from']) {
                        $leadQuery->whereDate('created_at', '>=', $filters['from']);
                    }
                    if ($filters['to']) {
                        $leadQuery->whereDate('created_at', '<=', $filters['to']);
                    }
                });
            });
    }
}
