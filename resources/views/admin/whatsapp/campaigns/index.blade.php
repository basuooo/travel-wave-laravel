@extends('layouts.admin')

@section('title', 'مراقبة وإدارة الحملات — Campaigns Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">Campaigns Management</h3>
            <p class="text-muted small mb-0">مراقبة تشغيل الحملات في الخلفية مع التحكم المباشر والنسخ والتوقف</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.whatsapp.retargeting.create') }}" class="btn btn-danger font-weight-bold">
                🎯 Retargeting Campaign
            </a>
            <a href="{{ route('admin.whatsapp.bulk.create') }}" class="btn btn-primary font-weight-bold">
                🚀 Bulk Campaign
            </a>
        </div>
    </div>

    @include('admin.whatsapp.nav')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>اسم الحملة</th>
                            <th>النوع</th>
                            <th>WhatsApp Account</th>
                            <th>الجمهور التراكمي</th>
                            <th>التقدم المباشر (Live Progress)</th>
                            <th>الحالة</th>
                            <th>تاريخ التأسيس</th>
                            <th>التحكم والإجراءات (Controls)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaigns as $camp)
                            @php($pct = $camp->total_contacts > 0 ? round(($camp->sent_count / $camp->total_contacts) * 100) : 0)
                            <tr>
                                <td>{{ $camp->id }}</td>
                                <td class="fw-bold">
                                    <a href="{{ route('admin.whatsapp.bulk.show', $camp->id) }}" class="text-decoration-none text-dark">
                                        {{ $camp->name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge {{ $camp->type === 'retargeting' ? 'bg-danger' : 'bg-primary' }}">
                                        {{ strtoupper($camp->type) }}
                                    </span>
                                </td>
                                <td>{{ $camp->account?->name ?? '-' }}</td>
                                <td>
                                    <span class="fw-bold">{{ number_format($camp->total_contacts) }}</span>
                                    @if($camp->type === 'retargeting')
                                        <div class="text-xxs text-muted">
                                            Prev: {{ $camp->previously_contacted_count }} | Not Prev: {{ $camp->not_previously_contacted_count }}
                                        </div>
                                    @endif
                                </td>
                                <td style="min-width: 180px;">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-success" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="small font-monospace fw-bold">{{ $pct }}%</span>
                                    </div>
                                    <div class="small text-muted font-monospace">
                                        {{ $camp->sent_count }} sent / {{ $camp->failed_count }} failed / {{ $camp->pending_count }} pending
                                    </div>
                                </td>
                                <td>
                                    @if($camp->status === 'running')
                                        <span class="badge bg-success">🟢 Running (In Background)</span>
                                    @elseif($camp->status === 'paused')
                                        <span class="badge bg-warning text-dark">⏸️ Paused</span>
                                    @elseif($camp->status === 'completed')
                                        <span class="badge bg-secondary">✅ Completed</span>
                                    @elseif($camp->status === 'scheduled')
                                        <span class="badge bg-info text-dark">📅 Scheduled</span>
                                    @else
                                        <span class="badge bg-dark">{{ $camp->status }}</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $camp->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @if($camp->status === 'running')
                                            <form action="{{ route('admin.whatsapp.bulk.pause', $camp->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-outline-warning" title="Pause">⏸️ Pause</button>
                                            </form>
                                        @elseif($camp->status === 'paused')
                                            <form action="{{ route('admin.whatsapp.bulk.resume', $camp->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-outline-success" title="Resume">▶️ Resume</button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.whatsapp.bulk.duplicate', $camp->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-outline-primary" title="Duplicate Campaign">📋 Duplicate</button>
                                        </form>

                                        <a href="{{ route('admin.whatsapp.bulk.show', $camp->id) }}" class="btn btn-outline-info" title="View Details & Reports">
                                            📊 Report
                                        </a>

                                        <form action="{{ route('admin.whatsapp.bulk.destroy', $camp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت تأكد من حذف هذه الحملة؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger" title="Delete">🗑️</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">لا توجد حملات منشأة بعد. اضغط "Retargeting Campaign" أو "Bulk Campaign" لإنشاء حملة جديدة.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($campaigns->hasPages())
                <div class="p-3">
                    {{ $campaigns->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
