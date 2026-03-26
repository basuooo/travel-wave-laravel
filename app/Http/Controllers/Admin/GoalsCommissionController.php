<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionStatement;
use App\Models\GoalTarget;
use App\Support\AuditLogService;
use App\Support\GoalsCommissionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GoalsCommissionController extends Controller
{
    public function targetsIndex(Request $request, GoalsCommissionService $service)
    {
        return view('admin.goals-commissions.targets.index', $service->targetsIndexData($request, $request->user()));
    }

    public function targetsCreate(Request $request, GoalsCommissionService $service)
    {
        return view('admin.goals-commissions.targets.form', [
            'goal' => new GoalTarget([
                'period_type' => GoalTarget::PERIOD_MONTHLY,
                'period_start' => now()->startOfMonth(),
                'period_end' => now()->endOfMonth(),
                'is_active' => true,
            ]),
            'isEdit' => false,
            'users' => $service->commissionableUsers($request->user()),
            'targetTypes' => $service->targetTypeOptions(),
            'periodTypes' => GoalTarget::periodTypeOptions(),
        ]);
    }

    public function targetsStore(Request $request, GoalsCommissionService $service, AuditLogService $auditLogService)
    {
        $data = $this->validatedGoal($request, $service);
        $goal = GoalTarget::query()->create($data + ['created_by' => $request->user()->id]);

        $auditLogService->log($request->user(), 'goals_commissions', 'created', $goal, [
            'title' => __('admin.goals_targets_ui_created_success'),
            'description' => $goal->user?->name,
            'new_values' => [
                'target_type' => $service->targetTypeOptions()[$goal->target_type] ?? $goal->target_type,
                'target_value' => $goal->target_value,
                'period_start' => optional($goal->period_start)->toDateString(),
                'period_end' => optional($goal->period_end)->toDateString(),
            ],
            'changed_fields' => ['target_type', 'target_value', 'period_start', 'period_end'],
        ]);

        return redirect()->route('admin.goals-commissions.targets.show', $goal)->with('success', __('admin.goals_targets_ui_created_success'));
    }

    public function targetsShow(GoalTarget $goalTarget, Request $request, GoalsCommissionService $service)
    {
        $this->authorizeOwnership($request->user(), $goalTarget->user_id);

        return view('admin.goals-commissions.targets.show', [
            'row' => $service->goalRow($goalTarget->load(['user', 'creator'])),
            'targetTypes' => $service->targetTypeOptions(),
        ]);
    }

    public function targetsEdit(GoalTarget $goalTarget, Request $request, GoalsCommissionService $service)
    {
        $this->authorizeOwnership($request->user(), $goalTarget->user_id, true);

        return view('admin.goals-commissions.targets.form', [
            'goal' => $goalTarget,
            'isEdit' => true,
            'users' => $service->commissionableUsers($request->user()),
            'targetTypes' => $service->targetTypeOptions(),
            'periodTypes' => GoalTarget::periodTypeOptions(),
        ]);
    }

    public function targetsUpdate(GoalTarget $goalTarget, Request $request, GoalsCommissionService $service, AuditLogService $auditLogService)
    {
        $this->authorizeOwnership($request->user(), $goalTarget->user_id, true);
        $before = [
            'target_type' => $goalTarget->target_type,
            'target_value' => $goalTarget->target_value,
            'period_type' => $goalTarget->period_type,
            'period_start' => optional($goalTarget->period_start)->toDateString(),
            'period_end' => optional($goalTarget->period_end)->toDateString(),
            'is_active' => $goalTarget->is_active,
        ];
        $data = $this->validatedGoal($request, $service);
        $goalTarget->update($data);

        $after = [
            'target_type' => $goalTarget->target_type,
            'target_value' => $goalTarget->target_value,
            'period_type' => $goalTarget->period_type,
            'period_start' => optional($goalTarget->period_start)->toDateString(),
            'period_end' => optional($goalTarget->period_end)->toDateString(),
            'is_active' => $goalTarget->is_active,
        ];
        $diff = $auditLogService->diff($before, $after);

        if ($diff['changed_fields'] !== []) {
            $auditLogService->log($request->user(), 'goals_commissions', 'updated', $goalTarget, [
                'title' => __('admin.goals_targets_ui_updated_success'),
                'description' => $goalTarget->user?->name,
                'old_values' => $diff['old_values'],
                'new_values' => $diff['new_values'],
                'changed_fields' => $diff['changed_fields'],
            ]);
        }

        return redirect()->route('admin.goals-commissions.targets.show', $goalTarget)->with('success', __('admin.goals_targets_ui_updated_success'));
    }

    public function commissionsIndex(Request $request, GoalsCommissionService $service)
    {
        return view('admin.goals-commissions.commissions.index', $service->commissionsIndexData($request, $request->user()) + [
            'defaultPeriodStart' => now()->startOfMonth()->toDateString(),
            'defaultPeriodEnd' => now()->endOfMonth()->toDateString(),
        ]);
    }

    public function commissionsStore(Request $request, GoalsCommissionService $service, AuditLogService $auditLogService)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'basis_type' => ['required', Rule::in(array_keys(CommissionStatement::basisTypeOptions()))],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'note' => ['nullable', 'string'],
        ]);

        $statement = $service->generateCommissionStatement($data, $request->user());

        $auditLogService->log($request->user(), 'goals_commissions', 'created', $statement, [
            'title' => __('admin.commission_statement_generated'),
            'description' => $statement->user?->name,
            'new_values' => [
                'basis_type' => $statement->localizedBasisType(),
                'earned_amount' => $statement->earned_amount,
                'paid_amount' => $statement->paid_amount,
                'remaining_amount' => $statement->remaining_amount,
            ],
            'changed_fields' => ['earned_amount', 'paid_amount', 'remaining_amount'],
        ]);

        return redirect()->route('admin.goals-commissions.commissions.show', $statement)->with('success', __('admin.commission_statement_generated'));
    }

    public function commissionsShow(CommissionStatement $commissionStatement, Request $request)
    {
        $this->authorizeOwnership($request->user(), $commissionStatement->user_id);
        $commissionStatement->load(['user', 'creator']);

        return view('admin.goals-commissions.commissions.show', [
            'statement' => $commissionStatement,
        ]);
    }

    protected function validatedGoal(Request $request, GoalsCommissionService $service): array
    {
        return $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'target_type' => ['required', Rule::in(array_keys($service->targetTypeOptions()))],
            'target_value' => ['required', 'numeric', 'min:0.01'],
            'period_type' => ['required', Rule::in(array_keys(GoalTarget::periodTypeOptions()))],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'note' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]) + [
            'is_active' => $request->boolean('is_active', true),
        ];
    }

    protected function authorizeOwnership($viewer, int $userId, bool $manage = false): void
    {
        $canViewAll = \App\Support\CrmLeadAccess::canViewAll($viewer);
        abort_unless($canViewAll || (int) $viewer?->id === $userId, 403);
        abort_if($manage && ! ($viewer?->hasPermission('goals_commissions.manage') ?? false), 403);
    }
}
