@extends('layouts.admin')

@section('page_title', $campaign->display_name)
@section('page_description', __('admin.marketing_campaign_details_desc'))

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card admin-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h2 class="h5 mb-1">{{ $campaign->display_name }}</h2>
                    <div class="text-muted">{{ $campaign->campaign_code ?: ($campaign->utm_campaign ?: '-') }}</div>
                </div>
                <span class="badge text-bg-{{ $campaign->statusBadgeClass() }}">{{ $campaign->localizedStatus() }}</span>
            </div>
            <dl class="row mb-0">
                <dt class="col-sm-4">{{ __('admin.platform') }}</dt><dd class="col-sm-8">{{ $campaign->platform ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.medium') }}</dt><dd class="col-sm-8">{{ $campaign->utm_medium ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.campaign_type') }}</dt><dd class="col-sm-8">{{ $campaign->campaign_type ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.marketing_objective') }}</dt><dd class="col-sm-8">{{ $campaign->objective ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.owner') }}</dt><dd class="col-sm-8">{{ $campaign->owner?->name ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.start_date') }}</dt><dd class="col-sm-8">{{ optional($campaign->start_date)->format('Y-m-d') ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.end_date') }}</dt><dd class="col-sm-8">{{ optional($campaign->end_date)->format('Y-m-d') ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.budget') }}</dt><dd class="col-sm-8">{{ $campaign->budget !== null ? number_format((float) $campaign->budget, 2) : '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.base_url') }}</dt><dd class="col-sm-8">{{ $campaign->base_url ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.final_url') }}</dt><dd class="col-sm-8"><span class="small text-break">{{ $campaign->generated_url ?: '-' }}</span></dd>
                <dt class="col-sm-4">UTM Source</dt><dd class="col-sm-8">{{ $campaign->utm_source ?: '-' }}</dd>
                <dt class="col-sm-4">UTM Campaign</dt><dd class="col-sm-8">{{ $campaign->utm_campaign ?: '-' }}</dd>
                <dt class="col-sm-4">{{ __('admin.notes') }}</dt><dd class="col-sm-8">{{ $campaign->notes ?: '-' }}</dd>
            </dl>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.total_traffic') }}</div><div class="fs-4 fw-semibold">{{ $summary['traffic'] }}</div><div class="small text-muted">{{ __('admin.unique_visits') }}: {{ $summary['unique_visits'] }}</div></div></div>
            <div class="col-md-4"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.total_leads') }}</div><div class="fs-4 fw-semibold">{{ $summary['leads'] }}</div><div class="small text-muted">{{ __('admin.marketing_delayed_leads') }}: {{ $summary['delayed_leads'] }}</div></div></div>
            <div class="col-md-4"><div class="card admin-card p-3 h-100"><div class="small text-muted">{{ __('admin.customers') }}</div><div class="fs-4 fw-semibold">{{ $summary['customers'] }}</div><div class="small text-muted">{{ __('admin.conversion_rate') }}: {{ $summary['conversion_rate'] }}%</div></div></div>
        </div>

        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">{{ __('admin.marketing_recent_leads') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.full_name') }}</th><th>{{ __('admin.status') }}</th><th>{{ __('admin.assigned_to') }}</th><th>{{ __('admin.created_date') }}</th><th class="text-end">{{ __('admin.actions') }}</th></tr></thead>
                    <tbody>
                    @forelse($recentLeads as $lead)
                        <tr>
                            <td>{{ $lead->full_name }}</td>
                            <td>{{ $lead->crmStatus?->localizedName() ?: '-' }}</td>
                            <td>{{ $lead->assignedUser?->name ?: '-' }}</td>
                            <td>{{ optional($lead->created_at)->format('Y-m-d H:i') ?: '-' }}</td>
                            <td class="text-end"><a href="{{ route('admin.crm.leads.show', $lead) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">{{ __('admin.no_data_available') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.marketing_recent_customers') }}</h2>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('admin.full_name') }}</th><th>{{ __('admin.customer_stage') }}</th><th>{{ __('admin.assigned_to') }}</th><th>{{ __('admin.converted_at') }}</th><th class="text-end">{{ __('admin.actions') }}</th></tr></thead>
                    <tbody>
                    @forelse($recentCustomers as $customer)
                        <tr>
                            <td>{{ $customer->full_name }}</td>
                            <td>{{ $customer->localizedStage() }}</td>
                            <td>{{ $customer->assignedUser?->name ?: '-' }}</td>
                            <td>{{ optional($customer->converted_at)->format('Y-m-d H:i') ?: '-' }}</td>
                            <td class="text-end"><a href="{{ route('admin.crm.customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">{{ __('admin.no_data_available') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">{{ __('admin.quick_actions') }}</h2>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.marketing-campaigns.edit', $campaign) }}" class="btn btn-primary">{{ __('admin.edit') }}</a>
                <a href="{{ route('admin.utm.edit', $campaign) }}" class="btn btn-outline-secondary">{{ __('admin.utm_edit_campaign') }}</a>
                @if($campaign->generated_url)
                    <a href="{{ $campaign->generated_url }}" target="_blank" rel="noopener" class="btn btn-outline-secondary">{{ __('admin.view') }} URL</a>
                @endif
            </div>
        </div>

        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">{{ __('admin.marketing_status_breakdown') }}</h2>
            <div class="d-grid gap-2">
                @forelse($statusRows as $row)
                    <div class="d-flex justify-content-between border rounded-3 px-3 py-2">
                        <span>{{ $row['label'] }}</span>
                        <strong>{{ $row['count'] }}</strong>
                    </div>
                @empty
                    <div class="text-muted">{{ __('admin.no_data_available') }}</div>
                @endforelse
            </div>
        </div>

        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.marketing_seller_distribution') }}</h2>
            <div class="d-grid gap-2">
                @forelse($sellerRows as $row)
                    <div class="border rounded-3 px-3 py-2">
                        <div class="fw-semibold">{{ $row['seller']->name }}</div>
                        <div class="small text-muted">{{ __('admin.total_leads') }}: {{ $row['leads'] }} / {{ __('admin.customers') }}: {{ $row['customers'] }}</div>
                    </div>
                @empty
                    <div class="text-muted">{{ __('admin.no_data_available') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
