@extends('layouts.admin')

@section('title', __('admin.funnels_dashboard'))

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">⚡ {{ __('admin.funnels_dashboard') }}</h1>
            <p class="text-muted mb-0">No-Code Interactive Funnel & Quiz Engine</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.funnels.templates.index') }}" class="btn btn-outline-primary">
                🎨 {{ __('admin.templates_library') }}
            </a>
            <a href="{{ route('admin.funnels.create') }}" class="btn btn-primary fw-bold">
                ➕ {{ __('admin.create_new_funnel') }}
            </a>
        </div>
    </div>

    @if(!empty($needsMigration))
        <div class="alert alert-warning border-warning shadow-sm rounded-4 p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1">⚠️ تنبيه: جداول الفانلات التفاعلية محتاجة تشغيل الـ Migrations</h5>
                    <p class="mb-0">يرجى الضغط على زر التحديث لتشغيل الـ Migrations وإنشاء الجداول على السيرفر فوراً.</p>
                </div>
                <a href="/migrate-db" target="_blank" class="btn btn-warning fw-bold px-4">
                    🚀 تشغيل Migrations السيرفر الان
                </a>
            </div>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">{{ __('admin.total_funnels') }}</div>
                        <div class="h2 fw-bold mb-0 mt-1">{{ number_format($funnelsCount) }}</div>
                        <div class="small text-muted mt-1">
                            <span class="text-success fw-bold">{{ $publishedCount }}</span> Published |
                            <span class="text-secondary fw-bold">{{ $draftCount }}</span> Draft
                        </div>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                        <iconify-icon icon="solar:funnel-bold-duotone" width="32"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">{{ __('admin.total_visitors') }}</div>
                        <div class="h2 fw-bold mb-0 mt-1">{{ number_format($metrics['total_visitors']) }}</div>
                        <div class="small text-info fw-bold mt-1">100% Starts</div>
                    </div>
                    <div class="bg-info-subtle text-info p-3 rounded-circle">
                        <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" width="32"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">{{ __('admin.total_completions') }}</div>
                        <div class="h2 fw-bold mb-0 mt-1">{{ number_format($metrics['total_completions']) }}</div>
                        <div class="small text-success fw-bold mt-1">{{ $metrics['conversion_rate'] }}% Conversion</div>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-circle">
                        <iconify-icon icon="solar:check-circle-bold-duotone" width="32"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold">{{ __('admin.crm_leads_captured') }}</div>
                        <div class="h2 fw-bold mb-0 mt-1">{{ number_format($metrics['total_leads']) }}</div>
                        <div class="small text-warning fw-bold mt-1">{{ $metrics['qualified_leads'] }} Qualified</div>
                    </div>
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                        <iconify-icon icon="solar:user-plus-bold-duotone" width="32"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Funnels List Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">📋 {{ __('admin.active_funnels') }}</h5>
            <a href="{{ route('admin.funnels.index') }}" class="btn btn-sm btn-outline-secondary">
                {{ __('admin.view_all_funnels') }}
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('admin.funnel_name') }}</th>
                        <th>{{ __('admin.status') }}</th>
                        <th>{{ __('admin.template') }}</th>
                        <th>{{ __('admin.responses') }}</th>
                        <th>{{ __('admin.completions') }}</th>
                        <th>{{ __('admin.created_at') }}</th>
                        <th class="text-end">{{ __('admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($funnels as $funnel)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $funnel->name }}</div>
                                <div class="small text-muted">
                                    <a href="{{ $funnel->publicUrl() }}" target="_blank" class="text-decoration-none text-muted">
                                        /f/{{ $funnel->slug }} 🔗
                                    </a>
                                </div>
                            </td>
                            <td>
                                @if($funnel->status === 'published')
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                        ● Published
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $funnel->template?->name ?: 'Scratch' }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold">{{ number_format($funnel->responses_count) }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-success">{{ number_format($funnel->completed_responses_count) }}</span>
                            </td>
                            <td class="text-muted small">
                                {{ $funnel->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('admin.funnels.builder', $funnel) }}" class="btn btn-sm btn-outline-primary" title="Builder">
                                        ✏️ Edit
                                    </a>
                                    <a href="{{ route('admin.funnels.analytics', $funnel) }}" class="btn btn-sm btn-outline-info" title="Analytics">
                                        📊
                                    </a>
                                    <a href="{{ $funnel->publicUrl() }}?preview=1" target="_blank" class="btn btn-sm btn-outline-secondary" title="Preview">
                                        👁️
                                    </a>
                                    <form action="{{ route('admin.funnels.duplicate', $funnel) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-dark" title="Duplicate">📋</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <iconify-icon icon="solar:folder-open-bold-duotone" width="48" class="mb-2 text-secondary"></iconify-icon>
                                <p class="mb-2">{{ __('admin.no_funnels_created_yet') }}</p>
                                <a href="{{ route('admin.funnels.create') }}" class="btn btn-sm btn-primary">
                                    Create First Funnel
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($funnels->hasPages())
            <div class="card-footer bg-transparent py-3">
                {{ $funnels->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
