@extends('layouts.admin')

@section('page_title', __('admin.crm_customers'))
@section('page_description', __('admin.crm_customers_desc'))

@section('content')
<form class="card admin-card p-4 mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.search') }}</label>
            <input class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ __('admin.customer_search_placeholder') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.customer_stage') }}</label>
            <select class="form-select" name="stage">
                <option value="">{{ __('admin.all_types') }}</option>
                @foreach($stageOptions as $value => $label)
                    <option value="{{ $value }}" @selected(request('stage') === $value)>{{ $label[app()->getLocale() === 'ar' ? 'ar' : 'en'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.assigned_to') }}</label>
            <select class="form-select" name="assigned_user_id">
                <option value="">{{ __('admin.all_types') }}</option>
                @if($canViewAllCustomers)
                    <option value="unassigned" @selected(request('assigned_user_id') === 'unassigned')>{{ __('admin.crm_unassigned') }}</option>
                @endif
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected((string) request('assigned_user_id') === (string) $user->id)>{{ $user->name }}</option>
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
            <label class="form-label">{{ __('admin.crm_service_type') }}</label>
            <select class="form-select" name="crm_service_type_id">
                <option value="">{{ __('admin.all_types') }}</option>
                @foreach($serviceTypes as $serviceType)
                    <option value="{{ $serviceType->id }}" @selected((string) request('crm_service_type_id') === (string) $serviceType->id)>{{ $serviceType->localizedName() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1">
            <button class="btn btn-primary w-100">{{ __('admin.filter') }}</button>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.status') }}</label>
            <select class="form-select" name="active_state">
                <option value="">{{ __('admin.all_types') }}</option>
                <option value="active" @selected(request('active_state') === 'active')>{{ __('admin.active_customer') }}</option>
                <option value="inactive" @selected(request('active_state') === 'inactive')>{{ __('admin.inactive_customer') }}</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.payment_status') }}</label>
            <select class="form-select" name="payment_status">
                <option value="">{{ __('admin.all_types') }}</option>
                @foreach(['unpaid' => __('admin.accounting_unpaid'), 'partially_paid' => __('admin.accounting_partially_paid'), 'fully_paid' => __('admin.accounting_fully_paid')] as $value => $label)
                    <option value="{{ $value }}" @selected(request('payment_status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.from_date') }}</label>
            <input type="date" class="form-control" name="from" value="{{ request('from') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.to_date') }}</label>
            <input type="date" class="form-control" name="to" value="{{ request('to') }}">
        </div>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.total_customers') }}</div><div class="fs-4 fw-semibold">{{ $summary['total'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.active_customer') }}</div><div class="fs-4 fw-semibold text-primary">{{ $summary['active'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.closed_customers') }}</div><div class="fs-4 fw-semibold text-secondary">{{ $summary['closed'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.converted_this_month') }}</div><div class="fs-4 fw-semibold text-success">{{ $summary['converted_this_month'] }}</div></div></div>
</div>

<div class="card admin-card p-4">
    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
        <h2 class="h5 mb-0">{{ __('admin.crm_customers') }}</h2>
        @if(auth()->user()?->hasPermission('customers.manage'))
            <a href="{{ route('admin.crm.customers.create') }}" class="btn btn-primary btn-sm">{{ __('admin.convert_to_customer') }}</a>
        @endif
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>{{ __('admin.customer_code') }}</th>
                    <th>{{ __('admin.full_name') }}</th>
                    <th>{{ __('admin.customer_stage') }}</th>
                    <th>{{ __('admin.assigned_to') }}</th>
                    <th>{{ __('admin.source') }}</th>
                    <th>{{ __('admin.crm_service_type') }}</th>
                    <th>{{ __('admin.payment_status') }}</th>
                    <th>{{ __('admin.converted_at') }}</th>
                    <th class="text-end">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="fw-semibold">{{ $item->customer_code ?: '-' }}</td>
                        <td>
                            <div class="fw-semibold">{{ $item->full_name }}</div>
                            <div class="text-muted small">{{ $item->phone ?: ($item->email ?: '-') }}</div>
                        </td>
                        <td><span class="badge text-bg-{{ $item->stageBadgeClass() }}">{{ $item->localizedStage() }}</span></td>
                        <td>{{ $item->assignedUser?->name ?: __('admin.crm_unassigned') }}</td>
                        <td>{{ $item->crmSource?->localizedName() ?: '-' }}</td>
                        <td>{{ $item->crmServiceType?->localizedName() ?: '-' }}</td>
                        <td>
                            @if($item->accountingAccount)
                                <span class="badge text-bg-light">{{ $item->accountingAccount->payment_status }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ optional($item->converted_at)->format('Y-m-d H:i') ?: '-' }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.crm.customers.show', $item) }}" class="btn btn-sm btn-primary">{{ __('admin.view') }}</a>
                                @if(auth()->user()?->hasPermission('customers.manage'))
                                    <a href="{{ route('admin.crm.customers.edit', $item) }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.edit') }}</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-muted">{{ __('admin.no_data_available') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $items->links() }}
</div>
@endsection
