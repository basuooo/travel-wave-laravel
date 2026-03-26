<?php

namespace App\Support;

use App\Models\AccountingCustomerAccount;
use App\Models\AccountingCustomerPayment;
use App\Models\CrmInformation;
use App\Models\CrmInformationRecipient;
use App\Models\CrmTask;
use App\Models\Inquiry;
use App\Models\User;
use App\Notifications\AdminDatabaseNotification;
use App\Notifications\CrmFollowUpReminderNotification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class AdminNotificationCenterService
{
    public const SEVERITY_INFO = 'info';
    public const SEVERITY_SUCCESS = 'success';
    public const SEVERITY_WARNING = 'warning';
    public const SEVERITY_DANGER = 'danger';

    public function isEnabled(): bool
    {
        return Schema::hasTable('notifications');
    }

    public function notifyUser(User $user, array $payload): void
    {
        if (! $this->isEnabled() || ! $user->is_active) {
            return;
        }

        $eventKey = $payload['event_key'] ?? null;

        if ($eventKey && $this->alreadySent($user, $eventKey)) {
            return;
        }

        $user->notify(new AdminDatabaseNotification($this->normalizePayload($payload)));
    }

    public function notifyUsers(iterable $users, array $payload): void
    {
        collect($users)
            ->filter(fn ($user) => $user instanceof User)
            ->unique('id')
            ->each(fn (User $user) => $this->notifyUser($user, $payload));
    }

    public function createTaskAssignedNotification(CrmTask $task, ?User $actor = null, ?string $context = null): void
    {
        $task->loadMissing(['assignedUser', 'creator', 'inquiry']);
        $recipient = $task->assignedUser;

        if (! $recipient) {
            return;
        }

        $isReassigned = $context === 'reassigned';

        $this->notifyUser($recipient, [
            'event_key' => sprintf('task_assigned:%d:%d:%s', $task->id, $recipient->id, $task->updated_at?->timestamp ?: now()->timestamp),
            'type' => $isReassigned ? 'task_reassigned' : 'task_assigned',
            'module' => 'tasks',
            'severity' => self::SEVERITY_INFO,
            'title_ar' => $isReassigned
                ? __('admin.notifications_payload_task_reassigned_title', locale: 'ar')
                : __('admin.notifications_payload_task_assigned_title', locale: 'ar'),
            'title_en' => $isReassigned
                ? __('admin.notifications_payload_task_reassigned_title', locale: 'en')
                : __('admin.notifications_payload_task_assigned_title', locale: 'en'),
            'message_ar' => __('admin.notifications_payload_task_label', ['title' => $task->title], locale: 'ar'),
            'message_en' => __('admin.notifications_payload_task_label', ['title' => $task->title], locale: 'en'),
            'subject_name' => $task->title,
            'lead_name' => $task->inquiry?->full_name,
            'url' => route('admin.crm.tasks.show', $task),
            'action_label_ar' => __('admin.notifications_payload_view_task', locale: 'ar'),
            'action_label_en' => __('admin.notifications_payload_view_task', locale: 'en'),
            'notifiable_type' => CrmTask::class,
            'notifiable_id' => $task->id,
            'created_by' => $actor?->id,
            'meta' => [
                'priority' => $task->priority,
                'status' => $task->status,
                'due_at' => optional($task->due_at)?->toIso8601String(),
            ],
        ]);
    }

    public function createTaskCompletedNotification(CrmTask $task, ?User $actor = null): void
    {
        $task->loadMissing(['creator', 'assignedUser']);

        if (! $task->creator || (int) $task->creator->id === (int) ($actor?->id)) {
            return;
        }

        $this->notifyUser($task->creator, [
            'event_key' => sprintf('task_completed:%d:%d', $task->id, $task->completed_at?->timestamp ?: now()->timestamp),
            'type' => 'task_completed',
            'module' => 'tasks',
            'severity' => self::SEVERITY_SUCCESS,
            'title_ar' => __('admin.notifications_payload_task_completed_title', locale: 'ar'),
            'title_en' => __('admin.notifications_payload_task_completed_title', locale: 'en'),
            'message_ar' => __('admin.notifications_payload_completed_task', ['title' => $task->title], locale: 'ar'),
            'message_en' => __('admin.notifications_payload_completed_task', ['title' => $task->title], locale: 'en'),
            'subject_name' => $task->title,
            'url' => route('admin.crm.tasks.show', $task),
            'action_label_ar' => __('admin.notifications_payload_view_task', locale: 'ar'),
            'action_label_en' => __('admin.notifications_payload_view_task', locale: 'en'),
            'notifiable_type' => CrmTask::class,
            'notifiable_id' => $task->id,
            'created_by' => $actor?->id,
        ]);
    }

    public function createLeadAssignedNotification(Inquiry $lead, ?int $oldAssignedUserId = null, ?User $actor = null): void
    {
        $lead->loadMissing(['assignedUser', 'crmStatus']);

        if (! $lead->assignedUser) {
            return;
        }

        $type = $oldAssignedUserId ? 'lead_reassigned' : 'lead_assigned';

        $this->notifyUser($lead->assignedUser, [
            'event_key' => sprintf('lead_assigned:%d:%d:%s', $lead->id, $lead->assignedUser->id, $lead->updated_at?->timestamp ?: now()->timestamp),
            'type' => $type,
            'module' => 'crm',
            'severity' => self::SEVERITY_INFO,
            'title_ar' => $oldAssignedUserId
                ? __('admin.notifications_payload_lead_reassigned_title', locale: 'ar')
                : __('admin.notifications_payload_lead_assigned_title', locale: 'ar'),
            'title_en' => $oldAssignedUserId
                ? __('admin.notifications_payload_lead_reassigned_title', locale: 'en')
                : __('admin.notifications_payload_lead_assigned_title', locale: 'en'),
            'message_ar' => __('admin.notifications_payload_lead_label', ['title' => $lead->full_name], locale: 'ar'),
            'message_en' => __('admin.notifications_payload_lead_label', ['title' => $lead->full_name], locale: 'en'),
            'subject_name' => $lead->full_name,
            'lead_name' => $lead->full_name,
            'url' => route('admin.crm.leads.show', $lead),
            'action_label_ar' => __('admin.notifications_payload_view_lead', locale: 'ar'),
            'action_label_en' => __('admin.notifications_payload_view_lead', locale: 'en'),
            'notifiable_type' => Inquiry::class,
            'notifiable_id' => $lead->id,
            'created_by' => $actor?->id,
            'meta' => [
                'crm_status_id' => $lead->crm_status_id,
                'phone' => $lead->phone,
            ],
        ]);
    }

    public function createInformationPublishedNotifications(CrmInformation $information): void
    {
        $information->loadMissing(['recipients.user', 'creator']);

        $information->recipients
            ->loadMissing('user')
            ->each(function (CrmInformationRecipient $recipient) use ($information) {
                if (! $recipient->user) {
                    return;
                }

                $severity = $information->priority === 'urgent' ? self::SEVERITY_DANGER : self::SEVERITY_INFO;

                $this->notifyUser($recipient->user, [
                    'event_key' => sprintf('information_new:%d:%d', $information->id, $recipient->user_id),
                    'type' => 'information_new',
                    'module' => 'information',
                    'severity' => $severity,
                    'title_ar' => __('admin.notifications_payload_information_new_title', locale: 'ar'),
                    'title_en' => __('admin.notifications_payload_information_new_title', locale: 'en'),
                    'message_ar' => $information->title,
                    'message_en' => $information->title,
                    'subject_name' => $information->title,
                    'url' => route('admin.crm.information.show', $information),
                    'action_label_ar' => __('admin.notifications_payload_view_information', locale: 'ar'),
                    'action_label_en' => __('admin.notifications_payload_view_information', locale: 'en'),
                    'notifiable_type' => CrmInformation::class,
                    'notifiable_id' => $information->id,
                    'created_by' => $information->created_by,
                    'meta' => [
                        'priority' => $information->priority,
                        'category' => $information->category,
                        'event_date' => optional($information->event_date)?->toDateString(),
                    ],
                ]);
            });
    }

    public function createInformationAckReminder(CrmInformationRecipient $recipient): void
    {
        $recipient->loadMissing(['information', 'user']);

        if (! $recipient->user || ! $recipient->information || ! is_null($recipient->acknowledged_at)) {
            return;
        }

        $severity = $recipient->information->priority === 'urgent' ? self::SEVERITY_DANGER : self::SEVERITY_WARNING;
        $dateKey = now()->toDateString();

        $this->notifyUser($recipient->user, [
            'event_key' => sprintf('information_ack_required:%d:%d:%s', $recipient->crm_information_id, $recipient->user_id, $dateKey),
            'type' => 'information_ack_required',
            'module' => 'information',
            'severity' => $severity,
            'title_ar' => __('admin.notifications_payload_ack_required_title', locale: 'ar'),
            'title_en' => __('admin.notifications_payload_ack_required_title', locale: 'en'),
            'message_ar' => $recipient->information->title,
            'message_en' => $recipient->information->title,
            'subject_name' => $recipient->information->title,
            'url' => route('admin.crm.information.show', $recipient->information),
            'action_label_ar' => __('admin.notifications_payload_ack_action', locale: 'ar'),
            'action_label_en' => __('admin.notifications_payload_ack_action', locale: 'en'),
            'notifiable_type' => CrmInformation::class,
            'notifiable_id' => $recipient->information->id,
            'created_by' => $recipient->information->created_by,
            'meta' => [
                'priority' => $recipient->information->priority,
                'recipient_id' => $recipient->id,
            ],
        ]);
    }

    public function createAccountingPaymentNotification(AccountingCustomerAccount $account, AccountingCustomerPayment $payment, ?User $actor = null): void
    {
        $account->loadMissing(['assignedUser', 'inquiry']);

        $recipients = collect();

        if ($account->assignedUser) {
            $recipients->push($account->assignedUser);
        }

        $this->usersWithPermission('accounting.view')->each(fn (User $user) => $recipients->push($user));

        $this->notifyUsers($recipients->unique('id'), [
            'event_key' => sprintf('accounting_payment:%d:%d', $account->id, $payment->id),
            'type' => 'accounting_payment',
            'module' => 'accounting',
            'severity' => self::SEVERITY_SUCCESS,
            'title_ar' => __('admin.notifications_payload_payment_new_title', locale: 'ar'),
            'title_en' => __('admin.notifications_payload_payment_new_title', locale: 'en'),
            'message_ar' => sprintf('%s - %s', $account->customer_name, number_format((float) $payment->amount, 2)),
            'message_en' => sprintf('%s - %s', $account->customer_name, number_format((float) $payment->amount, 2)),
            'subject_name' => $account->customer_name,
            'lead_name' => $account->customer_name,
            'url' => route('admin.accounting.customers.show', $account),
            'action_label_ar' => __('admin.notifications_payload_view_account', locale: 'ar'),
            'action_label_en' => __('admin.notifications_payload_view_account', locale: 'en'),
            'notifiable_type' => AccountingCustomerAccount::class,
            'notifiable_id' => $account->id,
            'created_by' => $actor?->id,
            'meta' => [
                'payment_id' => $payment->id,
                'amount' => (float) $payment->amount,
                'payment_date' => optional($payment->payment_date)?->toDateString(),
            ],
        ]);
    }

    public function dispatchOperationalReminders(): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        $this->dispatchTaskDueTodayNotifications();
        $this->dispatchDelayedTaskNotifications();
        $this->dispatchDelayedLeadNotifications();
        $this->dispatchInformationAcknowledgementReminders();
    }

    public function presentNotification(DatabaseNotification $notification): array
    {
        $data = (array) ($notification->data ?? []);
        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
        $type = $data['type'] ?? 'system_alert';
        $severity = $data['severity'] ?? $this->defaultSeverityFor($type);
        $title = $data['title_' . $locale] ?? $data['title_ar'] ?? $data['title_en'] ?? $this->localizedTypeLabel($type);
        $message = $data['message_' . $locale] ?? $data['message_ar'] ?? $data['message_en'] ?? ($data['lead_name'] ?? $data['subject_name'] ?? '');
        $url = $data['url'] ?? route('admin.dashboard');
        $module = $data['module'] ?? $this->defaultModuleFor($type);
        $typeLabel = $this->localizedTypeLabel($type);
        $actionLabel = $data['action_label_' . $locale] ?? $data['action_label_ar'] ?? $data['action_label_en'] ?? __('admin.notifications_ui_action_view');

        if ($notification->type === CrmFollowUpReminderNotification::class) {
            $type = 'lead_followup_due';
            $severity = $data['severity'] ?? self::SEVERITY_WARNING;
            $title = $data['title_' . $locale] ?? $data['title_ar'] ?? $data['title_en'] ?? $this->localizedTypeLabel($type);
            $message = $data['follow_up_note'] ?? ($data['lead_name'] ?? '');
            $url = $data['url'] ?? route('admin.crm.dashboard');
            $module = 'crm';
            $typeLabel = $this->localizedTypeLabel($type);
            $actionLabel = __('admin.notifications_ui_action_open_follow_up');
        }

        return [
            'id' => $notification->id,
            'type' => $type,
            'type_label' => $typeLabel,
            'module' => $module,
            'module_label' => $this->localizedModuleLabel($module),
            'severity' => $severity,
            'severity_label' => $this->localizedSeverityLabel($severity),
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'action_label' => $actionLabel,
            'subject_name' => $data['subject_name'] ?? $data['lead_name'] ?? null,
            'lead_name' => $data['lead_name'] ?? null,
            'created_at' => $notification->created_at,
            'read_at' => $notification->read_at,
            'is_read' => ! is_null($notification->read_at),
            'is_actionable' => filled($url),
            'meta' => $data['meta'] ?? [],
        ];
    }

    public function presentNotifications(iterable $notifications): Collection
    {
        return collect($notifications)->map(fn (DatabaseNotification $notification) => $this->presentNotification($notification));
    }

    public function presentPaginator(LengthAwarePaginator $paginator): LengthAwarePaginator
    {
        $paginator->setCollection($this->presentNotifications($paginator->getCollection()));

        return $paginator;
    }

    public function localizedTypeLabel(string $type): string
    {
        return match ($type) {
            'task_assigned' => __('admin.notifications_ui_type_task_assigned'),
            'task_reassigned' => __('admin.notifications_ui_type_task_reassigned'),
            'task_due' => __('admin.notifications_ui_type_task_due'),
            'task_delayed' => __('admin.notifications_ui_type_task_delayed'),
            'task_completed' => __('admin.notifications_ui_type_task_completed'),
            'lead_assigned' => __('admin.notifications_ui_type_lead_assigned'),
            'lead_reassigned' => __('admin.notifications_ui_type_lead_reassigned'),
            'lead_delayed' => __('admin.notifications_ui_type_lead_delayed'),
            'lead_followup_due' => __('admin.notifications_ui_type_lead_followup_due'),
            'information_new' => __('admin.notifications_ui_type_information_new'),
            'information_ack_required' => __('admin.notifications_ui_type_information_ack_required'),
            'accounting_payment' => __('admin.notifications_ui_type_accounting_payment'),
            'system_alert' => __('admin.notifications_ui_type_system_alert'),
            default => str_replace('_', ' ', ucfirst($type)),
        };
    }

    public function localizedSeverityLabel(string $severity): string
    {
        return match ($severity) {
            self::SEVERITY_INFO => __('admin.notifications_ui_severity_info'),
            self::SEVERITY_SUCCESS => __('admin.notifications_ui_severity_success'),
            self::SEVERITY_WARNING => __('admin.notifications_ui_severity_warning'),
            self::SEVERITY_DANGER => __('admin.notifications_ui_severity_danger'),
            default => ucfirst($severity),
        };
    }

    public function localizedModuleLabel(string $module): string
    {
        return match ($module) {
            'crm' => __('admin.notifications_ui_module_crm'),
            'tasks' => __('admin.notifications_ui_module_tasks'),
            'information' => __('admin.notifications_ui_module_information'),
            'accounting' => __('admin.notifications_ui_module_accounting'),
            'workflow' => __('admin.notifications_ui_module_workflow'),
            default => __('admin.notifications_ui_module_system'),
        };
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

    protected function alreadySent(User $user, string $eventKey): bool
    {
        return $user->notifications()
            ->where('type', AdminDatabaseNotification::class)
            ->where('data->event_key', $eventKey)
            ->exists();
    }

    protected function normalizePayload(array $payload): array
    {
        return [
            'event_key' => $payload['event_key'] ?? null,
            'type' => $payload['type'] ?? 'system_alert',
            'module' => $payload['module'] ?? 'system',
            'severity' => $payload['severity'] ?? self::SEVERITY_INFO,
            'title_ar' => $payload['title_ar'] ?? null,
            'title_en' => $payload['title_en'] ?? null,
            'message_ar' => $payload['message_ar'] ?? null,
            'message_en' => $payload['message_en'] ?? null,
            'subject_name' => $payload['subject_name'] ?? null,
            'lead_name' => $payload['lead_name'] ?? null,
            'url' => $payload['url'] ?? null,
            'action_label_ar' => $payload['action_label_ar'] ?? null,
            'action_label_en' => $payload['action_label_en'] ?? null,
            'notifiable_type' => $payload['notifiable_type'] ?? null,
            'notifiable_id' => $payload['notifiable_id'] ?? null,
            'created_by' => $payload['created_by'] ?? null,
            'meta' => $payload['meta'] ?? [],
        ];
    }

    protected function defaultSeverityFor(string $type): string
    {
        return match ($type) {
            'task_completed', 'accounting_payment' => self::SEVERITY_SUCCESS,
            'task_due', 'information_ack_required', 'lead_followup_due' => self::SEVERITY_WARNING,
            'task_delayed', 'lead_delayed' => self::SEVERITY_DANGER,
            default => self::SEVERITY_INFO,
        };
    }

    protected function defaultModuleFor(string $type): string
    {
        return match (true) {
            str_starts_with($type, 'task_') => 'tasks',
            str_starts_with($type, 'lead_') => 'crm',
            str_starts_with($type, 'information_') => 'information',
            str_starts_with($type, 'accounting_') => 'accounting',
            default => 'system',
        };
    }

    protected function dispatchTaskDueTodayNotifications(): void
    {
        CrmTask::query()
            ->with(['assignedUser', 'inquiry'])
            ->active()
            ->whereDate('due_at', today())
            ->chunkById(100, function ($tasks) {
                foreach ($tasks as $task) {
                    if (! $task->assignedUser) {
                        continue;
                    }

                    $this->notifyUser($task->assignedUser, [
                        'event_key' => sprintf('task_due:%d:%s', $task->id, today()->toDateString()),
                        'type' => 'task_due',
                        'module' => 'tasks',
                        'severity' => self::SEVERITY_WARNING,
                        'title_ar' => __('admin.notifications_payload_task_due_today_title', locale: 'ar'),
                        'title_en' => __('admin.notifications_payload_task_due_today_title', locale: 'en'),
                        'message_ar' => $task->title,
                        'message_en' => $task->title,
                        'subject_name' => $task->title,
                        'lead_name' => $task->inquiry?->full_name,
                        'url' => route('admin.crm.tasks.show', $task),
                        'action_label_ar' => __('admin.notifications_payload_view_task', locale: 'ar'),
                        'action_label_en' => __('admin.notifications_payload_view_task', locale: 'en'),
                        'notifiable_type' => CrmTask::class,
                        'notifiable_id' => $task->id,
                        'created_by' => $task->created_by,
                        'meta' => [
                            'due_at' => optional($task->due_at)?->toIso8601String(),
                            'priority' => $task->priority,
                        ],
                    ]);
                }
            });
    }

    protected function dispatchDelayedTaskNotifications(): void
    {
        CrmTask::query()
            ->with(['assignedUser', 'creator', 'inquiry'])
            ->delayed()
            ->chunkById(100, function ($tasks) {
                foreach ($tasks as $task) {
                    $recipients = collect();
                    if ($task->assignedUser) {
                        $recipients->push($task->assignedUser);
                    }
                    $this->managementRecipients()->each(fn (User $user) => $recipients->push($user));

                    $this->notifyUsers($recipients->unique('id'), [
                        'event_key' => sprintf('task_delayed:%d:%s', $task->id, today()->toDateString()),
                        'type' => 'task_delayed',
                        'module' => 'tasks',
                        'severity' => self::SEVERITY_DANGER,
                        'title_ar' => __('admin.notifications_payload_delayed_task_title', locale: 'ar'),
                        'title_en' => __('admin.notifications_payload_delayed_task_title', locale: 'en'),
                        'message_ar' => $task->title,
                        'message_en' => $task->title,
                        'subject_name' => $task->title,
                        'lead_name' => $task->inquiry?->full_name,
                        'url' => route('admin.crm.tasks.show', $task),
                        'action_label_ar' => __('admin.notifications_payload_view_task', locale: 'ar'),
                        'action_label_en' => __('admin.notifications_payload_view_task', locale: 'en'),
                        'notifiable_type' => CrmTask::class,
                        'notifiable_id' => $task->id,
                        'created_by' => $task->created_by,
                        'meta' => [
                            'due_at' => optional($task->due_at)?->toIso8601String(),
                            'overdue_label' => $task->overdueLabel(),
                        ],
                    ]);
                }
            });
    }

    protected function dispatchDelayedLeadNotifications(): void
    {
        $service = app(CrmDelayedLeadService::class);
        $leads = $service->annotate(
            $service->applyDelayedScope(
                Inquiry::query()->with(['assignedUser', 'crmStatus'])
            )->limit(300)->get()
        );

        $leads->each(function (Inquiry $lead) {
            $recipients = collect();

            if ($lead->assignedUser) {
                $recipients->push($lead->assignedUser);
            }

            $this->managementRecipients()->each(fn (User $user) => $recipients->push($user));

            $this->notifyUsers($recipients->unique('id'), [
                'event_key' => sprintf('lead_delayed:%d:%s', $lead->id, today()->toDateString()),
                'type' => 'lead_delayed',
                'module' => 'crm',
                'severity' => self::SEVERITY_DANGER,
                'title_ar' => __('admin.notifications_payload_delayed_lead_title', locale: 'ar'),
                'title_en' => __('admin.notifications_payload_delayed_lead_title', locale: 'en'),
                'message_ar' => $lead->full_name,
                'message_en' => $lead->full_name,
                'subject_name' => $lead->full_name,
                'lead_name' => $lead->full_name,
                'url' => route('admin.crm.leads.show', $lead),
                'action_label_ar' => __('admin.notifications_payload_view_lead', locale: 'ar'),
                'action_label_en' => __('admin.notifications_payload_view_lead', locale: 'en'),
                'notifiable_type' => Inquiry::class,
                'notifiable_id' => $lead->id,
                'created_by' => null,
                'meta' => [
                    'delay_reason' => $lead->getAttribute('delay_reason'),
                ],
            ]);
        });
    }

    protected function dispatchInformationAcknowledgementReminders(): void
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
                    $this->createInformationAckReminder($recipient);
                }
            });
    }
}
