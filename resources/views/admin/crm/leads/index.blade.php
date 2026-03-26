@extends('layouts.admin')

@section('page_title', __('admin.crm_leads'))
@section('page_description', __('admin.crm_leads_desc'))

@section('content')
<form class="card admin-card p-4 mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.search') }}</label>
            <input class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('admin.crm_search_placeholder') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.status') }}</label>
            <select class="form-select" name="crm_status_id">
                <option value="">{{ __('admin.all_types') }}</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}" @selected((string) request('crm_status_id') === (string) $status->id)>{{ $status->localizedName() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.source') }}</label>
            <select class="form-select" name="crm_source_id">
                <option value="">{{ __('admin.all_types') }}</option>
                @foreach($sources as $source)
                    <option value="{{ $source->id }}" @selected((string) request('crm_source_id') === (string) $source->id)>{{ $source->localizedName() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
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
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.crm_service_type') }}</label>
            <select class="form-select" name="crm_service_type_id">
                <option value="">{{ __('admin.all_types') }}</option>
                @foreach($serviceTypes as $serviceType)
                    <option value="{{ $serviceType->id }}" @selected((string) request('crm_service_type_id') === (string) $serviceType->id)>{{ $serviceType->localizedName() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1">
            <button class="btn btn-primary w-100">{{ __('admin.search') }}</button>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.created_date') }}</label>
            <input type="date" class="form-control" name="created_from" value="{{ request('created_from') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.to_date') }}</label>
            <input type="date" class="form-control" name="created_to" value="{{ request('created_to') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.crm_last_status_change') }}</label>
            <input type="date" class="form-control" name="changed_from" value="{{ request('changed_from') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.to_date') }}</label>
            <input type="date" class="form-control" name="changed_to" value="{{ request('changed_to') }}">
        </div>
    </div>
 </form>

<div class="card admin-card p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <h2 class="h5 mb-0">{{ __('admin.crm_leads') }}</h2>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.crm.leads.delayed') }}" class="btn btn-outline-danger btn-sm">
                {{ __('admin.crm_delayed_leads') }}
                @if(($delayedLeadsCount ?? 0) > 0)
                    <span class="badge text-bg-danger ms-1">{{ $delayedLeadsCount }}</span>
                @endif
            </a>
            @if(auth()->user()?->hasPermission('leads.edit') && $canViewAllLeads)
                <a href="{{ route('admin.crm.leads.create') }}" class="btn btn-primary btn-sm">{{ __('admin.crm_add_lead') }}</a>
                <a href="{{ route('admin.crm.leads.transfer', request()->query()) }}" class="btn btn-outline-secondary btn-sm">{{ __('admin.crm_import_export') }}</a>
            @endif
            @if(auth()->user()?->hasPermission('leads.export') && $canViewAllLeads)
                <a href="{{ route('admin.crm.leads.transfer', request()->query()) }}" class="btn btn-outline-secondary btn-sm">{{ __('admin.crm_export') }}</a>
            @endif
            @if(auth()->user()?->hasPermission('leads.edit'))
                <select class="form-select form-select-sm" name="bulk_status_id" form="crm-bulk-action-form" style="min-width: 180px;">
                    <option value="">{{ __('admin.crm_bulk_change_status') }}</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->localizedName() }}</option>
                    @endforeach
                </select>
                @if($canViewAllLeads)
                    <select class="form-select form-select-sm" name="bulk_assigned_user_id" form="crm-bulk-action-form" style="min-width: 180px;">
                        <option value="">{{ __('admin.crm_bulk_assign_to_seller') }}</option>
                        <option value="unassigned">{{ __('admin.crm_unassigned') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                @endif
                @if(auth()->user()?->hasPermission('leads.delete'))
                    <div class="form-check d-flex align-items-center px-2">
                        <input class="form-check-input" type="checkbox" id="bulk-move-to-trash" name="bulk_move_to_trash" value="1" form="crm-bulk-action-form">
                        <label class="form-check-label ms-2" for="bulk-move-to-trash">{{ __('admin.crm_bulk_move_to_trash') }}</label>
                    </div>
                @endif
                <input class="form-control form-control-sm" name="bulk_note" form="crm-bulk-action-form" placeholder="{{ __('admin.notes') }}" style="min-width: 220px;">
                <button class="btn btn-sm btn-primary" type="submit" form="crm-bulk-action-form">{{ __('admin.apply') }}</button>
            @endif
            @if(auth()->user()?->hasPermission('leads.delete'))
                <a href="{{ route('admin.crm.leads.trash') }}" class="btn btn-outline-secondary btn-sm">{{ __('admin.crm_deleted_leads') }}</a>
            @endif
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th style="width: 44px;">
                        <input type="checkbox" class="form-check-input" data-select-all>
                    </th>
                    <th>{{ __('admin.full_name') }}</th>
                    <th>{{ __('admin.phone') }}</th>
                    <th>{{ __('admin.whatsapp_number') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th>{{ __('admin.crm_service_type') }}</th>
                    <th>{{ __('admin.destination') }}</th>
                    <th>{{ __('admin.source') }}</th>
                    <th>{{ __('admin.assigned_to') }}</th>
                    <th>{{ __('admin.created_date') }}</th>
                    <th>{{ __('admin.crm_last_status_change') }}</th>
                    <th class="text-end">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td><input type="checkbox" class="form-check-input" name="lead_ids[]" value="{{ $item->id }}" form="crm-bulk-action-form" data-lead-checkbox></td>
                        <td>
                            <div class="fw-semibold">{{ $item->full_name }}</div>
                            <div class="text-muted small">{{ $item->email ?: ($item->country ?: '-') }}</div>
                        </td>
                        <td>{{ $item->phone ?: '-' }}</td>
                        <td>
                            @php($crmWhatsappUrl = $item->whatsappChatUrl(auth()->user()?->name))
                            @if($crmWhatsappUrl)
                                <a href="{{ $crmWhatsappUrl }}" target="_blank" rel="noopener noreferrer" class="admin-table-link">{{ $item->whatsapp_number }}</a>
                            @else
                                {{ $item->whatsapp_number ?: '-' }}
                            @endif
                        </td>
                        <td><span class="badge text-bg-primary">{{ $item->localizedStatus() }}</span></td>
                        <td>{{ $item->localizedServiceType() ?: '-' }}</td>
                        <td>{{ $item->serviceDestinationValue() ?: '-' }}</td>
                        <td>{{ $item->crmSource?->localizedName() ?: ($item->lead_source ?: '-') }}</td>
                        <td>
                            @if($item->assignedUser)
                                {{ $item->assignedUser->name }}
                            @else
                                <span class="text-muted">{{ __('admin.crm_unassigned') }}</span>
                            @endif
                        </td>
                        <td>{{ optional($item->created_at)->format('Y-m-d') }}</td>
                        <td>{{ optional($item->statusChangedAt())->format('Y-m-d H:i') ?: '-' }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.crm.leads.show', $item) }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.edit') }}</a>
                                <a href="{{ route('admin.crm.leads.show', $item) }}" class="btn btn-sm btn-primary">{{ __('admin.view') }}</a>
                                @if(auth()->user()?->hasPermission('leads.delete'))
                                    <form method="post" action="{{ route('admin.crm.leads.destroy', $item) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">{{ __('admin.delete') }}</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="12" class="text-muted">{{ __('admin.no_search_results') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $items->links() }}
</div>

<form id="crm-bulk-action-form" method="post" action="{{ route('admin.crm.leads.bulk-update') }}" class="d-none">
    @csrf
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const bulkForm = document.getElementById('crm-bulk-action-form');
    const selectAll = document.querySelector('[data-select-all]');
    const checkboxes = Array.from(document.querySelectorAll('[data-lead-checkbox]'));
    if (!bulkForm || !selectAll || checkboxes.length === 0) {
        return;
    }

    selectAll.addEventListener('change', function () {
        checkboxes.forEach((checkbox) => {
            checkbox.checked = selectAll.checked;
        });
    });

    bulkForm.addEventListener('submit', function (event) {
        const hasSelection = checkboxes.some((checkbox) => checkbox.checked);

        if (!hasSelection) {
            event.preventDefault();
            alert('{{ __('admin.no_search_results') }}');
        }
    });
});
</script>
@endsection
