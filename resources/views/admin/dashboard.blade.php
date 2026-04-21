@extends('layouts.admin')

@section('page_title', __('admin.dashboard'))
@section('page_description', __('admin.dashboard_overview_desc'))

@section('content')
<section class="admin-dashboard-grid">
    <div class="admin-hero-card">
        <div class="admin-hero-copy">
            <span class="admin-eyebrow">{{ __('admin.dashboard_welcome_badge') }}</span>
            <h2>{{ __('admin.dashboard_welcome_title') }}</h2>
            <p>{{ __('admin.dashboard_welcome_text') }}</p>
        </div>
        <div class="admin-hero-actions">
            <a class="btn btn-primary" href="{{ route('admin.pages.index') }}">{{ __('admin.dashboard_primary_action') }}</a>
            <a class="btn btn-outline-secondary" href="{{ route('admin.search') }}">{{ __('admin.dashboard_secondary_action') }}</a>
        </div>
    </div>

    <div class="admin-summary-panel">
        <div class="admin-summary-panel__header">
            <h3>{{ __('admin.dashboard_summary_title') }}</h3>
            <p>{{ __('admin.dashboard_summary_text') }}</p>
        </div>
        <div class="admin-summary-list">
            <div><span>{{ __('admin.visa_destinations') }}</span><strong>{{ $summary['visa_countries'] }}</strong></div>
            <div><span>{{ __('admin.destinations') }}</span><strong>{{ $summary['destinations'] }}</strong></div>
            <div><span>{{ __('admin.forms_manager') }}</span><strong>{{ $summary['forms'] }}</strong></div>
            <div><span>{{ __('admin.marketing_manager') }}</span><strong>{{ $summary['landing_pages'] }}</strong></div>
            <div><span>{{ __('admin.blog_posts') }}</span><strong>{{ $summary['posts'] }}</strong></div>
            <div><span>{{ __('admin.testimonials') }}</span><strong>{{ $summary['testimonials'] }}</strong></div>
        </div>
    </div>
</section>

<section class="row g-4 mb-4">
    @foreach($stats as $stat)
        <div class="col-sm-6 col-xl-4 col-xxl-2">
            <div class="admin-stat-card admin-stat-card--{{ $stat['tone'] }}">
                <div class="admin-stat-card__label">{{ $stat['label'] }}</div>
                <div class="admin-stat-card__value">{{ $stat['value'] }}</div>
            </div>
        </div>
    @endforeach
</section>

<section class="row g-4 mb-4">
    <div class="col-xl-7">
        <div class="card admin-card admin-surface-card h-100">
            <div class="card-body p-4 p-xl-5">
                <div class="admin-section-heading">
                    <div>
                        <span class="admin-eyebrow">{{ __('admin.dashboard_quick_access_badge') }}</span>
                        <h3>{{ __('admin.dashboard_quick_access_title') }}</h3>
                    </div>
                    <p>{{ __('admin.dashboard_quick_access_text') }}</p>
                </div>
                <div class="admin-quick-grid">
                    @foreach($quickAccess as $item)
                        <article class="admin-quick-card">
                            <h4>{{ $item['title'] }}</h4>
                            <p>{{ $item['text'] }}</p>
                            <a href="{{ $item['route'] }}" class="btn btn-sm btn-outline-secondary">{{ $item['button'] }}</a>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card admin-card admin-surface-card h-100">
            <div class="card-body p-4 p-xl-5">
                <div class="admin-section-heading">
                    <div>
                        <span class="admin-eyebrow">{{ __('admin.dashboard_activity_badge') }}</span>
                        <h3>{{ __('admin.dashboard_activity_title') }}</h3>
                    </div>
                    <p>{{ __('admin.dashboard_activity_text') }}</p>
                </div>
                <div class="admin-activity-list">
                    <div class="admin-activity-item">
                        <strong>{{ __('admin.dashboard_activity_inquiries') }}</strong>
                        <span>{{ $stats[0]['value'] }}</span>
                    </div>
                    <div class="admin-activity-item">
                        <strong>{{ __('admin.dashboard_activity_landing_pages') }}</strong>
                        <span>{{ $summary['landing_pages'] }}</span>
                    </div>
                    <div class="admin-activity-item">
                        <strong>{{ __('admin.dashboard_activity_hero_slides') }}</strong>
                        <span>{{ $summary['hero_slides'] }}</span>
                    </div>
                    <div class="admin-activity-item">
                        <strong>{{ __('admin.dashboard_activity_forms') }}</strong>
                        <span>{{ $summary['forms'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="row g-4">
    <div class="col-xl-6">
        <div class="card admin-card admin-surface-card h-100">
            <div class="card-body p-4">
                <div class="admin-section-heading mb-4">
                    <div>
                        <span class="admin-eyebrow">{{ __('admin.dashboard_inquiries_badge') }}</span>
                        <h3>{{ __('admin.dashboard_latest_inquiries') }}</h3>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table admin-table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>{{ __('admin.name') }}</th>
                            <th>{{ __('admin.type') }}</th>
                            <th>{{ __('admin.status') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($latestInquiries as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.inquiries.show', $item) }}" class="admin-table-link">{{ $item->full_name }}</a>
                                </td>
                                <td>{{ $item->type }}</td>
                                <td><span class="admin-status-pill">{{ $item->crmStatus ? $item->crmStatus->localizedName() : ucfirst($item->status) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">{{ __('admin.no_leads_yet') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card admin-card admin-surface-card h-100">
            <div class="card-body p-4">
                <div class="admin-section-heading mb-4">
                    <div>
                        <span class="admin-eyebrow">{{ __('admin.dashboard_content_badge') }}</span>
                        <h3>{{ __('admin.dashboard_latest_posts') }}</h3>
                    </div>
                </div>
                <div class="admin-stack-list">
                    @forelse($latestPosts as $item)
                        <article class="admin-stack-item">
                            <div>
                                <strong>{{ $item->localized('title') }}</strong>
                                <div class="text-muted small">{{ $item->slug }}</div>
                            </div>
                            <span>{{ optional($item->published_at)->format('d M Y') ?: '—' }}</span>
                        </article>
                    @empty
                        <div class="text-muted">{{ __('admin.no_blog_posts') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
