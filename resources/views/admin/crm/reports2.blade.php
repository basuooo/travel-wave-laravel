@extends('layouts.admin')

@section('page_title', __('admin.crm_reports2'))
@section('page_description', __('admin.crm_reports2_desc'))

@section('content')
<div class="card admin-card p-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h2 class="h5 mb-1">{{ __('admin.crm_reports2') }}</h2>
            <p class="text-muted mb-0">{{ __('admin.crm_reports2_desc') }}</p>
        </div>
        <a href="{{ route('admin.crm.reports2') }}" class="btn btn-outline-secondary">{{ __('admin.reset_filters') }}</a>
    </div>

    <form method="GET" action="{{ route('admin.crm.reports2') }}" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.from') }}</label>
            <input type="date" name="from" value="{{ $filters['from'] }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.to') }}</label>
            <input type="date" name="to" value="{{ $filters['to'] }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('admin.crm_salesman') }}</label>
            <select name="seller_id" class="form-select" @disabled(! $canViewAllLeads)>
                <option value="">{{ __('admin.all') }}</option>
                @foreach($sellers as $seller)
                    <option value="{{ $seller->id }}" @selected((int) ($filters['seller_id'] ?? 0) === (int) $seller->id)>{{ $seller->name }}</option>
                @endforeach
            </select>
            @if(! $canViewAllLeads)
                <input type="hidden" name="seller_id" value="{{ $filters['seller_id'] }}">
            @endif
        </div>
        <div class="col-12 d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-primary">{{ __('admin.search') }}</button>
            <a href="{{ route('admin.crm.reports2') }}" class="btn btn-outline-secondary">{{ __('admin.reset_filters') }}</a>
        </div>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-4">
        <div class="card admin-card p-3 h-100">
            <div class="text-muted small">{{ __('admin.crm_reports2_total_leads') }}</div>
            <div class="h3 mb-0">{{ $totalLeads }}</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card admin-card p-3 h-100">
            <div class="text-muted small">{{ __('admin.crm_salesman') }}</div>
            <div class="h5 mb-0">{{ $selectedSeller?->name ?: __('admin.all') }}</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-4">
        <div class="card admin-card p-3 h-100">
            <div class="text-muted small">{{ __('admin.crm_reports2_statuses_count') }}</div>
            <div class="h3 mb-0">{{ $rows->count() }}</div>
        </div>
    </div>
</div>

<div class="card admin-card p-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>{{ __('admin.status') }}</th>
                    <th class="text-end">{{ __('admin.count') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        <td>
                            <span class="badge text-bg-{{ $row['status']->color ?: 'secondary' }}">
                                {{ $row['status']->localizedName() }}
                            </span>
                        </td>
                        <td class="text-end fw-semibold">{{ $row['count'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted py-4">{{ __('admin.crm_reports2_empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
