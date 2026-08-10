@extends('layouts.admin')

@section('page_title', '🎯 Popup Manager Dashboard')

@section('content')
<div class="container-fluid py-4">

    <!-- TOP TITLE BAR -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">🎯 Popup Manager Dashboard</h3>
            <p class="text-muted mb-0">إدارة وتحكم احترافي بكافة الـ Popups المنبثقة، التوقيتات العشوائية، التتبع، ونسبة التحويل.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.popups.create') }}" class="btn btn-success fw-bold rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> + Create Popup جديد
            </a>
        </div>
    </div>

    <!-- STATS OVERVIEW CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">إجمالي الـ Popups</span>
                        <h2 class="fw-bold text-dark mb-0">{{ number_format($totalPopups) }}</h2>
                    </div>
                    <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-4">
                        <i class="bi bi-window-stack fs-2"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">الـ Popups النشطة الآن</span>
                        <h2 class="fw-bold text-success mb-0">{{ number_format($activeCount) }}</h2>
                    </div>
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-4">
                        <i class="bi bi-check-circle-fill fs-2"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">إجمالي المشاهدات (Views)</span>
                        <h2 class="fw-bold text-info mb-0">{{ number_format($totalViews) }}</h2>
                    </div>
                    <div class="p-3 bg-info bg-opacity-10 text-info rounded-4">
                        <i class="bi bi-eye-fill fs-2"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-bold d-block mb-1">معدل التحويل (CTR)</span>
                        <h2 class="fw-bold text-warning mb-0">{{ $overallConversionRate }}%</h2>
                    </div>
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-4">
                        <i class="bi bi-graph-up-arrow fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- POPUPS LIST GRID -->
    <h5 class="fw-bold text-dark mb-3">جميع الـ Popups المسجلة:</h5>

    <div class="row g-4">
        @forelse($popups as $popup)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1 small mb-2 d-inline-block">
                                    الموضع: {{ strtoupper($popup->layout ?? 'center') }}
                                </span>
                                <h5 class="fw-bold text-dark mb-1">{{ $popup->name }}</h5>
                                <code class="text-muted small">{{ $popup->slug }}</code>
                            </div>

                            <!-- ON / OFF TOGGLE SWITCH -->
                            <form method="POST" action="{{ route('admin.popups.toggle-active', $popup) }}">
                                @csrf
                                <div class="form-check form-switch">
                                    <input class="form-check-input fs-5 cursor-pointer" type="checkbox" onchange="this.form.submit()" @checked($popup->is_active)>
                                </div>
                            </form>
                        </div>

                        <!-- TRIGGER BADGE -->
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <span class="text-muted small d-block mb-1 fw-bold">نوع الزناد (Trigger):</span>
                            @php
                                $trig = $popup->trigger_settings['mode'] ?? 'random_time';
                            @endphp
                            @if($trig === 'random_time')
                                <span class="badge bg-warning text-dark"><i class="bi bi-shuffle me-1"></i> توقيت عشوائي ({{ $popup->trigger_settings['min_delay_seconds'] ?? 20 }} - {{ $popup->trigger_settings['max_delay_seconds'] ?? 60 }} ثانية)</span>
                            @elseif($trig === 'delay')
                                <span class="badge bg-info text-dark"><i class="bi bi-clock me-1"></i> تأخير زمني ({{ $popup->trigger_settings['delay_seconds'] ?? 10 }} ثانية)</span>
                            @elseif($trig === 'exit_intent')
                                <span class="badge bg-danger"><i class="bi bi-box-arrow-right me-1"></i> Exit Intent (محاولة المغادرة)</span>
                            @else
                                <span class="badge bg-secondary"><i class="bi bi-lightning me-1"></i> {{ strtoupper($trig) }}</span>
                            @endif
                        </div>

                        <!-- METRICS ROW -->
                        <div class="row text-center g-2 mb-3">
                            <div class="col-4">
                                <div class="p-2 border rounded-3 bg-light">
                                    <span class="text-muted text-xs d-block">مشاهدات</span>
                                    <span class="fw-bold text-dark fs-6">{{ number_format($popup->views_count) }}</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 border rounded-3 bg-light">
                                    <span class="text-muted text-xs d-block">نقرات</span>
                                    <span class="fw-bold text-primary fs-6">{{ number_format($popup->clicks_count) }}</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-2 border rounded-3 bg-light">
                                    <span class="text-muted text-xs d-block">تحويلات</span>
                                    <span class="fw-bold text-success fs-6">{{ number_format($popup->conversions_count) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- CARD FOOTER ACTIONS -->
                        <div class="d-flex align-items-center justify-content-between border-top pt-3">
                            <a href="{{ route('admin.popups.builder', $popup) }}" class="btn btn-sm btn-primary fw-bold rounded-pill px-3">
                                🛠️ Popup Builder
                            </a>

                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.popups.analytics', $popup) }}" class="btn btn-sm btn-outline-info" title="التحليلات والتتبع">
                                    <i class="bi bi-graph-up"></i>
                                </a>

                                <form method="POST" action="{{ route('admin.popups.duplicate', $popup) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="تكرار الـ Popup">
                                        <i class="bi bi-copy"></i>
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.popups.destroy', $popup) }}" class="d-inline" onsubmit="return confirm('هل أنت تأكد من حذف هذا الـ Popup؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 py-5 text-center bg-white rounded-4 shadow-sm">
                <i class="bi bi-window-stack fs-1 text-muted d-block mb-3"></i>
                <h5 class="fw-bold text-dark">لا توجد الـ Popups مسجلة حتى الآن</h5>
                <p class="text-muted mb-4">قم بإنشاء أول Popup احترافي للتحكم في المواعيد العشوائية، النماذج، والتتبع.</p>
                <a href="{{ route('admin.popups.create') }}" class="btn btn-success fw-bold rounded-pill px-4">
                    + إنشاء Popup جديد الآن
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $popups->links() }}
    </div>

</div>
@endsection
