@extends('layouts.admin')

@section('page_title', __('admin.marketing_campaigns'))
@section('page_description', __('admin.marketing_campaigns_desc'))

@section('content')
<form class="card admin-card p-4 mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.search') }}</label>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="{{ __('admin.marketing_campaign_search_placeholder') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.platform') }}</label>
            <select name="platform" class="form-select">
                <option value="">{{ __('admin.all') }}</option>
                @foreach($platforms as $platform)
                    <option value="{{ $platform }}" @selected(($filters['platform'] ?? null) === $platform)>{{ $platform }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.medium') }}</label>
            <select name="medium" class="form-select">
                <option value="">{{ __('admin.all') }}</option>
                @foreach($media as $medium)
                    <option value="{{ $medium }}" @selected(($filters['medium'] ?? null) === $medium)>{{ $medium }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.campaign_type') }}</label>
            <select name="campaign_type" class="form-select">
                <option value="">{{ __('admin.all') }}</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" @selected(($filters['campaign_type'] ?? null) === $type)>{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.owner') }}</label>
            <select name="owner_user_id" class="form-select">
                <option value="">{{ __('admin.all') }}</option>
                @foreach($owners as $owner)
                    <option value="{{ $owner->id }}" @selected((int) ($filters['owner_user_id'] ?? 0) === (int) $owner->id)>{{ $owner->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.status') }}</label>
            <select name="status" class="form-select">
                <option value="">{{ __('admin.all') }}</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? null) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.from_date') }}</label>
            <input type="date" name="from_date" value="{{ $filters['from_date'] ?? '' }}" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">{{ __('admin.to_date') }}</label>
            <input type="date" name="to_date" value="{{ $filters['to_date'] ?? '' }}" class="form-control">
        </div>
        <div class="col-md-6 d-flex gap-2">
            <button class="btn btn-primary">{{ __('admin.search') }}</button>
            <a href="{{ route('admin.marketing-campaigns.index') }}" class="btn btn-outline-secondary">{{ __('admin.reset_filters') }}</a>
            <a href="{{ route('admin.marketing-campaigns.create') }}" class="btn btn-outline-primary">{{ __('admin.add_marketing_campaign') }}</a>
        </div>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.total_campaigns') }}</div><div class="fs-4 fw-semibold">{{ $summary['total_campaigns'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.active_campaigns') }}</div><div class="fs-4 fw-semibold text-success">{{ $summary['active_campaigns'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.marketing_campaigns_with_leads') }}</div><div class="fs-4 fw-semibold">{{ $summary['campaigns_with_leads'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.marketing_top_campaign') }}</div><div class="fw-semibold">{{ $summary['top_campaign']?->display_name ?: '-' }}</div></div></div>
</div>

<div class="card admin-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>{{ __('admin.campaign_name') }}</th>
                    <th>{{ __('admin.platform') }}</th>
                    <th>{{ __('admin.medium') }}</th>
                    <th>{{ __('admin.campaign_type') }}</th>
                    <th>{{ __('admin.owner') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th class="text-end">{{ __('admin.total_leads') }}</th>
                    <th class="text-end">{{ __('admin.customers') }}</th>
                    <th class="text-end">{{ __('admin.total_traffic') }}</th>
                    <th class="text-end">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $item->display_name }}</div>
                            <div class="small text-muted">{{ $item->campaign_code ?: ($item->utm_campaign ?: '-') }}</div>
                        </td>
                        <td>{{ $item->platform ?: '-' }}</td>
                        <td>{{ $item->utm_medium ?: '-' }}</td>
                        <td>{{ $item->campaign_type ?: '-' }}</td>
                        <td>{{ $item->owner?->name ?: '-' }}</td>
                        <td><span class="badge text-bg-{{ $item->statusBadgeClass() }}">{{ $item->localizedStatus() }}</span></td>
                        <td class="text-end">{{ $item->inquiries_count }}</td>
                        <td class="text-end">{{ $item->customers_count }}</td>
                        <td class="text-end">{{ $item->visits_count }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.marketing-campaigns.show', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a>
                                <a href="{{ route('admin.marketing-campaigns.edit', $item) }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.edit') }}</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center text-muted py-5">{{ __('admin.no_data_available') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $items->links() }}
</div>
@endsection
