@extends('layouts.admin')

@section('title', 'تقارير الأداء وتحليلات الحملات — Reports')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">WhatsApp Campaign Reports</h3>
            <p class="text-muted small mb-0">تقارير معدلات التسليم والاستجابة والتصدير إلى Excel/CSV</p>
        </div>
    </div>

    @include('admin.whatsapp.nav')

    <!-- Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="text-muted small fw-bold">إجمالي الرسائل المعالجة</div>
                <div class="fs-2 fw-bold text-dark">{{ number_format($totalRecipients) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white border-start border-success border-4">
                <div class="text-muted small fw-bold">معدل التسليم الناجح (Delivery Rate)</div>
                <div class="fs-2 fw-bold text-success">{{ $deliveryRate }}%</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white border-start border-danger border-4">
                <div class="text-muted small fw-bold">إجمالي الرسائل الفاشلة</div>
                <div class="fs-2 fw-bold text-danger">{{ number_format($totalFailed) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 bg-white border-start border-warning border-4">
                <div class="text-muted small fw-bold">المستبعدين (Opt-Out / Blacklist)</div>
                <div class="fs-2 fw-bold text-warning">{{ number_format($totalOptOuts) }}</div>
            </div>
        </div>
    </div>

    <!-- Campaigns Reports List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-bold">
            تقارير جميع الحملات السابقة
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>اسم الحملة</th>
                            <th>النوع</th>
                            <th>WhatsApp Account</th>
                            <th>إجمالي الجمهور</th>
                            <th>المرسل</th>
                            <th>الفاشل</th>
                            <th>المستبعد</th>
                            <th>الحالة</th>
                            <th>تاريخ التأسيس</th>
                            <th>تصدير Report</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaigns as $camp)
                            <tr>
                                <td class="fw-bold">{{ $camp->name }}</td>
                                <td>
                                    <span class="badge {{ $camp->type === 'retargeting' ? 'bg-danger' : 'bg-primary' }}">
                                        {{ strtoupper($camp->type) }}
                                    </span>
                                </td>
                                <td>{{ $camp->account?->name ?? '-' }}</td>
                                <td class="fw-bold">{{ number_format($camp->total_contacts) }}</td>
                                <td class="text-success fw-bold">{{ number_format($camp->sent_count) }}</td>
                                <td class="text-danger fw-bold">{{ number_format($camp->failed_count) }}</td>
                                <td class="text-warning fw-bold">{{ number_format($camp->opt_out_count) }}</td>
                                <td><span class="badge bg-secondary">{{ $camp->status }}</span></td>
                                <td class="small text-muted">{{ $camp->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.whatsapp.reports.export-csv', $camp->id) }}" class="btn btn-sm btn-outline-success">
                                        📥 Export CSV
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">لا توجد تقارير حملات بعد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
