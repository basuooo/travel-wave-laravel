@extends('layouts.admin')

@section('title', __('admin.templates_library'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">🎨 {{ __('admin.templates_library') }}</h1>
            <p class="text-muted mb-0">اختر من مكتبة القوالب الاحترافية الجاهزة وعاينها مباشرة قبل الاستخدام</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.funnels.dashboard') }}" class="btn btn-outline-secondary rounded-3">
                ← لوحة الفانلات
            </a>
            <a href="{{ route('admin.funnels.create') }}" class="btn btn-primary rounded-3 fw-bold">
                ➕ إنشاء فانل من الصفر
            </a>
        </div>
    </div>

    <!-- Category Filter Pills -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('admin.funnels.templates.index') }}" class="btn btn-sm rounded-pill px-3 {{ !request('category') ? 'btn-primary' : 'btn-outline-secondary' }}">
            جميع القوالب (All)
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('admin.funnels.templates.index', ['category' => $cat]) }}" class="btn btn-sm rounded-pill px-3 {{ request('category') === $cat ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ $cat }}
            </a>
        @endforeach
    </div>

    <!-- Templates Grid -->
    <div class="row g-4">
        @forelse($templates as $template)
            <div class="col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden d-flex flex-column transition-hover">
                    <!-- Template Card Top Cover -->
                    <div class="bg-primary-subtle text-primary p-4 text-center position-relative">
                        <iconify-icon icon="solar:magic-stick-3-bold-duotone" width="56"></iconify-icon>
                        <span class="badge bg-primary position-absolute top-0 end-0 m-3">{{ $template->category }}</span>
                    </div>

                    <div class="card-body p-4 d-flex flex-column justify-content-between flex-grow-1">
                        <div>
                            <h5 class="fw-bold text-dark mb-2">{{ $template->name }}</h5>
                            <p class="text-muted small mb-3">{{ $template->description }}</p>
                        </div>

                        <div class="d-flex flex-column gap-2 mt-3 pt-3 border-top">
                            <!-- Preview Button -->
                            <a href="{{ route('admin.funnels.templates.preview', $template) }}" class="btn btn-outline-primary w-100 fw-bold d-flex align-items-center justify-content-center gap-2 rounded-3">
                                <iconify-icon icon="solar:eye-bold" width="18"></iconify-icon>
                                <span>معاينة تفاعلية (Preview)</span>
                            </a>

                            <!-- Use Template Form -->
                            <form action="{{ route('admin.funnels.templates.use', $template) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center gap-2 rounded-3">
                                    <iconify-icon icon="solar:play-circle-bold" width="18"></iconify-icon>
                                    <span>استخدام القالب 🚀</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <iconify-icon icon="solar:folder-open-line-duotone" width="64" class="text-muted mb-2"></iconify-icon>
                <p>لم يتم العثور على قوالب في هذا التصنيف.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    .transition-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .transition-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08) !important;
    }
</style>
@endsection
