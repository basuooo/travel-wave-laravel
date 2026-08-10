@extends('layouts.admin')

@section('page_title', '🚀 Landing Page Builder New')
@section('page_description', 'إنشاء وبناء صفحات الهبوط الاحترافية وتفكيك ملفات الـ ZIP والتعديل الهجين والمرئي مع الحفظ والتصدير.')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 fw-bold mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 fw-bold mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- STATS CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-gradient bg-primary text-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small mb-1">إجمالي صفحات الهبوط</div>
                        <div class="display-6 fw-bold">{{ $totalPages }}</div>
                    </div>
                    <div class="fs-1 text-white-50"><i class="bi bi-layers-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-gradient bg-success text-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small mb-1">الصفحات المنشورة (Published)</div>
                        <div class="display-6 fw-bold">{{ $publishedCount }}</div>
                    </div>
                    <div class="fs-1 text-white-50"><i class="bi bi-check-circle-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-gradient bg-info text-white h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small mb-1">الصفحات النشطة (Active)</div>
                        <div class="display-6 fw-bold">{{ $activeCount }}</div>
                    </div>
                    <div class="fs-1 text-white-50"><i class="bi bi-toggle-on"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-gradient bg-warning text-dark h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-dark-50 small mb-1">إجمالي طلبات العملاء (Leads)</div>
                        <div class="display-6 fw-bold">{{ $totalLeads }}</div>
                    </div>
                    <div class="fs-1 text-dark-50"><i class="bi bi-people-fill"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN HEADER & ACTIONS -->
    <div class="card admin-card border-0 shadow-sm rounded-4 p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h3 class="h4 mb-1 font-weight-bold text-navy">🚀 صفحات الهبوط (Landing Pages Dashboard)</h3>
                <p class="text-muted mb-0">إدارة واختبار واستيراد وتصدير كافة صفحات الهبوط المستقلة.</p>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <!-- IMPORT ZIP BUTTON -->
                <button type="button" class="btn btn-outline-success fw-bold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#importZipModal">
                    📦 استيراد حزمة ZIP (Import ZIP)
                </button>
                <a href="{{ route('admin.landing-pages-new.templates') }}" class="btn btn-outline-secondary rounded-pill px-3">
                    🧩 القوالب (Templates)
                </a>
                <a href="{{ route('admin.landing-pages-new.create') }}" class="btn btn-primary tw-btn-primary fw-bold rounded-pill px-4">
                    ➕ إنشاء صفحة هبوط جديدة
                </a>
            </div>
        </div>

        <!-- SEARCH & FILTERS FORM -->
        <form method="GET" action="{{ route('admin.landing-pages-new.dashboard') }}" class="row g-3 mb-4 bg-light p-3 rounded-3 border">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control text-end" dir="rtl" placeholder="🔍 ابحث بالاسم، العنوان، أو الـ Slug..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="brand_id" class="form-select text-end">
                    <option value="">جميع العلامات التجارية (Brands)</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" @selected(request('brand_id') == $brand->id)>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select text-end">
                    <option value="">جميع الحالات (Status)</option>
                    <option value="draft" @selected(request('status') === 'draft')>مسودة (Draft)</option>
                    <option value="published" @selected(request('status') === 'published')>منشورة (Published)</option>
                    <option value="archived" @selected(request('status') === 'archived')>مؤرشفة (Archived)</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-navy w-100 fw-bold">تصفية النتائج</button>
            </div>
        </form>

        <!-- TABLE OF LANDING PAGES -->
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>اسم الصفحة / العنوان</th>
                        <th>الرابط (Slug)</th>
                        <th>العلامة التجارية</th>
                        <th>الحالة</th>
                        <th>التفعيل (ON / OFF)</th>
                        <th>تاريخ التحديث</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $item)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $item->internal_name }}</div>
                                <div class="small text-muted" dir="rtl">{{ $item->title_ar }}</div>
                            </td>
                            <td>
                                <code>/lp-new/{{ $item->slug }}</code>
                                <a href="{{ $item->publicUrl() }}" target="_blank" class="ms-1 text-secondary" title="معاينة رابط الصفحة"><i class="bi bi-box-arrow-up-right"></i></a>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $item->brand?->name ?: 'ترافل ويف' }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $item->status === 'published' ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $item->status === 'published' ? 'Published' : ucfirst($item->status) }}
                                </span>
                            </td>
                            <td>
                                <!-- ON / OFF TOGGLE SWITCH -->
                                <form method="POST" action="{{ route('admin.landing-pages-new.toggle-active', $item) }}" class="d-inline">
                                    @csrf
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input" type="checkbox" role="switch" onchange="this.form.submit()" @checked($item->is_active)>
                                        <label class="form-check-label small fw-bold {{ $item->is_active ? 'text-success' : 'text-danger' }}">
                                            {{ $item->is_active ? 'ON' : 'OFF' }}
                                        </label>
                                    </div>
                                </form>
                            </td>
                            <td>
                                <span class="small text-muted" title="{{ $item->updated_at }}">{{ $item->updated_at->diffForHumans() }}</span>
                            </td>
                            <td class="text-end">
                                 <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                                     <a href="{{ route('admin.landing-pages-new.builder-v2', $item) }}" class="btn btn-sm btn-success fw-bold" title="⭐ Builder V2 — Easy & Advanced">
                                         ⭐ Builder V2
                                     </a>
                                     <a href="{{ route('admin.landing-pages-new.builder', $item) }}" class="btn btn-sm btn-outline-primary fw-bold" title="🛠️ Builder V1 Legacy">
                                         🛠️ Builder V1
                                     </a>
                                    <a href="{{ route('admin.landing-pages-new.edit', $item) }}" class="btn btn-sm btn-outline-secondary" title="تعديل الإعدادات والـ SEO">
                                        ⚙️ Edit
                                    </a>
                                    <a href="{{ route('admin.landing-pages-new.export', $item) }}" class="btn btn-sm btn-outline-info" title="تصدير حزمة ZIP">
                                        📥 Export
                                    </a>
                                    <form method="POST" action="{{ route('admin.landing-pages-new.duplicate', $item) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="تكرار الصفحة">
                                            📋 Duplicate
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.landing-pages-new.destroy', $item) }}" onsubmit="return confirm('هل تريد نقل هذه الصفحة لسلة المهملات؟')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">🗑️ Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox display-4 d-block mb-3 text-muted opacity-50"></i>
                                لم يتم إنشاء أو استيراد أي صفحات هبوط حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $pages->links() }}
        </div>
    </div>
</div>

<!-- IMPORT ZIP MODAL -->
<div class="modal fade" id="importZipModal" tabindex="-1" aria-hidden="true" style="z-index: 1056;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('admin.landing-pages-new.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-navy text-white">
                    <h5 class="modal-title fw-bold">📦 استيراد صفحة هبوط من ملف ZIP</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                    <div class="mb-4">
                        <label class="form-label fw-bold fs-6">اختر ملف الحزمة (ZIP Package) <span class="text-danger">*</span></label>
                        <input type="file" name="zip_file" class="form-control form-control-lg" accept=".zip" required>
                        <div class="form-text mt-2 text-muted">💡 يجب أن يحتوي الملف على index.html ومجلدات css/ و js/ و images/ إن وجدت.</div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">الاسم الداخلي للصفحة (اختياري)</label>
                            <input type="text" name="internal_name" class="form-control" placeholder="مثل: حملة فرنسا الشتوية">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">الـ Slug (اختياري)</label>
                            <input type="text" name="slug" class="form-control" placeholder="france-winter-campaign">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">🚀 بدء الاستيراد والتفكيك</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('importZipModal');
        if (modalEl) {
            document.body.appendChild(modalEl);
        }
    });
</script>
@endsection

