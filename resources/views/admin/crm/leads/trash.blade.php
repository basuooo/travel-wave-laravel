@extends('layouts.admin')

@section('page_title', __('admin.crm_deleted_leads'))
@section('page_description', __('admin.crm_deleted_leads_desc'))

@section('content')
<form class="card admin-card p-4 mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-3"><label class="form-label">{{ __('admin.search') }}</label><input class="form-control" name="q" value="{{ request('q') }}"></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.status') }}</label><select class="form-select" name="crm_status_id"><option value="">{{ __('admin.all_types') }}</option>@foreach($statuses as $status)<option value="{{ $status->id }}" @selected((string) request('crm_status_id') === (string) $status->id)>{{ $status->localizedName() }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.source') }}</label><select class="form-select" name="crm_source_id"><option value="">{{ __('admin.all_types') }}</option>@foreach($sources as $source)<option value="{{ $source->id }}" @selected((string) request('crm_source_id') === (string) $source->id)>{{ $source->localizedName() }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label">{{ __('admin.assigned_to') }}</label><select class="form-select" name="assigned_user_id"><option value="">{{ __('admin.all_types') }}</option>@if($canViewAllLeads)<option value="unassigned" @selected(request('assigned_user_id') === 'unassigned')>{{ __('admin.crm_unassigned') }}</option>@endif @foreach($users as $user)<option value="{{ $user->id }}" @selected((string) request('assigned_user_id') === (string) $user->id)>{{ $user->name }}</option>@endforeach</select></div>
        <div class="col-md-1"><button class="btn btn-primary w-100">{{ __('admin.search') }}</button></div>
    </div>
</form>

<div class="card admin-card p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>{{ __('admin.full_name') }}</th><th>{{ __('admin.status') }}</th><th>{{ __('admin.deleted_at') }}</th><th>{{ __('admin.deleted_by') }}</th><th class="text-end">{{ __('admin.actions') }}</th></tr></thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td><div class="fw-semibold">{{ $item->full_name }}</div><div class="text-muted small">{{ $item->phone ?: '—' }}</div></td>
                        <td>{{ $item->localizedStatus() }}</td>
                        <td>{{ optional($item->deleted_at)->format('Y-m-d H:i') ?: '—' }}</td>
                        <td>{{ $item->deletedBy?->name ?: '—' }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <form method="post" action="{{ route('admin.crm.leads.restore', $item->id) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-secondary">{{ __('admin.restore') }}</button>
                                </form>
                                <form method="post" action="{{ route('admin.crm.leads.force-destroy', $item->id) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">{{ __('admin.delete_permanently') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted">{{ __('admin.no_search_results') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $items->links() }}
</div>
@endsection
