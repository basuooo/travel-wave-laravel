@extends('layouts.admin')

@section('page_title', __('admin.crm_delayed_leads'))
@section('page_description', __('admin.crm_delayed_leads_desc'))

@section('content')
<form class="card admin-card p-4 mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.search') }}</label>
            <input class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('admin.crm_search_placeholder') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.status') }}</label>
            <select class="form-select" name="crm_status_id">
                <option value="">{{ __('admin.all_types') }}</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}" @selected((string) request('crm_status_id') === (string) $status->id)>{{ $status->localizedName() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.assigned_to') }}</label>
            <select class="form-select" name="assigned_user_id">
                <option value="">{{ __('admin.all_types') }}</option>
                @if($canViewAllLeads)
                    <option value="unassigned" @selected(request('assigned_user_id') === 'unassigned')>{{ __('admin.crm_unassigned') }}</option>
                @endif
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected((string) request('assigned_user_id') === (string) $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary flex-grow-1">{{ __('admin.search') }}</button>
            <a href="{{ route('admin.crm.leads.index') }}" class="btn btn-outline-secondary">{{ __('admin.crm_leads') }}</a>
        </div>
    </div>
</form>

<div class="card admin-card p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h2 class="h5 mb-1">{{ __('admin.crm_delayed_leads') }}</h2>
            <p class="text-muted mb-0">{{ __('admin.crm_delayed_leads_hint', ['count' => $delayedLeadsCount ?? 0]) }}</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>{{ __('admin.full_name') }}</th>
                    <th>{{ __('admin.phone') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th>{{ __('admin.assigned_to') }}</th>
                    <th>{{ __('admin.crm_scheduled_date') }}</th>
                    <th>{{ __('admin.crm_last_action_date') }}</th>
                    <th>{{ __('admin.crm_delay_reason') }}</th>
                    <th class="text-end">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $item->full_name }}</div>
                            <div class="text-muted small">{{ $item->email ?: ($item->country ?: '-') }}</div>
                        </td>
                        <td>{{ $item->phone ?: ($item->whatsapp_number ?: '-') }}</td>
                        <td><span class="badge text-bg-primary">{{ $item->localizedStatus() }}</span></td>
                        <td>{{ $item->assignedUser?->name ?: __('admin.crm_unassigned') }}</td>
                        <td>{{ $item->delay_reference_at ? \Carbon\Carbon::parse($item->delay_reference_at)->format('Y-m-d H:i') : '-' }}</td>
                        <td>{{ $item->delay_last_action_at ? \Carbon\Carbon::parse($item->delay_last_action_at)->format('Y-m-d H:i') : '-' }}</td>
                        <td>
                            <span class="badge text-bg-danger">{{ $item->delay_reason ?: __('admin.crm_delayed_lead') }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.crm.leads.show', $item) }}" class="btn btn-sm btn-primary">{{ __('admin.view') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-muted">{{ __('admin.crm_no_delayed_leads') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $items->links() }}
</div>
@endsection
