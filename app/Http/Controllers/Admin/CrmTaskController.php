<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmTask;
use App\Models\CrmTaskActivity;
use App\Models\Inquiry;
use App\Models\User;
use App\Support\AdminNotificationCenterService;
use App\Support\AuditLogService;
use App\Support\CrmLeadAccess;
use App\Support\CrmTaskReportService;
use App\Support\WorkflowAutomationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CrmTaskController extends Controller
{
    public function index(Request $request)
    {
        return $this->renderTaskBoard('board', $request);
    }

    public function list(Request $request)
    {
        return $this->renderTaskList('list', $request);
    }

    public function myTasks(Request $request)
    {
        return $this->renderTaskBoard('my', $request);
    }

    public function today(Request $request)
    {
        return $this->renderTaskBoard('today', $request);
    }

    public function delayed(Request $request)
    {
        return $this->renderTaskBoard('delayed', $request);
    }

    public function completed(Request $request)
    {
        return $this->renderTaskBoard('completed', $request);
    }

    public function board(Request $request)
    {
        return $this->renderTaskBoard('board', $request);
    }

    public function reports(Request $request, CrmTaskReportService $reportService)
    {
        $users = $this->taskUsers();

        return view('admin.crm.tasks.reports', $reportService->build(
            $request,
            auth()->user(),
            $users,
            fn (Request $innerRequest) => $this->baseQuery($innerRequest)
        ) + [
            'users' => $users,
        ]);
    }

    public function create(Request $request)
    {
        $lead = $request->filled('inquiry_id') ? Inquiry::query()->findOrFail($request->integer('inquiry_id')) : null;

        if ($lead) {
            $this->authorizeLeadContext($lead);
        }

        return view('admin.crm.tasks.create', [
            'task' => new CrmTask([
                'task_type' => $lead ? CrmTask::TYPE_LEAD : CrmTask::TYPE_GENERAL,
                'category' => $lead ? CrmTask::CATEGORY_CUSTOMER_FOLLOWUP : CrmTask::CATEGORY_INTERNAL,
                'status' => CrmTask::STATUS_NEW,
                'priority' => CrmTask::PRIORITY_MEDIUM,
                'assigned_user_id' => $lead?->assigned_user_id ?: auth()->id(),
                'inquiry_id' => $lead?->id,
            ]),
            'lead' => $lead,
            'users' => $this->taskUsers(),
            'leads' => $this->leadOptions(),
            'statusOptions' => CrmTask::statusOptions(),
            'priorityOptions' => CrmTask::priorityOptions(),
            'typeOptions' => CrmTask::typeOptions(),
            'categoryOptions' => CrmTask::categoryOptions(),
            'formAction' => route('admin.crm.tasks.store'),
            'formMethod' => 'POST',
        ]);
    }

    public function store(Request $request, AdminNotificationCenterService $notificationCenterService)
    {
        $auditLogService = app(AuditLogService::class);
        $data = $this->validateTask($request);
        $closingStatus = in_array($data['status'], [CrmTask::STATUS_COMPLETED, CrmTask::STATUS_CANCELLED], true);

        $task = CrmTask::query()->create($data + [
            'created_by' => auth()->id(),
            'last_activity_at' => now(),
            'completed_at' => $data['status'] === CrmTask::STATUS_COMPLETED ? now() : null,
            'closed_by' => $closingStatus ? auth()->id() : null,
        ]);

        $this->logActivity($task, 'created', null, $task->status, $data['notes'] ?? null);
        $auditLogService->log(
            $request->user(),
            'tasks',
            'created',
            $task->fresh(['assignedUser', 'inquiry']),
            [
                'title' => __('admin.crm_task_created'),
                'description' => $task->title,
                'new_values' => $this->taskAuditValues($task),
                'changed_fields' => array_keys($this->taskAuditValues($task)),
            ]
        );
        $notificationCenterService->createTaskAssignedNotification($task->fresh(['assignedUser', 'creator', 'inquiry']), $request->user());

        if ($task->status === CrmTask::STATUS_COMPLETED) {
            $notificationCenterService->createTaskCompletedNotification($task->fresh(['creator']), $request->user());
        }

        if ($task->assigned_user_id) {
            app(WorkflowAutomationService::class)->dispatch(WorkflowAutomationService::TRIGGER_TASK_ASSIGNED, $task->fresh(['assignedUser', 'creator', 'inquiry']), [
                'actor' => $request->user(),
            ]);
        }

        if ($request->boolean('return_to_lead') && $task->inquiry_id) {
            return redirect()->route('admin.crm.leads.show', $task->inquiry_id)->with('success', __('admin.crm_task_created'));
        }

        if ($redirect = $this->resolveRedirectTarget($request)) {
            return redirect()->to($redirect)->with('success', __('admin.crm_task_created'));
        }

        return redirect()->route('admin.crm.tasks.show', $task)->with('success', __('admin.crm_task_created'));
    }

    public function show(CrmTask $task)
    {
        $this->authorizeTask($task);

        $task->load(['inquiry.crmStatus', 'assignedUser', 'creator', 'closer', 'activities.user']);

        return view('admin.crm.tasks.show', [
            'task' => $task,
            'statusOptions' => CrmTask::statusOptions(),
            'priorityOptions' => CrmTask::priorityOptions(),
            'typeOptions' => CrmTask::typeOptions(),
            'categoryOptions' => CrmTask::categoryOptions(),
        ]);
    }

    public function edit(CrmTask $task)
    {
        $this->authorizeTask($task, manage: true);

        return view('admin.crm.tasks.edit', [
            'task' => $task->load(['inquiry', 'assignedUser']),
            'lead' => $task->inquiry,
            'users' => $this->taskUsers(),
            'leads' => $this->leadOptions(),
            'statusOptions' => CrmTask::statusOptions(),
            'priorityOptions' => CrmTask::priorityOptions(),
            'typeOptions' => CrmTask::typeOptions(),
            'categoryOptions' => CrmTask::categoryOptions(),
            'formAction' => route('admin.crm.tasks.update', $task),
            'formMethod' => 'PUT',
        ]);
    }

    public function update(Request $request, CrmTask $task, AdminNotificationCenterService $notificationCenterService)
    {
        $auditLogService = app(AuditLogService::class);
        $this->authorizeTask($task, manage: true);

        $data = $this->validateTask($request, $task);
        $original = $task->replicate();
        $beforeAudit = $this->taskAuditValues($task->loadMissing(['assignedUser', 'inquiry']));
        $closingStatus = in_array($data['status'], [CrmTask::STATUS_COMPLETED, CrmTask::STATUS_CANCELLED], true);

        $task->fill($data + [
            'completed_at' => $data['status'] === CrmTask::STATUS_COMPLETED ? ($task->completed_at ?: now()) : null,
            'closed_by' => $closingStatus ? auth()->id() : null,
            'closed_note' => $closingStatus ? ($data['closed_note'] ?? $task->closed_note) : null,
            'last_activity_at' => now(),
        ])->save();

        $this->logDiffActivities($task, $original, $data['notes'] ?? null);
        $afterAudit = $this->taskAuditValues($task->fresh(['assignedUser', 'inquiry']));
        $diff = $auditLogService->diff($beforeAudit, $afterAudit);
        $generalChangedFields = array_values(array_diff($diff['changed_fields'], ['status', 'assigned_user_id']));

        if ($generalChangedFields !== []) {
            $auditLogService->log(
                $request->user(),
                'tasks',
                'updated',
                $task,
                [
                    'title' => __('admin.crm_task_updated'),
                    'description' => $task->title,
                    'old_values' => array_intersect_key($diff['old_values'], array_flip($generalChangedFields)),
                    'new_values' => array_intersect_key($diff['new_values'], array_flip($generalChangedFields)),
                    'changed_fields' => $generalChangedFields,
                ]
            );
        }

        if ((int) $original->assigned_user_id !== (int) $task->assigned_user_id) {
            $auditLogService->log(
                $request->user(),
                'tasks',
                'reassigned',
                $task,
                [
                    'title' => __('admin.audit_action_reassigned'),
                    'description' => $task->title,
                    'old_values' => ['assigned_user_id' => $beforeAudit['assigned_user_id'] ?? null],
                    'new_values' => ['assigned_user_id' => $afterAudit['assigned_user_id'] ?? null],
                    'changed_fields' => ['assigned_user_id'],
                ]
            );
            $notificationCenterService->createTaskAssignedNotification($task->fresh(['assignedUser', 'creator', 'inquiry']), $request->user(), 'reassigned');
            app(WorkflowAutomationService::class)->dispatch(WorkflowAutomationService::TRIGGER_TASK_ASSIGNED, $task->fresh(['assignedUser', 'creator', 'inquiry']), [
                'actor' => $request->user(),
            ]);
        }

        if ($task->status === CrmTask::STATUS_COMPLETED && $original->status !== CrmTask::STATUS_COMPLETED) {
            $auditLogService->log(
                $request->user(),
                'tasks',
                'completed',
                $task,
                [
                    'title' => __('admin.audit_action_completed'),
                    'description' => $task->title,
                    'old_values' => ['status' => $beforeAudit['status'] ?? null],
                    'new_values' => ['status' => $afterAudit['status'] ?? null],
                    'changed_fields' => ['status'],
                ]
            );
            $notificationCenterService->createTaskCompletedNotification($task->fresh(['creator']), $request->user());
        } elseif ($original->status !== $task->status) {
            $auditLogService->log(
                $request->user(),
                'tasks',
                'status_changed',
                $task,
                [
                    'title' => __('admin.audit_action_status_changed'),
                    'description' => $task->title,
                    'old_values' => ['status' => $beforeAudit['status'] ?? null],
                    'new_values' => ['status' => $afterAudit['status'] ?? null],
                    'changed_fields' => ['status'],
                ]
            );
        }

        if ($original->status !== $task->status) {
            app(WorkflowAutomationService::class)->dispatch(WorkflowAutomationService::TRIGGER_TASK_STATUS_CHANGED, $task->fresh(['assignedUser', 'creator', 'inquiry']), [
                'actor' => $request->user(),
                'new_status' => $task->status,
            ]);
        }

        if ($request->boolean('return_to_lead') && $task->inquiry_id) {
            return redirect()->route('admin.crm.leads.show', $task->inquiry_id)->with('success', __('admin.crm_task_updated'));
        }

        if ($redirect = $this->resolveRedirectTarget($request)) {
            return redirect()->to($redirect)->with('success', __('admin.crm_task_updated'));
        }

        return redirect()->route('admin.crm.tasks.show', $task)->with('success', __('admin.crm_task_updated'));
    }

    public function updateStatus(Request $request, CrmTask $task)
    {
        $auditLogService = app(AuditLogService::class);
        $this->authorizeTaskStatusUpdate($task);

        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(CrmTask::statusOptions()))],
        ]);

        $originalStatus = $task->status;
        $newStatus = $data['status'];

        if ($originalStatus !== $newStatus) {
            $beforeAudit = $this->taskAuditValues($task->loadMissing(['assignedUser', 'inquiry']));
            $task->status = $newStatus;
            $task->last_activity_at = now();

            if ($newStatus === CrmTask::STATUS_COMPLETED) {
                $task->completed_at = now();
                $task->closed_by = auth()->id();
            } elseif ($originalStatus === CrmTask::STATUS_COMPLETED) {
                $task->completed_at = null;
                $task->closed_by = null;
            } elseif ($newStatus === CrmTask::STATUS_CANCELLED) {
                $task->closed_by = auth()->id();
            } elseif ($originalStatus === CrmTask::STATUS_CANCELLED) {
                $task->closed_by = null;
            }

            if (! in_array($newStatus, [CrmTask::STATUS_COMPLETED, CrmTask::STATUS_CANCELLED], true)) {
                $task->closed_note = null;
            }

            $task->save();

            $this->logActivity($task, 'updated_status', $originalStatus, $newStatus);
            $afterAudit = $this->taskAuditValues($task->fresh(['assignedUser', 'inquiry']));
            $auditLogService->log(
                $request->user(),
                'tasks',
                $newStatus === CrmTask::STATUS_COMPLETED ? 'completed' : 'status_changed',
                $task,
                [
                    'title' => $newStatus === CrmTask::STATUS_COMPLETED ? __('admin.audit_action_completed') : __('admin.audit_action_status_changed'),
                    'description' => $task->title,
                    'old_values' => ['status' => $beforeAudit['status'] ?? null],
                    'new_values' => ['status' => $afterAudit['status'] ?? null],
                    'changed_fields' => ['status'],
                ]
            );

            if ($newStatus === CrmTask::STATUS_COMPLETED) {
                app(AdminNotificationCenterService::class)
                    ->createTaskCompletedNotification($task->fresh(['creator']), $request->user());
            }

            app(WorkflowAutomationService::class)->dispatch(WorkflowAutomationService::TRIGGER_TASK_STATUS_CHANGED, $task->fresh(['assignedUser', 'creator', 'inquiry']), [
                'actor' => $request->user(),
                'new_status' => $task->status,
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'task' => [
                'id' => $task->id,
                'status' => $task->status,
                'localized_status' => $task->localizedStatus(),
                'visual_status' => $task->visualStatus(),
                'completed_at' => optional($task->completed_at)?->toIso8601String(),
                'is_delayed' => $task->isDelayed(),
                'overdue_label' => $task->overdueLabel(),
            ],
        ]);
    }

    protected function renderTaskBoard(string $preset, Request $request)
    {
        $items = $this->baseQuery($request, $preset)->limit(250)->get();
        $workflowStatuses = [
            CrmTask::STATUS_NEW,
            CrmTask::STATUS_IN_PROGRESS,
            CrmTask::STATUS_WAITING,
            CrmTask::STATUS_COMPLETED,
            CrmTask::STATUS_CANCELLED,
        ];

        $columns = collect($workflowStatuses)->mapWithKeys(function (string $status) use ($items) {
            return [$status => $items->where('status', $status)->values()];
        });

        return view('admin.crm.tasks.board', [
            'columns' => $columns,
            'items' => $items,
            'users' => $this->taskUsers(),
            'leads' => $this->leadOptions(),
            'statusOptions' => CrmTask::statusOptions(),
            'priorityOptions' => CrmTask::priorityOptions(),
            'typeOptions' => CrmTask::typeOptions(),
            'categoryOptions' => CrmTask::categoryOptions(),
            'filters' => $this->currentFilters($request),
            'presets' => $this->taskPresets(),
            'activePreset' => $preset,
            'summary' => $this->taskSummary($request, $preset),
            'canViewAllTasks' => CrmLeadAccess::canViewAll(auth()->user()),
            'canManageTasks' => auth()->user()?->hasPermission('leads.edit') ?? false,
        ]);
    }

    protected function renderTaskList(string $preset, Request $request)
    {
        $query = $this->baseQuery($request, $preset);

        return view('admin.crm.tasks.index', [
            'items' => $query->paginate(20)->withQueryString(),
            'users' => $this->taskUsers(),
            'statusOptions' => CrmTask::statusOptions(),
            'priorityOptions' => CrmTask::priorityOptions(),
            'typeOptions' => CrmTask::typeOptions(),
            'categoryOptions' => CrmTask::categoryOptions(),
            'filters' => $this->currentFilters($request),
            'presets' => $this->taskPresets(),
            'activePreset' => $preset,
            'summary' => $this->taskSummary($request, $preset),
            'canViewAllTasks' => CrmLeadAccess::canViewAll(auth()->user()),
        ]);
    }

    protected function baseQuery(Request $request, ?string $preset = null)
    {
        $query = CrmTask::query()
            ->with(['inquiry.crmStatus', 'assignedUser', 'creator'])
            ->visibleTo(auth()->user())
            ->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_at')
            ->latest('id');

        if ($preset === 'my') {
            $query->where('assigned_user_id', auth()->id());
        }

        if ($preset === 'today') {
            $query->dueToday();
        }

        if ($preset === 'delayed') {
            $query->delayed();
        }

        if ($preset === 'completed') {
            $query->where('status', CrmTask::STATUS_COMPLETED);
        }

        if ($request->filled('q')) {
            $needle = '%' . trim((string) $request->query('q')) . '%';
            $query->where(function ($builder) use ($needle) {
                $builder->where('title', 'like', $needle)
                    ->orWhere('description', 'like', $needle)
                    ->orWhere('notes', 'like', $needle)
                    ->orWhereHas('inquiry', function ($leadQuery) use ($needle) {
                        $leadQuery->where('full_name', 'like', $needle)
                            ->orWhere('phone', 'like', $needle);
                    });
            });
        }

        foreach (['assigned_user_id', 'created_by', 'inquiry_id'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->integer($field));
            }
        }

        foreach (['status', 'priority', 'task_type', 'category'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->string($field)->toString());
            }
        }

        if ($request->filled('linked_state')) {
            if ($request->string('linked_state')->toString() === 'linked') {
                $query->whereNotNull('inquiry_id');
            } elseif ($request->string('linked_state')->toString() === 'unlinked') {
                $query->whereNull('inquiry_id');
            }
        }

        if ($request->boolean('delayed_only')) {
            $query->delayed();
        }

        if ($request->boolean('completed_only')) {
            $query->where('status', CrmTask::STATUS_COMPLETED);
        }

        if ($request->boolean('today_only')) {
            $query->dueToday();
        }

        if ($request->filled('from')) {
            $query->whereDate('due_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('due_at', '<=', $request->date('to'));
        }

        return $query;
    }

    protected function validateTask(Request $request, ?CrmTask $task = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'task_type' => ['required', 'in:' . implode(',', array_keys(CrmTask::typeOptions()))],
            'category' => ['nullable', 'in:' . implode(',', array_keys(CrmTask::categoryOptions()))],
            'inquiry_id' => ['nullable', 'exists:inquiries,id'],
            'assigned_user_id' => ['required', 'exists:users,id'],
            'priority' => ['required', 'in:' . implode(',', array_keys(CrmTask::priorityOptions()))],
            'status' => ['required', 'in:' . implode(',', array_keys(CrmTask::statusOptions()))],
            'due_at' => ['nullable', 'date'],
            'closed_note' => ['nullable', 'string'],
        ]);

        if (! empty($data['inquiry_id'])) {
            $lead = Inquiry::query()->findOrFail($data['inquiry_id']);
            $this->authorizeLeadContext($lead);
        }

        return $data;
    }

    protected function taskUsers()
    {
        return User::query()->where('is_active', true)->orderBy('name')->get();
    }

    protected function leadOptions()
    {
        return CrmLeadAccess::applyVisibilityScope(
            Inquiry::query()->select('id', 'full_name', 'phone', 'assigned_user_id')->latest()->limit(200),
            auth()->user()
        )->get();
    }

    protected function currentFilters(Request $request): array
    {
        return $request->only([
            'q', 'assigned_user_id', 'created_by', 'status', 'priority', 'task_type', 'category',
            'linked_state', 'inquiry_id', 'from', 'to', 'delayed_only', 'completed_only', 'today_only',
        ]);
    }

    protected function taskPresets(): array
    {
        return [
            'board' => ['label' => __('admin.crm_task_preset_board'), 'route' => 'admin.crm.tasks.index'],
            'list' => ['label' => __('admin.crm_task_preset_list'), 'route' => 'admin.crm.tasks.list'],
            'my' => ['label' => __('admin.crm_task_preset_my'), 'route' => 'admin.crm.tasks.my'],
            'today' => ['label' => __('admin.crm_task_preset_today'), 'route' => 'admin.crm.tasks.today'],
            'delayed' => ['label' => __('admin.crm_task_preset_delayed'), 'route' => 'admin.crm.tasks.delayed'],
            'completed' => ['label' => __('admin.crm_task_preset_completed'), 'route' => 'admin.crm.tasks.completed'],
            'reports' => ['label' => __('admin.crm_task_preset_reports'), 'route' => 'admin.crm.tasks.reports'],
        ];
    }

    protected function taskSummary(Request $request, ?string $preset = null): array
    {
        $items = $this->baseQuery($request, $preset)->get();

        return [
            'open' => $items->whereNotIn('status', [CrmTask::STATUS_COMPLETED, CrmTask::STATUS_CANCELLED])->count(),
            'today' => $items->filter(fn (CrmTask $task) => optional($task->due_at)?->isToday())->count(),
            'delayed' => $items->filter(fn (CrmTask $task) => $task->isDelayed())->count(),
            'completed_today' => $items->filter(fn (CrmTask $task) => optional($task->completed_at)?->isToday())->count(),
            'my_open' => $items->where('assigned_user_id', auth()->id())->whereNotIn('status', [CrmTask::STATUS_COMPLETED, CrmTask::STATUS_CANCELLED])->count(),
            'my_delayed' => $items->where('assigned_user_id', auth()->id())->filter(fn (CrmTask $task) => $task->isDelayed())->count(),
            'urgent' => $items->where('priority', CrmTask::PRIORITY_URGENT)->count(),
        ];
    }

    protected function authorizeTask(CrmTask $task, bool $manage = false): void
    {
        $viewer = auth()->user();
        $canViewAll = CrmLeadAccess::canViewAll($viewer);
        $ownsTask = (int) $task->assigned_user_id === (int) $viewer?->id || (int) $task->created_by === (int) $viewer?->id;

        abort_unless($canViewAll || $ownsTask, 403);

        if ($manage) {
            abort_unless(($viewer?->hasPermission('leads.edit') ?? false) && ($canViewAll || $ownsTask), 403);
        }
    }

    protected function authorizeTaskStatusUpdate(CrmTask $task): void
    {
        $viewer = auth()->user();
        $canViewAll = CrmLeadAccess::canViewAll($viewer);
        $ownsTask = (int) $task->assigned_user_id === (int) $viewer?->id || (int) $task->created_by === (int) $viewer?->id;

        abort_unless($canViewAll || $ownsTask, 403);
    }

    protected function authorizeLeadContext(Inquiry $lead): void
    {
        abort_unless(CrmLeadAccess::canAccessLead(auth()->user(), $lead), 403);
    }

    protected function logActivity(CrmTask $task, string $actionType, ?string $oldValue = null, ?string $newValue = null, ?string $note = null): void
    {
        CrmTaskActivity::query()->create([
            'crm_task_id' => $task->id,
            'user_id' => auth()->id(),
            'action_type' => $actionType,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'note' => $note,
        ]);
    }

    protected function logDiffActivities(CrmTask $task, CrmTask $original, ?string $note = null): void
    {
        foreach (['status', 'assigned_user_id', 'priority', 'due_at', 'title', 'description', 'notes', 'category', 'task_type'] as $field) {
            $before = (string) ($original->{$field} ?? '');
            $after = (string) ($task->{$field} ?? '');

            if ($before !== $after) {
                $this->logActivity($task, 'updated_' . $field, $before, $after, $note);
            }
        }
    }

    protected function resolveRedirectTarget(Request $request): ?string
    {
        $target = trim((string) $request->input('redirect_to'));

        if ($target === '') {
            return null;
        }

        if (str_starts_with($target, '/')) {
            return $target;
        }

        $host = parse_url(url('/'), PHP_URL_HOST);
        $targetHost = parse_url($target, PHP_URL_HOST);

        if ($targetHost && $host && strcasecmp($targetHost, $host) === 0) {
            return $target;
        }

        return null;
    }

    protected function taskAuditValues(CrmTask $task): array
    {
        return [
            'title' => $task->title,
            'status' => $task->localizedStatus(),
            'assigned_user_id' => $task->assignedUser?->name ?? $task->assigned_user_id,
            'priority' => $task->localizedPriority(),
            'task_type' => $task->localizedType(),
            'category' => $task->localizedCategory(),
            'due_at' => optional($task->due_at)->toDateTimeString(),
            'inquiry_id' => $task->inquiry?->full_name ?? $task->inquiry_id,
        ];
    }
}
