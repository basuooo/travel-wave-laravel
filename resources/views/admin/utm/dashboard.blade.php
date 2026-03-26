@extends('layouts.admin')

@section('page_title', __('admin.utm_analytics'))
@section('page_description', __('admin.utm_analytics_desc'))

@section('content')
<div class="card admin-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h2 class="h5 mb-1">{{ __('admin.utm_analytics') }}</h2>
            <p class="text-muted mb-0">{{ __('admin.utm_analytics_desc') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.utm.index') }}" class="btn btn-outline-secondary">{{ __('admin.utm_saved_campaigns') }}</a>
            <a href="{{ route('admin.utm.create') }}" class="btn btn-primary">{{ __('admin.utm_build_link') }}</a>
        </div>
    </div>

    <form method="get" action="{{ route('admin.utm.dashboard') }}" class="row g-3">
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.from_date') }}</label>
            <input type="date" name="from_date" value="{{ $filters['from_date'] ?? '' }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.to_date') }}</label>
            <input type="date" name="to_date" value="{{ $filters['to_date'] ?? '' }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.source') }}</label>
            <input type="text" name="source" value="{{ $filters['source'] ?? '' }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.medium') }}</label>
            <input type="text" name="medium" value="{{ $filters['medium'] ?? '' }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.campaign_name') }}</label>
            <select name="campaign_id" class="form-select">
                <option value="">{{ __('admin.all') }}</option>
                @foreach($campaigns as $campaign)
                    <option value="{{ $campaign->id }}" @selected((int) ($filters['campaign_id'] ?? 0) === (int) $campaign->id)>{{ $campaign->display_name }}</option>
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
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.crm_salesman') }}</label>
            <select name="seller_id" class="form-select">
                <option value="">{{ __('admin.all') }}</option>
                @foreach($owners as $owner)
                    <option value="{{ $owner->id }}" @selected((int) ($filters['seller_id'] ?? 0) === (int) $owner->id)>{{ $owner->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.status') }}</label>
            <select name="crm_status_id" class="form-select">
                <option value="">{{ __('admin.all') }}</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}" @selected((int) ($filters['crm_status_id'] ?? 0) === (int) $status->id)>{{ $status->localizedName() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary">{{ __('admin.search') }}</button>
            <a href="{{ route('admin.utm.dashboard') }}" class="btn btn-outline-secondary">{{ __('admin.reset_filters') }}</a>
        </div>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.total_campaigns') }}</div><div class="h3 mb-0">{{ $summary['campaigns'] }}</div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.total_traffic') }}</div><div class="h3 mb-0">{{ $summary['traffic'] }}</div><div class="small text-muted">{{ __('admin.unique_visits') }}: {{ $summary['unique_visits'] }}</div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.total_leads') }}</div><div class="h3 mb-0">{{ $summary['leads'] }}</div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card admin-card p-3 h-100"><div class="text-muted small">{{ __('admin.conversion_rate') }}</div><div class="h3 mb-0">{{ $summary['conversion_rate'] }}%</div></div></div>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">{{ __('admin.utm_campaign_performance') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('admin.campaign_name') }}</th>
                            <th>{{ __('admin.source') }}</th>
                            <th>{{ __('admin.medium') }}</th>
                            <th>{{ __('admin.owner') }}</th>
                            <th class="text-end">{{ __('admin.total_traffic') }}</th>
                            <th class="text-end">{{ __('admin.total_leads') }}</th>
                            <th class="text-end">{{ __('admin.conversion_rate') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaignPerformance as $row)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $row['campaign']->display_name }}</div>
                                    <div class="small text-muted">{{ $row['campaign']->utm_campaign ?: $row['campaign']->generated_url }}</div>
                                </td>
                                <td>{{ $row['campaign']->utm_source ?: '—' }}</td>
                                <td>{{ $row['campaign']->utm_medium ?: '—' }}</td>
                                <td>{{ $row['campaign']->owner?->name ?: '—' }}</td>
                                <td class="text-end">{{ $row['traffic'] }}</td>
                                <td class="text-end">{{ $row['leads'] }}</td>
                                <td class="text-end">{{ $row['conversion_rate'] }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">{{ __('admin.no_data_available') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.utm_daily_trend') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('admin.date') }}</th>
                            <th class="text-end">{{ __('admin.total_traffic') }}</th>
                            <th class="text-end">{{ __('admin.total_leads') }}</th>
                            <th class="text-end">{{ __('admin.conversion_rate') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dateRows as $row)
                            <tr>
                                <td>{{ $row['date'] }}</td>
                                <td class="text-end">{{ $row['traffic'] }}</td>
                                <td class="text-end">{{ $row['leads'] }}</td>
                                <td class="text-end">{{ $row['conversion_rate'] }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">{{ __('admin.no_data_available') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        @foreach([
            __('admin.utm_source_report') => $sourceRows,
            __('admin.utm_medium_report') => $mediumRows,
            __('admin.utm_landing_page_report') => $landingRows,
        ] as $title => $rows)
            <div class="card admin-card p-4 mb-4">
                <h2 class="h5 mb-3">{{ $title }}</h2>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('admin.name') }}</th>
                                <th class="text-end">{{ __('admin.total_traffic') }}</th>
                                <th class="text-end">{{ __('admin.total_leads') }}</th>
                                <th class="text-end">{{ __('admin.conversion_rate') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $row)
                                <tr>
                                    <td class="small">{{ $row['label'] }}</td>
                                    <td class="text-end">{{ $row['traffic'] }}</td>
                                    <td class="text-end">{{ $row['leads'] }}</td>
                                    <td class="text-end">{{ $row['conversion_rate'] }}%</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">{{ __('admin.no_data_available') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.utm_seller_report') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('admin.crm_salesman') }}</th>
                            <th class="text-end">{{ __('admin.total_leads') }}</th>
                            <th class="text-end">{{ __('admin.converted') }}</th>
                            <th class="text-end">{{ __('admin.conversion_rate') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sellerRows as $row)
                            <tr>
                                <td>{{ $row['seller']->name }}</td>
                                <td class="text-end">{{ $row['leads'] }}</td>
                                <td class="text-end">{{ $row['converted'] }}</td>
                                <td class="text-end">{{ $row['conversion_rate'] }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">{{ __('admin.no_data_available') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
