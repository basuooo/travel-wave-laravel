<?php

namespace App\Support;

use App\Models\AccountingCustomerAccount;
use App\Models\AccountingCustomerExpense;
use App\Models\AccountingCustomerPayment;
use App\Models\AccountingGeneralExpense;
use App\Models\CrmDocument;
use App\Models\CrmInformationRecipient;
use App\Models\CrmStatus;
use App\Models\CrmTask;
use App\Models\Inquiry;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KpiDashboardService
{
    public function __construct(
        protected CrmDelayedLeadService $delayedLeadService,
        protected CrmDocumentService $documentService
    ) {
    }

    public function build(Request $request, User $viewer): array
    {
        $filters = $this->normalizeFilters($request, $viewer);
        $canViewAll = CrmLeadAccess::canViewAll($viewer);
        $canViewFinance = $viewer->hasPermission('accounting.view');
        $canViewDocuments = $viewer->hasPermission('documents.view') && Schema::hasTable('crm_documents');
        $canViewInformation = Schema::hasTable('crm_information_recipients') && Schema::hasTable('crm_information');

        $leadScopedQuery = $this->leadScopedQuery($viewer, $filters['seller_id']);
        $leadPeriodQuery = (clone $leadScopedQuery)
            ->whereBetween('created_at', [$filters['from_at'], $filters['to_at']]);
        $delayedLeadsQuery = $this->delayedLeadService->applyDelayedScope(clone $leadScopedQuery);

        $taskScopedQuery = $this->taskScopedQuery($viewer, $filters['seller_id']);
        $openTasksQuery = (clone $taskScopedQuery)->active();
        $delayedTasksQuery = (clone $taskScopedQuery)->delayed();
        $dueTodayTasksQuery = (clone $taskScopedQuery)->active()->dueToday();
        $completedTasksPeriodQuery = (clone $taskScopedQuery)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$filters['from_at'], $filters['to_at']]);

        $leadStatuses = CrmStatus::query()->where('is_active', true)->orderBy('sort_order')->get();
        $leadStatusBreakdown = $leadStatuses->map(function (CrmStatus $status) use ($leadPeriodQuery) {
            return [
                'label' => $status->localizedName(),
                'count' => (clone $leadPeriodQuery)->where('crm_status_id', $status->id)->count(),
                'color' => $status->color ?: 'secondary',
            ];
        })->filter(fn (array $row) => $row['count'] > 0)->values();

        $taskStatusBreakdown = collect(CrmTask::statusOptions())->map(function (array $label, string $status) use ($taskScopedQuery) {
            return [
                'label' => $label[app()->getLocale() === 'ar' ? 'ar' : 'en'],
                'count' => (clone $taskScopedQuery)->where('status', $status)->count(),
                'color' => match ($status) {
                    CrmTask::STATUS_COMPLETED => 'success',
                    CrmTask::STATUS_CANCELLED => 'secondary',
                    CrmTask::STATUS_WAITING => 'warning',
                    CrmTask::STATUS_IN_PROGRESS => 'primary',
                    default => 'light',
                },
            ];
        })->values();

        $taskPriorityBreakdown = collect(CrmTask::priorityOptions())->map(function (array $label, string $priority) use ($openTasksQuery) {
            return [
                'label' => $label[app()->getLocale() === 'ar' ? 'ar' : 'en'],
                'count' => (clone $openTasksQuery)->where('priority', $priority)->count(),
                'color' => match ($priority) {
                    CrmTask::PRIORITY_URGENT => 'danger',
                    CrmTask::PRIORITY_HIGH => 'warning',
                    CrmTask::PRIORITY_MEDIUM => 'primary',
                    default => 'secondary',
                },
            ];
        })->values();

        $finance = $canViewFinance ? $this->financeMetrics($filters) : $this->emptyFinanceMetrics();
        $documents = $canViewDocuments ? $this->documentMetrics($filters, $viewer) : $this->emptyDocumentMetrics();
        $information = $canViewInformation ? $this->informationMetrics($filters, $viewer, $canViewAll) : $this->emptyInformationMetrics();
        $leadTrend = $this->dailyTrend(clone $leadScopedQuery, 'created_at', $filters['from_at'], $filters['to_at']);

        return [
            'filters' => [
                'from_date' => $filters['from_date'],
                'to_date' => $filters['to_date'],
                'seller_id' => $filters['seller_id'],
            ],
            'summaryCards' => array_values(array_filter([
                ['label' => __('admin.kpi_total_leads_period'), 'value' => number_format((clone $leadPeriodQuery)->count()), 'tone' => 'primary'],
                ['label' => __('admin.kpi_delayed_leads'), 'value' => number_format((clone $delayedLeadsQuery)->count()), 'tone' => 'danger'],
                ['label' => __('admin.kpi_open_tasks'), 'value' => number_format((clone $openTasksQuery)->count()), 'tone' => 'accent'],
                ['label' => __('admin.kpi_delayed_tasks'), 'value' => number_format((clone $delayedTasksQuery)->count()), 'tone' => 'danger'],
                $canViewFinance ? ['label' => __('admin.kpi_total_collected'), 'value' => number_format($finance['summary']['total_collected'], 2), 'tone' => 'success'] : null,
                $canViewFinance ? ['label' => __('admin.kpi_remaining_amount'), 'value' => number_format($finance['summary']['total_remaining'], 2), 'tone' => 'warning'] : null,
                $canViewFinance ? ['label' => __('admin.kpi_total_expenses'), 'value' => number_format($finance['summary']['total_expenses'], 2), 'tone' => 'slate'] : null,
                $canViewFinance ? ['label' => __('admin.kpi_final_net_profit'), 'value' => number_format($finance['summary']['final_net_profit'], 2), 'tone' => 'primary'] : null,
            ])),
            'crm' => [
                'summary' => [
                    'period_total' => (clone $leadPeriodQuery)->count(),
                    'today' => (clone $leadScopedQuery)->whereDate('created_at', today())->count(),
                    'this_week' => (clone $leadScopedQuery)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                    'this_month' => (clone $leadScopedQuery)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
                    'delayed' => (clone $delayedLeadsQuery)->count(),
                    'new' => (clone $leadPeriodQuery)->whereHas('crmStatus', fn (Builder $query) => $query->where('slug', 'new-lead'))->count(),
                    'no_answer' => (clone $leadPeriodQuery)->whereHas('crmStatus', fn (Builder $query) => $query->where('slug', 'no-answer'))->count(),
                    'assigned' => (clone $leadPeriodQuery)->whereNotNull('assigned_user_id')->count(),
                    'unassigned' => (clone $leadPeriodQuery)->whereNull('assigned_user_id')->count(),
                ],
                'status_breakdown' => $leadStatusBreakdown,
            ],
            'tasks' => [
                'summary' => [
                    'open' => (clone $openTasksQuery)->count(),
                    'due_today' => (clone $dueTodayTasksQuery)->count(),
                    'delayed' => (clone $delayedTasksQuery)->count(),
                    'completed_period' => (clone $completedTasksPeriodQuery)->count(),
                ],
                'status_breakdown' => $taskStatusBreakdown,
                'priority_breakdown' => $taskPriorityBreakdown,
            ],
            'finance' => $finance,
            'documents' => $documents,
            'information' => $information,
            'teamPerformance' => $this->teamPerformance($viewer, $filters, $canViewFinance),
            'alerts' => [
                'delayed_leads' => (clone $delayedLeadsQuery)->with(['crmStatus', 'assignedUser'])->limit(5)->get(),
                'delayed_tasks' => (clone $delayedTasksQuery)->with(['assignedUser', 'inquiry'])->limit(5)->get(),
                'high_remaining_accounts' => $finance['high_remaining_accounts'] ?? collect(),
                'urgent_information' => $information['urgent_items'] ?? collect(),
            ],
            'trends' => [
                'leads' => $leadTrend,
                'collections' => $canViewFinance ? $this->dailyAmountTrend(
                    AccountingCustomerPayment::query()
                        ->whereBetween('payment_date', [$filters['from_date'], $filters['to_date']])
                        ->when($filters['seller_id'], fn (Builder $query, $sellerId) => $query->whereHas('account', fn (Builder $accountQuery) => $accountQuery->where('assigned_user_id', $sellerId))),
                    'payment_date'
                ) : collect(),
                'general_expenses' => $canViewFinance ? $this->dailyAmountTrend(
                    AccountingGeneralExpense::query()->whereBetween('expense_date', [$filters['from_date'], $filters['to_date']]),
                    'expense_date'
                ) : collect(),
            ],
            'availableUsers' => $this->availableUsers($viewer),
            'canViewFinance' => $canViewFinance,
            'canViewDocuments' => $canViewDocuments,
            'canViewInformation' => $canViewInformation,
            'skippedMetrics' => [
                'calendar events because a stable calendar module is not available yet',
                'final conversion-rate KPIs because customer conversion analytics are not standardized enough yet',
                'missing-document checklist KPIs because document checklist rules are not implemented yet',
            ],
        ];
    }

    protected function normalizeFilters(Request $request, User $viewer): array
    {
        $from = $request->filled('from_date') ? Carbon::parse($request->string('from_date')->toString())->startOfDay() : now()->startOfMonth();
        $to = $request->filled('to_date') ? Carbon::parse($request->string('to_date')->toString())->endOfDay() : now()->endOfDay();

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $sellerId = $request->filled('seller_id') ? $request->integer('seller_id') : null;

        if (! CrmLeadAccess::canViewAll($viewer)) {
            $sellerId = $viewer->id;
        }

        return [
            'from_at' => $from,
            'to_at' => $to,
            'from_date' => $from->toDateString(),
            'to_date' => $to->toDateString(),
            'seller_id' => $sellerId,
        ];
    }

    protected function availableUsers(User $viewer): Collection
    {
        $query = User::query()->where('is_active', true)->orderBy('name');

        if (! CrmLeadAccess::canViewAll($viewer)) {
            $query->whereKey($viewer->id);
        }

        return $query->get();
    }

    protected function leadScopedQuery(User $viewer, ?int $sellerId = null): Builder
    {
        $query = CrmLeadAccess::applyVisibilityScope(Inquiry::query(), $viewer);

        if ($sellerId) {
            $query->where('assigned_user_id', $sellerId);
        }

        return $query;
    }

    protected function taskScopedQuery(User $viewer, ?int $sellerId = null): Builder
    {
        $query = CrmTask::query()->visibleTo($viewer);

        if ($sellerId) {
            $query->where('assigned_user_id', $sellerId);
        }

        return $query;
    }

    protected function financeMetrics(array $filters): array
    {
        $accounts = AccountingCustomerAccount::query()
            ->with(['assignedUser', 'inquiry', 'customer'])
            ->when($filters['seller_id'], fn (Builder $query, $sellerId) => $query->where('assigned_user_id', $sellerId))
            ->where(function (Builder $query) use ($filters) {
                $query->whereBetween('created_at', [$filters['from_at'], $filters['to_at']])
                    ->orWhereHas('inquiry', fn (Builder $leadQuery) => $leadQuery->whereBetween('created_at', [$filters['from_at'], $filters['to_at']]));
            })
            ->get();

        $paymentsQuery = AccountingCustomerPayment::query()
            ->whereBetween('payment_date', [$filters['from_date'], $filters['to_date']])
            ->when($filters['seller_id'], fn (Builder $query, $sellerId) => $query->whereHas('account', fn (Builder $accountQuery) => $accountQuery->where('assigned_user_id', $sellerId)));

        $customerExpensesQuery = AccountingCustomerExpense::query()
            ->whereBetween('expense_date', [$filters['from_date'], $filters['to_date']])
            ->when($filters['seller_id'], fn (Builder $query, $sellerId) => $query->whereHas('account', fn (Builder $accountQuery) => $accountQuery->where('assigned_user_id', $sellerId)));

        $generalExpensesTotal = round((float) AccountingGeneralExpense::query()
            ->whereBetween('expense_date', [$filters['from_date'], $filters['to_date']])
            ->sum('amount'), 2);

        $customerExpensesTotal = round((float) (clone $customerExpensesQuery)->sum('amount'), 2);
        $finalCompanyProfit = round((float) $accounts->sum('final_company_profit'), 2);

        return [
            'summary' => [
                'total_amount' => round((float) $accounts->sum('total_amount'), 2),
                'total_collected' => round((float) (clone $paymentsQuery)->sum('amount'), 2),
                'total_remaining' => round((float) $accounts->sum('remaining_amount'), 2),
                'total_customer_expenses' => $customerExpensesTotal,
                'total_general_expenses' => $generalExpensesTotal,
                'total_expenses' => round($customerExpensesTotal + $generalExpensesTotal, 2),
                'company_profit_before_seller' => round((float) $accounts->sum('company_profit_before_seller'), 2),
                'seller_profit_total' => round((float) $accounts->sum('seller_profit'), 2),
                'final_company_profit' => $finalCompanyProfit,
                'final_net_profit' => round($finalCompanyProfit - $generalExpensesTotal, 2),
                'fully_paid' => $accounts->where('payment_status', 'fully_paid')->count(),
                'partially_paid' => $accounts->where('payment_status', 'partially_paid')->count(),
                'unpaid' => $accounts->where('payment_status', 'unpaid')->count(),
            ],
            'seller_rows' => $accounts->groupBy('assigned_user_id')->map(function (Collection $rows) {
                $user = $rows->first()?->assignedUser;

                return [
                    'user' => $user,
                    'customers' => $rows->count(),
                    'total_amount' => round((float) $rows->sum('total_amount'), 2),
                    'total_paid' => round((float) $rows->sum('paid_amount'), 2),
                    'remaining_amount' => round((float) $rows->sum('remaining_amount'), 2),
                    'final_profit' => round((float) $rows->sum('final_company_profit'), 2),
                ];
            })->filter(fn (array $row) => $row['user'])->sortByDesc('final_profit')->values(),
            'high_remaining_accounts' => $accounts->filter(fn (AccountingCustomerAccount $account) => (float) $account->remaining_amount > 0)->sortByDesc('remaining_amount')->take(5)->values(),
        ];
    }

    protected function emptyFinanceMetrics(): array
    {
        return [
            'summary' => [
                'total_amount' => 0,
                'total_collected' => 0,
                'total_remaining' => 0,
                'total_customer_expenses' => 0,
                'total_general_expenses' => 0,
                'total_expenses' => 0,
                'company_profit_before_seller' => 0,
                'seller_profit_total' => 0,
                'final_company_profit' => 0,
                'final_net_profit' => 0,
                'fully_paid' => 0,
                'partially_paid' => 0,
                'unpaid' => 0,
            ],
            'seller_rows' => collect(),
            'high_remaining_accounts' => collect(),
        ];
    }

    protected function documentMetrics(array $filters, User $viewer): array
    {
        $documents = $this->documentService->visibleQuery($viewer)
            ->whereBetween('uploaded_at', [$filters['from_at'], $filters['to_at']])
            ->get();

        return [
            'summary' => [
                'total' => $documents->count(),
                'this_week' => $documents->filter(fn (CrmDocument $document) => $document->uploaded_at?->between(now()->startOfWeek(), now()->endOfWeek()))->count(),
                'today' => $documents->filter(fn (CrmDocument $document) => $document->uploaded_at?->isToday())->count(),
            ],
            'recent' => $documents->sortByDesc('uploaded_at')->take(5)->values(),
        ];
    }

    protected function emptyDocumentMetrics(): array
    {
        return [
            'summary' => ['total' => 0, 'this_week' => 0, 'today' => 0],
            'recent' => collect(),
        ];
    }

    protected function informationMetrics(array $filters, User $viewer, bool $canViewAll): array
    {
        $query = CrmInformationRecipient::query()
            ->with(['information', 'user'])
            ->whereHas('information', fn (Builder $informationQuery) => $informationQuery->where('is_active', true));

        if (! $canViewAll || $filters['seller_id']) {
            $query->where('user_id', $filters['seller_id'] ?: $viewer->id);
        }

        $urgentBase = (clone $query)->whereHas('information', fn (Builder $informationQuery) => $informationQuery->where('priority', 'urgent'));

        return [
            'summary' => [
                'unacknowledged' => (clone $query)->whereNull('acknowledged_at')->count(),
                'urgent_unacknowledged' => (clone $urgentBase)->whereNull('acknowledged_at')->count(),
            ],
            'urgent_items' => (clone $urgentBase)->whereNull('acknowledged_at')->latest()->limit(5)->get(),
        ];
    }

    protected function emptyInformationMetrics(): array
    {
        return [
            'summary' => ['unacknowledged' => 0, 'urgent_unacknowledged' => 0],
            'urgent_items' => collect(),
        ];
    }

    protected function teamPerformance(User $viewer, array $filters, bool $canViewFinance): Collection
    {
        return $this->availableUsers($viewer)->map(function (User $user) use ($filters, $canViewFinance) {
            $leadQuery = Inquiry::query()->where('assigned_user_id', $user->id);
            $leadCount = (clone $leadQuery)->whereBetween('created_at', [$filters['from_at'], $filters['to_at']])->count();
            $delayedLeads = $this->delayedLeadService->applyDelayedScope(clone $leadQuery)->count();
            $openTasks = CrmTask::query()->where('assigned_user_id', $user->id)->active()->count();
            $completedTasks = CrmTask::query()->where('assigned_user_id', $user->id)->whereBetween('completed_at', [$filters['from_at'], $filters['to_at']])->count();
            $delayedTasks = CrmTask::query()->where('assigned_user_id', $user->id)->delayed()->count();
            $collections = $canViewFinance
                ? round((float) AccountingCustomerPayment::query()->whereBetween('payment_date', [$filters['from_date'], $filters['to_date']])->whereHas('account', fn (Builder $query) => $query->where('assigned_user_id', $user->id))->sum('amount'), 2)
                : 0;

            return [
                'user' => $user,
                'lead_count' => $leadCount,
                'delayed_leads' => $delayedLeads,
                'open_tasks' => $openTasks,
                'completed_tasks' => $completedTasks,
                'delayed_tasks' => $delayedTasks,
                'collections' => $collections,
            ];
        })->sortByDesc(fn (array $row) => $canViewFinance ? $row['collections'] : $row['lead_count'])->values();
    }

    protected function dailyTrend(Builder $query, string $column, Carbon $from, Carbon $to): Collection
    {
        $rows = $query
            ->selectRaw('DATE(' . $column . ') as day, COUNT(*) as total')
            ->whereBetween($column, [$from, $to])
            ->groupBy(DB::raw('DATE(' . $column . ')'))
            ->orderBy('day')
            ->pluck('total', 'day');

        return collect(CarbonPeriod::create($from->copy()->startOfDay(), $to->copy()->startOfDay()))
            ->map(function (Carbon $day) use ($rows) {
                $key = $day->toDateString();

                return [
                    'day' => $key,
                    'label' => $day->format('m/d'),
                    'count' => (int) ($rows[$key] ?? 0),
                ];
            });
    }

    protected function dailyAmountTrend(Builder $query, string $dateColumn): Collection
    {
        return $query
            ->selectRaw('DATE(' . $dateColumn . ') as day, SUM(amount) as total')
            ->groupBy(DB::raw('DATE(' . $dateColumn . ')'))
            ->orderBy('day')
            ->get()
            ->map(fn ($row) => [
                'day' => $row->day,
                'label' => Carbon::parse($row->day)->format('m/d'),
                'amount' => round((float) $row->total, 2),
            ]);
    }
}
