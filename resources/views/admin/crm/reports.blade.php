@extends('layouts.admin')

@section('page_title', __('admin.crm_reports'))
@section('page_description', __('admin.crm_reports_desc'))

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card admin-card p-3"><div class="text-muted small">{{ __('admin.total_leads') }}</div><div class="h3 mb-0">{{ $summary['total'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3"><div class="text-muted small">{{ __('admin.crm_no_answer') }}</div><div class="h3 mb-0">{{ $summary['no_answer'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3"><div class="text-muted small">{{ __('admin.crm_complete_leads') }}</div><div class="h3 mb-0">{{ $summary['complete'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3"><div class="text-muted small">{{ __('admin.crm_duplicate_leads') }}</div><div class="h3 mb-0">{{ $summary['duplicate'] }}</div></div></div>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card admin-card p-3"><div class="text-muted small">{{ __('admin.crm_today_updates') }}</div><div class="h3 mb-0">{{ $summary['today_updates'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3"><div class="text-muted small">{{ __('admin.crm_yesterday_updates') }}</div><div class="h3 mb-0">{{ $summary['yesterday_updates'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3"><div class="text-muted small">{{ __('admin.crm_followups_due_today') }}</div><div class="h3 mb-0">{{ $summary['follow_ups_due'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3"><div class="text-muted small">{{ __('admin.conversion_rate') }}</div><div class="h3 mb-0">{{ $summary['conversion_rate'] }}%</div></div></div>
</div>
<div class="row g-4">
    <div class="col-lg-6"><div class="card admin-card p-4 h-100"><h2 class="h5 mb-3">{{ __('admin.status') }}</h2><table class="table mb-0"><tbody>@foreach($statusCounts as $row)<tr><td>{{ $row->label_localized }}</td><td class="text-end">{{ $row->total }}</td></tr>@endforeach</tbody></table></div></div>
    <div class="col-lg-6"><div class="card admin-card p-4 h-100"><h2 class="h5 mb-3">{{ __('admin.country') }}</h2><table class="table mb-0"><tbody>@foreach($countryCounts as $row)<tr><td>{{ $row->country }}</td><td class="text-end">{{ $row->total }}</td></tr>@endforeach</tbody></table></div></div>
    <div class="col-lg-6"><div class="card admin-card p-4 h-100"><h2 class="h5 mb-3">{{ __('admin.assigned_to') }}</h2><table class="table mb-0"><tbody>@foreach($userCounts as $row)<tr><td>{{ $row->name }}</td><td class="text-end">{{ $row->assigned_leads_count }}</td></tr>@endforeach</tbody></table></div></div>
    <div class="col-lg-6"><div class="card admin-card p-4 h-100"><h2 class="h5 mb-3">{{ __('admin.source') }}</h2><table class="table mb-0"><tbody>@foreach($sourceCounts as $row)<tr><td>{{ $row->localizedName() }}</td><td class="text-end">{{ $row->leads_count }}</td></tr>@endforeach</tbody></table></div></div>
    <div class="col-lg-12"><div class="card admin-card p-4 h-100"><h2 class="h5 mb-3">{{ __('admin.crm_updates_by_employee') }}</h2><table class="table mb-0"><thead><tr><th>{{ __('admin.name') }}</th><th>{{ __('admin.crm_status_updates') }}</th></tr></thead><tbody>@forelse($updatesByUser as $row)<tr><td>{{ $row['user']->name }}</td><td>{{ $row['status_updates'] }}</td></tr>@empty<tr><td colspan="2" class="text-muted">{{ __('admin.crm_no_updates_today') }}</td></tr>@endforelse</tbody></table></div></div>
</div>
@endsection
