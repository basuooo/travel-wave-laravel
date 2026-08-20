@extends('layouts.admin')

@php
    $isAr = app()->getLocale() === 'ar';
@endphp

@section('title', $isAr ? 'مكتبة القوالب الجاهزة' : 'Funnel Templates Library')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 fw-bold mb-1">🎨 {{ $isAr ? 'مكتبة القوالب الجاهزة' : 'Funnel Templates Library' }}</h1>
            <p class="text-muted mb-0">{{ $isAr ? 'اختر من مكتبة القوالب الاحترافية الجاهزة وعاينها مباشرة قبل الاستخدام' : 'Choose from high-converting ready-to-use funnel templates and preview them live' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.funnels.index') }}" class="btn btn-outline-secondary rounded-3">
                {{ $isAr ? '← قائمة الفانلات' : '← Funnels List' }}
            </a>
            <a href="{{ route('admin.funnels.create') }}" class="btn btn-primary rounded-3 fw-bold">
                ➕ {{ $isAr ? 'إنشاء فانل من الصفر' : 'Create from Scratch' }}
            </a>
        </div>
    </div>

    <!-- Search & Filter Controls Bar -->
    <form method="GET" action="{{ route('admin.funnels.templates.index') }}" class="card border-0 shadow-sm p-3 mb-4 rounded-4 bg-body-tertiary">
        <div class="row g-3 align-items-center">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif

            <!-- Search Field -->
            <div class="col-md-7 col-lg-8">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0 shadow-sm text-muted">
                        <iconify-icon icon="solar:magnifer-bold" width="18"></iconify-icon>
                    </span>
                    <input type="search" name="search" class="form-control border-0 shadow-sm py-2" value="{{ request('search') }}" placeholder="{{ $isAr ? 'بحث باسم القالب أو المجالات...' : 'Search templates by name or category...' }}">
                    @if(request('search') || request('sort'))
                        <a href="{{ route('admin.funnels.templates.index', array_filter(['category' => request('category')])) }}" class="btn btn-light shadow-sm text-muted">
                            ✕ {{ $isAr ? 'إعادة ضبط' : 'Reset' }}
                        </a>
                    @endif
                </div>
            </div>

            <!-- Sorting Select Dropdown -->
            <div class="col-md-5 col-lg-4 ms-auto">
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0 fw-bold small text-muted text-nowrap">⚡ {{ $isAr ? 'ترتيب حسب:' : 'Sort by:' }}</label>
                    <select name="sort" onchange="this.form.submit()" class="form-select border-0 shadow-sm py-2 rounded-3 fw-bold text-dark">
                        <option value="default" {{ ($sortBy ?? 'default') === 'default' ? 'selected' : '' }}>⭐ {{ $isAr ? 'الترتيب الافتراضي (المميز)' : 'Featured Order' }}</option>
                        <option value="latest" {{ ($sortBy ?? '') === 'latest' ? 'selected' : '' }}>🆕 {{ $isAr ? 'الأحدث إضافتاً (Newest Added)' : 'Newest Added' }}</option>
                        <option value="most_used" {{ ($sortBy ?? '') === 'most_used' ? 'selected' : '' }}>🔥 {{ $isAr ? 'الأكثر استخداماً (Most Used)' : 'Most Popular' }}</option>
                        <option value="name" {{ ($sortBy ?? '') === 'name' ? 'selected' : '' }}>🔤 {{ $isAr ? 'حسب الاسم أبجدياً (Alphabetical)' : 'Alphabetical' }}</option>
                    </select>
                </div>
            </div>
        </div>
    </form>

    <!-- Category Filter Pills -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('admin.funnels.templates.index', array_filter(['sort' => request('sort'), 'search' => request('search')])) }}" class="btn btn-sm rounded-pill px-3 {{ !request('category') ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ $isAr ? 'جميع القوالب (All)' : 'All Templates' }}
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('admin.funnels.templates.index', array_filter(['category' => $cat, 'sort' => request('sort'), 'search' => request('search')])) }}" class="btn btn-sm rounded-pill px-3 {{ request('category') === $cat ? 'btn-primary' : 'btn-outline-secondary' }}">
                {{ $cat }}
            </a>
        @endforeach
    </div>

    <!-- Templates Grid -->
    <div class="row g-4">
        @forelse($templates as $template)
            @php
                $themeColor = $template->schema_data['design_settings']['primary_color'] ?? '#2563eb';
                $createdDate = $template->created_at ? $template->created_at->format('Y-m-d') : date('Y-m-d');
                $usageCount = $template->funnels_count ?? 0;
            @endphp
            <div class="col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden d-flex flex-column transition-hover">
                    <!-- Template Card Top Cover -->
                    <div class="p-4 text-center position-relative text-white" style="background: linear-gradient(135deg, {{ $themeColor }}, #0f172a);">
                        <iconify-icon icon="solar:magic-stick-3-bold-duotone" width="56" class="opacity-75"></iconify-icon>
                        <span class="badge bg-white text-dark position-absolute top-0 end-0 m-3 shadow-sm" style="font-size: 11px;">{{ $template->category }}</span>
                    </div>

                    <div class="card-body p-4 d-flex flex-column justify-content-between flex-grow-1">
                        <div>
                            <h5 class="fw-bold text-dark mb-2">{{ $template->name }}</h5>
                            <p class="text-muted small mb-3" style="font-size: 12px; line-height: 1.5;">{{ $template->description }}</p>

                            <!-- Date Added & Usage Metadata -->
                            <div class="d-flex align-items-center justify-content-between text-muted small bg-light p-2 rounded-3 mb-2" style="font-size: 11px;">
                                <span title="تاريخ إضافة القالب">🗓️ {{ $isAr ? 'أُضيف:' : 'Added:' }} {{ $createdDate }}</span>
                                <span class="badge bg-primary-subtle text-primary fw-bold" title="عدد الفانلات التي استخدمت هذا القالب">🔥 {{ $usageCount }} {{ $isAr ? 'استخدام' : 'uses' }}</span>
                            </div>
                        </div>

                        <div class="d-flex flex-column gap-2 mt-3 pt-3 border-top">
                            <!-- Preview Button -->
                            <a href="{{ route('admin.funnels.templates.preview', $template) }}" class="btn btn-outline-primary w-100 fw-bold d-flex align-items-center justify-content-center gap-2 rounded-3">
                                <iconify-icon icon="solar:eye-bold" width="18"></iconify-icon>
                                <span>{{ $isAr ? 'معاينة تفاعلية (Preview)' : 'Live Preview' }}</span>
                            </a>

                            <!-- Use Template Form -->
                            <form action="{{ route('admin.funnels.templates.use', $template) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center gap-2 rounded-3">
                                    <iconify-icon icon="solar:play-circle-bold" width="18"></iconify-icon>
                                    <span>{{ $isAr ? 'استخدام القالب 🚀' : 'Use Template 🚀' }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <iconify-icon icon="solar:folder-open-line-duotone" width="64" class="text-muted mb-2"></iconify-icon>
                <p>{{ $isAr ? 'لم يتم العثور على قوالب تطابق خيارات البحث والتصنيف الحالية.' : 'No templates found matching current criteria.' }}</p>
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
