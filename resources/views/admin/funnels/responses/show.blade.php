@extends('layouts.admin')

@section('title', 'Response #' . $response->id)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">📬 Response #{{ $response->id }}</h1>
            <p class="text-muted mb-0">Funnel: {{ $response->funnel?->name }}</p>
        </div>
        <a href="{{ route('admin.funnels.responses.index') }}" class="btn btn-outline-secondary">
            ← Back to Responses
        </a>
    </div>

    <div class="row g-4">
        <!-- Main Answers Column -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-3">Submitted Answers</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Question</th>
                                <th>Answer</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($response->answers as $ans)
                                <tr>
                                    <td class="fw-semibold">{{ $ans->question_label }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border p-2 fs-6">
                                            {{ $ans->answer_value }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-muted">No answer details recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Summary -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold mb-3">Score & Outcome</h5>
                <div class="mb-3">
                    <label class="text-muted small">Total Score</label>
                    <div class="h2 fw-bold text-primary">{{ $response->score }} pts</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Resolved Result</label>
                    <div class="fw-bold text-success">{{ $response->result?->title ?: 'Default Result' }}</div>
                </div>
                <hr>
                <div class="mb-3">
                    <label class="text-muted small">CRM Lead Link</label>
                    <div>
                        @if($response->inquiry)
                            <a href="{{ route('admin.inquiries.index') }}?search={{ $response->inquiry->id }}" class="btn btn-sm btn-outline-success">
                                🟢 View CRM Inquiry (#{{ $response->inquiry->id }})
                            </a>
                        @else
                            <span class="text-muted">No CRM Inquiry Linked</span>
                            @if($response->crm_sync_status === 'failed')
                                <form action="{{ route('admin.funnels.responses.retry-crm', $response) }}" method="POST" class="mt-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">Retry CRM Sync</button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
