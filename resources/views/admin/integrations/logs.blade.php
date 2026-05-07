@extends('layouts.admin')

@section('page_title', __('admin.api_webhook_logs') ?? 'API & Webhook Logs')

@section('content')
<ul class="nav nav-tabs mb-4" id="logTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="webhooks-tab" data-bs-toggle="tab" data-bs-target="#webhooks" type="button" role="tab">
            <i class="bi bi-broadcast me-1"></i> Webhooks
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="api-tab" data-bs-toggle="tab" data-bs-target="#api" type="button" role="tab">
            <i class="bi bi-cloud-arrow-up me-1"></i> Outgoing API Calls
        </button>
    </li>
</ul>

<div class="tab-content" id="logTabsContent">
    <!-- Webhook Logs -->
    <div class="tab-pane fade show active" id="webhooks" role="tabpanel">
        <div class="card admin-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Time</th>
                            <th>Integration</th>
                            <th>Platform</th>
                            <th>Status</th>
                            <th>IP Address</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($webhookLogs as $log)
                            <tr>
                                <td class="small">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $log->integration?->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-secondary text-uppercase">{{ $log->platform }}</span></td>
                                <td>
                                    @if($log->status === 'processed')
                                        <span class="badge bg-success">Processed</span>
                                    @elseif($log->status === 'failed')
                                        <span class="badge bg-danger" title="{{ $log->error_message }}">Failed</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $log->request_ip }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#webhookModal{{ $log->id }}">
                                        View Payload
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal -->
                            <div class="modal fade" id="webhookModal{{ $log->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Webhook Payload #{{ $log->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-close="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <pre class="bg-dark text-info p-3 rounded"><code>{{ json_encode($log->payload, JSON_PRETTY_PRINT) }}</code></pre>
                                            @if($log->error_message)
                                                <div class="alert alert-danger mt-3 mb-0">
                                                    <strong>Error:</strong> {{ $log->error_message }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr><td colspan="6" class="text-center py-4 text-muted">No webhook logs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top-0">
                {{ $webhookLogs->links() }}
            </div>
        </div>
    </div>

    <!-- API Logs -->
    <div class="tab-pane fade" id="api" role="tabpanel">
        <div class="card admin-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Time</th>
                            <th>Integration</th>
                            <th>Method</th>
                            <th>Endpoint</th>
                            <th>Status</th>
                            <th>Duration</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($apiLogs as $log)
                            <tr>
                                <td class="small">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $log->integration?->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-info text-dark">{{ $log->method }}</span></td>
                                <td class="small text-truncate" style="max-width: 250px;">{{ $log->endpoint }}</td>
                                <td>
                                    <span class="badge {{ $log->status_code >= 200 && $log->status_code < 300 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $log->status_code }}
                                    </td>
                                <td>{{ $log->duration_ms }}ms</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#apiModal{{ $log->id }}">
                                        View Detail
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal -->
                            <div class="modal fade" id="apiModal{{ $log->id }}" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">API Request/Response #{{ $log->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-close="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6>Request Payload</h6>
                                                    <pre class="bg-dark text-info p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code>{{ json_encode($log->request_payload, JSON_PRETTY_PRINT) }}</code></pre>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Response Payload</h6>
                                                    <pre class="bg-dark text-success p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code>{{ json_encode($log->response_payload, JSON_PRETTY_PRINT) }}</code></pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">No API logs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top-0">
                {{ $apiLogs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
