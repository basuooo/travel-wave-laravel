@extends('layouts.admin')

@php
    $isAr = app()->getLocale() === 'ar';
@endphp

@section('title', ($isAr ? 'التحليلات والإحصائيات | ' : 'Analytics | ') . $funnel->name)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 fw-bold mb-1">📊 {{ $isAr ? 'التحليلات والأداء' : 'Analytics & Performance' }} | {{ $funnel->name }}</h1>
            <p class="text-muted mb-0">{{ $isAr ? 'الرابط العام للفانل:' : 'Public URL:' }} <a href="{{ $funnel->publicUrl() }}" target="_blank">/f/{{ $funnel->slug }} 🔗</a></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.funnels.builder', $funnel) }}" class="btn btn-outline-primary fw-bold">
                ⚡ {{ $isAr ? 'فتح البيلدر' : 'Open Builder' }}
            </a>
            <a href="{{ route('admin.funnels.index') }}" class="btn btn-outline-secondary">
                {{ $isAr ? '← العودة للفانلات' : '← Back to Funnels' }}
            </a>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
                <div class="text-muted small fw-semibold">{{ $isAr ? 'إجمالي الزيارات والبدء' : 'Visitors / Starts' }}</div>
                <div class="h2 fw-bold mb-0 mt-1">{{ number_format($metrics['total_visitors']) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
                <div class="text-muted small fw-semibold">{{ $isAr ? 'المكتملة بنجاح' : 'Completions' }}</div>
                <div class="h2 fw-bold text-success mb-0 mt-1">{{ number_format($metrics['total_completions']) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
                <div class="text-muted small fw-semibold">{{ $isAr ? 'معدل التحويل' : 'Conversion Rate' }}</div>
                <div class="h2 fw-bold text-primary mb-0 mt-1">{{ $metrics['conversion_rate'] }}%</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-body">
                <div class="text-muted small fw-semibold">{{ $isAr ? 'متوسط نقاط التقييم' : 'Avg. Quiz Score' }}</div>
                <div class="h2 fw-bold text-warning mb-0 mt-1">{{ $metrics['average_score'] }} {{ $isAr ? 'نقطة' : 'pts' }}</div>
            </div>
        </div>
    </div>

    <!-- Step Drop-Off Analysis Table -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <h5 class="fw-bold mb-3">📉 {{ $isAr ? 'تحليل مسار الخطوات ونسب الانسحاب (Step Drop-Off)' : 'Step Drop-Off Funnel Analysis' }}</h5>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>{{ $isAr ? 'عنوان الخطوة' : 'Step Title' }}</th>
                        <th>{{ $isAr ? 'نوع الخطوة' : 'Step Type' }}</th>
                        <th>{{ $isAr ? 'الزوار الواصلين' : 'Visitors Reached' }}</th>
                        <th>{{ $isAr ? 'المنسحبين' : 'Drop-off Count' }}</th>
                        <th>{{ $isAr ? 'نسبة الانسحاب' : 'Drop-off Rate' }}</th>
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
                            <td colspan="5" class="text-muted text-center py-4">{{ $isAr ? 'لا توجد بيانات انسحاب مسجلة حتى الآن.' : 'No step drop-off data available.' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
