@extends('layouts.admin')

@section('page_title', 'قوالب صفحات الهبوط')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">🧩 مكتبة القوالب الجاهزة (Templates Library)</h4>
                <p class="text-muted mb-0">قوالب مصممة مسبقاً لبدء الحملات بسرعة.</p>
            </div>
            <a href="{{ route('admin.landing-pages-new.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
                ➕ إنشاء صفحة بقالب جديد
            </a>
        </div>

        <div class="row g-4">
            @forelse($templates as $tpl)
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                        <div class="bg-light text-center py-5 border-bottom">
                            <i class="bi bi-layout-wysiwyg display-1 text-primary"></i>
                        </div>
                        <div class="card-body p-4">
                            <span class="badge text-bg-info mb-2">{{ $tpl->category?->name_ar ?: 'قوالب عامة' }}</span>
                            <h5 class="fw-bold">{{ $tpl->name_ar ?: $tpl->name_en }}</h5>
                            <p class="text-muted small mb-3">{{ $tpl->description_ar ?: 'قالب جاهز للاستخدام المباشر والتعديل المرئي.' }}</p>
                            <a href="{{ route('admin.landing-pages-new.create') }}?template_id={{ $tpl->id }}" class="btn btn-outline-primary w-100 rounded-pill fw-bold">
                                استخدام هذا القالب
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-layers display-4 d-block mb-3 opacity-50"></i>
                    لا توجد قوالب مخصصة حالياً، يمكنك إنشاء أول صفحة من الصفر وحفظها كقالب.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
