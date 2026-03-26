@extends('layouts.admin')

@section('page_title', $customer->full_name)
@section('page_description', __('admin.customer_details_desc'))

@section('content')
@php($lead = $customer->inquiry)
@php($customerTasks = $lead?->crmTasks ?? collect())
@php($openTasks = $customerTasks->whereNotIn('status', [\App\Models\CrmTask::STATUS_COMPLETED, \App\Models\CrmTask::STATUS_CANCELLED]))
@php($delayedTasks = $customerTasks->filter(fn ($task) => $task->isDelayed()))
@php($completedTasks = $customerTasks->where('status', \App\Models\CrmTask::STATUS_COMPLETED))

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card admin-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <div class="text-muted small">{{ __('admin.customer_code') }}</div>
                    <h2 class="h4 mb-1">{{ $customer->customer_code ?: '-' }}</h2>
                    <div class="text-muted">{{ $customer->full_name }}</div>
                </div>
                <span class="badge text-bg-{{ $customer->stageBadgeClass() }}">{{ $customer->localizedStage() }}</span>
            </div>
            <dl class="row mb-0">
                <dt class="col-sm-4">{{ __('admin.full_name') }}</dt><dd class="col-sm-8">{{ $customer->full_name ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.phone') }}</dt><dd class="col-sm-8">{{ $customer->phone ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.whatsapp_number') }}</dt><dd class="col-sm-8">{{ $customer->whatsapp_number ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.email') }}</dt><dd class="col-sm-8">{{ $customer->email ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.nationality') }}</dt><dd class="col-sm-8">{{ $customer->nationality ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.country') }}</dt><dd class="col-sm-8">{{ $customer->country ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.destination') }}</dt><dd class="col-sm-8">{{ $customer->destination ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.crm_service_type') }}</dt><dd class="col-sm-8">{{ $customer->crmServiceType?->localizedName() ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.crm_service_subtype') }}</dt><dd class="col-sm-8">{{ $customer->crmServiceSubtype?->localizedName() ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.assigned_to') }}</dt><dd class="col-sm-8">{{ $customer->assignedUser?->name ?: __('admin.crm_unassigned') }}</dd>
                <dt class="col-sm-4">{{ __('admin.source') }}</dt><dd class="col-sm-8">{{ $customer->crmSource?->localizedName() ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.converted_at') }}</dt><dd class="col-sm-8">{{ optional($customer->converted_at)->format('Y-m-d H:i') ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.appointment_date') }}</dt><dd class="col-sm-8">{{ optional($customer->appointment_at)->format('Y-m-d H:i') ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.submission_date') }}</dt><dd class="col-sm-8">{{ optional($customer->submission_at)->format('Y-m-d H:i') ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.notes') }}</dt><dd class="col-sm-8">{{ $customer->notes ?: '-' }}</dd>
            </dl>
        </div>

        <div class="card admin-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                <h2 class="h5 mb-0">{{ __('admin.source_lead') }}</h2>
                @if($lead)
                    <a href="{{ route('admin.crm.leads.show', $lead) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a>
                @endif
            </div>
            @if($lead)
                <div class="row g-3">
                    <div class="col-md-6"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.full_name') }}</div><div class="fw-semibold">{{ $lead->full_name }}</div></div></div>
                    <div class="col-md-6"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.status') }}</div><div class="fw-semibold">{{ $lead->localizedStatus() }}</div></div></div>
                    <div class="col-md-6"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.phone') }}</div><div class="fw-semibold">{{ $lead->phone ?: '-' }}</div></div></div>
                    <div class="col-md-6"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.created_date') }}</div><div class="fw-semibold">{{ optional($lead->created_at)->format('Y-m-d H:i') ?: '-' }}</div></div></div>
                    <div class="col-md-6"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.marketing_campaign') }}</div><div class="fw-semibold">{{ $lead->utmCampaign?->display_name ?: ($lead->campaign_name ?: '-') }}</div></div></div>
                </div>
            @else
                <div class="text-muted">{{ __('admin.no_data_available') }}</div>
            @endif
        </div>

        <div class="card admin-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                <h2 class="h5 mb-0">{{ __('admin.documents') }}</h2>
                @if(auth()->user()?->hasPermission('documents.manage'))
                    <a href="{{ route('admin.documents.create', ['documentable_type' => 'customer', 'documentable_id' => $customer->id]) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.upload_document') }}</a>
                @endif
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.total_documents') }}</div><div class="fs-4 fw-semibold">{{ $customer->documents->count() }}</div></div></div>
                <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.documents_latest_upload') }}</div><div class="fw-semibold">{{ optional($customer->documents->first()?->uploaded_at)->format('Y-m-d H:i') ?: '-' }}</div></div></div>
                <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.document_categories') }}</div><div class="fw-semibold">{{ $customer->documents->pluck('crm_document_category_id')->filter()->unique()->count() }}</div></div></div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.document_title') }}</th><th>{{ __('admin.document_category') }}</th><th>{{ __('admin.uploaded_by') }}</th><th>{{ __('admin.uploaded_at') }}</th><th class="text-end">{{ __('admin.actions') }}</th></tr></thead>
                    <tbody>
                    @forelse($customer->documents as $document)
                        <tr>
                            <td><div class="fw-semibold">{{ $document->title }}</div><div class="small text-muted">{{ $document->original_file_name }}</div></td>
                            <td>{{ $document->category?->localizedName() ?: '-' }}</td>
                            <td>{{ $document->uploader?->name ?: '-' }}</td>
                            <td>{{ optional($document->uploaded_at)->format('Y-m-d H:i') ?: '-' }}</td>
                            <td class="text-end"><div class="d-inline-flex gap-2"><a href="{{ route('admin.documents.show', $document) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a><a href="{{ route('admin.documents.download', $document) }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.download') }}</a></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">{{ __('admin.no_documents') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card admin-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                <h2 class="h5 mb-0">{{ __('admin.accounting_customer_accounts') }}</h2>
                @if($customer->accountingAccount)
                    <a href="{{ route('admin.accounting.customers.show', $customer->accountingAccount) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a>
                @endif
            </div>
            @if($customer->accountingAccount)
                <div class="row g-3">
                    <div class="col-md-3"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.accounting_total_amount') }}</div><div class="fw-semibold">{{ number_format((float) $customer->accountingAccount->total_amount, 2) }}</div></div></div>
                    <div class="col-md-3"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.accounting_paid_amount') }}</div><div class="fw-semibold text-success">{{ number_format((float) $customer->accountingAccount->paid_amount, 2) }}</div></div></div>
                    <div class="col-md-3"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.accounting_remaining_amount') }}</div><div class="fw-semibold text-danger">{{ number_format((float) $customer->accountingAccount->remaining_amount, 2) }}</div></div></div>
                    <div class="col-md-3"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.payment_status') }}</div><div class="fw-semibold">{{ $customer->accountingAccount->payment_status }}</div></div></div>
                </div>
            @else
                <div class="text-muted">{{ __('admin.no_data_available') }}</div>
            @endif
        </div>

        <div class="card admin-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                <h2 class="h5 mb-0">{{ __('admin.crm_tasks') }}</h2>
                @if($lead)
                    <a href="{{ route('admin.crm.tasks.create', ['inquiry_id' => $lead->id]) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.crm_task_create') }}</a>
                @endif
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.open_tasks') }}</div><div class="fs-4 fw-semibold">{{ $openTasks->count() }}</div></div></div>
                <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.crm_delayed_tasks') }}</div><div class="fs-4 fw-semibold text-danger">{{ $delayedTasks->count() }}</div></div></div>
                <div class="col-md-4"><div class="border rounded-4 p-3 h-100"><div class="small text-muted">{{ __('admin.completed_tasks') }}</div><div class="fs-4 fw-semibold text-success">{{ $completedTasks->count() }}</div></div></div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.title') }}</th><th>{{ __('admin.priority') }}</th><th>{{ __('admin.status') }}</th><th>{{ __('admin.assigned_to') }}</th><th>{{ __('admin.crm_task_due_date') }}</th><th class="text-end">{{ __('admin.actions') }}</th></tr></thead>
                    <tbody>
                    @forelse($customerTasks as $task)
                        <tr>
                            <td><div class="fw-semibold">{{ $task->title }}</div><div class="text-muted small">{{ $task->description }}</div></td>
                            <td><span class="badge" style="{{ $task->priorityBadgeStyle() }}">{{ $task->localizedPriority() }}</span></td>
                            <td><span class="badge text-bg-{{ $task->visualStatus() }}">{{ $task->localizedStatus() }}</span></td>
                            <td>{{ $task->assignedUser?->name ?: '-' }}</td>
                            <td>{{ optional($task->due_at)->format('Y-m-d H:i') ?: '-' }}</td>
                            <td class="text-end"><a href="{{ route('admin.crm.tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted">{{ __('admin.crm_no_tasks') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.customer_activity') }}</h2>
            <div class="d-grid gap-3">
                @forelse($customer->activities as $activity)
                    <div class="border rounded-4 p-3">
                        <div class="d-flex justify-content-between gap-3 mb-2">
                            <strong>{{ $activity->user?->name ?: 'System' }}</strong>
                            <span class="text-muted small">{{ optional($activity->created_at)->format('Y-m-d H:i') }}</span>
                        </div>
                        <div class="fw-semibold">{{ $activity->localizedAction() }}</div>
                        @if($activity->old_value || $activity->new_value)
                            <div class="small text-muted mt-2">{{ $activity->old_value ?: '-' }} -> {{ $activity->new_value ?: '-' }}</div>
                        @endif
                        @if($activity->note)
                            <div class="small text-muted mt-2">{{ $activity->note }}</div>
                        @endif
                    </div>
                @empty
                    <div class="text-muted">{{ __('admin.no_data_available') }}</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">{{ __('admin.quick_actions') }}</h2>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.crm.customers.edit', $customer) }}" class="btn btn-primary">{{ __('admin.edit') }}</a>
                @if($lead)
                    <a href="{{ route('admin.crm.leads.show', $lead) }}" class="btn btn-outline-secondary">{{ __('admin.source_lead') }}</a>
                    <a href="{{ route('admin.crm.tasks.create', ['inquiry_id' => $lead->id]) }}" class="btn btn-outline-secondary">{{ __('admin.crm_task_create') }}</a>
                @endif
                @if($customer->accountingAccount)
                    <a href="{{ route('admin.accounting.customers.show', $customer->accountingAccount) }}" class="btn btn-outline-secondary">{{ __('admin.accounting_customer_accounts') }}</a>
                @endif
            </div>
        </div>

        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.customer_case_summary') }}</h2>
            <div class="d-grid gap-3">
                <div class="border rounded-4 p-3">
                    <div class="small text-muted">{{ __('admin.status') }}</div>
                    <div class="fw-semibold">{{ $customer->localizedStage() }}</div>
                </div>
                <div class="border rounded-4 p-3">
                    <div class="small text-muted">{{ __('admin.assigned_to') }}</div>
                    <div class="fw-semibold">{{ $customer->assignedUser?->name ?: __('admin.crm_unassigned') }}</div>
                </div>
                <div class="border rounded-4 p-3">
                    <div class="small text-muted">{{ __('admin.converted_at') }}</div>
                    <div class="fw-semibold">{{ optional($customer->converted_at)->format('Y-m-d H:i') ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
