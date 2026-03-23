@extends('layouts.admin')

@section('page_title', __('admin.crm_pipeline'))
@section('page_description', __('admin.crm_pipeline_desc'))

@section('content')
<form class="card admin-card p-4 mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-4"><label class="form-label">{{ __('admin.full_name') }}</label><input class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('admin.crm_search_by_name') }}"></div>
        <div class="col-md-3"><label class="form-label">{{ __('admin.status') }}</label><select class="form-select" name="crm_status_id"><option value="">{{ __('admin.all_types') }}</option>@foreach($statuses as $status)<option value="{{ $status->id }}" @selected((string) request('crm_status_id') === (string) $status->id)>{{ $status->localizedName() }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">{{ __('admin.assigned_to') }}</label><select class="form-select" name="assigned_user_id"><option value="">{{ __('admin.all_types') }}</option>@if($canViewAllLeads)<option value="unassigned" @selected(request('assigned_user_id') === 'unassigned')>{{ __('admin.crm_unassigned') }}</option>@endif @foreach($users as $user)<option value="{{ $user->id }}" @selected((string) request('assigned_user_id') === (string) $user->id)>{{ $user->name }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.created_date') }}</label><input type="date" class="form-control" name="created_from" value="{{ request('created_from') }}"></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.to_date') }}</label><input type="date" class="form-control" name="created_to" value="{{ request('created_to') }}"></div>
        <div class="col-md-1"><button class="btn btn-primary w-100">{{ __('admin.search') }}</button></div>
    </div>
</form>

<div class="card admin-card p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>{{ __('admin.full_name') }}</th><th>{{ __('admin.status') }}</th><th>{{ __('admin.source') }}</th><th>{{ __('admin.assigned_to') }}</th><th>{{ __('admin.created_date') }}</th><th class="text-end">{{ __('admin.actions') }}</th></tr></thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td><div class="fw-semibold">{{ $item->full_name }}</div><div class="text-muted small">{{ $item->phone ?: $item->whatsapp_number ?: '—' }}</div></td>
                        <td><span class="badge text-bg-primary">{{ $item->localizedStatus() }}</span></td>
                        <td>{{ $item->crmSource?->localizedName() ?: ($item->lead_source ?: '—') }}</td>
                        <td>{{ $item->assignedUser?->name ?: '—' }}</td>
                        <td>{{ optional($item->created_at)->format('Y-m-d') }}</td>
                        <td class="text-end"><a href="{{ route('admin.crm.leads.show', $item) }}" class="btn btn-sm btn-primary">{{ __('admin.view') }}</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-muted">{{ __('admin.no_search_results') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $items->links() }}
</div>
@endsection
