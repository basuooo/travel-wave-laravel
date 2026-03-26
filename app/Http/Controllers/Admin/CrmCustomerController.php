<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmCustomer;
use App\Models\Inquiry;
use App\Models\User;
use App\Support\CrmLeadAccess;
use App\Support\CustomerConversionService;
use App\Support\AuditLogService;
use App\Support\WorkflowAutomationService;
use Illuminate\Http\Request;

class CrmCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = CrmCustomer::query()
            ->with(['inquiry.crmStatus', 'assignedUser', 'crmSource', 'crmServiceType', 'accountingAccount'])
            ->visibleTo($request->user())
            ->latest('converted_at');

        if ($request->filled('q')) {
            $needle = '%' . trim((string) $request->query('q')) . '%';
            $query->where(function ($builder) use ($needle) {
                $builder->where('customer_code', 'like', $needle)
                    ->orWhere('full_name', 'like', $needle)
                    ->orWhere('phone', 'like', $needle)
                    ->orWhere('email', 'like', $needle);
            });
        }

        if ($request->filled('assigned_user_id')) {
            if ($request->string('assigned_user_id')->toString() === 'unassigned') {
                $query->whereNull('assigned_user_id');
            } else {
                $query->where('assigned_user_id', $request->integer('assigned_user_id'));
            }
        }

        foreach (['stage', 'crm_source_id', 'crm_service_type_id'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }

        if ($request->filled('active_state')) {
            $request->string('active_state')->toString() === 'active'
                ? $query->where('is_active', true)
                : $query->where('is_active', false);
        }

        if ($request->filled('payment_status')) {
            $query->whereHas('accountingAccount', fn ($builder) => $builder->where('payment_status', $request->string('payment_status')->toString()));
        }

        if ($request->filled('from')) {
            $query->whereDate('converted_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('converted_at', '<=', $request->date('to'));
        }

        $items = $query->paginate(20)->withQueryString();

        return view('admin.crm.customers.index', [
            'items' => $items,
            'users' => $this->assignableUsers(),
            'stageOptions' => CrmCustomer::stageOptions(),
            'sources' => \App\Models\CrmLeadSource::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'serviceTypes' => \App\Models\CrmServiceType::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'canViewAllCustomers' => CrmLeadAccess::canViewAll($request->user()),
            'summary' => [
                'total' => CrmCustomer::query()->visibleTo($request->user())->count(),
                'active' => CrmCustomer::query()->visibleTo($request->user())->where('is_active', true)->count(),
                'closed' => CrmCustomer::query()->visibleTo($request->user())->whereIn('stage', [CrmCustomer::STAGE_CLOSED, CrmCustomer::STAGE_CANCELLED])->count(),
                'converted_this_month' => CrmCustomer::query()->visibleTo($request->user())->whereBetween('converted_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            ],
        ]);
    }

    public function create(Request $request)
    {
        $lead = $request->filled('inquiry_id') ? Inquiry::query()->findOrFail($request->integer('inquiry_id')) : null;

        if ($lead) {
            $this->authorizeLeadContext($lead);
        }

        return view('admin.crm.customers.create', [
            'customer' => new CrmCustomer([
                'inquiry_id' => $lead?->id,
                'full_name' => $lead?->full_name,
                'phone' => $lead?->phone,
                'whatsapp_number' => $lead?->whatsapp_number,
                'email' => $lead?->email,
                'nationality' => $lead?->nationality,
                'country' => $lead?->country,
                'destination' => $lead?->serviceDestinationValue(),
                'assigned_user_id' => $lead?->assigned_user_id,
                'stage' => CrmCustomer::STAGE_NEW,
                'converted_at' => now(),
            ]),
            'lead' => $lead,
            'availableLeads' => $this->availableLeads(),
            'users' => $this->assignableUsers(),
            'stageOptions' => CrmCustomer::stageOptions(),
            'formAction' => route('admin.crm.customers.store'),
            'formMethod' => 'POST',
        ]);
    }

    public function store(Request $request, CustomerConversionService $conversionService)
    {
        $data = $request->validate([
            'inquiry_id' => ['required', 'exists:inquiries,id', 'unique:crm_customers,inquiry_id'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'destination' => ['nullable', 'string', 'max:255'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'stage' => ['required', 'in:' . implode(',', array_keys(CrmCustomer::stageOptions()))],
            'appointment_at' => ['nullable', 'date'],
            'submission_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $lead = Inquiry::query()->findOrFail($data['inquiry_id']);
        $this->authorizeLeadContext($lead);

        $customer = $conversionService->convertFromLead($lead, $request->user(), $data);

        return redirect()
            ->route('admin.crm.customers.show', $customer)
            ->with('success', __('admin.customer_created_success'));
    }

    public function show(CrmCustomer $customer)
    {
        $this->authorizeCustomer($customer);

        $customer->load([
            'inquiry.crmStatus',
            'inquiry.utmCampaign',
            'inquiry.crmNotes.user',
            'inquiry.crmTasks.assignedUser',
            'inquiry.crmTasks.creator',
            'inquiry.crmFollowUps.assignedUser',
            'assignedUser',
            'creator',
            'crmSource',
            'crmServiceType',
            'crmServiceSubtype',
            'documents.category',
            'documents.uploader',
            'activities.user',
            'accountingAccount.payments.creator',
            'accountingAccount.expenses.category',
        ]);

        return view('admin.crm.customers.show', [
            'customer' => $customer,
            'stageOptions' => CrmCustomer::stageOptions(),
            'users' => $this->assignableUsers(),
            'canViewAllCustomers' => CrmLeadAccess::canViewAll(auth()->user()),
        ]);
    }

    public function edit(CrmCustomer $customer)
    {
        $this->authorizeCustomer($customer, true);

        return view('admin.crm.customers.edit', [
            'customer' => $customer->load('inquiry.crmStatus'),
            'lead' => $customer->inquiry,
            'availableLeads' => collect([$customer->inquiry])->filter(),
            'users' => $this->assignableUsers(),
            'stageOptions' => CrmCustomer::stageOptions(),
            'formAction' => route('admin.crm.customers.update', $customer),
            'formMethod' => 'PUT',
        ]);
    }

    public function update(Request $request, CrmCustomer $customer, CustomerConversionService $conversionService)
    {
        $auditLogService = app(AuditLogService::class);
        $this->authorizeCustomer($customer, true);

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'destination' => ['nullable', 'string', 'max:255'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'stage' => ['required', 'in:' . implode(',', array_keys(CrmCustomer::stageOptions()))],
            'is_active' => ['nullable', 'boolean'],
            'appointment_at' => ['nullable', 'date'],
            'submission_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $beforeAudit = $this->customerAuditValues($customer->loadMissing(['assignedUser']));
        $original = $customer->replicate();

        $customer->fill($data + [
            'is_active' => $request->boolean('is_active', ! in_array($data['stage'], [CrmCustomer::STAGE_CLOSED, CrmCustomer::STAGE_CANCELLED], true)),
            'closed_at' => in_array($data['stage'], [CrmCustomer::STAGE_CLOSED, CrmCustomer::STAGE_CANCELLED], true)
                ? ($customer->closed_at ?: now())
                : null,
        ])->save();

        $this->logActivities($customer, $original, $request->user()->id);
        $conversionService->syncCustomerAccountLink($customer->fresh('inquiry.accountingAccount'));
        $afterAudit = $this->customerAuditValues($customer->fresh(['assignedUser']));
        $diff = $auditLogService->diff($beforeAudit, $afterAudit);
        $generalChangedFields = array_values(array_diff($diff['changed_fields'], ['stage', 'assigned_user_id']));

        if ($generalChangedFields !== []) {
            $auditLogService->log(
                $request->user(),
                'customers',
                'updated',
                $customer,
                [
                    'title' => __('admin.customer_updated_success'),
                    'description' => $customer->full_name,
                    'old_values' => array_intersect_key($diff['old_values'], array_flip($generalChangedFields)),
                    'new_values' => array_intersect_key($diff['new_values'], array_flip($generalChangedFields)),
                    'changed_fields' => $generalChangedFields,
                ]
            );
        }

        if (($beforeAudit['stage'] ?? null) !== ($afterAudit['stage'] ?? null)) {
            $auditLogService->log(
                $request->user(),
                'customers',
                'status_changed',
                $customer,
                [
                    'title' => __('admin.audit_action_status_changed'),
                    'description' => $customer->full_name,
                    'old_values' => ['stage' => $beforeAudit['stage'] ?? null],
                    'new_values' => ['stage' => $afterAudit['stage'] ?? null],
                    'changed_fields' => ['stage'],
                ]
            );
        }

        if (($beforeAudit['assigned_user_id'] ?? null) !== ($afterAudit['assigned_user_id'] ?? null)) {
            $auditLogService->log(
                $request->user(),
                'customers',
                'reassigned',
                $customer,
                [
                    'title' => __('admin.audit_action_reassigned'),
                    'description' => $customer->full_name,
                    'old_values' => ['assigned_user_id' => $beforeAudit['assigned_user_id'] ?? null],
                    'new_values' => ['assigned_user_id' => $afterAudit['assigned_user_id'] ?? null],
                    'changed_fields' => ['assigned_user_id'],
                ]
            );
        }

        if (($beforeAudit['stage'] ?? null) !== ($afterAudit['stage'] ?? null)) {
            app(WorkflowAutomationService::class)->dispatch(WorkflowAutomationService::TRIGGER_CUSTOMER_STATUS_CHANGED, $customer->fresh(['inquiry', 'assignedUser']), [
                'actor' => $request->user(),
                'new_stage' => $customer->stage,
            ]);
        }

        return redirect()
            ->route('admin.crm.customers.show', $customer)
            ->with('success', __('admin.customer_updated_success'));
    }

    protected function assignableUsers()
    {
        return User::query()->where('is_active', true)->orderBy('name')->get();
    }

    protected function availableLeads()
    {
        $query = Inquiry::query()
            ->select('id', 'full_name', 'phone', 'assigned_user_id')
            ->doesntHave('crmCustomer')
            ->latest();

        return CrmLeadAccess::applyVisibilityScope($query, auth()->user())
            ->limit(200)
            ->get();
    }

    protected function authorizeLeadContext(Inquiry $lead): void
    {
        abort_unless(CrmLeadAccess::canAccessLead(auth()->user(), $lead), 403);
    }

    protected function authorizeCustomer(CrmCustomer $customer, bool $manage = false): void
    {
        $viewer = auth()->user();
        $canAccess = CrmLeadAccess::canViewAll($viewer) || (int) $customer->assigned_user_id === (int) $viewer?->id;

        abort_unless($canAccess, 403);
        abort_if($manage && ! ($viewer?->hasPermission('customers.manage') ?? false), 403);
    }

    protected function logActivities(CrmCustomer $customer, CrmCustomer $original, int $userId): void
    {
        $fieldToAction = [
            'stage' => 'updated_stage',
            'assigned_user_id' => 'updated_assigned_user_id',
            'notes' => 'updated_notes',
        ];

        $profileFields = ['full_name', 'phone', 'whatsapp_number', 'email', 'nationality', 'country', 'destination', 'appointment_at', 'submission_at'];

        foreach ($fieldToAction as $field => $action) {
            if ($original->{$field} != $customer->{$field}) {
                $customer->activities()->create([
                    'user_id' => $userId,
                    'action_type' => $action,
                    'old_value' => (string) $original->{$field},
                    'new_value' => (string) $customer->{$field},
                ]);
            }
        }

        if (collect($profileFields)->contains(fn ($field) => $original->{$field} != $customer->{$field})) {
            $customer->activities()->create([
                'user_id' => $userId,
                'action_type' => 'updated_profile',
                'old_value' => null,
                'new_value' => $customer->full_name,
            ]);
        }
    }

    protected function customerAuditValues(CrmCustomer $customer): array
    {
        return [
            'full_name' => $customer->full_name,
            'phone' => $customer->phone,
            'whatsapp_number' => $customer->whatsapp_number,
            'email' => $customer->email,
            'assigned_user_id' => $customer->assignedUser?->name ?? $customer->assigned_user_id,
            'stage' => $customer->localizedStage(),
            'destination' => $customer->destination,
            'appointment_at' => optional($customer->appointment_at)->toDateTimeString(),
            'submission_at' => optional($customer->submission_at)->toDateTimeString(),
            'is_active' => $customer->is_active,
        ];
    }
}
