@extends('layouts.admin')

@section('page_title', __('admin.landing_pages_dashboard'))
@section('page_description', 'Comprehensive management, performance analytics, and visual builder for landing pages.')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-1">🚀 {{ __('admin.landing_pages') }}</h3>
        <p class="text-muted mb-0">Create, customize, and manage high-converting landing pages and marketing campaigns.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.landing-pages.create') }}" class="btn btn-primary fw-bold px-3">
            <i class="bi bi-plus-lg me-1"></i> {{ __('admin.create_landing_page') }}
        </a>
        <a href="{{ route('admin.landing-pages.index') }}" class="btn btn-outline-secondary px-3">
            <i class="bi bi-list me-1"></i> {{ __('admin.all_pages') }}
        </a>
    </div>
</div>

<!-- Stats Overview -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card admin-card p-3 border-start border-4 border-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small font-monospace">TOTAL PAGES</div>
                    <div class="fs-3 fw-bold text-dark">{{ $totalPages }}</div>
                </div>
                <div class="fs-1 text-primary"><i class="bi bi-journal-text"></i></div>
            </div>
            <div class="mt-2 text-muted small">
                <span class="text-success fw-bold">{{ $publishedPages }} Published</span> • 
                <span class="text-secondary">{{ $draftPages }} Draft</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card admin-card p-3 border-start border-4 border-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small font-monospace">ACTIVE STATUS</div>
                    <div class="fs-3 fw-bold text-success">{{ $activePages }}</div>
                </div>
                <div class="fs-1 text-success"><i class="bi bi-toggle-on"></i></div>
            </div>
            <div class="mt-2 text-muted small">
                <span>{{ $inactivePages }} Inactive Pages</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card admin-card p-3 border-start border-4 border-warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small font-monospace">TEMPLATES & SECTIONS</div>
                    <div class="fs-3 fw-bold text-warning">{{ $totalTemplates }}</div>
                </div>
                <div class="fs-1 text-warning"><i class="bi bi-grid-1x2-fill"></i></div>
            </div>
            <div class="mt-2 text-muted small">
                <span>{{ $totalSections }} Pre-built Sections</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card admin-card p-3 border-start border-4 border-info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small font-monospace">CAPTURED LEADS</div>
                    <div class="fs-3 fw-bold text-info">{{ $totalLeads }}</div>
                </div>
                <div class="fs-1 text-info"><i class="bi bi-people-fill"></i></div>
            </div>
            <div class="mt-2 text-muted small">
                <span>Integrated with CRM System</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Pages -->
    <div class="col-lg-7">
        <div class="card admin-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0">📄 Recent Landing Pages</h5>
                <a href="{{ route('admin.landing-pages.index') }}" class="btn btn-sm btn-link text-decoration-none">View All →</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Page</th>
                            <th>Status</th>
                            <th>Active</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPages as $page)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $page->internal_name }}</div>
                                <div class="small text-muted font-monospace">/lp/{{ $page->slug }}</div>
                            </td>
                            <td>
                                @if($page->status === 'published')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Published</span>
                                @elseif($page->status === 'draft')
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">Draft</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Archived</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $page->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $page->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.landing-pages.builder', $page) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil-square"></i> Builder
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No landing pages created yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Activity Log -->
    <div class="col-lg-5">
        <div class="card admin-card p-4">
            <h5 class="fw-bold mb-3">🕒 Recent Activity</h5>
            <div class="list-group list-group-flush">
                @forelse($recentActivity as $log)
                <div class="list-group-item px-0 py-2 border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="badge bg-light text-dark border">{{ str_replace('_', ' ', $log->action) }}</span>
                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="small text-dark mt-1">
                        <strong>{{ $log->user?->name ?? 'System' }}</strong> updated 
                        <span class="text-primary font-monospace">{{ $log->landingPage?->internal_name ?? '#' . $log->landing_page_id }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted small">No activity recorded yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
