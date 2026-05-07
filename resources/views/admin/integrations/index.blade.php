@extends('layouts.admin')

@section('page_title', __('admin.integrations_management'))
@section('page_description', __('admin.integrations_management_desc'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.integrations.logs') }}" class="btn btn-outline-secondary">
            <i class="bi bi-journal-text me-1"></i> {{ __('admin.view_api_logs') ?? 'View Logs' }}
        </a>
    </div>
    <a href="{{ route('admin.integrations.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> {{ __('admin.create_integration') ?? 'Add Integration' }}
    </a>
</div>

<div class="row g-4">
    @forelse($integrations as $integration)
        <div class="col-md-6 col-xl-4">
            <div class="card admin-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <div class="platform-icon me-3">
                                @if($integration->platform === 'meta')
                                    <i class="bi bi-facebook text-primary fs-2"></i>
                                @elseif($integration->platform === 'tiktok')
                                    <i class="bi bi-tiktok text-dark fs-2"></i>
                                @else
                                    <i class="bi bi-plug text-secondary fs-2"></i>
                                @endif
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $integration->name }}</h5>
                                <span class="badge text-uppercase {{ $integration->platform === 'meta' ? 'bg-primary-subtle text-primary' : 'bg-dark-subtle text-dark' }}">
                                    {{ $integration->platform }}
                                </span>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" @checked($integration->is_active) disabled>
                        </div>
                    </div>

                    <div class="stats-row d-flex gap-4 mb-4">
                        <div class="stat-item">
                            <div class="text-muted small">{{ __('admin.total_leads') }}</div>
                            <div class="fw-bold fs-5">{{ $integration->leads_count }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="text-muted small">{{ __('admin.webhooks_received') ?? 'Webhooks' }}</div>
                            <div class="fw-bold fs-5">{{ $integration->webhook_logs_count }}</div>
                        </div>
                        <div class="stat-item">
                            <div class="text-muted small">{{ __('admin.status') }}</div>
                            <div>
                                @if($integration->connection_status === 'connected')
                                    <span class="text-success small fw-bold"><i class="bi bi-check-circle-fill"></i> Connected</span>
                                @elseif($integration->connection_status === 'failed')
                                    <span class="text-danger small fw-bold"><i class="bi bi-x-circle-fill"></i> Failed</span>
                                @else
                                    <span class="text-muted small fw-bold">Unknown</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.integrations.edit', $integration) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                            {{ __('admin.edit') }}
                        </a>
                        <form action="{{ route('admin.integrations.test', $integration) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <button class="btn btn-sm btn-outline-info w-100">
                                {{ __('admin.test_connection') ?? 'Test' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card admin-card text-center py-5">
                <div class="text-muted mb-3"><i class="bi bi-plug fs-1"></i></div>
                <h5>No integrations found</h5>
                <p class="text-muted">Start by connecting an advertising platform to receive leads automatically.</p>
                <div class="mt-3">
                    <a href="{{ route('admin.integrations.create') }}" class="btn btn-primary">
                        {{ __('admin.create_integration') ?? 'Add First Integration' }}
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>
@endsection
