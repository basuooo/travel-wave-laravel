@extends('layouts.admin')

@section('page_title', __('admin.crm'))
@section('page_description', __('admin.crm_desc'))

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-2"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.total_leads') }}</div><div class="h3 mb-0">{{ $summary['total'] }}</div></div></div>
    <div class="col-md-2"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_new_leads') }}</div><div class="h3 mb-0">{{ $summary['new'] }}</div></div></div>
    <div class="col-md-2"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_no_answer') }}</div><div class="h3 mb-0">{{ $summary['no_answer'] }}</div></div></div>
    <div class="col-md-2"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_closed_leads') }}</div><div class="h3 mb-0">{{ $summary['closed'] }}</div></div></div>
    <div class="col-md-2"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_duplicate_leads') }}</div><div class="h3 mb-0">{{ $summary['duplicate'] }}</div></div></div>
    <div class="col-md-2"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.crm_followups_due_today') }}</div><div class="h3 mb-0">{{ $summary['due_today'] }}</div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card admin-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">{{ __('admin.crm_status_overview') }}</h2>
                <a href="{{ route('admin.crm.pipeline') }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.view') }}</a>
            </div>
            <div class="row g-3">
                @forelse($statusCounts as $row)
                    <div class="col-md-4">
                        <div class="border rounded-4 p-3 h-100 bg-light-subtle">
                            <div class="fw-semibold mb-1">{{ $row['status']->localizedName() }}</div>
                            <div class="text-muted small">{{ $row['status']->slug }}</div>
                            <div class="display-6 mt-3 mb-0">{{ $row['count'] }}</div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-muted">{{ __('admin.no_search_results') }}</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card admin-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">{{ __('admin.latest_submissions') }}</h2>
                <a href="{{ route('admin.crm.leads.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.view') }}</a>
            </div>
            <div class="d-grid gap-3">
                @foreach($latestLeads as $lead)
                    <a href="{{ route('admin.crm.leads.show', $lead) }}" class="text-decoration-none text-reset border rounded-4 p-3">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <div class="fw-semibold">{{ $lead->full_name ?: '—' }}</div>
                                <div class="text-muted small">{{ $lead->phone ?: $lead->whatsapp_number ?: '—' }}</div>
                            </div>
                            <span class="badge text-bg-light">{{ $lead->localizedStatus() }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-6">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.crm_updates_by_employee') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.name') }}</th><th>{{ __('admin.crm_status_updates') }}</th></tr></thead>
                    <tbody>
                        @forelse($updatesByUser as $row)
                            <tr>
                                <td>{{ $row['user']->name }}</td>
                                <td>{{ $row['status_updates'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">{{ __('admin.crm_no_updates_today') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.crm_recent_notes') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.name') }}</th><th>{{ __('admin.comment') }}</th><th>{{ __('admin.created_date') }}</th></tr></thead>
                    <tbody>
                        @forelse($latestNotes as $note)
                            <tr>
                                <td><a href="{{ route('admin.crm.leads.show', $note->inquiry) }}">{{ $note->inquiry?->full_name }}</a></td>
                                <td>{{ \Illuminate\Support\Str::limit($note->body, 100) }}</td>
                                <td>{{ optional($note->created_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted">{{ __('admin.crm_no_notes') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card admin-card p-4 mt-4">
    <h2 class="h5 mb-3">{{ __('admin.notifications') }}</h2>
    <div class="d-grid gap-3">
        @forelse(($adminNotifications ?? collect()) as $notification)
            @php($payload = $notification->data ?? [])
            <a href="{{ $payload['url'] ?? '#' }}" class="text-decoration-none text-reset border rounded-4 p-3">
                <div class="d-flex justify-content-between gap-3">
                    <div>
                        <div class="fw-semibold">{{ app()->getLocale() === 'ar' ? ($payload['title_ar'] ?? ($payload['title_en'] ?? 'Notification')) : ($payload['title_en'] ?? ($payload['title_ar'] ?? 'Notification')) }}</div>
                        <div class="text-muted small">{{ $payload['lead_name'] ?? '' }}</div>
                    </div>
                    <div class="text-muted small">{{ optional($notification->created_at)->format('Y-m-d H:i') }}</div>
                </div>
            </a>
        @empty
            <div class="text-muted">{{ __('admin.no_notifications') }}</div>
        @endforelse
    </div>
</div>

@if(auth()->user()?->hasPermission('leads.delete'))
    <div class="card admin-card p-4 mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h5 mb-1">{{ __('admin.crm_deleted_leads') }}</h2>
                <div class="text-muted small">{{ __('admin.crm_deleted_leads_desc') }}</div>
            </div>
            <a href="{{ route('admin.crm.leads.trash') }}" class="btn btn-outline-secondary">{{ __('admin.view') }}</a>
        </div>
    </div>
@endif
@endsection
