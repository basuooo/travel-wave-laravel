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
use App\Models\User;
use App\Support\CrmLeadAccess;
use App\Support\CrmLeadTransferService;
use App\Support\SimpleSpreadsheet;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CrmLeadController extends Controller
{
    protected const IMPORT_PREVIEW_SESSION_KEY = 'crm_leads_import_preview';
    protected const IMPORT_RESULT_SESSION_KEY = 'crm_leads_import_result';

    public function index(Request $request)
    {
        $viewer = auth()->user();

        return view('admin.crm.leads.index', [
            'items' => $this->filteredLeadsQuery($request)->paginate(20)->withQueryString(),
            'statuses' => $this->activeStatuses(),
            'sources' => $this->activeSources(),
            'serviceTypes' => $this->activeServiceTypes(),
            'users' => $this->assignableUsers(),
            'canViewAllLeads' => CrmLeadAccess::canViewAll($viewer),
        ]);
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
            'items' => $this->filteredLeadsQuery($request, true)->paginate(20)->withQueryString(),
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
            'crmStatusUpdates.oldStatus',
            'crmStatusUpdates.newStatus',
            'crmStatusUpdates.user',
            'marketingLandingPage',
        ]);

        return view('admin.crm.leads.show', [
            'lead' => $lead,
            'statuses' => $this->activeStatuses(),
            'sources' => $this->activeSources(),
            'serviceTypes' => $this->activeServiceTypes(),
            'users' => $this->assignableUsers(),
            'canViewAllLeads' => CrmLeadAccess::canViewAll(auth()->user()),
        ]);
    }

    public function update(Request $request, Inquiry $lead)
    {
        $this->authorizeLeadVisibility($lead);

        $data = $request->validate([
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
            'utm_source' => ['nullable', 'string', 'max:255'],
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
            'status_change_note' => ['nullable', 'string'],
            'scheduled_follow_up_date' => ['nullable', 'date'],
            'scheduled_follow_up_time' => ['nullable', 'date_format:H:i'],
            'follow_up_reminder_offset' => ['nullable', 'in:15,30,60,1440'],
            'follow_up_schedule_note' => ['nullable', 'string'],
        ]);

        $updates = $data;
        $updates['crm_status2_id'] = null;
        $updates['status_2_updated_at'] = null;
        $updates['status_2_updated_by'] = null;
        $updates['crm_service_subtype_id'] = $data['crm_service_subtype_id'] ?? null;

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

        if (! array_key_exists('net_price', $data) || $data['net_price'] === null) {
            $totalPrice = (float) ($data['total_price'] ?? $lead->total_price ?? 0);
            $expenses = (float) ($data['expenses'] ?? $lead->expenses ?? 0);
            $updates['net_price'] = $totalPrice - $expenses;
        }

        $now = now();
        $userId = auth()->id();
        $statusChanged = (int) ($lead->crm_status_id ?? 0) !== (int) ($data['crm_status_id'] ?? 0);
        $assignmentChanged = (int) ($lead->assigned_user_id ?? 0) !== (int) ($data['assigned_user_id'] ?? 0);
        if ($assignmentChanged && ! CrmLeadAccess::canViewAll(auth()->user())) {
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

        $lead->update($updates);

        if ($assignmentChanged) {
            $this->logAssignmentChange(
                $lead,
                $lead->getOriginal('assigned_user_id'),
                $lead->assigned_user_id,
                auth()->id(),
                $now,
                null
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
        $this->ensureDeletionPermission();
        $this->authorizeLeadVisibility($lead);

        $lead->deleted_by = auth()->id();
        $lead->save();
        $lead->delete();

        return redirect()->route('admin.crm.leads.index')->with('success', __('admin.crm_lead_trashed'));
    }

    public function restore(int $lead)
    {
        $this->ensureDeletionPermission();

        $item = Inquiry::withTrashed()->findOrFail($lead);
        $this->authorizeLeadVisibility($item);
        $item->restore();
        $item->forceFill(['deleted_by' => null])->save();

        return redirect()->route('admin.crm.leads.trash')->with('success', __('admin.crm_lead_restored'));
    }

    public function forceDestroy(int $lead)
    {
        $this->ensureDeletionPermission();

        $item = Inquiry::withTrashed()->findOrFail($lead);
        $this->authorizeLeadVisibility($item);
        abort_unless($item->trashed(), 404);
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
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'due_at' => ['nullable', 'date'],
        ]);

        $lead->crmTasks()->create($data + [
            'created_by' => auth()->id(),
            'status' => 'open',
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
            abort_unless(CrmLeadAccess::canViewAll(auth()->user()), 403);

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

        foreach ($visibleLeads as $lead) {
            if ($hasAssignmentChange) {
                $oldAssigned = $lead->assigned_user_id;

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
                    ->orWhere('lead_source', 'like', $needle);
            });
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

        return $query;
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

    protected function authorizeLeadTransfer(?User $user, bool $export = false): void
    {
        if ($export) {
            abort_unless($this->canExportLeads($user), 403);

            return;
        }

        abort_unless($user && CrmLeadAccess::canViewAll($user) && $user->hasPermission('leads.edit'), 403);
    }

    protected function canExportLeads(?User $user): bool
    {
        return (bool) ($user && CrmLeadAccess::canViewAll($user) && $user->hasPermission('leads.export'));
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
}
