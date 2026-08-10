@extends('layouts.admin')

@section('page_title', '📊 تحليلات الـ Popup: ' . $popup->name)

@section('content')
<div class="container-fluid py-4">

    <!-- TOP HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">📊 تحليلات الـ Popup: {{ $popup->name }}</h4>
            <p class="text-muted mb-0">تتبع المشاهدات والتفاعل وسجل الأحداث الحية للـ Popup.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.popups.builder', $popup) }}" class="btn btn-primary fw-bold rounded-pill px-4">
                🛠️ فتح الـ Builder
            </a>
            <a href="{{ route('admin.popups.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-3">
                ← الداشبورد
            </a>
        </div>
    </div>

    <!-- STATS CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
                <span class="text-muted small fw-bold d-block mb-1">المشاهدات (Impressions)</span>
                <h2 class="fw-bold text-dark mb-0">{{ number_format($impressions) }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
                <span class="text-muted small fw-bold d-block mb-1">النقرات (Clicks)</span>
                <h2 class="fw-bold text-primary mb-0">{{ number_format($clicks) }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
                <span class="text-muted small fw-bold d-block mb-1">التحويلات (Conversions)</span>
                <h2 class="fw-bold text-success mb-0">{{ number_format($conversions) }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white text-center">
                <span class="text-muted small fw-bold d-block mb-1">الإغلاقات (Closes)</span>
                <h2 class="fw-bold text-danger mb-0">{{ number_format($closes) }}</h2>
            </div>
        </div>
    </div>

    <!-- RECENT LOGS TABLE -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
        <h5 class="fw-bold text-dark mb-3">سجل الأحداث الأخيرة (Live Activity Stream):</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>الوقت</th>
                        <th>نوع الحدث (Event)</th>
                        <th>الصفحة (Page URL)</th>
                        <th>الجهاز (Device)</th>
                        <th>مصدر الحملة (UTM)</th>
                        <th>عنوان IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAnalytics as $log)
                        <tr>
                            <td><span class="small text-muted">{{ $log->created_at->diffForHumans() }}</span></td>
                            <td>
                                @if($log->event_type === 'impression')
                                    <span class="badge bg-info text-dark">مشاهدة (Impression)</span>
                                @elseif($log->event_type === 'click')
                                    <span class="badge bg-primary">نقر (Click)</span>
                                @elseif($log->event_type === 'conversion')
                                    <span class="badge bg-success">تحويل (Conversion)</span>
                                @else
                                    <span class="badge bg-secondary">إغلاق (Close)</span>
                                @endif
                            </td>
                            <td><code class="small text-dark">{{ Str::limit($log->page_url, 40) }}</code></td>
                            <td><span class="badge bg-light text-dark border">{{ strtoupper($log->device ?? 'DESKTOP') }}</span></td>
                            <td><span class="small text-muted">{{ $log->utm_source ?? '-' }}</span></td>
                            <td><span class="small text-muted font-monospace">{{ $log->ip_address ?? '-' }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">لا توجد أحداث مسجلة حتى الآن لهذا الـ Popup.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
