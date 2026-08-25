@extends('layouts.admin')

@section('title', 'تفاصيل ومراقبة الحملة — ' . $campaign->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">{{ $campaign->name }}</h3>
            <p class="text-muted small mb-0">تقرير الحملة، حالة التسليم لكل مستلم، وتصدير البيانات</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.whatsapp.reports.export-csv', $campaign->id) }}" class="btn btn-outline-success font-weight-bold">
                📥 تصدير Txt / CSV Report
            </a>
            <a href="{{ route('admin.whatsapp.bulk.index') }}" class="btn btn-secondary">
                ← العودة للحملات
            </a>
        </div>
    </div>

    @include('admin.whatsapp.nav')

    <!-- Metrics Header -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="text-muted small fw-bold">إجمالي الجمهور (Total Contacts)</div>
                <div class="fs-2 fw-bold text-dark">{{ number_format($campaign->total_contacts) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white border-start border-success border-4">
                <div class="text-muted small fw-bold">الرسائل الناجحة (Sent)</div>
                <div class="fs-2 fw-bold text-success">{{ number_format($campaign->sent_count) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white border-start border-danger border-4">
                <div class="text-muted small fw-bold">الرسائل الفاشلة (Failed)</div>
                <div class="fs-2 fw-bold text-danger">{{ number_format($campaign->failed_count) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white border-start border-warning border-4">
                <div class="text-muted small fw-bold">قيد الانتظار (Pending)</div>
                <div class="fs-2 fw-bold text-warning">{{ number_format($campaign->pending_count) }}</div>
            </div>
        </div>
    </div>

    <!-- Recipients Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-bold">
            سجل مستلمي الحملة (Recipients Execution Log)
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>رقم الهاتف</th>
                            <th>حالة المطابقة (Contact Status)</th>
                            <th>حالة الإرسال (Delivery Status)</th>
                            <th>وقت الإرسال</th>
                            <th>تفاصيل الخطأ / الملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaign->recipients as $rec)
                            <tr>
                                <td>{{ $rec->id }}</td>
                                <td class="fw-bold">{{ $rec->contact_name ?: 'Contact' }}</td>
                                <td><span class="font-monospace text-primary fw-bold">{{ $rec->phone }}</span></td>
                                <td>
                                    <span class="badge {{ $rec->contact_status === 'previously_contacted' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $rec->contact_status }}
                                    </span>
                                </td>
                                <td>
                                    @if($rec->status === 'sent')
                                        <span class="badge bg-success">Sent</span>
                                    @elseif($rec->status === 'failed')
                                        <span class="badge bg-danger">Failed</span>
                                    @elseif($rec->status === 'processing')
                                        <span class="badge bg-info text-dark">Processing</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $rec->status }}</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $rec->sent_at ? $rec->sent_at->format('Y-m-d H:i:s') : '-' }}</td>
                                <td class="small text-danger">{{ $rec->error_message ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">لا توجد سجلات مستلمين لهذه الحملة.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
