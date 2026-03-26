<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmLeadNote;
use App\Models\CrmFollowUp;
use App\Models\CrmLeadSource;
use App\Models\CrmServiceSubtype;
use App\Models\CrmServiceType;
use App\Models\CrmStatus;
use App\Models\CrmStatusUpdate;
use App\Models\CrmTask;
use App\Models\Inquiry;
use App\Models\User;
use App\Support\CrmLeadAccess;
use App\Support\CrmDelayedLeadService;
use App\Support\CrmReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrmController extends Controller
{
    public function dashboard(Request $request)
    {
        $leadQuery = CrmLeadAccess::applyVisibilityScope(Inquiry::query(), auth()->user());
        $statuses = $this->activeStatuses();
        $updatesByUser = $this->updatesByUser($request);
        $delayedLeadCount = app(CrmDelayedLeadService::class)
            ->applyDelayedScope(CrmLeadAccess::applyVisibilityScope(Inquiry::query(), auth()->user()))
            ->count();

        return view('admin.crm.dashboard', [
            'summary' => [
                'total' => (clone $leadQuery)->count(),
                'new' => $this->countBySlug('new-lead'),
                'no_answer' => $this->countBySlug('no-answer'),
                'closed' => $this->countBySlug('closed'),
                'duplicate' => $this->countBySlug('duplicate'),
                'due_today' => $this->visibleFollowUpsQuery()->whereDate('scheduled_at', today())->where('status', CrmFollowUp::STATUS_PENDING)->count(),
                'deleted' => CrmLeadAccess::applyVisibilityScope(Inquiry::onlyTrashed(), auth()->user())->count(),
                'overdue_followups' => $this->visibleFollowUpsQuery()->where('status', CrmFollowUp::STATUS_PENDING)->where('scheduled_at', '<', now())->count(),
                'delayed' => $delayedLeadCount,
            ],
            'statusCounts' => $statuses->map(fn (CrmStatus $status) => [
                'status' => $status,
                'count' => CrmLeadAccess::applyVisibilityScope(Inquiry::query(), auth()->user())->where('crm_status_id', $status->id)->count(),
            ])->filter(fn ($row) => $row['count'] > 0)->values(),
            'updatesByUser' => $updatesByUser,
            'latestLeads' => CrmLeadAccess::applyVisibilityScope(Inquiry::query()->with(['crmStatus', 'crmSource', 'assignedUser']), auth()->user())->latest()->limit(8)->get(),
            'latestNotes' => CrmLeadNote::query()->with(['inquiry', 'user'])->whereHas('inquiry', fn ($query) => CrmLeadAccess::applyVisibilityScope($query, auth()->user()))->latest()->limit(8)->get(),
            'canViewAllLeads' => CrmLeadAccess::canViewAll(auth()->user()),
        ]);
    }

    public function pipeline(Request $request)
    {
        $query = Inquiry::query()
            ->with(['crmStatus', 'crmSource', 'assignedUser'])
            ->latest('crm_status_updated_at');

        $query = CrmLeadAccess::applyVisibilityScope($query, auth()->user());

        if ($request->filled('q')) {
            $needle = '%' . trim((string) $request->query('q')) . '%';
            $query->where(function ($builder) use ($needle) {
                $builder->where('full_name', 'like', $needle)
                    ->orWhere('phone', 'like', $needle)
                    ->orWhere('whatsapp_number', 'like', $needle);
            });
        }

        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->date('created_from'));
        }

        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->date('created_to'));
        }

        if ($request->filled('crm_status_id')) {
            $query->where('crm_status_id', $request->integer('crm_status_id'));
        }

        if ($request->filled('assigned_user_id')) {
            if ($request->string('assigned_user_id')->toString() === 'unassigned') {
                $query->whereNull('assigned_user_id');
            } else {
                $query->where('assigned_user_id', $request->integer('assigned_user_id'));
            }
        }

        return view('admin.crm.pipeline', [
            'items' => $query->paginate(20)->withQueryString(),
            'statuses' => $this->activeStatuses(),
            'users' => $this->assignableUsers(),
            'canViewAllLeads' => CrmLeadAccess::canViewAll(auth()->user()),
        ]);
    }

    public function followUps(Request $request)
    {
        $query = $this->visibleFollowUpsQuery()
            ->with(['inquiry.crmStatus', 'assignedUser', 'creator', 'completedBy'])
            ->latest('scheduled_at');

        if ($request->string('range')->toString() === 'overdue') {
            $query->where('status', CrmFollowUp::STATUS_PENDING)->where('scheduled_at', '<', now());
        } elseif ($request->string('range')->toString() === 'today') {
            $query->whereDate('scheduled_at', today());
        } elseif ($request->string('range')->toString() === 'completed') {
            $query->where('status', CrmFollowUp::STATUS_COMPLETED);
        } elseif ($request->string('range')->toString() === 'upcoming') {
            $query->where('status', CrmFollowUp::STATUS_PENDING)->where('scheduled_at', '>', now());
        }

        if ($request->filled('assigned_user_id')) {
            if ($request->string('assigned_user_id')->toString() === 'unassigned') {
                $query->whereNull('assigned_user_id');
            } else {
                $query->where('assigned_user_id', $request->integer('assigned_user_id'));
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_at', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_at', '<=', $request->date('date_to'));
        }

        return view('admin.crm.follow-ups', [
            'items' => $query->paginate(20)->withQueryString(),
            'users' => $this->assignableUsers(),
            'canViewAllLeads' => CrmLeadAccess::canViewAll(auth()->user()),
        ]);
    }

    public function tasks(Request $request)
    {
        $query = CrmTask::query()
            ->with(['inquiry.crmStatus', 'assignedUser', 'creator'])
            ->whereHas('inquiry', fn ($builder) => CrmLeadAccess::applyVisibilityScope($builder, auth()->user()))
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return view('admin.crm.tasks', [
            'items' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function updateTask(Request $request, CrmTask $task)
    {
        $data = $request->validate([
            'status' => ['required', 'in:open,in_progress,completed,cancelled'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'due_at' => ['nullable', 'date'],
        ]);

        $task->update($data + [
            'completed_at' => $data['status'] === 'completed' ? now() : null,
        ]);

        return back()->with('success', __('admin.crm_task_updated'));
    }

    public function statuses()
    {
        $statuses = CrmStatus::query()->orderBy('sort_order')->get();

        return view('admin.crm.statuses', [
            'statuses' => $statuses,
            'statusMap' => $statuses->map(fn (CrmStatus $status) => [
                'status' => $status,
                'count' => CrmLeadAccess::applyVisibilityScope(Inquiry::query(), auth()->user())->where('crm_status_id', $status->id)->count(),
            ]),
        ]);
    }

    public function storeStatus(Request $request)
    {
        $data = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:crm_statuses,slug'],
            'color' => ['nullable', 'string', 'max:30'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        CrmStatus::query()->create([
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'],
            'slug' => $data['slug'] ?: Str::slug($data['name_en']),
            'status_group' => 'lead',
            'color' => $data['color'] ?? 'secondary',
            'sort_order' => $data['sort_order'] ?? 0,
            'is_system' => false,
            'is_active' => true,
        ]);

        return back()->with('success', __('admin.crm_status_saved'));
    }

    public function updateStatus(Request $request, CrmStatus $status)
    {
        $data = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:30'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $status->update($data + [
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $data['sort_order'] ?? $status->sort_order,
            'status_group' => 'lead',
        ]);

        return back()->with('success', __('admin.crm_status_updated'));
    }

    public function sources()
    {
        $sources = CrmLeadSource::query()->orderBy('sort_order')->get();

        return view('admin.crm.sources', [
            'sources' => $sources,
            'sourceMap' => $sources->map(fn (CrmLeadSource $source) => [
                'source' => $source,
                'count' => CrmLeadAccess::applyVisibilityScope(Inquiry::query(), auth()->user())->where('crm_source_id', $source->id)->count(),
            ]),
        ]);
    }

    public function storeSource(Request $request)
    {
        $data = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:crm_lead_sources,slug'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        CrmLeadSource::query()->create([
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'],
            'slug' => $data['slug'] ?: Str::slug($data['name_en']),
            'sort_order' => $data['sort_order'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', __('admin.crm_source_saved'));
    }

    public function updateSource(Request $request, CrmLeadSource $source)
    {
        $data = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $source->update($data + [
            'sort_order' => $data['sort_order'] ?? $source->sort_order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        Inquiry::query()
            ->where('crm_source_id', $source->id)
            ->update(['lead_source' => $source->name_en]);

        return back()->with('success', __('admin.crm_source_updated'));
    }

    public function serviceTypes()
    {
        $types = CrmServiceType::query()
            ->with(['subtypes' => fn ($query) => $query->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return view('admin.crm.service-types', [
            'types' => $types,
            'typeMap' => $types->map(fn (CrmServiceType $type) => [
                'type' => $type,
                'count' => CrmLeadAccess::applyVisibilityScope(Inquiry::query(), auth()->user())->where('crm_service_type_id', $type->id)->count(),
            ]),
        ]);
    }

    public function storeServiceType(Request $request)
    {
        $data = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:crm_service_types,slug'],
            'destination_label_en' => ['nullable', 'string', 'max:255'],
            'destination_label_ar' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        CrmServiceType::query()->create([
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'],
            'slug' => $data['slug'] ?: Str::slug($data['name_en']),
            'destination_label_en' => $data['destination_label_en'] ?? null,
            'destination_label_ar' => $data['destination_label_ar'] ?? null,
            'requires_subtype' => $request->boolean('requires_subtype'),
            'sort_order' => $data['sort_order'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'is_active' => true,
            'is_default' => false,
        ]);

        return back()->with('success', __('admin.crm_service_type_saved'));
    }

    public function updateServiceType(Request $request, CrmServiceType $serviceType)
    {
        $data = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'destination_label_en' => ['nullable', 'string', 'max:255'],
            'destination_label_ar' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $serviceType->update([
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'],
            'destination_label_en' => $data['destination_label_en'] ?? null,
            'destination_label_ar' => $data['destination_label_ar'] ?? null,
            'requires_subtype' => $request->boolean('requires_subtype'),
            'sort_order' => $data['sort_order'] ?? $serviceType->sort_order,
            'notes' => $data['notes'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', __('admin.crm_service_type_updated'));
    }

    public function destroyServiceType(CrmServiceType $serviceType)
    {
        if ($serviceType->leads()->exists()) {
            return back()->withErrors([
                'service_type_delete' => __('admin.crm_service_type_in_use'),
            ]);
        }

        if ($serviceType->subtypes()->exists()) {
            return back()->withErrors([
                'service_type_delete' => __('admin.crm_service_type_has_subtypes'),
            ]);
        }

        $serviceType->delete();

        return back()->with('success', __('admin.crm_service_type_deleted'));
    }

    public function storeServiceSubtype(Request $request)
    {
        $data = $request->validate([
            'crm_service_type_id' => ['required', 'exists:crm_service_types,id'],
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:crm_service_subtypes,slug'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        CrmServiceSubtype::query()->create([
            'crm_service_type_id' => $data['crm_service_type_id'],
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'],
            'slug' => $data['slug'] ?: Str::slug($data['name_en']),
            'sort_order' => $data['sort_order'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', __('admin.crm_service_subtype_saved'));
    }

    public function updateServiceSubtype(Request $request, CrmServiceSubtype $serviceSubtype)
    {
        $data = $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $serviceSubtype->update([
            'name_en' => $data['name_en'],
            'name_ar' => $data['name_ar'],
            'sort_order' => $data['sort_order'] ?? $serviceSubtype->sort_order,
            'notes' => $data['notes'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', __('admin.crm_service_subtype_updated'));
    }

    public function destroyServiceSubtype(CrmServiceSubtype $serviceSubtype)
    {
        if ($serviceSubtype->leads()->exists()) {
            return back()->withErrors([
                'service_subtype_delete' => __('admin.crm_service_subtype_in_use'),
            ]);
        }

        $serviceSubtype->delete();

        return back()->with('success', __('admin.crm_service_subtype_deleted'));
    }

    public function reports(Request $request, CrmReportService $reportService)
    {
        return view('admin.crm.reports', $reportService->build($request, auth()->user()) + [
            'statuses' => $this->activeStatuses(),
            'sources' => $this->activeSources(),
            'users' => $this->assignableUsers(),
            'canViewAllLeads' => CrmLeadAccess::canViewAll(auth()->user()),
        ]);
    }

    public function reports2(Request $request)
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'seller_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $statuses = $this->activeStatuses();
        $sellers = $this->reportSalesUsers();
        $selectedSellerId = isset($data['seller_id']) ? (int) $data['seller_id'] : null;

        if (! CrmLeadAccess::canViewAll(auth()->user())) {
            $selectedSellerId = auth()->id();
        }

        $query = CrmLeadAccess::applyVisibilityScope(Inquiry::query(), auth()->user());

        if (! empty($data['from'])) {
            $query->where('created_at', '>=', $request->date('from')->startOfDay());
        }

        if (! empty($data['to'])) {
            $query->where('created_at', '<=', $request->date('to')->endOfDay());
        }

        if ($selectedSellerId) {
            $query->where('assigned_user_id', $selectedSellerId);
        }

        $counts = (clone $query)
            ->select('crm_status_id', DB::raw('COUNT(*) as aggregate'))
            ->whereNotNull('crm_status_id')
            ->groupBy('crm_status_id')
            ->pluck('aggregate', 'crm_status_id');

        $rows = $statuses->map(fn (CrmStatus $status) => [
            'status' => $status,
            'count' => (int) ($counts[$status->id] ?? 0),
        ]);

        return view('admin.crm.reports2', [
            'rows' => $rows,
            'statuses' => $statuses,
            'sellers' => $sellers,
            'filters' => [
                'from' => $data['from'] ?? null,
                'to' => $data['to'] ?? null,
                'seller_id' => $selectedSellerId,
            ],
            'canViewAllLeads' => CrmLeadAccess::canViewAll(auth()->user()),
            'selectedSeller' => $selectedSellerId ? $sellers->firstWhere('id', $selectedSellerId) : null,
            'totalLeads' => $rows->sum('count'),
        ]);
    }

    protected function activeStatuses()
    {
        return CrmStatus::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    protected function activeSources()
    {
        return CrmLeadSource::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    protected function countBySlug(string $slug): int
    {
        $statusId = (int) CrmStatus::query()->where('slug', $slug)->value('id');

        return $statusId
            ? CrmLeadAccess::applyVisibilityScope(Inquiry::query(), auth()->user())->where('crm_status_id', $statusId)->count()
            : 0;
    }

    protected function conversionRate(): float
    {
        $total = CrmLeadAccess::applyVisibilityScope(Inquiry::query(), auth()->user())->count();
        if ($total === 0) {
            return 0;
        }

        return round(($this->countBySlug('documents-complete') / $total) * 100, 1);
    }

    protected function updatesByUser(Request $request)
    {
        $from = $request->filled('date_from') ? $request->date('date_from') : today();
        $to = $request->filled('date_to') ? $request->date('date_to') : today();

        $users = CrmLeadAccess::canViewAll(auth()->user())
            ? User::query()->orderBy('name')->get()
            : User::query()->whereKey(auth()->id())->get();

        return $users
            ->map(function (User $user) use ($from, $to) {
                $updates = CrmStatusUpdate::query()
                    ->where('changed_by', $user->id)
                    ->whereHas('inquiry', fn ($query) => CrmLeadAccess::applyVisibilityScope($query, auth()->user()))
                    ->whereDate('changed_at', '>=', $from)
                    ->whereDate('changed_at', '<=', $to)
                    ->count();

                return [
                    'user' => $user,
                    'status_updates' => $updates,
                ];
            })
            ->filter(fn (array $row) => $row['status_updates'] > 0)
            ->values();
    }

    protected function visibleFollowUpsQuery()
    {
        return CrmFollowUp::query()
            ->whereHas('inquiry', fn ($query) => CrmLeadAccess::applyVisibilityScope($query, auth()->user()));
    }

    protected function assignableUsers()
    {
        return User::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    protected function reportSalesUsers()
    {
        $users = $this->assignableUsers()->loadMissing('roles');

        $salesUsers = $users->filter(function (User $user) {
            return $user->roles->contains(fn ($role) => $role->slug === 'sales-leads-manager');
        })->values();

        if ($salesUsers->isEmpty()) {
            $salesUsers = $users->filter(fn (User $user) => $user->hasPermission('leads.edit'))->values();
        }

        if (! CrmLeadAccess::canViewAll(auth()->user())) {
            return $salesUsers->where('id', auth()->id())->values();
        }

        return $salesUsers;
    }
}
