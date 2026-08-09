@extends('layouts.admin')

@section('page_title', 'Captured Leads - ' . $landingPage->internal_name)
@section('page_description', 'View and export all inquiry submissions captured from this landing page.')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <a href="{{ route('admin.landing-pages.index') }}" class="btn btn-sm btn-outline-secondary mb-2">← Back to All Pages</a>
        <h3 class="fw-bold mb-1">👥 Captured Leads for {{ $landingPage->internal_name }}</h3>
        <p class="text-muted mb-0 font-monospace">Total Leads: {{ $leads->total() }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.landing-pages.leads', [$landingPage, 'export' => 'csv']) }}" class="btn btn-success fw-bold">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
        </a>
    </div>
</div>

<div class="card admin-card p-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Phone / WhatsApp</th>
                    <th>Email</th>
                    <th>Source / Campaign</th>
                    <th>Date Captured</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leads as $lead)
                <tr>
                    <td class="font-monospace">#{{ $lead->id }}</td>
                    <td class="fw-bold text-dark">{{ $lead->full_name }}</td>
                    <td>
                        <div><i class="bi bi-telephone text-muted"></i> {{ $lead->phone }}</div>
                        @if($lead->whatsapp_number)
                            <div class="small text-success"><i class="bi bi-whatsapp"></i> {{ $lead->whatsapp_number }}</div>
                        @endif
                    </td>
                    <td>{{ $lead->email ?: '-' }}</td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $lead->utm_source ?: 'Direct' }}</span>
                        @if($lead->utm_campaign)
                            <span class="small text-muted d-block">{{ $lead->utm_campaign }}</span>
                        @endif
                    </td>
                    <td class="small text-muted">
                        {{ $lead->created_at->format('Y-m-d H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No leads captured for this landing page yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($leads->hasPages())
    <div class="p-3 border-top">
        {{ $leads->links() }}
    </div>
    @endif
</div>
@endsection
