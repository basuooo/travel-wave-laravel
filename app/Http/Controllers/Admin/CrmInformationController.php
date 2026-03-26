<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmInformation;
use App\Models\CrmInformationRecipient;
use App\Models\Role;
use App\Models\User;
use App\Support\AdminNotificationCenterService;
use App\Support\AuditLogService;
use App\Support\CrmLeadAccess;
use App\Support\WorkflowAutomationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CrmInformationController extends Controller
{
    public function index(Request $request)
    {
        $viewer = $request->user();
        $canManage = $viewer?->hasPermission('information.manage') ?? false;

        if ($canManage) {
            $query = CrmInformation::query()
                ->with(['creator'])
                ->withCount([
                    'recipients as recipients_count',
                    'recipients as acknowledged_count' => fn ($builder) => $builder->whereNotNull('acknowledged_at'),
                    'recipients as pending_count' => fn ($builder) => $builder->whereNull('acknowledged_at'),
                ])
                ->latest();

            if ($request->filled('audience_type')) {
                $query->where('audience_type', $request->string('audience_type'));
            }

            if ($request->filled('created_by')) {
                $query->where('created_by', $request->integer('created_by'));
            }

            if ($request->filled('priority')) {
                $query->where('priority', $request->string('priority'));
            }

            if ($request->filled('event_date')) {
                $query->whereDate('event_date', $request->date('event_date'));
            }

            if ($request->filled('ack_status')) {
                $ackStatus = $request->string('ack_status')->toString();

                if ($ackStatus === 'pending') {
                    $query->whereHas('recipients', fn ($builder) => $builder->whereNull('acknowledged_at'));
                } elseif ($ackStatus === 'acknowledged') {
                    $query->whereDoesntHave('recipients', fn ($builder) => $builder->whereNull('acknowledged_at'));
                }
            }

            if ($request->filled('user_id')) {
                $userId = $request->integer('user_id');
                $query->whereHas('recipients', fn ($builder) => $builder->where('user_id', $userId));

                if ($request->string('user_ack_state')->toString() === 'pending') {
                    $query->whereHas('recipients', fn ($builder) => $builder->where('user_id', $userId)->whereNull('acknowledged_at'));
                } elseif ($request->string('user_ack_state')->toString() === 'acknowledged') {
                    $query->whereHas('recipients', fn ($builder) => $builder->where('user_id', $userId)->whereNotNull('acknowledged_at'));
                }
            }

            return view('admin.crm.information.index', [
                'items' => $query->paginate(20)->withQueryString(),
                'canManageInformation' => true,
                'audienceOptions' => $this->audienceOptions(),
                'priorityOptions' => $this->priorityOptions(),
                'users' => $this->audienceUsers(),
            ]);
        }

        $query = CrmInformationRecipient::query()
            ->with(['information.creator'])
            ->where('user_id', $viewer?->id)
            ->whereHas('information', function ($builder) {
                $builder->where('is_active', true)
                    ->where(function ($query) {
                        $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                    });
            })
            ->orderByRaw('CASE WHEN acknowledged_at IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('created_at');

        return view('admin.crm.information.index', [
            'items' => $query->paginate(20)->withQueryString(),
            'canManageInformation' => false,
            'audienceOptions' => $this->audienceOptions(),
            'priorityOptions' => $this->priorityOptions(),
            'users' => collect(),
        ]);
    }

    public function create()
    {
        return view('admin.crm.information.create', [
            'audienceOptions' => $this->audienceOptions(),
            'categoryOptions' => $this->categoryOptions(),
            'priorityOptions' => $this->priorityOptions(),
            'users' => $this->audienceUsers(),
        ]);
    }

    public function store(Request $request, AdminNotificationCenterService $notificationCenterService)
    {
        $auditLogService = app(AuditLogService::class);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['nullable', Rule::in(array_keys($this->categoryOptions()))],
            'priority' => ['nullable', Rule::in(array_keys($this->priorityOptions()))],
            'event_date' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'audience_type' => ['required', Rule::in(array_keys($this->audienceOptions()))],
            'selected_users' => ['required_if:audience_type,' . CrmInformation::AUDIENCE_SELECTED_USERS, 'array'],
            'selected_users.*' => ['integer', 'exists:users,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $information = CrmInformation::query()->create([
            'title' => $data['title'],
            'content' => $data['content'],
            'category' => $data['category'] ?? null,
            'priority' => $data['priority'] ?? 'normal',
            'event_date' => $data['event_date'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'audience_type' => $data['audience_type'],
            'created_by' => $request->user()?->id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $recipientRows = $this->resolveAudienceUsers($data['audience_type'], collect($data['selected_users'] ?? []))
            ->map(fn (User $user) => [
                'user_id' => $user->id,
                'delivered_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        $information->recipients()->createMany($recipientRows->all());
        $notificationCenterService->createInformationPublishedNotifications($information->fresh(['recipients.user', 'creator']));
        $auditLogService->log(
            $request->user(),
            'information',
            'created',
            $information->fresh(),
            [
                'title' => $information->title,
                'description' => $information->content,
                'new_values' => [
                    'title' => $information->title,
                    'category' => $information->category,
                    'audience_type' => $information->audience_type,
                    'is_active' => $information->is_active,
                ],
                'changed_fields' => ['title', 'category', 'audience_type', 'is_active'],
            ]
        );
        app(WorkflowAutomationService::class)->dispatch(WorkflowAutomationService::TRIGGER_INFORMATION_PUBLISHED, $information->fresh(['creator', 'recipients.user']), [
            'actor' => $request->user(),
        ]);

        return redirect()
            ->route('admin.crm.information.show', $information)
            ->with('success', __('admin.crm_information_published_success'));
    }

    public function show(Request $request, CrmInformation $information)
    {
        $viewer = $request->user();
        $canManage = $viewer?->hasPermission('information.manage') ?? false;

        if ($canManage) {
            $information->load(['creator', 'recipients.user.roles']);

            return view('admin.crm.information.show', [
                'information' => $information,
                'canManageInformation' => true,
                'recipient' => null,
            ]);
        }

        $recipient = $information->recipients()
            ->where('user_id', $viewer?->id)
            ->with(['information.creator'])
            ->firstOrFail();

        if (is_null($recipient->seen_at)) {
            $recipient->forceFill(['seen_at' => now()])->save();
        }

        return view('admin.crm.information.show', [
            'information' => $information->load('creator'),
            'canManageInformation' => false,
            'recipient' => $recipient->fresh(),
        ]);
    }

    public function acknowledge(Request $request, CrmInformation $information)
    {
        $auditLogService = app(AuditLogService::class);
        $recipient = $information->recipients()
            ->where('user_id', $request->user()?->id)
            ->firstOrFail();

        if (is_null($recipient->acknowledged_at)) {
            $recipient->forceFill([
                'seen_at' => $recipient->seen_at ?: now(),
                'acknowledged_at' => now(),
            ])->save();

            $auditLogService->log(
                $request->user(),
                'information',
                'acknowledged',
                $information,
                [
                    'title' => $information->title,
                    'description' => $information->title,
                    'new_values' => [
                        'user_id' => $request->user()?->name,
                        'acknowledged_at' => optional($recipient->acknowledged_at)->toDateTimeString(),
                    ],
                    'changed_fields' => ['user_id', 'acknowledged_at'],
                ]
            );
        }

        return redirect()
            ->route('admin.crm.information.show', $information)
            ->with('success', __('admin.crm_information_ack_saved'));
    }

    protected function audienceOptions(): array
    {
        return [
            CrmInformation::AUDIENCE_ALL => __('admin.crm_information_audience_all'),
            CrmInformation::AUDIENCE_ADMINS => __('admin.crm_information_audience_admins'),
            CrmInformation::AUDIENCE_SELLERS => __('admin.crm_information_audience_sellers'),
            CrmInformation::AUDIENCE_SELECTED_USERS => __('admin.crm_information_audience_selected'),
        ];
    }

    protected function categoryOptions(): array
    {
        return [
            'embassy_decision' => __('admin.crm_information_category_embassy_decision'),
            'price_update' => __('admin.crm_information_category_price_update'),
            'holiday' => __('admin.crm_information_category_holiday'),
            'internal_process' => __('admin.crm_information_category_internal_process'),
            'general_notice' => __('admin.crm_information_category_general_notice'),
        ];
    }

    protected function priorityOptions(): array
    {
        return [
            'normal' => __('admin.crm_information_priority_normal'),
            'important' => __('admin.crm_information_priority_important'),
            'urgent' => __('admin.crm_information_priority_urgent'),
        ];
    }

    protected function audienceUsers()
    {
        return User::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    protected function resolveAudienceUsers(string $audienceType, $selectedUsers)
    {
        return match ($audienceType) {
            CrmInformation::AUDIENCE_ALL => $this->audienceUsers()->filter(fn (User $user) => $user->canAccessDashboard())->values(),
            CrmInformation::AUDIENCE_ADMINS => $this->audienceUsers()->filter(fn (User $user) => CrmLeadAccess::canViewAll($user))->values(),
            CrmInformation::AUDIENCE_SELLERS => $this->audienceUsers()->filter(fn (User $user) => ! CrmLeadAccess::canViewAll($user) && $user->hasPermission('leads.view'))->values(),
            CrmInformation::AUDIENCE_SELECTED_USERS => $this->audienceUsers()->whereIn('id', $selectedUsers->map(fn ($id) => (int) $id))->values(),
            default => collect(),
        };
    }
}

