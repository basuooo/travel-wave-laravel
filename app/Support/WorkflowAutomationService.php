<?php

namespace App\Support;

use App\Models\AccountingCustomerAccount;
use App\Models\AccountingCustomerPayment;
use App\Models\CrmCustomer;
use App\Models\CrmInformation;
use App\Models\CrmInformationRecipient;
use App\Models\CrmLeadAssignment;
use App\Models\CrmTask;
use App\Models\Inquiry;
use App\Models\User;
use App\Models\WorkflowAutomation;
use App\Models\WorkflowExecutionLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class WorkflowAutomationService
{
    public const TRIGGER_LEAD_CREATED = 'lead_created';
    public const TRIGGER_LEAD_STATUS_CHANGED = 'lead_status_changed';
    public const TRIGGER_LEAD_BECAME_DELAYED = 'lead_became_delayed';
    public const TRIGGER_CUSTOMER_CREATED = 'customer_created';
    public const TRIGGER_CUSTOMER_STATUS_CHANGED = 'customer_status_changed';
    public const TRIGGER_TASK_ASSIGNED = 'task_assigned';
    public const TRIGGER_TASK_STATUS_CHANGED = 'task_status_changed';
    public const TRIGGER_TASK_OVERDUE = 'task_overdue';
    public const TRIGGER_PAYMENT_ADDED = 'payment_added';
    public const TRIGGER_INFORMATION_PUBLISHED = 'information_published';
    public const TRIGGER_INFORMATION_UNACKNOWLEDGED = 'information_unacknowledged';
    public const TRIGGER_DOCUMENT_UPLOADED = 'document_uploaded';

    public const RECIPIENT_LINKED_OWNER = 'linked_owner';
    public const RECIPIENT_MANAGERS = 'managers';
    public const RECIPIENT_ACCOUNTING = 'accounting';
    public const RECIPIENT_SPECIFIC_USER = 'specific_user';
    public const RECIPIENT_ACTOR = 'actor';
    public const RECIPIENT_INFORMATION_RECIPIENT = 'information_recipient';

    public const ASSIGNEE_LINKED_OWNER = 'linked_owner';
    public const ASSIGNEE_SPECIFIC_USER = 'specific_user';
    public const ASSIGNEE_ACTOR = 'actor';

    public function triggerOptions(): array
    {
        return [
            self::TRIGGER_LEAD_CREATED => ['entity_type' => 'lead', 'label' => __('admin.workflow_ui_trigger_lead_created')],
            self::TRIGGER_LEAD_STATUS_CHANGED => ['entity_type' => 'lead', 'label' => __('admin.workflow_ui_trigger_lead_status_changed')],
            self::TRIGGER_LEAD_BECAME_DELAYED => ['entity_type' => 'lead', 'label' => __('admin.workflow_ui_trigger_lead_became_delayed')],
            self::TRIGGER_CUSTOMER_CREATED => ['entity_type' => 'customer', 'label' => __('admin.workflow_ui_trigger_customer_created')],
            self::TRIGGER_CUSTOMER_STATUS_CHANGED => ['entity_type' => 'customer', 'label' => __('admin.workflow_ui_trigger_customer_status_changed')],
            self::TRIGGER_TASK_ASSIGNED => ['entity_type' => 'task', 'label' => __('admin.workflow_ui_trigger_task_assigned')],
            self::TRIGGER_TASK_STATUS_CHANGED => ['entity_type' => 'task', 'label' => __('admin.workflow_ui_trigger_task_status_changed')],
            self::TRIGGER_TASK_OVERDUE => ['entity_type' => 'task', 'label' => __('admin.workflow_ui_trigger_task_overdue')],
            self::TRIGGER_PAYMENT_ADDED => ['entity_type' => 'payment', 'label' => __('admin.workflow_ui_trigger_payment_added')],
            self::TRIGGER_INFORMATION_PUBLISHED => ['entity_type' => 'information', 'label' => __('admin.workflow_ui_trigger_information_published')],
            self::TRIGGER_INFORMATION_UNACKNOWLEDGED => ['entity_type' => 'information_recipient', 'label' => __('admin.workflow_ui_trigger_information_unacknowledged')],
            self::TRIGGER_DOCUMENT_UPLOADED => ['entity_type' => 'document', 'label' => __('admin.workflow_ui_trigger_document_uploaded')],
        ];
    }

    public function triggerSelectOptions(): array
    {
        return collect($this->triggerOptions())->mapWithKeys(fn (array $item, string $key) => [$key => $item['label']])->all();
    }

    public function actionRecipientOptions(): array
    {
        return [
            self::RECIPIENT_LINKED_OWNER => __('admin.workflow_ui_recipient_linked_owner'),
            self::RECIPIENT_MANAGERS => __('admin.workflow_ui_recipient_managers'),
            self::RECIPIENT_ACCOUNTING => __('admin.workflow_ui_recipient_accounting'),
            self::RECIPIENT_SPECIFIC_USER => __('admin.workflow_ui_recipient_specific_user'),
            self::RECIPIENT_ACTOR => __('admin.workflow_ui_recipient_actor'),
            self::RECIPIENT_INFORMATION_RECIPIENT => __('admin.workflow_ui_recipient_information_recipient'),
        ];
    }

    public function taskAssigneeOptions(): array
    {
        return [
            self::ASSIGNEE_LINKED_OWNER => __('admin.workflow_ui_recipient_linked_owner'),
            self::ASSIGNEE_SPECIFIC_USER => __('admin.workflow_ui_recipient_specific_user'),
            self::ASSIGNEE_ACTOR => __('admin.workflow_ui_recipient_actor'),
        ];
    }

    public function normalizeConditions(array $input): array
    {
        $conditions = [
            'crm_source_id' => filled($input['crm_source_id'] ?? null) ? (int) $input['crm_source_id'] : null,
            'lead_status_id' => filled($input['lead_status_id'] ?? null) ? (int) $input['lead_status_id'] : null,
            'customer_stage' => $input['customer_stage'] ?: null,
            'task_status' => $input['task_status'] ?: null,
            'information_priority' => $input['information_priority'] ?: null,
            'payment_status' => $input['payment_status'] ?: null,
            'assigned_user_empty' => ! empty($input['assigned_user_empty']),
            'min_overdue_days' => filled($input['min_overdue_days'] ?? null) ? (int) $input['min_overdue_days'] : null,
            'inactive_days' => filled($input['inactive_days'] ?? null) ? (int) $input['inactive_days'] : null,
            'amount_min' => filled($input['amount_min'] ?? null) ? round((float) $input['amount_min'], 2) : null,
        ];

        return array_filter($conditions, fn ($value) => ! is_null($value) && $value !== false && $value !== '');
    }

    public function normalizeActions(array $input): array
    {
        $actions = [];

        if (filled($input['assign_user_id'] ?? null)) {
            $actions['assign_user'] = ['user_id' => (int) $input['assign_user_id']];
        }

        if (! empty($input['create_task_enabled'])) {
            $actions['create_task'] = array_filter([
                'title' => trim((string) ($input['create_task_title'] ?? '')),
                'description' => trim((string) ($input['create_task_description'] ?? '')),
                'priority' => $input['create_task_priority'] ?: CrmTask::PRIORITY_MEDIUM,
                'category' => $input['create_task_category'] ?: CrmTask::CATEGORY_INTERNAL,
                'due_in_days' => filled($input['create_task_due_in_days'] ?? null) ? (int) $input['create_task_due_in_days'] : null,
                'assign_to' => $input['create_task_assign_to'] ?: self::ASSIGNEE_LINKED_OWNER,
                'assigned_user_id' => filled($input['create_task_assigned_user_id'] ?? null) ? (int) $input['create_task_assigned_user_id'] : null,
            ], fn ($value) => ! is_null($value) && $value !== '');
        }

        if (! empty($input['send_notification_enabled'])) {
            $actions['send_notification'] = array_filter([
                'recipient_mode' => $input['notification_recipient_mode'] ?: self::RECIPIENT_LINKED_OWNER,
                'user_id' => filled($input['notification_user_id'] ?? null) ? (int) $input['notification_user_id'] : null,
                'severity' => $input['notification_severity'] ?: AdminNotificationCenterService::SEVERITY_INFO,
                'title_ar' => trim((string) ($input['notification_title_ar'] ?? '')),
                'title_en' => trim((string) ($input['notification_title_en'] ?? '')),
                'message_ar' => trim((string) ($input['notification_message_ar'] ?? '')),
                'message_en' => trim((string) ($input['notification_message_en'] ?? '')),
            ], fn ($value) => ! is_null($value) && $value !== '');
        }

        return $actions;
    }

    public function actionSummary(WorkflowAutomation $automation): string
    {
        $actions = $automation->actions ?? [];
        $parts = [];

        if (! empty($actions['assign_user']['user_id'])) {
            $user = User::query()->find($actions['assign_user']['user_id']);
            $parts[] = __('admin.workflow_ui_action_assign_user') . ': ' . ($user?->name ?? $actions['assign_user']['user_id']);
        }

        if (! empty($actions['create_task']['title'])) {
            $parts[] = __('admin.workflow_ui_action_create_task') . ': ' . $actions['create_task']['title'];
        }

        if (! empty($actions['send_notification']['recipient_mode'])) {
            $parts[] = __('admin.workflow_ui_action_send_notification') . ': ' . ($this->actionRecipientOptions()[$actions['send_notification']['recipient_mode']] ?? $actions['send_notification']['recipient_mode']);
        }

        return implode(' | ', $parts);
    }

    public function triggerLabel(string $triggerType): string
    {
        return $this->triggerOptions()[$triggerType]['label'] ?? $triggerType;
    }

    public function dispatch(string $triggerType, Model $entity, array $context = []): Collection
    {
        $entityType = $this->entityAlias($entity);
        $automations = WorkflowAutomation::query()
            ->active()
            ->where('trigger_type', $triggerType)
            ->where(function ($query) use ($entityType) {
                $query->whereNull('entity_type')->orWhere('entity_type', $entityType);
            })
            ->orderByDesc('priority')
            ->orderBy('id')
            ->get();

        $results = collect();

        foreach ($automations as $automation) {
            if (! $this->conditionsMatch($automation, $entity, $context)) {
                continue;
            }

            $results->push($this->executeAutomation($automation, $triggerType, $entity, $context));
        }

        return $results;
    }

    public function runScheduledChecks(): void
    {
        $this->runDelayedLeadChecks();
        $this->runOverdueTaskChecks();
        $this->runInformationAckChecks();
    }

    protected function runDelayedLeadChecks(): void
    {
        $delayedLeadService = app(CrmDelayedLeadService::class);
        $leads = $delayedLeadService->annotate(
            $delayedLeadService->applyDelayedScope(
                Inquiry::query()->with(['crmStatus', 'assignedUser', 'crmSource'])
            )->limit(300)->get()
        );

        foreach ($leads as $lead) {
            $reference = $lead->getAttribute('delay_reference_at') ?: $lead->getAttribute('delay_last_action_at');
            $referenceAt = $reference ? Carbon::parse($reference) : null;

            $this->dispatch(self::TRIGGER_LEAD_BECAME_DELAYED, $lead, [
                'delay_reason' => $lead->getAttribute('delay_reason'),
                'delay_reason_type' => $lead->getAttribute('delay_reason_type'),
                'inactive_days' => $referenceAt ? max(0, $referenceAt->diffInDays(now())) : null,
            ]);
        }
    }

    protected function runOverdueTaskChecks(): void
    {
        CrmTask::query()
            ->with(['assignedUser', 'inquiry'])
            ->delayed()
            ->chunkById(100, function ($tasks) {
                foreach ($tasks as $task) {
                    $days = $task->due_at ? max(1, $task->due_at->diffInDays(now())) : 0;
                    $this->dispatch(self::TRIGGER_TASK_OVERDUE, $task, [
                        'overdue_days' => $days,
                    ]);
                }
            });
    }

    protected function runInformationAckChecks(): void
    {
        CrmInformationRecipient::query()
            ->with(['information', 'user'])
            ->whereNull('acknowledged_at')
            ->whereHas('information', function ($query) {
                $query->where('is_active', true)
                    ->whereIn('priority', ['important', 'urgent'])
                    ->where(function ($builder) {
                        $builder->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                    });
            })
            ->chunkById(100, function ($recipients) {
                foreach ($recipients as $recipient) {
                    $hours = $recipient->created_at ? $recipient->created_at->diffInHours(now()) : 0;
                    $this->dispatch(self::TRIGGER_INFORMATION_UNACKNOWLEDGED, $recipient, [
                        'information' => $recipient->information,
                        'recipient_user' => $recipient->user,
                        'overdue_hours' => $hours,
                    ]);
                }
            });
    }

    protected function executeAutomation(WorkflowAutomation $automation, string $triggerType, Model $entity, array $context): WorkflowExecutionLog
    {
        if ($skipLog = $this->guardExecution($automation, $triggerType, $entity, $context)) {
            return $skipLog;
        }

        try {
            $summaries = [];

            DB::transaction(function () use ($automation, $entity, $context, &$summaries) {
                $actions = $automation->actions ?? [];

                if (! empty($actions['assign_user']['user_id'])) {
                    $summaries[] = $this->applyAssignUser($automation, $entity, (int) $actions['assign_user']['user_id'], $context);
                }

                if (! empty($actions['create_task']['title'])) {
                    $summaries[] = $this->applyCreateTask($automation, $entity, $actions['create_task'], $context);
                }

                if (! empty($actions['send_notification']['recipient_mode'])) {
                    $summaries[] = $this->applySendNotification($automation, $entity, $actions['send_notification'], $context);
                }
            });

            $automation->forceFill(['last_executed_at' => now()])->save();

            return WorkflowExecutionLog::query()->create([
                'workflow_automation_id' => $automation->id,
                'entity_type' => $this->entityAlias($entity),
                'entity_id' => $entity->getKey(),
                'trigger_type' => $triggerType,
                'execution_status' => WorkflowExecutionLog::STATUS_SUCCESS,
                'target_label' => $this->entityLabel($entity),
                'result_summary' => implode(' | ', array_filter($summaries)),
                'context' => $this->loggableContext($context),
                'executed_at' => now(),
            ]);
        } catch (Throwable $exception) {
            return WorkflowExecutionLog::query()->create([
                'workflow_automation_id' => $automation->id,
                'entity_type' => $this->entityAlias($entity),
                'entity_id' => $entity->getKey(),
                'trigger_type' => $triggerType,
                'execution_status' => WorkflowExecutionLog::STATUS_FAILED,
                'target_label' => $this->entityLabel($entity),
                'result_summary' => __('admin.workflow_ui_execution_failed'),
                'error_message' => $exception->getMessage(),
                'context' => $this->loggableContext($context),
                'executed_at' => now(),
            ]);
        }
    }

    protected function guardExecution(WorkflowAutomation $automation, string $triggerType, Model $entity, array $context): ?WorkflowExecutionLog
    {
        $successfulQuery = WorkflowExecutionLog::query()
            ->where('workflow_automation_id', $automation->id)
            ->where('trigger_type', $triggerType)
            ->where('entity_type', $this->entityAlias($entity))
            ->where('entity_id', $entity->getKey())
            ->where('execution_status', WorkflowExecutionLog::STATUS_SUCCESS);

        if ($automation->run_once && $successfulQuery->exists()) {
            return WorkflowExecutionLog::query()->create([
                'workflow_automation_id' => $automation->id,
                'entity_type' => $this->entityAlias($entity),
                'entity_id' => $entity->getKey(),
                'trigger_type' => $triggerType,
                'execution_status' => WorkflowExecutionLog::STATUS_SKIPPED,
                'target_label' => $this->entityLabel($entity),
                'result_summary' => __('admin.workflow_ui_execution_skipped_run_once'),
                'context' => $this->loggableContext($context),
                'executed_at' => now(),
            ]);
        }

        if ($automation->cooldown_minutes) {
            $recent = (clone $successfulQuery)
                ->where('executed_at', '>=', now()->subMinutes($automation->cooldown_minutes))
                ->exists();

            if ($recent) {
                return WorkflowExecutionLog::query()->create([
                    'workflow_automation_id' => $automation->id,
                    'entity_type' => $this->entityAlias($entity),
                    'entity_id' => $entity->getKey(),
                    'trigger_type' => $triggerType,
                    'execution_status' => WorkflowExecutionLog::STATUS_SKIPPED,
                    'target_label' => $this->entityLabel($entity),
                    'result_summary' => __('admin.workflow_ui_execution_skipped_cooldown'),
                    'context' => $this->loggableContext($context),
                    'executed_at' => now(),
                ]);
            }
        }

        return null;
    }

    protected function conditionsMatch(WorkflowAutomation $automation, Model $entity, array $context): bool
    {
        $conditions = $automation->conditions ?? [];

        if (($conditions['assigned_user_empty'] ?? false) && ! empty($entity->assigned_user_id)) {
            return false;
        }

        if (isset($conditions['crm_source_id']) && (int) ($entity->crm_source_id ?? 0) !== (int) $conditions['crm_source_id']) {
            return false;
        }

        if (isset($conditions['lead_status_id'])) {
            $leadStatusId = $context['new_status_id'] ?? $entity->crm_status_id ?? null;

            if ((int) $leadStatusId !== (int) $conditions['lead_status_id']) {
                return false;
            }
        }

        if (isset($conditions['customer_stage'])) {
            $stage = $context['new_stage'] ?? $entity->stage ?? null;

            if ((string) $stage !== (string) $conditions['customer_stage']) {
                return false;
            }
        }

        if (isset($conditions['task_status'])) {
            $taskStatus = $context['new_status'] ?? $entity->status ?? null;

            if ((string) $taskStatus !== (string) $conditions['task_status']) {
                return false;
            }
        }

        if (isset($conditions['information_priority'])) {
            $priority = $context['information']->priority ?? $entity->priority ?? null;

            if ((string) $priority !== (string) $conditions['information_priority']) {
                return false;
            }
        }

        if (isset($conditions['payment_status'])) {
            $status = $context['payment_status'] ?? $entity->payment_status ?? $entity->account?->payment_status ?? null;

            if ((string) $status !== (string) $conditions['payment_status']) {
                return false;
            }
        }

        if (isset($conditions['min_overdue_days'])) {
            $overdueDays = (int) ($context['overdue_days'] ?? $context['inactive_days'] ?? 0);

            if ($overdueDays < (int) $conditions['min_overdue_days']) {
                return false;
            }
        }

        if (isset($conditions['inactive_days'])) {
            $inactiveDays = (int) ($context['inactive_days'] ?? 0);

            if ($inactiveDays < (int) $conditions['inactive_days']) {
                return false;
            }
        }

        if (isset($conditions['amount_min'])) {
            $amount = (float) ($context['amount'] ?? $entity->amount ?? 0);

            if ($amount < (float) $conditions['amount_min']) {
                return false;
            }
        }

        return true;
    }

    protected function applyAssignUser(WorkflowAutomation $automation, Model $entity, int $userId, array $context): string
    {
        $user = User::query()->where('is_active', true)->findOrFail($userId);
        $actor = $context['actor'] ?? null;

        if ($entity instanceof Inquiry && (int) $entity->assigned_user_id !== (int) $user->id) {
            $oldAssigned = $entity->assigned_user_id;
            $entity->forceFill(['assigned_user_id' => $user->id])->save();

            CrmLeadAssignment::query()->create([
                'inquiry_id' => $entity->id,
                'old_assigned_user_id' => $oldAssigned,
                'new_assigned_user_id' => $user->id,
                'changed_by' => $actor?->id,
                'changed_at' => now(),
                'note' => __('admin.workflow_ui_assigned_by_automation', ['name' => $automation->name]),
            ]);

            app(AdminNotificationCenterService::class)->createLeadAssignedNotification(
                $entity->fresh(['assignedUser', 'crmStatus']),
                $oldAssigned,
                $actor
            );
        }

        if ($entity instanceof CrmTask && (int) $entity->assigned_user_id !== (int) $user->id) {
            $entity->forceFill(['assigned_user_id' => $user->id, 'last_activity_at' => now()])->save();
            app(AdminNotificationCenterService::class)->createTaskAssignedNotification(
                $entity->fresh(['assignedUser', 'creator', 'inquiry']),
                $actor,
                'reassigned'
            );
        }

        if ($entity instanceof CrmCustomer && (int) $entity->assigned_user_id !== (int) $user->id) {
            $entity->forceFill(['assigned_user_id' => $user->id])->save();
            app(CustomerConversionService::class)->syncCustomerAccountLink($entity->fresh('inquiry.accountingAccount'));
        }

        return __('admin.workflow_ui_action_assign_user') . ': ' . $user->name;
    }

    protected function applyCreateTask(WorkflowAutomation $automation, Model $entity, array $action, array $context): string
    {
        $inquiry = $this->linkedInquiry($entity, $context);
        $assignee = $this->resolveTaskAssignee($action, $entity, $context);
        $actor = $context['actor'] ?? null;

        if (! $assignee) {
            return __('admin.workflow_ui_action_create_task') . ': ' . __('admin.workflow_ui_no_recipient_resolved');
        }

        $task = CrmTask::query()->create([
            'inquiry_id' => $inquiry?->id,
            'assigned_user_id' => $assignee->id,
            'created_by' => $actor?->id,
            'title' => $this->interpolateTemplate($action['title'] ?? __('admin.workflow_ui_default_task_title'), $entity, $context),
            'description' => $this->interpolateTemplate((string) ($action['description'] ?? ''), $entity, $context) ?: null,
            'task_type' => $inquiry ? CrmTask::TYPE_LEAD : CrmTask::TYPE_GENERAL,
            'category' => $action['category'] ?? CrmTask::CATEGORY_INTERNAL,
            'priority' => $action['priority'] ?? CrmTask::PRIORITY_MEDIUM,
            'status' => CrmTask::STATUS_NEW,
            'due_at' => isset($action['due_in_days']) ? now()->addDays((int) $action['due_in_days']) : null,
            'last_activity_at' => now(),
        ]);

        app(AdminNotificationCenterService::class)->createTaskAssignedNotification(
            $task->fresh(['assignedUser', 'creator', 'inquiry']),
            $actor
        );

        return __('admin.workflow_ui_action_create_task') . ': ' . $task->title;
    }

    protected function applySendNotification(WorkflowAutomation $automation, Model $entity, array $action, array $context): string
    {
        $recipients = $this->resolveNotificationRecipients($action, $entity, $context);
        $actor = $context['actor'] ?? null;

        if ($recipients->isEmpty()) {
            return __('admin.workflow_ui_action_send_notification') . ': ' . __('admin.workflow_ui_no_recipient_resolved');
        }

        app(AdminNotificationCenterService::class)->notifyUsers($recipients, [
            'event_key' => sprintf('workflow:%d:%s:%s:%d:%s', $automation->id, $automation->trigger_type, $this->entityAlias($entity), $entity->getKey(), now()->timestamp),
            'type' => 'system_alert',
            'module' => 'workflow',
            'severity' => $action['severity'] ?? AdminNotificationCenterService::SEVERITY_INFO,
            'title_ar' => $this->interpolateTemplate((string) ($action['title_ar'] ?? $automation->name), $entity, $context),
            'title_en' => $this->interpolateTemplate((string) ($action['title_en'] ?? $automation->name), $entity, $context),
            'message_ar' => $this->interpolateTemplate((string) ($action['message_ar'] ?? $this->entityLabel($entity)), $entity, $context),
            'message_en' => $this->interpolateTemplate((string) ($action['message_en'] ?? $this->entityLabel($entity)), $entity, $context),
            'subject_name' => $this->entityLabel($entity),
            'url' => $this->entityUrl($entity),
            'action_label_ar' => __('admin.workflow_ui_details', locale: 'ar'),
            'action_label_en' => 'View',
            'notifiable_type' => get_class($entity),
            'notifiable_id' => $entity->getKey(),
            'created_by' => $actor?->id,
            'meta' => [
                'automation_id' => $automation->id,
                'trigger_type' => $automation->trigger_type,
            ],
        ]);

        return __('admin.workflow_ui_action_send_notification') . ': ' . $recipients->pluck('name')->implode(', ');
    }

    protected function resolveTaskAssignee(array $action, Model $entity, array $context): ?User
    {
        $mode = $action['assign_to'] ?? self::ASSIGNEE_LINKED_OWNER;

        return match ($mode) {
            self::ASSIGNEE_ACTOR => $context['actor'] instanceof User ? $context['actor'] : null,
            self::ASSIGNEE_SPECIFIC_USER => User::query()->where('is_active', true)->find($action['assigned_user_id'] ?? null),
            default => $this->linkedOwner($entity, $context),
        };
    }

    protected function resolveNotificationRecipients(array $action, Model $entity, array $context): Collection
    {
        $mode = $action['recipient_mode'] ?? self::RECIPIENT_LINKED_OWNER;

        return match ($mode) {
            self::RECIPIENT_ACTOR => collect([$context['actor'] ?? null])->filter(fn ($user) => $user instanceof User)->values(),
            self::RECIPIENT_SPECIFIC_USER => collect([User::query()->where('is_active', true)->find($action['user_id'] ?? null)])->filter(),
            self::RECIPIENT_MANAGERS => $this->managementRecipients(),
            self::RECIPIENT_ACCOUNTING => $this->usersWithPermission('accounting.view'),
            self::RECIPIENT_INFORMATION_RECIPIENT => collect([$context['recipient_user'] ?? null])->filter(fn ($user) => $user instanceof User)->values(),
            default => collect([$this->linkedOwner($entity, $context)])->filter(fn ($user) => $user instanceof User)->values(),
        };
    }

    protected function linkedOwner(Model $entity, array $context): ?User
    {
        if ($entity instanceof Inquiry || $entity instanceof CrmCustomer || $entity instanceof CrmTask || $entity instanceof AccountingCustomerAccount) {
            return $entity->assignedUser ?? User::query()->find($entity->assigned_user_id);
        }

        if ($entity instanceof AccountingCustomerPayment) {
            $entity->loadMissing('account.assignedUser');

            return $entity->account?->assignedUser;
        }

        if ($entity instanceof CrmInformationRecipient) {
            return $entity->user;
        }

        if ($entity instanceof \App\Models\CrmDocument) {
            $documentable = $entity->documentable;

            if ($documentable instanceof Inquiry || $documentable instanceof CrmCustomer) {
                return $documentable->assignedUser;
            }
        }

        return $context['actor'] ?? null;
    }

    protected function linkedInquiry(Model $entity, array $context): ?Inquiry
    {
        if ($entity instanceof Inquiry) {
            return $entity;
        }

        if ($entity instanceof CrmCustomer) {
            return $entity->inquiry;
        }

        if ($entity instanceof CrmTask) {
            return $entity->inquiry;
        }

        if ($entity instanceof AccountingCustomerPayment) {
            $entity->loadMissing('account.inquiry');

            return $entity->account?->inquiry;
        }

        if ($entity instanceof \App\Models\CrmDocument) {
            $documentable = $entity->documentable;

            if ($documentable instanceof Inquiry) {
                return $documentable;
            }

            if ($documentable instanceof CrmCustomer) {
                return $documentable->inquiry;
            }
        }

        return $context['inquiry'] ?? null;
    }

    protected function entityAlias(Model $entity): string
    {
        return match (true) {
            $entity instanceof Inquiry => 'lead',
            $entity instanceof CrmCustomer => 'customer',
            $entity instanceof CrmTask => 'task',
            $entity instanceof AccountingCustomerPayment => 'payment',
            $entity instanceof CrmInformation => 'information',
            $entity instanceof CrmInformationRecipient => 'information_recipient',
            $entity instanceof \App\Models\CrmDocument => 'document',
            default => class_basename($entity),
        };
    }

    protected function entityLabel(Model $entity): string
    {
        return match (true) {
            $entity instanceof Inquiry => $entity->full_name,
            $entity instanceof CrmCustomer => $entity->customer_code ?: $entity->full_name,
            $entity instanceof CrmTask => $entity->title,
            $entity instanceof AccountingCustomerPayment => $entity->account?->customer_name ?: ('Payment #' . $entity->id),
            $entity instanceof CrmInformation => $entity->title,
            $entity instanceof CrmInformationRecipient => $entity->information?->title ?: ('Recipient #' . $entity->id),
            $entity instanceof \App\Models\CrmDocument => $entity->title ?: $entity->original_file_name,
            default => class_basename($entity) . ' #' . $entity->getKey(),
        };
    }

    protected function entityUrl(Model $entity): ?string
    {
        return match (true) {
            $entity instanceof Inquiry => route('admin.crm.leads.show', $entity),
            $entity instanceof CrmCustomer => route('admin.crm.customers.show', $entity),
            $entity instanceof CrmTask => route('admin.crm.tasks.show', $entity),
            $entity instanceof AccountingCustomerPayment => route('admin.accounting.customers.show', $entity->account_id),
            $entity instanceof CrmInformation => route('admin.crm.information.show', $entity),
            $entity instanceof CrmInformationRecipient => $entity->information ? route('admin.crm.information.show', $entity->information) : null,
            $entity instanceof \App\Models\CrmDocument => route('admin.documents.show', $entity),
            default => null,
        };
    }

    protected function interpolateTemplate(string $value, Model $entity, array $context): string
    {
        $inquiry = $this->linkedInquiry($entity, $context);
        $linkedOwner = $this->linkedOwner($entity, $context);
        $payment = $entity instanceof AccountingCustomerPayment ? $entity : null;
        $information = $entity instanceof CrmInformation ? $entity : ($context['information'] ?? null);
        $document = $entity instanceof \App\Models\CrmDocument ? $entity : null;
        $customer = $entity instanceof CrmCustomer ? $entity : null;

        return strtr($value, [
            '{entity_label}' => $this->entityLabel($entity),
            '{lead_name}' => $inquiry?->full_name ?? '',
            '{customer_name}' => $customer?->full_name ?? ($inquiry?->full_name ?? ''),
            '{task_title}' => $entity instanceof CrmTask ? $entity->title : '',
            '{information_title}' => $information?->title ?? '',
            '{document_title}' => $document?->title ?: ($document?->original_file_name ?? ''),
            '{payment_amount}' => $payment ? number_format((float) $payment->amount, 2) : '',
            '{owner_name}' => $linkedOwner?->name ?? '',
        ]);
    }

    protected function usersWithPermission(string $permission): Collection
    {
        return User::query()
            ->where('is_active', true)
            ->get()
            ->filter(fn (User $user) => $user->hasPermission($permission))
            ->values();
    }

    protected function managementRecipients(): Collection
    {
        return User::query()
            ->where('is_active', true)
            ->get()
            ->filter(fn (User $user) => CrmLeadAccess::canViewAll($user) || $user->hasPermission('reports.view'))
            ->values();
    }

    protected function loggableContext(array $context): array
    {
        return collect($context)->map(function ($value) {
            if ($value instanceof User) {
                return ['id' => $value->id, 'name' => $value->name];
            }

            if ($value instanceof Model) {
                return ['id' => $value->getKey(), 'type' => get_class($value)];
            }

            return $value;
        })->all();
    }
}
