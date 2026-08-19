@extends('layouts.admin')

@section('title', 'Analytics | ' . $funnel->name)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">📊 Analytics | {{ $funnel->name }}</h1>
            <p class="text-muted mb-0">Public URL: <a href="{{ $funnel->publicUrl() }}" target="_blank">/f/{{ $funnel->slug }} 🔗</a></p>
        </div>
        <a href="{{ route('admin.funnels.index') }}" class="btn btn-outline-secondary">
            ← Back to Funnels
        </a>
    </div>

    <!-- Key Metrics Grid -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
                <div class="text-muted small fw-semibold">Visitors / Starts</div>
                <div class="h2 fw-bold mb-0 mt-1">{{ number_format($metrics['total_visitors']) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
                <div class="text-muted small fw-semibold">Completions</div>
                <div class="h2 fw-bold text-success mb-0 mt-1">{{ number_format($metrics['total_completions']) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
                <div class="text-muted small fw-semibold">Conversion Rate</div>
                <div class="h2 fw-bold text-primary mb-0 mt-1">{{ $metrics['conversion_rate'] }}%</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
                <div class="text-muted small fw-semibold">Avg. Quiz Score</div>
                <div class="h2 fw-bold text-warning mb-0 mt-1">{{ $metrics['average_score'] }} pts</div>
            </div>
        </div>
    </div>

    <!-- Step Drop-Off Analysis Table -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <h5 class="fw-bold mb-3">📉 Step Drop-Off Funnel Analysis</h5>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Step Title</th>
                        <th>Step Type</th>
                        <th>Visitors Reached</th>
                        <th>Drop-off Count</th>
                        <th>Drop-off Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stepDropOff as $s)
                        <tr>
                            <td class="fw-bold text-dark">{{ $s['step_title'] }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $s['step_type'] }}</span></td>
                            <td><span class="fw-bold">{{ number_format($s['visitors']) }}</span></td>
                            <td><span class="text-danger fw-bold">-{{ number_format($s['drop_off_count']) }}</span></td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-danger" style="width: {{ $s['drop_off_rate'] }}%">
                                        {{ $s['drop_off_rate'] }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted text-center py-4">No step drop-off data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
