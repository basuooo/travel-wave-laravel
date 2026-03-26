<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmCustomer;
use App\Models\CrmLeadSource;
use App\Models\CrmStatus;
use App\Models\CrmTask;
use App\Models\User;
use App\Models\WorkflowAutomation;
use App\Models\WorkflowExecutionLog;
use App\Support\AuditLogService;
use App\Support\WorkflowAutomationService;
use Illuminate\Http\Request;

class WorkflowAutomationController extends Controller
{
    public function index(Request $request, WorkflowAutomationService $workflowService)
    {
        $query = WorkflowAutomation::query()->with(['creator', 'updater'])->latest('updated_at');

        if ($request->filled('q')) {
            $needle = '%' . trim((string) $request->query('q')) . '%';
            $query->where(function ($builder) use ($needle) {
                $builder->where('name', 'like', $needle)
                    ->orWhere('description', 'like', $needle);
            });
        }

        if ($request->filled('trigger_type')) {
            $query->where('trigger_type', $request->string('trigger_type')->toString());
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->string('is_active')->toString() === '1');
        }

        return view('admin.workflow-automations.index', [
            'items' => $query->paginate(20)->withQueryString(),
            'triggerOptions' => $workflowService->triggerSelectOptions(),
            'workflowService' => $workflowService,
        ]);
    }

    public function create(WorkflowAutomationService $workflowService)
    {
        return view('admin.workflow-automations.create', $this->formData(new WorkflowAutomation([
            'is_active' => true,
            'priority' => 100,
        ]), $workflowService) + [
            'formAction' => route('admin.workflow-automations.store'),
            'formMethod' => 'POST',
        ]);
    }

    public function store(Request $request, WorkflowAutomationService $workflowService)
    {
        $data = $this->validatedData($request, $workflowService);
        $automation = WorkflowAutomation::query()->create($data + [
            'created_by' => $request->user()?->id,
        ]);

        app(AuditLogService::class)->log(
            $request->user(),
            'workflow_automations',
            'created',
            $automation,
            [
                'title' => $automation->name,
                'new_values' => [
                    'trigger_type' => $workflowService->triggerLabel($automation->trigger_type),
                    'is_active' => $automation->is_active,
                ],
                'changed_fields' => ['trigger_type', 'is_active'],
            ]
        );

        return redirect()
            ->route('admin.workflow-automations.show', $automation)
            ->with('success', __('admin.workflow_ui_rule_created_success'));
    }

    public function show(WorkflowAutomation $workflowAutomation, WorkflowAutomationService $workflowService)
    {
        $workflowAutomation->load(['creator', 'updater']);

        return view('admin.workflow-automations.show', [
            'automation' => $workflowAutomation,
            'workflowService' => $workflowService,
            'recentLogs' => $workflowAutomation->executionLogs()->latest('executed_at')->paginate(15, ['*'], 'logs_page'),
        ]);
    }

    public function edit(WorkflowAutomation $workflowAutomation, WorkflowAutomationService $workflowService)
    {
        return view('admin.workflow-automations.edit', $this->formData($workflowAutomation, $workflowService) + [
            'formAction' => route('admin.workflow-automations.update', $workflowAutomation),
            'formMethod' => 'PUT',
        ]);
    }

    public function update(Request $request, WorkflowAutomation $workflowAutomation, WorkflowAutomationService $workflowService)
    {
        $before = [
            'name' => $workflowAutomation->name,
            'trigger_type' => $workflowAutomation->trigger_type,
            'is_active' => $workflowAutomation->is_active,
            'priority' => $workflowAutomation->priority,
        ];

        $data = $this->validatedData($request, $workflowService);
        $workflowAutomation->fill($data + [
            'updated_by' => $request->user()?->id,
        ])->save();

        $after = [
            'name' => $workflowAutomation->name,
            'trigger_type' => $workflowAutomation->trigger_type,
            'is_active' => $workflowAutomation->is_active,
            'priority' => $workflowAutomation->priority,
        ];

        $diff = app(AuditLogService::class)->diff($before, $after);

        if ($diff['changed_fields'] !== []) {
            app(AuditLogService::class)->log(
                $request->user(),
                'workflow_automations',
                'updated',
                $workflowAutomation,
                [
                    'title' => $workflowAutomation->name,
                    'old_values' => $diff['old_values'],
                    'new_values' => $diff['new_values'],
                    'changed_fields' => $diff['changed_fields'],
                ]
            );
        }

        return redirect()
            ->route('admin.workflow-automations.show', $workflowAutomation)
            ->with('success', __('admin.workflow_ui_rule_updated_success'));
    }

    public function toggle(Request $request, WorkflowAutomation $workflowAutomation)
    {
        $workflowAutomation->forceFill([
            'is_active' => ! $workflowAutomation->is_active,
            'updated_by' => $request->user()?->id,
        ])->save();

        return back()->with('success', __('admin.workflow_ui_rule_updated_success'));
    }

    public function executionLogs(Request $request, WorkflowAutomationService $workflowService)
    {
        $query = WorkflowExecutionLog::query()->with('automation')->latest('executed_at');

        if ($request->filled('workflow_automation_id')) {
            $query->where('workflow_automation_id', $request->integer('workflow_automation_id'));
        }

        foreach (['trigger_type', 'execution_status', 'entity_type'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->string($field)->toString());
            }
        }

        if ($request->filled('entity_id')) {
            $query->where('entity_id', $request->integer('entity_id'));
        }

        if ($request->filled('from')) {
            $query->whereDate('executed_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('executed_at', '<=', $request->date('to'));
        }

        if ($request->filled('q')) {
            $needle = '%' . trim((string) $request->query('q')) . '%';
            $query->where(function ($builder) use ($needle) {
                $builder->where('target_label', 'like', $needle)
                    ->orWhere('result_summary', 'like', $needle)
                    ->orWhere('error_message', 'like', $needle);
            });
        }

        return view('admin.workflow-automations.logs', [
            'items' => $query->paginate(20)->withQueryString(),
            'automations' => WorkflowAutomation::query()->orderBy('name')->get(),
            'triggerOptions' => $workflowService->triggerSelectOptions(),
        ]);
    }

    protected function validatedData(Request $request, WorkflowAutomationService $workflowService): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'trigger_type' => ['required', 'in:' . implode(',', array_keys($workflowService->triggerOptions()))],
            'priority' => ['nullable', 'integer', 'min:0'],
            'run_once' => ['nullable', 'boolean'],
            'cooldown_minutes' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'crm_source_id' => ['nullable', 'exists:crm_lead_sources,id'],
            'lead_status_id' => ['nullable', 'exists:crm_statuses,id'],
            'customer_stage' => ['nullable', 'in:' . implode(',', array_keys(CrmCustomer::stageOptions()))],
            'task_status' => ['nullable', 'in:' . implode(',', array_keys(CrmTask::statusOptions()))],
            'information_priority' => ['nullable', 'in:normal,important,urgent'],
            'payment_status' => ['nullable', 'in:unpaid,partially_paid,fully_paid'],
            'assigned_user_empty' => ['nullable', 'boolean'],
            'min_overdue_days' => ['nullable', 'integer', 'min:1'],
            'inactive_days' => ['nullable', 'integer', 'min:1'],
            'amount_min' => ['nullable', 'numeric', 'min:0'],
            'assign_user_id' => ['nullable', 'exists:users,id'],
            'create_task_enabled' => ['nullable', 'boolean'],
            'create_task_title' => ['nullable', 'string', 'max:255'],
            'create_task_description' => ['nullable', 'string'],
            'create_task_priority' => ['nullable', 'in:' . implode(',', array_keys(CrmTask::priorityOptions()))],
            'create_task_category' => ['nullable', 'in:' . implode(',', array_keys(CrmTask::categoryOptions()))],
            'create_task_due_in_days' => ['nullable', 'integer', 'min:0'],
            'create_task_assign_to' => ['nullable', 'in:' . implode(',', array_keys($workflowService->taskAssigneeOptions()))],
            'create_task_assigned_user_id' => ['nullable', 'exists:users,id'],
            'send_notification_enabled' => ['nullable', 'boolean'],
            'notification_recipient_mode' => ['nullable', 'in:' . implode(',', array_keys($workflowService->actionRecipientOptions()))],
            'notification_user_id' => ['nullable', 'exists:users,id'],
            'notification_severity' => ['nullable', 'in:info,success,warning,danger'],
            'notification_title_ar' => ['nullable', 'string', 'max:255'],
            'notification_title_en' => ['nullable', 'string', 'max:255'],
            'notification_message_ar' => ['nullable', 'string'],
            'notification_message_en' => ['nullable', 'string'],
        ]);

        $conditions = $workflowService->normalizeConditions($data);
        $actions = $workflowService->normalizeActions($data);

        abort_if($actions === [], 422, 'At least one automation action is required.');

        return [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'trigger_type' => $data['trigger_type'],
            'entity_type' => $workflowService->triggerOptions()[$data['trigger_type']]['entity_type'] ?? null,
            'conditions' => $conditions ?: null,
            'actions' => $actions,
            'is_active' => $request->boolean('is_active', true),
            'priority' => (int) ($data['priority'] ?? 100),
            'run_once' => $request->boolean('run_once'),
            'cooldown_minutes' => $data['cooldown_minutes'] ?? null,
        ];
    }

    protected function formData(WorkflowAutomation $automation, WorkflowAutomationService $workflowService): array
    {
        return [
            'automation' => $automation,
            'workflowService' => $workflowService,
            'triggerOptions' => $workflowService->triggerSelectOptions(),
            'leadStatuses' => CrmStatus::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'leadSources' => CrmLeadSource::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'users' => User::query()->where('is_active', true)->orderBy('name')->get(),
            'customerStages' => CrmCustomer::stageOptions(),
            'taskStatuses' => CrmTask::statusOptions(),
            'taskPriorities' => CrmTask::priorityOptions(),
            'taskCategories' => CrmTask::categoryOptions(),
            'notificationRecipientOptions' => $workflowService->actionRecipientOptions(),
            'taskAssigneeOptions' => $workflowService->taskAssigneeOptions(),
        ];
    }
}
