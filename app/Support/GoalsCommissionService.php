<?php

namespace App\Support;

use App\Models\AccountingCustomerExpense;
use App\Models\AccountingCustomerPayment;
use App\Models\AccountingEmployeeTransaction;
use App\Models\CommissionStatement;
use App\Models\CrmCustomer;
use App\Models\GoalTarget;
use App\Models\Inquiry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GoalsCommissionService
{
    public function targetTypeOptions(): array
    {
        return [
            'lead_count' => __('admin.goals_targets_ui_type_lead_count'),
            'converted_customer_count' => __('admin.goals_targets_ui_type_converted_customer_count'),
            'paid_customer_count' => __('admin.goals_targets_ui_type_paid_customer_count'),
            'collected_amount' => __('admin.goals_targets_ui_type_collected_amount'),
            'company_profit_before_seller' => __('admin.goals_targets_ui_type_company_profit_before_seller'),
            'final_net_profit_contribution' => __('admin.goals_targets_ui_type_final_net_profit_contribution'),
        ];
    }

    public function commissionableUsers(?User $viewer = null): Collection
    {
        $query = User::query()->where('is_active', true)->with('roles')->orderBy('name');

        if ($viewer && ! CrmLeadAccess::canViewAll($viewer)) {
            $query->whereKey($viewer->id);
        }

        return $query->get()->filter(function (User $user) {
            return $user->roles->contains(fn ($role) => in_array($role->slug, ['sales-leads-manager', 'admin', 'super-admin'], true))
                || $user->assignedLeads()->exists()
                || $user->assignedCustomers()->exists()
                || $user->accountingCustomerAccounts()->exists();
        })->values();
    }

    public function targetsIndexData(Request $request, User $viewer): array
    {
        $filters = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'target_type' => ['nullable', 'string', 'max:100'],
            'period_type' => ['nullable', 'string', 'max:30'],
            'active_state' => ['nullable', 'in:active,inactive'],
            'achievement_state' => ['nullable', 'in:achieved,not_achieved'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $query = GoalTarget::query()->with(['user', 'creator'])->latest('period_start');

        if (! CrmLeadAccess::canViewAll($viewer)) {
            $query->where('user_id', $viewer->id);
        } elseif (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        foreach (['target_type', 'period_type'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }

        if (($filters['active_state'] ?? null) === 'active') {
            $query->where('is_active', true);
        } elseif (($filters['active_state'] ?? null) === 'inactive') {
            $query->where('is_active', false);
        }

        if (! empty($filters['from'])) {
            $query->whereDate('period_start', '>=', $request->date('from'));
        }

        if (! empty($filters['to'])) {
            $query->whereDate('period_end', '<=', $request->date('to'));
        }

        $items = $query->paginate(20)->withQueryString();
        $rows = collect($items->items())->map(fn (GoalTarget $goal) => $this->goalRow($goal));

        if (($filters['achievement_state'] ?? null) === 'achieved') {
            $rows = $rows->where('is_achieved', true)->values();
        } elseif (($filters['achievement_state'] ?? null) === 'not_achieved') {
            $rows = $rows->where('is_achieved', false)->values();
        }

        $items->setCollection($rows);

        $ranking = GoalTarget::query()
            ->with('user')
            ->when(! CrmLeadAccess::canViewAll($viewer), fn (Builder $query) => $query->where('user_id', $viewer->id))
            ->latest('period_start')
            ->get()
            ->map(fn (GoalTarget $goal) => $this->goalRow($goal))
            ->groupBy(fn (array $row) => $row['goal']->user_id)
            ->map(function (Collection $goals) {
                $user = $goals->first()['goal']->user;
                $avgProgress = round((float) $goals->avg('progress_percent'), 1);

                return [
                    'user' => $user,
                    'average_progress' => $avgProgress,
                    'achieved_goals' => $goals->where('is_achieved', true)->count(),
                    'total_goals' => $goals->count(),
                ];
            })
            ->sortByDesc('average_progress')
            ->values();

        return [
            'items' => $items,
            'filters' => $filters,
            'users' => $this->commissionableUsers($viewer),
            'targetTypes' => $this->targetTypeOptions(),
            'periodTypes' => GoalTarget::periodTypeOptions(),
            'summary' => [
                'total_targets' => GoalTarget::query()->when(! CrmLeadAccess::canViewAll($viewer), fn (Builder $query) => $query->where('user_id', $viewer->id))->count(),
                'active_targets' => GoalTarget::query()->when(! CrmLeadAccess::canViewAll($viewer), fn (Builder $query) => $query->where('user_id', $viewer->id))->where('is_active', true)->count(),
                'achieved_targets' => $rows->where('is_achieved', true)->count(),
                'average_progress' => round((float) $rows->avg('progress_percent'), 1),
            ],
            'ranking' => $ranking,
        ];
    }

    public function goalRow(GoalTarget $goal): array
    {
        $achieved = $this->goalAchievedValue($goal);
        $targetValue = round((float) $goal->target_value, 2);
        $progress = $targetValue > 0 ? min(100, round(($achieved / $targetValue) * 100, 1)) : 0;

        return [
            'goal' => $goal,
            'achieved_value' => round($achieved, 2),
            'progress_percent' => $progress,
            'is_achieved' => $achieved >= $targetValue && $targetValue > 0,
        ];
    }

    public function goalAchievedValue(GoalTarget $goal): float
    {
        $start = $goal->period_start->copy()->startOfDay();
        $end = $goal->period_end->copy()->endOfDay();

        return match ($goal->target_type) {
            'lead_count' => (float) Inquiry::query()
                ->where('assigned_user_id', $goal->user_id)
                ->whereBetween('created_at', [$start, $end])
                ->count(),
            'converted_customer_count' => (float) CrmCustomer::query()
                ->where('assigned_user_id', $goal->user_id)
                ->whereBetween('converted_at', [$start, $end])
                ->count(),
            'paid_customer_count' => (float) AccountingCustomerPayment::query()
                ->whereBetween('payment_date', [$goal->period_start->toDateString(), $goal->period_end->toDateString()])
                ->whereHas('account', fn (Builder $query) => $query->where('assigned_user_id', $goal->user_id))
                ->distinct('accounting_customer_account_id')
                ->count('accounting_customer_account_id'),
            'collected_amount' => round((float) AccountingCustomerPayment::query()
                ->whereBetween('payment_date', [$goal->period_start->toDateString(), $goal->period_end->toDateString()])
                ->whereHas('account', fn (Builder $query) => $query->where('assigned_user_id', $goal->user_id))
                ->sum('amount'), 2),
            'company_profit_before_seller' => $this->periodProfitBeforeSeller($goal->user_id, $goal->period_start, $goal->period_end),
            'final_net_profit_contribution' => $this->periodFinalNetContribution($goal->user_id, $goal->period_start, $goal->period_end),
            default => 0,
        };
    }

    public function commissionsIndexData(Request $request, User $viewer): array
    {
        $filters = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'basis_type' => ['nullable', 'string', 'max:100'],
            'payment_status' => ['nullable', 'string', 'max:30'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $query = CommissionStatement::query()->with(['user', 'creator'])->latest('period_start');

        if (! CrmLeadAccess::canViewAll($viewer)) {
            $query->where('user_id', $viewer->id);
        } elseif (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        foreach (['basis_type', 'payment_status'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }

        if (! empty($filters['from'])) {
            $query->whereDate('period_start', '>=', $request->date('from'));
        }

        if (! empty($filters['to'])) {
            $query->whereDate('period_end', '<=', $request->date('to'));
        }

        $items = $query->paginate(20)->withQueryString();
        $ranking = CommissionStatement::query()
            ->when(! CrmLeadAccess::canViewAll($viewer), fn (Builder $query) => $query->where('user_id', $viewer->id))
            ->with('user')
            ->get()
            ->groupBy('user_id')
            ->map(function (Collection $rows) {
                $user = $rows->first()?->user;

                return [
                    'user' => $user,
                    'earned_amount' => round((float) $rows->sum('earned_amount'), 2),
                    'paid_amount' => round((float) $rows->sum('paid_amount'), 2),
                    'remaining_amount' => round((float) $rows->sum('remaining_amount'), 2),
                ];
            })
            ->filter(fn (array $row) => $row['user'])
            ->sortByDesc('earned_amount')
            ->values();

        return [
            'items' => $items,
            'filters' => $filters,
            'users' => $this->commissionableUsers($viewer),
            'basisTypes' => CommissionStatement::basisTypeOptions(),
            'summary' => [
                'earned' => round((float) $items->getCollection()->sum('earned_amount'), 2),
                'paid' => round((float) $items->getCollection()->sum('paid_amount'), 2),
                'remaining' => round((float) $items->getCollection()->sum('remaining_amount'), 2),
                'unpaid_count' => $items->getCollection()->where('payment_status', 'unpaid')->count(),
            ],
            'ranking' => $ranking,
        ];
    }

    public function generateCommissionStatement(array $data, User $actor): CommissionStatement
    {
        $snapshot = $this->commissionSnapshot(
            (int) $data['user_id'],
            $data['basis_type'],
            Carbon::parse($data['period_start']),
            Carbon::parse($data['period_end'])
        );

        return CommissionStatement::query()->updateOrCreate(
            [
                'user_id' => (int) $data['user_id'],
                'basis_type' => $data['basis_type'],
                'period_start' => $snapshot['period_start'],
                'period_end' => $snapshot['period_end'],
            ],
            [
                'earned_amount' => $snapshot['earned_amount'],
                'paid_amount' => $snapshot['paid_amount'],
                'remaining_amount' => $snapshot['remaining_amount'],
                'payment_status' => $snapshot['payment_status'],
                'calculation_snapshot' => $snapshot['calculation_snapshot'],
                'note' => $data['note'] ?? null,
                'created_by' => $actor->id,
            ]
        );
    }

    public function commissionSnapshot(int $userId, string $basisType, Carbon $start, Carbon $end): array
    {
        $periodStart = $start->copy()->startOfDay();
        $periodEnd = $end->copy()->endOfDay();
        $profitBeforeSeller = $this->periodProfitBeforeSeller($userId, $periodStart, $periodEnd);
        $commissionRate = 0.10;
        $earned = $basisType === CommissionStatement::BASIS_SELLER_PROFIT_SHARE
            ? round(max(0, $profitBeforeSeller) * $commissionRate, 2)
            : 0.0;
        $paid = round((float) AccountingEmployeeTransaction::query()
            ->where('user_id', $userId)
            ->where('transaction_type', AccountingEmployeeTransaction::TYPE_COMMISSION)
            ->whereBetween('transaction_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->sum('amount'), 2);
        $remaining = max(0, round($earned - $paid, 2));
        $paymentStatus = $paid <= 0
            ? 'unpaid'
            : ($remaining <= 0 ? 'fully_paid' : 'partially_paid');

        return [
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
            'earned_amount' => $earned,
            'paid_amount' => $paid,
            'remaining_amount' => $remaining,
            'payment_status' => $paymentStatus,
            'calculation_snapshot' => [
                'basis_type' => $basisType,
                'commission_rate' => $commissionRate,
                'collected_amount' => $this->periodCollectedAmount($userId, $periodStart, $periodEnd),
                'customer_expenses' => $this->periodCustomerExpenses($userId, $periodStart, $periodEnd),
                'company_profit_before_seller' => $profitBeforeSeller,
                'final_net_profit_contribution' => round(max(0, $profitBeforeSeller) - $earned, 2),
                'paid_commission_transactions' => $paid,
            ],
        ];
    }

    public function periodCollectedAmount(int $userId, Carbon $start, Carbon $end): float
    {
        return round((float) AccountingCustomerPayment::query()
            ->whereBetween('payment_date', [$start->toDateString(), $end->toDateString()])
            ->whereHas('account', fn (Builder $query) => $query->where('assigned_user_id', $userId))
            ->sum('amount'), 2);
    }

    public function periodCustomerExpenses(int $userId, Carbon $start, Carbon $end): float
    {
        return round((float) AccountingCustomerExpense::query()
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->whereHas('account', fn (Builder $query) => $query->where('assigned_user_id', $userId))
            ->sum('amount'), 2);
    }

    public function periodProfitBeforeSeller(int $userId, Carbon $start, Carbon $end): float
    {
        return round($this->periodCollectedAmount($userId, $start, $end) - $this->periodCustomerExpenses($userId, $start, $end), 2);
    }

    public function periodFinalNetContribution(int $userId, Carbon $start, Carbon $end): float
    {
        $profitBeforeSeller = $this->periodProfitBeforeSeller($userId, $start, $end);
        $earned = round(max(0, $profitBeforeSeller) * 0.10, 2);

        return round($profitBeforeSeller - $earned, 2);
    }
}
