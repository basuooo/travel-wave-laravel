<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmStatus;
use App\Models\CrmStatusUpdate;
use App\Models\CrmFollowUp;
use App\Models\CrmLeadAssignment;
use App\Models\CrmTask;
use App\Models\CrmLeadSource;
use App\Models\CrmServiceSubtype;
use App\Models\CrmServiceType;
use App\Models\Inquiry;
use App\Models\UtmCampaign;
use App\Models\User;
use App\Support\AccountingCalculatorService;
use App\Support\AuditLogService;
use App\Support\AdminNotificationCenterService;
use App\Support\CrmLeadAccess;
use App\Support\CrmDelayedLeadService;
use App\Support\CrmLeadTransferService;
use App\Support\WorkflowAutomationService;
use App\Support\SimpleSpreadsheet;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmLeadController extends Controller
{
    protected const IMPORT_PREVIEW_SESSION_KEY = 'crm_leads_import_preview';
    protected const IMPORT_RESULT_SESSION_KEY = 'crm_leads_import_result';

    public function index(Request $request)
    {
        $viewer = auth()->user();

        return view('admin.crm.leads.index', [
            'items' => $this->paginateFilteredLeads($this->filteredLeadsQuery($request), $request)->withQueryString(),
            'statuses' => $this->activeStatuses(),
            'sources' => $this->activeSources(),
            'serviceTypes' => $this->activeServiceTypes(),
            'users' => $this->assignableUsers(),
            'canViewAllLeads' => CrmLeadAccess::canViewAll($viewer),
            'delayedLeadsCount' => $this->delayedLeadsCount(),
        ]);
    }

    public function delayed(Request $request, CrmDelayedLeadService $delayedLeadService)
    {
        $viewer = auth()->user();
        $query = Inquiry::query()
            ->with([
                'crmStatus',
                'crmSource',
                'assignedUser',
            ]);

        $query = CrmLeadAccess::applyVisibilityScope($query, $viewer);
        $query = $delayedLeadService->applyDelayedScope($query);

        if ($request->filled('q')) {
            $needle = '%' . trim((string) $request->query('q')) . '%';
            $query->where(function ($builder) use ($needle) {
                $builder->where('full_name', 'like', $needle)
                    ->orWhere('phone', 'like', $needle)
                    ->orWhere('whatsapp_number', 'like', $needle)
                    ->orWhere('email', 'like', $needle)
                    ->orWhere('country', 'like', $needle)
                    ->orWhere('destination', 'like', $needle);
            });
        }

        if ($request->filled('assigned_user_id')) {
            if ($request->string('assigned_user_id')->toString() === 'unassigned') {
                $query->whereNull('assigned_user_id');
            } else {
                $query->where('assigned_user_id', $request->integer('assigned_user_id'));
            }
        }

        if ($request->filled('crm_status_id')) {
            $query->where('crm_status_id', $request->integer('crm_status_id'));
        }

        $items = $this->paginateFilteredLeads($query, $request)->withQueryString();
        $items->setCollection($delayedLeadService->annotate($items->getCollection()));

        return view('admin.crm.leads.delayed', [
            'items' => $items,
            'statuses' => $this->activeStatuses(),
            'users' => $this->assignableUsers(),
            'canViewAllLeads' => CrmLeadAccess::canViewAll($viewer),
            'delayedLeadsCount' => $this->delayedLeadsCount(),
        ]);
    }

    protected function paginateFilteredLeads($query, Request $request)
    {
        $perPage = (string) $request->query('per_page', '20');
        $allowedPerPage = ['20', '50', '100', '500', '1000', 'all'];

        if (! in_array($perPage, $allowedPerPage, true)) {
            $perPage = '20';
        }

        if ($perPage === 'all') {
            return $query->paginate(max(1, (clone $query)->count()));
        }

        return $query->paginate((int) $perPage);
    }

    public function transfer(Request $request, CrmLeadTransferService $transferService)
    {
        $this->authorizeLeadTransfer($request->user(), export: false);

        return view('admin.crm.leads.transfer', [
            'statuses' => $this->activeStatuses(),
            'sources' => $this->activeSources(),
            'serviceTypes' => $this->activeServiceTypes(),
            'users' => $this->assignableUsers(),
            'preview' => session(self::IMPORT_PREVIEW_SESSION_KEY),
            'importResult' => session(self::IMPORT_RESULT_SESSION_KEY),
            'fieldOptions' => $transferService->exportFieldOptions(),
            'duplicateHandlingOptions' => $transferService->duplicateHandlingOptions(),
            'duplicateDetectorOptions' => $transferService->duplicateDetectorOptions(),
            'templateHeaders' => $transferService->templateHeaders('ar'),
            'sampleRows' => $transferService->sampleRows(),
            'currentFilters' => $request->only([
                'q', 'crm_status_id', 'crm_source_id', 'assigned_user_id',
                'crm_service_type_id', 'created_from', 'created_to', 'changed_from', 'changed_to',
            ]),
            'canExportLeads' => $this->canExportLeads($request->user()),
        ]);
    }

    public function create()
    {
        return view('admin.crm.leads.create', [
            'statuses' => $this->activeStatuses(),
            'defaultStatus' => $this->defaultCrmStatus(),
        ]);
    }

    public function store(Request $request)
    {
        $auditLogService = app(AuditLogService::class);
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'destination' => ['nullable', 'string', 'max:255'],
            'admin_notes' => ['nullable', 'string'],
            'additional_notes' => ['nullable', 'string'],
            'crm_status_id' => ['nullable', 'exists:crm_statuses,id'],
        ]);

        $this->guardManualLeadDuplicates($data);

        $status = ! empty($data['crm_status_id'])
            ? CrmStatus::query()->find($data['crm_status_id'])
            : $this->defaultCrmStatus();

        $manualSource = $this->ensureManualSource();
        $now = now();

        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'whatsapp_number' => $data['whatsapp_number'] ?? null,
            'email' => $data['email'] ?? null,
            'country' => $data['country'] ?? null,
            'destination' => $data['destination'] ?? ($data['country'] ?? null),
            'admin_notes' => $data['admin_notes'] ?? null,
            'additional_notes' => $data['additional_notes'] ?? null,
            'crm_status_id' => $status?->id,
            'status' => $status?->slug ?? 'new',
            'crm_source_id' => $manualSource?->id,
            'lead_source' => 'manual',
            'source_page' => 'admin-manual',
            'crm_status_updated_at' => $status ? $now : null,
            'crm_status_updated_by' => $status ? auth()->id() : null,
            'status_1_updated_at' => $status ? $now : null,
            'status_1_updated_by' => $status ? auth()->id() : null,
        ]);

        $auditLogService->log(
            $request->user(),
            'crm_leads',
            'created',
            $lead,
            [
                'title' => __('admin.crm_lead_created'),
                'description' => $lead->full_name,
                'new_values' => $this->leadAuditValues($lead),
                'changed_fields' => array_keys($this->leadAuditValues($lead)),
            ]
        );
        app(WorkflowAutomationService::class)->dispatch(WorkflowAutomationService::TRIGGER_LEAD_CREATED, $lead->fresh(['crmStatus', 'crmSource', 'assignedUser']), [
            'actor' => $request->user(),
        ]);

        return redirect()
            ->route('admin.crm.leads.index')
            ->with('success', __('admin.crm_lead_created'));
    }

    public function previewImport(Request $request, CrmLeadTransferService $transferService)
    {
        $this->authorizeLeadTransfer($request->user(), export: false);

        $data = $request->validate([
            'duplicate_mode' => ['required', 'in:none,skip,merge_existing'],
            'duplicate_detector' => ['nullable', 'in:full_name,phone,whatsapp_number'],
            'import_file' => ['nullable', 'file', 'mimes:csv,txt,xlsx,xls'],
            'google_sheet_url' => ['nullable', 'url'],
        ]);

        if (($data['duplicate_mode'] ?? 'none') !== 'none' && blank($data['duplicate_detector'] ?? null)) {
            return back()->withErrors([
                'duplicate_detector' => __('admin.crm_duplicate_detector_required'),
            ])->withInput();
        }

        if (! $request->hasFile('import_file') && blank($data['google_sheet_url'] ?? null)) {
            return back()->withErrors([
                'import_source' => __('admin.crm_import_source_required'),
            ]);
        }

        try {
            $preview = $request->hasFile('import_file')
                ? $transferService->previewFromUpload($request->file('import_file'), $data['duplicate_mode'], $data['duplicate_detector'] ?? null)
                : $transferService->previewFromGoogleSheet($data['google_sheet_url'], $data['duplicate_mode'], $data['duplicate_detector'] ?? null);
        } catch (\Throwable $exception) {
            return back()->withErrors([
                'import_file' => $exception->getMessage(),
            ])->withInput();
        }

        session([self::IMPORT_PREVIEW_SESSION_KEY => $preview]);
        session()->forget(self::IMPORT_RESULT_SESSION_KEY);

        return redirect()
            ->route('admin.crm.leads.transfer')
            ->with('success', __('admin.crm_import_preview_ready'));
    }

    public function import(Request $request, CrmLeadTransferService $transferService)
    {
        $this->authorizeLeadTransfer($request->user(), export: false);

        $preview = session(self::IMPORT_PREVIEW_SESSION_KEY);

        abort_if(empty($preview), 422, 'No import preview found.');

        $summary = $transferService->importPreview($preview, $request->user());
        $result = $transferService->buildImportResult($preview, $summary);

        session()->forget(self::IMPORT_PREVIEW_SESSION_KEY);
        session([self::IMPORT_RESULT_SESSION_KEY => $result]);

        return redirect()
            ->route('admin.crm.leads.transfer')
            ->with('success', __('admin.crm_import_completed', $summary));
    }

    public function downloadImportReport(
        Request $request,
        string $report,
        CrmLeadTransferService $transferService,
        SimpleSpreadsheet $spreadsheet
    ) {
        $this->authorizeLeadTransfer($request->user(), export: false);

        abort_unless(in_array($report, ['duplicates', 'invalid', 'merged'], true), 404);

        $preview = session(self::IMPORT_PREVIEW_SESSION_KEY);

        if (empty($preview)) {
            $result = session(self::IMPORT_RESULT_SESSION_KEY);
            abort_if(empty($result['preview'] ?? null), 404);
            $preview = $result['preview'];
        }

        $export = $transferService->buildIssueExport($preview, $report, app()->getLocale());
        abort_if(empty($export['rows']), 404);

        $format = $request->string('format')->toString() ?: 'xlsx';
        $filenamePrefix = $report === 'duplicates' ? 'crm-leads-duplicates' : 'crm-leads-invalid';

        if ($format === 'csv') {
            return $this->csvDownloadResponse($filenamePrefix . '.csv', $export['headers'], $export['rows']);
        }

        $binary = $spreadsheet->buildXlsx($export['headers'], $export['rows']);

        return response($binary, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filenamePrefix . '.xlsx"',
        ]);
    }

    public function downloadTemplate(Request $request, CrmLeadTransferService $transferService, SimpleSpreadsheet $spreadsheet)
    {
        $this->authorizeLeadTransfer($request->user(), export: false);

        $format = $request->string('format')->toString() ?: 'csv';
        $headers = $transferService->templateHeaders('ar');
        $rows = $transferService->sampleRows();

        if ($format === 'xlsx') {
            $binary = $spreadsheet->buildXlsx($headers, $rows);

            return response($binary, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="crm-leads-import-template.xlsx"',
            ]);
        }

        return $this->csvDownloadResponse('crm-leads-import-template.csv', $headers, $rows);
    }

    public function export(Request $request, CrmLeadTransferService $transferService, SimpleSpreadsheet $spreadsheet)
    {
        $this->authorizeLeadTransfer($request->user(), export: true);

        $data = $request->validate([
            'format' => ['required', 'in:csv,xlsx'],
            'fields' => ['nullable', 'array'],
            'fields.*' => ['string'],
            'lead_ids' => ['nullable', 'array'],
            'lead_ids.*' => ['integer', 'exists:inquiries,id'],
        ]);

        $query = $this->filteredLeadsQuery($request);

        if (! empty($data['lead_ids'])) {
            $query->whereIn('id', $data['lead_ids']);
        }

        $leads = $query->get();
        $export = $transferService->exportRows($leads, $data['fields'] ?? [], app()->getLocale());

        if ($data['format'] === 'xlsx') {
            $binary = $spreadsheet->buildXlsx($export['headers'], $export['rows']);

            return response($binary, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="crm-leads-export.xlsx"',
            ]);
        }

        return $this->csvDownloadResponse('crm-leads-export.csv', $export['headers'], $export['rows']);
    }

    public function trash(Request $request)
    {
        $viewer = auth()->user();

        return view('admin.crm.leads.trash', [
            'items' => $this->paginateFilteredLeads($this->filteredLeadsQuery($request, true), $request)->withQueryString(),
            'statuses' => $this->activeStatuses(),
            'sources' => $this->activeSources(),
            'serviceTypes' => $this->activeServiceTypes(),
            'users' => $this->assignableUsers(),
            'canViewAllLeads' => CrmLeadAccess::canViewAll($viewer),
        ]);
    }

    public function show(Inquiry $lead)
    {
        $this->authorizeLeadVisibility($lead);

        $lead->load([
            'crmStatus',
            'crmSource',
            'crmServiceType.subtypes',
            'crmServiceSubtype',
            'assignedUser',
            'crmAssignments.oldAssignedUser',
            'crmAssignments.newAssignedUser',
            'crmAssignments.changedByUser',
            'crmStatusUpdatedBy',
            'crmNotes.user',
            'crmTasks.assignedUser',
            'crmTasks.creator',
            'crmFollowUps.assignedUser',
            'crmFollowUps.creator',
            'crmFollowUps.completedBy',
            'documents.category',
            'documents.uploader',
            'crmStatusUpdates.oldStatus',
            'crmStatusUpdates.newStatus',
            'crmStatusUpdates.user',
            'marketingLandingPage',
            'utmCampaign.owner',
            'crmCustomer.assignedUser',
            'accountingAccount.payments.creator',
            'accountingAccount.expenses.category',
            'accountingAccount.expenses.subcategory',
        ]);

        return view('admin.crm.leads.show', [
            'lead' => $lead,
            'statuses' => $this->activeStatuses(),
            'sources' => $this->activeSources(),
            'serviceTypes' => $this->activeServiceTypes(),
            'users' => $this->assignableUsers(),
            'campaigns' => $this->activeMarketingCampaigns(),
            'canViewAllLeads' => CrmLeadAccess::canViewAll(auth()->user()),
            'canManageAccounting' => auth()->user()?->hasPermission('accounting.manage') ?? false,
        ]);
    }

    public function update(
        Request $request,
        Inquiry $lead,
        AdminNotificationCenterService $notificationCenterService,
        AccountingCalculatorService $accountingCalculatorService
    )
    {
        $auditLogService = app(AuditLogService::class);
        $this->authorizeLeadVisibility($lead);
        $beforeAudit = $this->leadAuditValues($lead->loadMissing(['crmStatus', 'crmSource', 'assignedUser']));
        $rules = [
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'crm_status_id' => ['nullable', 'exists:crm_statuses,id'],
            'crm_source_id' => ['nullable', 'exists:crm_lead_sources,id'],
            'crm_service_type_id' => ['nullable', 'exists:crm_service_types,id'],
            'crm_service_subtype_id' => ['nullable', 'exists:crm_service_subtypes,id'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'destination' => ['nullable', 'string', 'max:255'],
            'service_type' => ['nullable', 'string', 'max:255'],
            'service_country_name' => ['nullable', 'string', 'max:255'],
            'tourism_destination' => ['nullable', 'string', 'max:255'],
            'travel_destination' => ['nullable', 'string', 'max:255'],
            'hotel_destination' => ['nullable', 'string', 'max:255'],
            'travelers_count' => ['nullable', 'integer', 'min:1'],
            'country' => ['nullable', 'string', 'max:255'],
            'lead_source' => ['nullable', 'string', 'max:255'],
            'campaign_name' => ['nullable', 'string', 'max:255'],
            'utm_campaign_id' => ['nullable', 'exists:utm_campaigns,id'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
            'travel_date' => ['nullable', 'date'],
            'last_follow_up_at' => ['nullable', 'date'],
            'next_follow_up_at' => ['nullable', 'date'],
            'follow_up_result' => ['nullable', 'string'],
            'message' => ['nullable', 'string'],
            'admin_notes' => ['nullable', 'string'],
            'additional_notes' => ['nullable', 'string'],
            'total_price' => ['nullable', 'numeric', 'min:0'],
            'expenses' => ['nullable', 'numeric', 'min:0'],
            'net_price' => ['nullable', 'numeric'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'status_change_note' => ['nullable', 'string'],
            'scheduled_follow_up_date' => ['nullable', 'date'],
            'scheduled_follow_up_time' => ['nullable', 'date_format:H:i'],
            'follow_up_reminder_offset' => ['nullable', 'in:15,30,60,1440'],
            'follow_up_schedule_note' => ['nullable', 'string'],
        ];

        $data = $request->validate($rules);
        $canManageAccounting = $request->user()?->hasPermission('accounting.manage') ?? false;

        if (! $canManageAccounting) {
            unset($data['total_amount'], $data['paid_amount']);
        }

        $updates = $data;
        $updates['crm_status2_id'] = null;
        $updates['status_2_updated_at'] = null;
        $updates['status_2_updated_by'] = null;
        $updates['crm_service_subtype_id'] = $data['crm_service_subtype_id'] ?? null;

        if (! empty($data['utm_campaign_id'])) {
            $linkedCampaign = UtmCampaign::query()->find($data['utm_campaign_id']);

            if ($linkedCampaign) {
                $updates['utm_campaign_id'] = $linkedCampaign->id;
                $updates['campaign_name'] = $linkedCampaign->display_name;
                $updates['utm_source'] = $data['utm_source'] ?? $linkedCampaign->utm_source;
                $updates['utm_medium'] = $data['utm_medium'] ?? $linkedCampaign->utm_medium;
                $updates['utm_campaign'] = $data['utm_campaign'] ?? $linkedCampaign->utm_campaign;
            }
        }

        $selectedSource = ! empty($data['crm_source_id'])
            ? CrmLeadSource::query()->find($data['crm_source_id'])
            : null;

        $selectedServiceType = ! empty($data['crm_service_type_id'])
            ? CrmServiceType::query()->with('subtypes')->find($data['crm_service_type_id'])
            : null;

        $selectedServiceSubtype = ! empty($data['crm_service_subtype_id'])
            ? CrmServiceSubtype::query()->find($data['crm_service_subtype_id'])
            : null;

        if ($selectedServiceSubtype && (int) $selectedServiceSubtype->crm_service_type_id !== (int) $selectedServiceType?->id) {
            return redirect()->route('admin.crm.leads.show', $lead)->withErrors([
                'crm_service_subtype_id' => __('admin.crm_invalid_service_subtype'),
            ])->withInput();
        }

        $this->validateDynamicServiceFields($request, $selectedServiceType);

        $updates['lead_source'] = $selectedSource?->name_en
            ?? ($data['lead_source'] ?? $lead->lead_source);

        $updates = array_merge($updates, $this->normalizeServiceFields(
            $lead,
            $selectedServiceType,
            $selectedServiceSubtype,
            $data
        ));

        $now = now();
        $userId = auth()->id();
        $statusChanged = (int) ($lead->crm_status_id ?? 0) !== (int) ($data['crm_status_id'] ?? 0);
        $assignmentChanged = (int) ($lead->assigned_user_id ?? 0) !== (int) ($data['assigned_user_id'] ?? 0);
        if (
    $assignmentChanged
    && ! auth()->user()?->hasPermission('leads.change_assigned_to')
) {
    abort(403);
}
        $callLaterStatus = ! empty($data['crm_status_id'])
            ? CrmStatus::query()->find($data['crm_status_id'])
            : null;
        $requiresFollowUp = $callLaterStatus?->slug === 'call-later';

        if ($requiresFollowUp) {
            $request->validate([
                'scheduled_follow_up_date' => ['required', 'date'],
                'scheduled_follow_up_time' => ['required', 'date_format:H:i'],
                'follow_up_reminder_offset' => ['required', 'in:15,30,60,1440'],
            ]);
        }

        if ($statusChanged) {
            $updates['crm_status_updated_at'] = $now;
            $updates['crm_status_updated_by'] = $userId;
            $updates['status_1_updated_at'] = $now;
            $updates['status_1_updated_by'] = $userId;
            $this->logStatusUpdate(
                $lead,
                $lead->crm_status_id,
                $data['crm_status_id'] ?? null,
                $userId,
                $now,
                $data['status_change_note'] ?? null
            );
        }

        $status = ! empty($data['crm_status_id'])
            ? CrmStatus::query()->find($data['crm_status_id'])
            : null;

        $updates['status'] = $status?->slug ?? $lead->status;

        $originalAssignedUserId = (int) ($lead->assigned_user_id ?? 0);
        $originalPaidAmount = round((float) ($lead->paid_amount ?? 0), 2);
        $legacyPricing = $this->normalizeLegacyPricing($lead, $data);
        $accountingPayload = $this->normalizeAccountingSummaryPayload($lead, $data, $canManageAccounting);

        $lead = DB::transaction(function () use (
            $lead,
            $updates,
            $legacyPricing,
            $accountingPayload,
            $canManageAccounting,
            $accountingCalculatorService,
            $notificationCenterService,
            $request,
            $originalPaidAmount
        ) {
            $lead->update(array_merge($updates, $legacyPricing, $accountingPayload['lead_updates']));
            $lead->refresh();

            if (! $canManageAccounting || ! $accountingPayload['should_sync']) {
                return $lead;
            }

            $account = $accountingCalculatorService->syncLeadAccount($lead, auth()->id());
            $paymentDelta = round($accountingPayload['paid_amount'] - $originalPaidAmount, 2);

            if ($paymentDelta > 0) {
                $payment = $account->payments()->create([
                    'created_by' => auth()->id(),
                    'amount' => $paymentDelta,
                    'payment_date' => now()->toDateString(),
                    'payment_type' => 'payment',
                    'note' => __('admin.accounting_payment_synced_from_crm'),
                ]);

                $account = $accountingCalculatorService->syncLeadPaymentSummary($lead->fresh(), $accountingPayload['paid_amount'], auth()->id());
                $notificationCenterService->createAccountingPaymentNotification($account->fresh(['assignedUser', 'inquiry']), $payment->fresh(), $request->user());
            } elseif ($paymentDelta !== 0.0 || $accountingPayload['total_amount_changed']) {
                $accountingCalculatorService->syncLeadPaymentSummary($lead->fresh(), $accountingPayload['paid_amount'], auth()->id());
            }

            return $lead->fresh(['assignedUser', 'crmStatus']);
        });

        if ($assignmentChanged) {
            $this->logAssignmentChange(
                $lead,
                $originalAssignedUserId ?: null,
                $lead->assigned_user_id,
                auth()->id(),
                $now,
                null
            );
            $notificationCenterService->createLeadAssignedNotification(
                $lead->fresh(['assignedUser', 'crmStatus']),
                $originalAssignedUserId,
                $request->user()
            );
        }

        if ($requiresFollowUp) {
            $this->upsertCallLaterFollowUp($lead, $data);
        } elseif ($lead->crmFollowUps()->where('status', CrmFollowUp::STATUS_PENDING)->exists()) {
            $lead->crmFollowUps()
                ->where('status', CrmFollowUp::STATUS_PENDING)
                ->update([
                    'status' => CrmFollowUp::STATUS_CANCELLED,
                    'cancelled_at' => now(),
                ]);
        }

        $lead->loadMissing(['crmStatus', 'crmSource', 'assignedUser']);
        $afterAudit = $this->leadAuditValues($lead);
        $diff = $auditLogService->diff($beforeAudit, $afterAudit);
        $generalChangedFields = array_values(array_diff($diff['changed_fields'], ['crm_status_id', 'assigned_user_id']));

        if ($generalChangedFields !== []) {
            $auditLogService->log(
                $request->user(),
                'crm_leads',
                'updated',
                $lead,
                [
                    'title' => __('admin.crm_lead_updated'),
                    'description' => $lead->full_name,
                    'old_values' => array_intersect_key($diff['old_values'], array_flip($generalChangedFields)),
                    'new_values' => array_intersect_key($diff['new_values'], array_flip($generalChangedFields)),
                    'changed_fields' => $generalChangedFields,
                ]
            );
        }

        if ($statusChanged) {
            $auditLogService->log(
                $request->user(),
                'crm_leads',
                'status_changed',
                $lead,
                [
                    'title' => __('admin.audit_action_status_changed'),
                    'description' => $lead->full_name,
                    'old_values' => ['crm_status_id' => $beforeAudit['crm_status_id'] ?? null],
                    'new_values' => ['crm_status_id' => $afterAudit['crm_status_id'] ?? null],
                    'changed_fields' => ['crm_status_id'],
                ]
            );
        }

        if ($assignmentChanged) {
            $auditLogService->log(
                $request->user(),
                'crm_leads',
                $originalAssignedUserId ? 'reassigned' : 'assigned',
                $lead,
                [
                    'title' => __('admin.audit_action_reassigned'),
                    'description' => $lead->full_name,
                    'old_values' => ['assigned_user_id' => $beforeAudit['assigned_user_id'] ?? null],
                    'new_values' => ['assigned_user_id' => $afterAudit['assigned_user_id'] ?? null],
                    'changed_fields' => ['assigned_user_id'],
                ]
            );
        }

        if ($statusChanged) {
            app(WorkflowAutomationService::class)->dispatch(WorkflowAutomationService::TRIGGER_LEAD_STATUS_CHANGED, $lead->fresh(['crmStatus', 'crmSource', 'assignedUser']), [
                'actor' => $request->user(),
                'new_status_id' => $lead->crm_status_id,
            ]);
        }

        return redirect()
            ->route('admin.crm.leads.show', $lead)
            ->with('success', __('admin.crm_lead_updated'));
    }

    public function updateFollowUp(Request $request, CrmFollowUp $followUp)
    {
        $this->authorizeLeadVisibility($followUp->inquiry()->firstOrFail());

        $data = $request->validate([
            'action' => ['required', 'in:complete,reschedule,cancel,snooze'],
            'scheduled_at' => ['nullable', 'date'],
            'reminder_offset_minutes' => ['nullable', 'in:15,30,60,1440'],
            'snooze_minutes' => ['nullable', 'in:15,30,60'],
            'completion_note' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
        ]);

        if ($data['action'] === 'complete') {
            $followUp->update([
                'status' => CrmFollowUp::STATUS_COMPLETED,
                'completed_at' => now(),
                'completed_by' => auth()->id(),
                'completion_note' => $data['completion_note'] ?? null,
            ]);
        } elseif ($data['action'] === 'cancel') {
            $followUp->update([
                'status' => CrmFollowUp::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'note' => $data['note'] ?? $followUp->note,
            ]);
        } elseif ($data['action'] === 'snooze') {
            $snoozeMinutes = (int) ($data['snooze_minutes'] ?? 15);
            $followUp->update([
                'status' => CrmFollowUp::STATUS_PENDING,
                'remind_at' => now()->addMinutes($snoozeMinutes),
                'reminder_sent_at' => null,
                'cancelled_at' => null,
                'note' => $data['note'] ?? $followUp->note,
            ]);
        } else {
            $scheduledAt = $request->date('scheduled_at');
            $offset = (int) ($data['reminder_offset_minutes'] ?? $followUp->reminder_offset_minutes);
            $followUp->update([
                'status' => CrmFollowUp::STATUS_PENDING,
                'scheduled_at' => $scheduledAt,
                'reminder_offset_minutes' => $offset,
                'remind_at' => $scheduledAt?->copy()->subMinutes($offset),
                'reminder_sent_at' => null,
                'note' => $data['note'] ?? $followUp->note,
                'cancelled_at' => null,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'follow_up_status' => $followUp->fresh()->status,
            ]);
        }

        return redirect()
            ->route('admin.crm.leads.show', $followUp->inquiry_id)
            ->with('success', __('admin.crm_follow_up_updated'));
    }

    public function destroy(Inquiry $lead)
    {
        $auditLogService = app(AuditLogService::class);
        $this->ensureDeletionPermission();
        $this->authorizeLeadVisibility($lead);

        $auditLogService->log(
            auth()->user(),
            'crm_leads',
            'deleted',
            $lead,
            [
                'title' => __('admin.crm_lead_trashed'),
                'description' => $lead->full_name,
                'old_values' => $this->leadAuditValues($lead),
                'changed_fields' => ['deleted_at'],
            ]
        );

        $lead->deleted_by = auth()->id();
        $lead->save();
        $lead->delete();

        return redirect()->route('admin.crm.leads.index')->with('success', __('admin.crm_lead_trashed'));
    }

    public function restore(int $lead)
    {
        $auditLogService = app(AuditLogService::class);
        $this->ensureDeletionPermission();

        $item = Inquiry::withTrashed()->findOrFail($lead);
        $this->authorizeLeadVisibility($item);
        $item->restore();
        $item->forceFill(['deleted_by' => null])->save();

        $auditLogService->log(
            auth()->user(),
            'crm_leads',
            'restored',
            $item->fresh(),
            [
                'title' => __('admin.crm_lead_restored'),
                'description' => $item->full_name,
            ]
        );

        return redirect()->route('admin.crm.leads.trash')->with('success', __('admin.crm_lead_restored'));
    }

    public function forceDestroy(int $lead)
    {
        $auditLogService = app(AuditLogService::class);
        $this->ensureDeletionPermission();

        $item = Inquiry::withTrashed()->findOrFail($lead);
        $this->authorizeLeadVisibility($item);
        abort_unless($item->trashed(), 404);

        $auditLogService->log(
            auth()->user(),
            'crm_leads',
            'force_deleted',
            $item,
            [
                'title' => __('admin.crm_lead_deleted_permanently'),
                'description' => $item->full_name,
                'old_values' => $this->leadAuditValues($item),
            ]
        );
        $item->forceDelete();

        return redirect()->route('admin.crm.leads.trash')->with('success', __('admin.crm_lead_deleted_permanently'));
    }

    public function storeNote(Request $request, Inquiry $lead)
    {
        $this->authorizeLeadVisibility($lead);

        $data = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $lead->crmNotes()->create([
            'user_id' => auth()->id(),
            'body' => $data['body'],
        ]);

        return redirect()
            ->route('admin.crm.leads.show', $lead)
            ->with('success', __('admin.crm_note_added'));
    }

    public function storeTask(Request $request, Inquiry $lead)
    {
        $this->authorizeLeadVisibility($lead);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_user_id' => ['required', 'exists:users,id'],
            'task_type' => ['nullable', 'in:lead,general,team'],
            'category' => ['nullable', 'in:' . implode(',', array_keys(CrmTask::categoryOptions()))],
            'priority' => ['nullable', 'in:low,medium,high,urgent'],
            'status' => ['nullable', 'in:new,in_progress,waiting,completed,cancelled'],
            'due_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $task = $lead->crmTasks()->create($data + [
            'created_by' => auth()->id(),
            'task_type' => $data['task_type'] ?? CrmTask::TYPE_LEAD,
            'category' => $data['category'] ?? CrmTask::CATEGORY_CUSTOMER_FOLLOWUP,
            'priority' => $data['priority'] ?? CrmTask::PRIORITY_MEDIUM,
            'status' => $data['status'] ?? CrmTask::STATUS_NEW,
            'completed_at' => ($data['status'] ?? CrmTask::STATUS_NEW) === CrmTask::STATUS_COMPLETED ? now() : null,
            'last_activity_at' => now(),
        ]);

        $task->activities()->create([
            'user_id' => auth()->id(),
            'action_type' => 'created',
            'new_value' => $task->status,
            'note' => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.crm.leads.show', $lead)
            ->with('success', __('admin.crm_task_added'));
    }

    public function bulkUpdate(Request $request)
    {
        $data = $request->validate([
            'lead_ids' => ['required', 'array', 'min:1'],
            'lead_ids.*' => ['integer', 'exists:inquiries,id'],
            'action' => ['nullable', 'in:assign,status,trash'],
            'bulk_assigned_user_id' => ['nullable'],
            'bulk_status_id' => ['nullable', 'exists:crm_statuses,id'],
            'bulk_move_to_trash' => ['nullable', 'boolean'],
            'bulk_note' => ['nullable', 'string'],
        ]);

        $visibleLeads = $this->filteredLeadsQuery(new Request(), false)
            ->whereIn('id', $data['lead_ids'])
            ->get();

        abort_if($visibleLeads->isEmpty(), 403);

        $legacyAction = $data['action'] ?? null;
        $shouldTrash = $request->boolean('bulk_move_to_trash') || $legacyAction === 'trash';
        $hasStatusChange = ! blank($data['bulk_status_id'] ?? null) || $legacyAction === 'status';
        $hasAssignmentChange = $request->has('bulk_assigned_user_id') || $legacyAction === 'assign';

        if (! $shouldTrash && ! $hasStatusChange && ! $hasAssignmentChange) {
            return back()->withErrors([
                'bulk_action' => __('admin.crm_bulk_no_changes_selected'),
            ]);
        }

        if ($shouldTrash) {
            $this->ensureDeletionPermission();

            foreach ($visibleLeads as $lead) {
                $lead->update(['deleted_by' => auth()->id()]);
                $lead->delete();
            }

            return back()->with('success', __('admin.crm_bulk_leads_trashed'));
        }

        $newAssignedUserId = null;
        if ($hasAssignmentChange) {
            abort_unless(auth()->user()?->hasPermission('leads.change_assigned_to'), 403);

            $newAssignedUserId = $data['bulk_assigned_user_id'] === 'unassigned' || blank($data['bulk_assigned_user_id'])
                ? null
                : (int) $data['bulk_assigned_user_id'];

            if ($newAssignedUserId) {
                abort_unless(User::query()->whereKey($newAssignedUserId)->exists(), 422);
            }
        }

        $status = null;
        if ($hasStatusChange) {
            $status = CrmStatus::query()->findOrFail($data['bulk_status_id']);
        }

        if ($status && $status->slug === 'call-later') {
            return back()->withErrors([
                'bulk_status_id' => __('admin.crm_bulk_call_later_requires_individual_schedule'),
            ]);
        }

        $notificationCenterService = app(AdminNotificationCenterService::class);

        foreach ($visibleLeads as $lead) {
            $oldAssigned = $lead->assigned_user_id;

            if ($hasAssignmentChange) {
                if ((int) $oldAssigned !== (int) $newAssignedUserId) {
                    $lead->assigned_user_id = $newAssignedUserId;
                    $this->logAssignmentChange($lead, $oldAssigned, $newAssignedUserId, auth()->id(), now(), $data['bulk_note'] ?? null);
                }
            }

            if ($status && (int) $lead->crm_status_id !== (int) $status->id) {
                $oldStatusId = $lead->crm_status_id;
                $now = now();
                $lead->crm_status_id = $status->id;
                $lead->status = $status->slug;
                $lead->crm_status_updated_at = $now;
                $lead->crm_status_updated_by = auth()->id();
                $lead->status_1_updated_at = $now;
                $lead->status_1_updated_by = auth()->id();

                $this->logStatusUpdate($lead, $oldStatusId, $status->id, auth()->id(), $now, $data['bulk_note'] ?? null);

                $lead->crmFollowUps()
                    ->where('status', CrmFollowUp::STATUS_PENDING)
                    ->update([
                        'status' => CrmFollowUp::STATUS_CANCELLED,
                        'cancelled_at' => now(),
                    ]);
            }

            if ($lead->isDirty()) {
                $lead->save();

                if ($hasAssignmentChange && (int) $oldAssigned !== (int) $lead->assigned_user_id) {
                    $notificationCenterService->createLeadAssignedNotification(
                        $lead->fresh(['assignedUser', 'crmStatus']),
                        (int) $oldAssigned,
                        $request->user()
                    );
                }
            }
        }

        if ($hasAssignmentChange && $status) {
            return back()->with('success', __('admin.crm_bulk_assignment_and_status_updated'));
        }

        if ($hasAssignmentChange) {
            return back()->with('success', __('admin.crm_bulk_assignment_updated'));
        }

        return back()->with('success', __('admin.crm_bulk_status_updated'));
    }

    protected function filteredLeadsQuery(Request $request, bool $onlyTrashed = false)
    {
        $query = Inquiry::query()
            ->with([
                'crmStatus',
                'crmSource',
                'crmServiceType',
                'crmServiceSubtype',
                'assignedUser',
                'crmStatusUpdatedBy',
                'deletedBy',
                'utmCampaign',
            ])
            ->latest();

        $query = CrmLeadAccess::applyVisibilityScope($query, auth()->user());

        if ($onlyTrashed) {
            $query->onlyTrashed();
        }

        if ($request->filled('q')) {
            $needle = '%' . trim((string) $request->query('q')) . '%';
            $query->where(function ($builder) use ($needle) {
                $builder->where('full_name', 'like', $needle)
                    ->orWhere('phone', 'like', $needle)
                    ->orWhere('whatsapp_number', 'like', $needle)
                    ->orWhere('email', 'like', $needle)
                    ->orWhere('country', 'like', $needle)
                    ->orWhere('destination', 'like', $needle)
                    ->orWhere('service_type', 'like', $needle)
                    ->orWhere('service_country_name', 'like', $needle)
                    ->orWhere('tourism_destination', 'like', $needle)
                    ->orWhere('travel_destination', 'like', $needle)
                    ->orWhere('hotel_destination', 'like', $needle)
                    ->orWhere('lead_source', 'like', $needle)
                    ->orWhere('admin_notes', 'like', $needle)
                    ->orWhere('additional_notes', 'like', $needle);
            });
        }

        if ($request->filled('admin_notes')) {
            $query->where('admin_notes', 'like', '%' . trim((string) $request->query('admin_notes')) . '%');
        }

        if ($request->filled('additional_notes')) {
            $query->where('additional_notes', 'like', '%' . trim((string) $request->query('additional_notes')) . '%');
        }

        foreach (['crm_status_id', 'crm_source_id'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->integer($field));
            }
        }

        if ($request->filled('assigned_user_id')) {
            if ($request->string('assigned_user_id')->toString() === 'unassigned') {
                $query->whereNull('assigned_user_id');
            } else {
                $query->where('assigned_user_id', $request->integer('assigned_user_id'));
            }
        }

        if ($request->filled('crm_service_type_id')) {
            $query->where('crm_service_type_id', $request->integer('crm_service_type_id'));
        }

        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->date('created_from'));
        }

        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->date('created_to'));
        }

        if ($request->filled('changed_from')) {
            $query->whereDate('crm_status_updated_at', '>=', $request->date('changed_from'));
        }

        if ($request->filled('changed_to')) {
            $query->whereDate('crm_status_updated_at', '<=', $request->date('changed_to'));
        }

        if ($request->filled('updated_from')) {
            $query->whereDate('updated_at', '>=', $request->date('updated_from'));
        }

        if ($request->filled('updated_to')) {
            $query->whereDate('updated_at', '<=', $request->date('updated_to'));
        }

        return $query;
    }

    protected function activeStatuses()
    {
        return CrmStatus::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    protected function defaultCrmStatus(): ?CrmStatus
    {
        return CrmStatus::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderByRaw("case when slug = 'new-lead' then 0 else 1 end")
            ->orderBy('sort_order')
            ->first();
    }

    protected function activeSources()
    {
        return CrmLeadSource::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    protected function activeServiceTypes()
    {
        return CrmServiceType::query()
            ->where('is_active', true)
            ->with(['subtypes' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
    }

    protected function assignableUsers()
    {
        return User::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    protected function activeMarketingCampaigns()
    {
        return UtmCampaign::query()
            ->orderBy('display_name')
            ->get();
    }

    protected function delayedLeadsCount(): int
    {
        $service = app(CrmDelayedLeadService::class);

        return $service
            ->applyDelayedScope(CrmLeadAccess::applyVisibilityScope(Inquiry::query(), auth()->user()))
            ->count();
    }

    protected function logStatusUpdate(Inquiry $lead, $oldStatusId, $newStatusId, ?int $userId, $changedAt, ?string $note): void
    {
        CrmStatusUpdate::query()->create([
            'inquiry_id' => $lead->id,
            'status_level' => 'main',
            'old_status_id' => $oldStatusId,
            'new_status_id' => $newStatusId,
            'changed_by' => $userId,
            'changed_at' => $changedAt,
            'note' => $note,
        ]);
    }

    protected function ensureDeletionPermission(): void
    {
        abort_unless(auth()->user()?->hasPermission('leads.delete'), 403);
    }

    protected function authorizeLeadVisibility(Inquiry $lead): void
    {
        abort_unless(CrmLeadAccess::canAccessLead(auth()->user(), $lead), 403);
    }

    protected function upsertCallLaterFollowUp(Inquiry $lead, array $data): void
    {
        $scheduledAt = \Carbon\Carbon::parse($data['scheduled_follow_up_date'] . ' ' . $data['scheduled_follow_up_time']);
        $offset = (int) ($data['follow_up_reminder_offset'] ?? 30);
        $statusId = $data['crm_status_id'] ?? $lead->crm_status_id;

        $followUp = $lead->crmFollowUps()
            ->whereIn('status', [CrmFollowUp::STATUS_PENDING, 'due_soon', 'overdue'])
            ->latest('scheduled_at')
            ->first();

        $payload = [
            'crm_status_id' => $statusId,
            'assigned_user_id' => $data['assigned_user_id'] ?? $lead->assigned_user_id,
            'created_by' => $followUp?->created_by ?: auth()->id(),
            'status' => CrmFollowUp::STATUS_PENDING,
            'scheduled_at' => $scheduledAt,
            'reminder_offset_minutes' => $offset,
            'remind_at' => $scheduledAt->copy()->subMinutes($offset),
            'reminder_sent_at' => null,
            'note' => $data['follow_up_schedule_note'] ?? null,
            'cancelled_at' => null,
        ];

        if ($followUp) {
            $followUp->update($payload);
        } else {
            $lead->crmFollowUps()->create($payload);
        }
    }

    protected function logAssignmentChange(Inquiry $lead, $oldAssignedUserId, $newAssignedUserId, ?int $changedBy, $changedAt, ?string $note): void
    {
        CrmLeadAssignment::query()->create([
            'inquiry_id' => $lead->id,
            'old_assigned_user_id' => $oldAssignedUserId,
            'new_assigned_user_id' => $newAssignedUserId,
            'changed_by' => $changedBy,
            'changed_at' => $changedAt,
            'note' => $note,
        ]);
    }

    protected function validateDynamicServiceFields(Request $request, ?CrmServiceType $serviceType): void
    {
        if (! $serviceType) {
            return;
        }

        $rules = [];

        if ($serviceType->requires_subtype) {
            $rules['crm_service_subtype_id'] = ['required', 'exists:crm_service_subtypes,id'];
            $rules['service_country_name'] = ['required', 'string', 'max:255'];
        }

        if ($serviceType->slug === 'domestic-tourism') {
            $rules['tourism_destination'] = ['required', 'string', 'max:255'];
        }

        if ($serviceType->slug === 'flight-tickets') {
            $rules['travel_destination'] = ['required', 'string', 'max:255'];
        }

        if ($serviceType->slug === 'hotel-booking') {
            $rules['hotel_destination'] = ['required', 'string', 'max:255'];
        }

        if ($rules !== []) {
            $request->validate($rules);
        }
    }

    protected function normalizeServiceFields(
        Inquiry $lead,
        ?CrmServiceType $serviceType,
        ?CrmServiceSubtype $serviceSubtype,
        array $data
    ): array {
        $payload = [
            'crm_service_type_id' => $serviceType?->id,
            'crm_service_subtype_id' => $serviceSubtype?->id,
            'service_country_name' => null,
            'tourism_destination' => null,
            'travel_destination' => null,
            'hotel_destination' => null,
        ];

        if (! $serviceType) {
            $payload['service_type'] = $data['service_type'] ?? $lead->service_type;
            $payload['destination'] = $data['destination'] ?? $lead->destination;
            $payload['country'] = $data['country'] ?? $lead->country;

            return $payload;
        }

        $payload['service_type'] = $serviceType->localizedName('ar');

        if ($serviceType->slug === 'external-visas') {
            $payload['service_country_name'] = $data['service_country_name'] ?? null;
            $payload['country'] = $data['service_country_name'] ?? null;
            $payload['destination'] = $data['service_country_name'] ?? null;
            $payload['service_type'] = $serviceSubtype?->localizedName('ar') ?: $payload['service_type'];
        } elseif ($serviceType->slug === 'domestic-tourism') {
            $payload['tourism_destination'] = $data['tourism_destination'] ?? null;
            $payload['destination'] = $data['tourism_destination'] ?? null;
        } elseif ($serviceType->slug === 'flight-tickets') {
            $payload['travel_destination'] = $data['travel_destination'] ?? null;
            $payload['destination'] = $data['travel_destination'] ?? null;
        } elseif ($serviceType->slug === 'hotel-booking') {
            $payload['hotel_destination'] = $data['hotel_destination'] ?? null;
            $payload['destination'] = $data['hotel_destination'] ?? null;
        } else {
            $genericDestination = $data['destination'] ?? null;
            $payload['destination'] = $genericDestination;
        }

        return $payload;
    }

    protected function normalizeLegacyPricing(Inquiry $lead, array $data): array
    {
        $hasTotalPrice = array_key_exists('total_price', $data);
        $hasExpenses = array_key_exists('expenses', $data);
        $hasNetPrice = array_key_exists('net_price', $data);

        if (! $hasTotalPrice && ! $hasExpenses && ! $hasNetPrice) {
            return [];
        }

        $totalPrice = $hasTotalPrice
            ? ($data['total_price'] !== null ? round((float) $data['total_price'], 2) : null)
            : ($lead->total_price !== null ? round((float) $lead->total_price, 2) : null);

        $expenses = $hasExpenses
            ? ($data['expenses'] !== null ? round((float) $data['expenses'], 2) : null)
            : ($lead->expenses !== null ? round((float) $lead->expenses, 2) : null);

        if ($hasNetPrice && $data['net_price'] !== null) {
            $netPrice = round((float) $data['net_price'], 2);
        } elseif ($totalPrice !== null && $expenses !== null) {
            $netPrice = round($totalPrice - $expenses, 2);
        } else {
            $netPrice = $lead->net_price !== null ? round((float) $lead->net_price, 2) : null;
        }

        return [
            'total_price' => $totalPrice,
            'expenses' => $expenses,
            'net_price' => $netPrice,
        ];
    }

    protected function normalizeAccountingSummaryPayload(Inquiry $lead, array $data, bool $canManageAccounting): array
    {
        if (! $canManageAccounting) {
            return [
                'should_sync' => false,
                'paid_amount' => round((float) ($lead->paid_amount ?? 0), 2),
                'lead_updates' => [],
                'total_amount_changed' => false,
            ];
        }

        $hasTotalAmount = array_key_exists('total_amount', $data);
        $hasPaidAmount = array_key_exists('paid_amount', $data);

        if (! $hasTotalAmount && ! $hasPaidAmount) {
            return [
                'should_sync' => false,
                'paid_amount' => round((float) ($lead->paid_amount ?? 0), 2),
                'lead_updates' => [],
                'total_amount_changed' => false,
            ];
        }

        $totalAmount = $hasTotalAmount
            ? round((float) ($data['total_amount'] ?? 0), 2)
            : round((float) ($lead->total_amount ?? 0), 2);
        $paidAmount = $hasPaidAmount
            ? round((float) ($data['paid_amount'] ?? 0), 2)
            : round((float) ($lead->paid_amount ?? 0), 2);

        if ($paidAmount > $totalAmount) {
            throw ValidationException::withMessages([
                'paid_amount' => __('admin.accounting_paid_amount_exceeds_total'),
            ]);
        }

        return [
            'should_sync' => true,
            'paid_amount' => $paidAmount,
            'total_amount_changed' => $totalAmount !== round((float) ($lead->total_amount ?? 0), 2),
            'lead_updates' => [
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'remaining_amount' => max(0, round($totalAmount - $paidAmount, 2)),
                'payment_status' => $paidAmount <= 0
                    ? 'unpaid'
                    : ($paidAmount >= $totalAmount ? 'fully_paid' : 'partially_paid'),
            ],
        ];
    }

    protected function authorizeLeadTransfer(?User $user, bool $export = false): void
    {
        if ($export) {
            abort_unless($this->canExportLeads($user), 403);

            return;
        }

        abort_unless($user && ($user->hasPermission('leads.create') || $user->hasPermission('leads.export')), 403);
    }

    protected function canExportLeads(?User $user): bool
    {
        return (bool) ($user && $user->hasPermission('leads.export'));
    }

    protected function guardManualLeadDuplicates(array $data): void
    {
        if (! blank($data['phone'] ?? null)) {
            $duplicate = Inquiry::query()->where('phone', trim((string) $data['phone']))->first();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'phone' => __('admin.crm_duplicate_phone_exists'),
                ]);
            }
        }

        if (! blank($data['whatsapp_number'] ?? null)) {
            $duplicate = Inquiry::query()->where('whatsapp_number', trim((string) $data['whatsapp_number']))->first();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'whatsapp_number' => __('admin.crm_duplicate_whatsapp_exists'),
                ]);
            }
        }

        if (blank($data['phone'] ?? null) && blank($data['whatsapp_number'] ?? null) && ! blank($data['full_name'] ?? null)) {
            $duplicate = Inquiry::query()->where('full_name', trim((string) $data['full_name']))->first();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'full_name' => __('admin.crm_duplicate_name_exists'),
                ]);
            }
        }
    }

    protected function ensureManualSource(): ?CrmLeadSource
    {
        return CrmLeadSource::query()->firstOrCreate(
            ['slug' => 'manual'],
            [
                'name_en' => 'Manual',
                'name_ar' => 'يدوي',
                'is_default' => false,
                'is_active' => true,
                'sort_order' => 999,
            ]
        );
    }

    protected function csvDownloadResponse(string $filename, array $headers, array $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function leadAuditValues(Inquiry $lead): array
    {
        return [
            'full_name' => $lead->full_name,
            'phone' => $lead->phone,
            'whatsapp_number' => $lead->whatsapp_number,
            'email' => $lead->email,
            'crm_status_id' => $lead->crmStatus?->localizedName() ?? $lead->crm_status_id,
            'crm_source_id' => $lead->crmSource?->localizedName() ?? $lead->crm_source_id,
            'assigned_user_id' => $lead->assignedUser?->name ?? $lead->assigned_user_id,
            'priority' => $lead->priority,
            'destination' => $lead->destination,
            'travel_date' => optional($lead->travel_date)?->toDateString(),
            'next_follow_up_at' => optional($lead->next_follow_up_at)?->toDateTimeString(),
            'follow_up_result' => $lead->follow_up_result,
            'total_amount' => $lead->total_amount,
            'paid_amount' => $lead->paid_amount,
            'remaining_amount' => $lead->remaining_amount,
            'payment_status' => $lead->localizedPaymentStatus(),
        ];
    }
}