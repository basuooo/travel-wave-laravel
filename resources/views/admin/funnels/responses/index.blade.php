@extends('layouts.admin')

@section('title', __('admin.funnel_responses'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">📬 {{ __('admin.funnel_responses') }}</h1>
            <p class="text-muted mb-0">Review submitted funnel entries and CRM sync status</p>
        </div>
    </div>

    <!-- Responses Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Response ID</th>
                        <th>Funnel</th>
                        <th>Score</th>
                        <th>Result</th>
                        <th>CRM Sync Status</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($responses as $res)
                        <tr>
                            <td>
                                <span class="fw-bold font-monospace">#{{ $res->id }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">{{ $res->funnel?->name }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary fs-6">{{ $res->score }} pts</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-success">{{ $res->result?->title ?: 'N/A' }}</span>
                            </td>
                            <td>
                                @if($res->crm_sync_status === 'synced')
                                    <span class="badge bg-success-subtle text-success">Synced (#{{ $res->crm_inquiry_id }})</span>
                                @elseif($res->crm_sync_status === 'failed')
                                    <span class="badge bg-danger-subtle text-danger">Failed</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($res->crm_sync_status) }}</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $res->created_at->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.funnels.responses.show', $res) }}" class="btn btn-sm btn-outline-primary">
                                    👁️ Details
                                </a>
                                @if($res->crm_sync_status === 'failed')
                                    <form action="{{ route('admin.funnels.responses.retry-crm', $res) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning">🔄 Retry CRM</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No responses captured yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($responses->hasPages())
            <div class="card-footer bg-transparent py-3">
                {{ $responses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
