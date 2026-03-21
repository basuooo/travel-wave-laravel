@extends('layouts.admin')

@section('page_title', __('admin.seo_manager'))
@section('page_description', __('admin.seo_manager_desc'))

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card admin-card p-4 h-100"><div class="text-muted small">{{ __('admin.seo_indexed_ready_pages') }}</div><div class="h3 mb-0">{{ $summary['indexed_ready_pages'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-4 h-100"><div class="text-muted small">{{ __('admin.seo_missing_meta') }}</div><div class="h3 mb-0">{{ $summary['missing_meta_title'] + $summary['missing_meta_description'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-4 h-100"><div class="text-muted small">{{ __('admin.seo_redirects') }}</div><div class="h3 mb-0">{{ $summary['redirects_count'] }}</div></div></div>
    <div class="col-md-3"><div class="card admin-card p-4 h-100"><div class="text-muted small">{{ __('admin.seo_sitemap_status') }}</div><div class="small">{{ $summary['sitemap_last_generated_at']?->diffForHumans() ?: __('admin.seo_not_generated') }}</div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card admin-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="h5 mb-1">{{ __('admin.seo_sitemap_manager') }}</h2>
                    <div class="text-muted small">{{ __('admin.seo_sitemap_desc') }}</div>
                </div>
                <form method="post" action="{{ route('admin.seo.sitemap.regenerate') }}">
                    @csrf
                    <button class="btn btn-primary">{{ __('admin.seo_regenerate_sitemap') }}</button>
                </form>
            </div>
            <div class="small text-muted mb-3">{{ __('admin.seo_last_generated') }}: {{ $summary['sitemap_last_generated_at']?->format('Y-m-d H:i') ?: '-' }}</div>
            <ul class="mb-0">
                @foreach($sitemapUrls as $label => $url)
                    <li><a href="{{ $url }}" target="_blank">{{ $url }}</a></li>
                @endforeach
            </ul>
        </div>

        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.seo_health_audit') }}</h2>
            <div class="row g-3 mb-3">
                <div class="col-md-3"><div class="border rounded p-3 h-100"><div class="small text-muted">{{ __('admin.seo_missing_title') }}</div><div class="h4 mb-0">{{ $summary['missing_meta_title'] }}</div></div></div>
                <div class="col-md-3"><div class="border rounded p-3 h-100"><div class="small text-muted">{{ __('admin.seo_missing_description') }}</div><div class="h4 mb-0">{{ $summary['missing_meta_description'] }}</div></div></div>
                <div class="col-md-3"><div class="border rounded p-3 h-100"><div class="small text-muted">{{ __('admin.seo_missing_canonical') }}</div><div class="h4 mb-0">{{ $summary['missing_canonical'] }}</div></div></div>
                <div class="col-md-3"><div class="border rounded p-3 h-100"><div class="small text-muted">{{ __('admin.seo_missing_schema') }}</div><div class="h4 mb-0">{{ $summary['missing_schema'] }}</div></div></div>
            </div>
            <ul class="mb-0">
                @forelse($summary['latest_issues'] as $issue)
                    <li>{{ __('admin.' . $issue['type']) }}: {{ $issue['label'] }}</li>
                @empty
                    <li>{{ __('admin.seo_no_issues') }}</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">{{ __('admin.seo_quick_actions') }}</h2>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.seo.settings') }}" class="btn btn-outline-primary">{{ __('admin.seo_global_settings') }}</a>
                <a href="{{ route('admin.seo.meta.index') }}" class="btn btn-outline-primary">{{ __('admin.seo_meta_manager') }}</a>
                <a href="{{ route('admin.seo.redirects.index') }}" class="btn btn-outline-primary">{{ __('admin.seo_redirects_manager') }}</a>
            </div>
        </div>

        <div class="card admin-card p-4">
            <h2 class="h5 mb-3">{{ __('admin.seo_tools_status') }}</h2>
            <div class="small text-muted mb-2">robots.txt</div>
            <div class="mb-3"><a href="{{ url('/robots.txt') }}" target="_blank">{{ url('/robots.txt') }}</a></div>
            <div class="small text-muted mb-2">{{ __('admin.search_console_property') }}</div>
            <div>{{ $settings->search_console_property ?: '-' }}</div>
        </div>
    </div>
</div>
@endsection
