@extends('layouts.admin')

@section('page_title', __('admin.marketing_manager'))
@section('page_description', __('admin.marketing_manager_desc'))

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card admin-card p-4 h-100">
            <div class="text-muted small text-uppercase">{{ __('admin.total_landing_pages') }}</div>
            <div class="display-6 fw-bold">{{ $summary['landing_pages'] }}</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card admin-card p-4 h-100">
            <div class="text-muted small text-uppercase">{{ __('admin.active_campaigns') }}</div>
            <div class="display-6 fw-bold">{{ $summary['active_campaigns'] }}</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card admin-card p-4 h-100">
            <div class="text-muted small text-uppercase">{{ __('admin.total_visits') }}</div>
            <div class="display-6 fw-bold">{{ $summary['visits'] }}</div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card admin-card p-4 h-100">
            <div class="text-muted small text-uppercase">{{ __('admin.total_leads') }}</div>
            <div class="display-6 fw-bold">{{ $summary['leads'] }}</div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        @if($summary['best_page'])
            <div class="small text-muted">{{ __('admin.best_converting_page') }}</div>
            <div class="fw-semibold">
                {{ $summary['best_page']['page']->internal_name }}
                <span class="text-muted">({{ $summary['best_page']['stats']['conversion_rate'] }}%)</span>
            </div>
        @endif
    </div>
    <a href="{{ route('admin.marketing-landing-pages.create') }}" class="btn btn-primary">{{ __('admin.create_landing_page') }}</a>
</div>

<div class="card admin-card p-0 overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>{{ __('admin.landing_page_name') }}</th>
                    <th>{{ __('admin.slug_key') }}</th>
                    <th>{{ __('admin.campaign_name') }}</th>
                    <th>{{ __('admin.platform') }}</th>
                    <th>{{ __('admin.status') }}</th>
                    <th>{{ __('admin.total_visits') }}</th>
                    <th>{{ __('admin.leads') }}</th>
                    <th>{{ __('admin.conversion_rate') }}</th>
                    <th>{{ __('admin.created_date') }}</th>
                    <th class="text-end">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    @php($stats = $item->analytics ?? [])
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $item->internal_name }}</div>
                            <div class="text-muted small">{{ $item->title_en }} / {{ $item->title_ar }}</div>
                        </td>
                        <td>
                            <div>{{ $item->slug }}</div>
                            <a href="{{ $item->publicUrl() }}" class="small text-decoration-none" target="_blank">{{ __('admin.view_public_page') }}</a>
                        </td>
                        <td>{{ $item->campaign_name ?: '—' }}</td>
                        <td>{{ $item->ad_platform ?: '—' }}</td>
                        <td>
                            <span class="badge text-bg-{{ $item->status === 'published' ? 'success' : ($item->status === 'archived' ? 'secondary' : 'warning') }}">
                                {{ __('admin.status_' . $item->status) }}
                            </span>
                        </td>
                        <td>{{ $stats['visits'] ?? 0 }}</td>
                        <td>{{ $stats['leads'] ?? 0 }}</td>
                        <td>{{ $stats['conversion_rate'] ?? 0 }}%</td>
                        <td>{{ $item->created_at?->format('d M Y') }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.marketing-landing-pages.edit', $item) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.edit') }}</a>
                                <form method="post" action="{{ route('admin.marketing-landing-pages.duplicate', $item) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-secondary">{{ __('admin.duplicate') }}</button>
                                </form>
                                <form method="post" action="{{ route('admin.marketing-landing-pages.destroy', $item) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">{{ __('admin.delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">{{ __('admin.no_landing_pages') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.top_performing_pages') }}</h2>
            <div class="list-group list-group-flush">
                @forelse($topPages as $row)
                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">{{ $row['page']->internal_name }}</div>
                            <div class="text-muted small">{{ $row['page']->campaign_name ?: $row['page']->slug }}</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-semibold">{{ $row['stats']['leads'] }} {{ __('admin.leads') }}</div>
                            <div class="small text-muted">{{ $row['stats']['conversion_rate'] }}%</div>
                        </div>
                    </div>
                @empty
                    <div class="text-muted">{{ __('admin.no_landing_pages') }}</div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card admin-card p-4 h-100">
            <h2 class="h5 mb-3">{{ __('admin.latest_submissions') }}</h2>
            <div class="list-group list-group-flush">
                @forelse($summary['latest_submissions'] as $submission)
                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">{{ $submission->full_name }}</div>
                            <div class="text-muted small">{{ $submission->marketingLandingPage?->internal_name }}</div>
                        </div>
                        <a href="{{ route('admin.inquiries.show', $submission) }}" class="btn btn-sm btn-outline-primary">{{ __('admin.view') }}</a>
                    </div>
                @empty
                    <div class="text-muted">{{ __('admin.no_leads_yet') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $items->links() }}
</div>
@endsection
