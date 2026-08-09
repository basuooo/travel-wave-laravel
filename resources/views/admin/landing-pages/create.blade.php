@extends('layouts.admin')

@section('page_title', __('admin.create_landing_page'))
@section('page_description', 'Choose between starting from scratch with a blank canvas or previewing and using a pre-built template.')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.landing-pages.index') }}" class="btn btn-sm btn-outline-secondary mb-2">← {{ __('admin.all_pages') }}</a>
    <h3 class="fw-bold mb-1">✨ {{ __('admin.create_landing_page') }}</h3>
    <p class="text-muted">اختر طريقة البدء: إنشاء صفحة فارغة من الصفر أو معاينة واستخدام أحد القوالب المجهزة مسبقاً.</p>
</div>

<!-- Option 1: Start From Scratch Banner -->
<div class="card admin-card p-4 mb-5 border-start border-4 border-primary bg-primary-subtle">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1"><i class="bi bi-file-earmark-plus text-primary me-2"></i> البدء بصفحة فارغة من الصفر (Blank Canvas)</h4>
            <p class="text-muted mb-0">افتح مسرح البناء الفضي وابنِ تصميمك خطوة بخطوة باستخدام مكتبة العناصر والأقسام.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary fw-bold px-4 py-2" data-bs-toggle="modal" data-bs-target="#scratchModal">
                إنشاء صفحة فارغة الآن →
            </button>
        </div>
    </div>
</div>

<!-- Option 2: Pre-built Master Templates Grid with Preview & Use Buttons -->
<div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="fw-bold m-0"><i class="bi bi-grid-1x2-fill text-warning me-2"></i> القوالب المجهزة مسبقاً (Master Templates)</h4>
    <span class="badge bg-dark fs-6">{{ $templates->count() }} قوالب متاحة</span>
</div>

<div class="row g-4 mb-5">
    @forelse($templates as $template)
    @php
        $tplName = $template->name_ar ?: $template->name_en;
        $previewUrl = route('admin.landing-pages.templates.preview', $template);
    @endphp
    <div class="col-md-6 col-lg-4">
        <div class="card admin-card h-100 border rounded-4 overflow-hidden shadow-sm hover-shadow d-flex flex-column">
            <!-- Visual Mock Preview Header -->
            <div class="bg-dark p-4 text-center text-white position-relative" style="min-height: 160px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-3 font-monospace">
                    {{ $template->category?->name_ar ?: ($template->category?->name_en ?: 'عام') }}
                </span>
                <div class="my-3">
                    <i class="{{ $template->category?->icon ?: 'bi-window' }} display-3 text-info"></i>
                </div>
                <div class="small text-white-50 font-monospace">Master Template v2.0</div>
            </div>

            <!-- Card Body -->
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <h5 class="fw-bold text-dark mb-2">{{ $tplName }}</h5>
                    <p class="text-muted small mb-3">
                        {{ $template->description_ar ?: ($template->description_en ?: 'قالب عالي التحويل مُصمم خصيصاً للحملات الإعلانية والسياحية.') }}
                    </p>
                </div>

                <!-- Action Buttons: Preview & Use with direct OnClick handlers -->
                <div class="d-grid gap-2 pt-3 border-top mt-auto">
                    <button type="button" class="btn btn-outline-primary fw-bold" 
                            onclick="previewTemplate({{ $template->id }}, '{{ addslashes($tplName) }}', '{{ $previewUrl }}')">
                        <i class="bi bi-eye me-1"></i> معاينة القالب
                    </button>
                    
                    <button type="button" class="btn btn-warning text-dark fw-bold" 
                            onclick="useTemplate({{ $template->id }}, '{{ addslashes($tplName) }}')">
                        <i class="bi bi-box-arrow-in-right me-1"></i> استخدام القالب وتنفيذه
                    </button>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        لا توجد قوالب مجهزة حالياً. يمكنك التأسيس بصفحة فارغة وحفظها كـ Template في أي وقت.
    </div>
    @endforelse
</div>

<!-- Modal 1: Scratch Creation -->
<div class="modal fade" id="scratchModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="{{ route('admin.landing-pages.store') }}" class="modal-content">
            @csrf
            <input type="hidden" name="creation_mode" value="scratch">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">إنشاء صفحة من الصفر</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">اسم الصفحة الداخلي *</label>
                    <input type="text" name="internal_name" class="form-control" placeholder="مثال: حملة تأشيرات الصيف 2026" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary fw-bold">فتح مسرح البناء الفارغ →</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Use Template Form -->
<div class="modal fade" id="useTemplateModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="post" action="{{ route('admin.landing-pages.store') }}" class="modal-content">
            @csrf
            <input type="hidden" name="creation_mode" value="template">
            <input type="hidden" name="template_id" id="modalTemplateId">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold text-dark">استخدام واستنساخ القالب</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 small mb-3">
                    سيتم استنساخ القالب <strong id="modalTemplateName"></strong> كنسخة جديدة منفصلة لحملتك دون التأثير على القالب الأصلي.
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">اسم الصفحة الداخلي للحملة *</label>
                    <input type="text" name="internal_name" class="form-control" placeholder="مثال: صفحة ترويج شنغن - إعلان فيسبوك" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-warning text-dark fw-bold">تأكيد واستخدام القالب 🚀</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Preview Template Full Screen Modal -->
<div class="modal fade" id="previewTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="height: 85vh;">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="previewModalTitle">معاينة القالب</h5>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-warning fw-bold" id="previewModalUseBtn">
                        استخدام هذا القالب 🚀
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-0 bg-light">
                <iframe id="previewIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Inline JavaScript Handlers -->
<script>
    function useTemplate(id, name) {
        document.getElementById('modalTemplateId').value = id;
        document.getElementById('modalTemplateName').textContent = name;

        if (typeof bootstrap !== 'undefined') {
            var useModal = new bootstrap.Modal(document.getElementById('useTemplateModal'));
            useModal.show();
        } else {
            $('#useTemplateModal').modal('show');
        }
    }

    function previewTemplate(id, name, previewUrl) {
        document.getElementById('previewModalTitle').textContent = 'معاينة القالب: ' + name;
        document.getElementById('previewIframe').src = previewUrl;

        document.getElementById('previewModalUseBtn').onclick = function() {
            var prevModalEl = document.getElementById('previewTemplateModal');
            if (typeof bootstrap !== 'undefined') {
                var prevModal = bootstrap.Modal.getInstance(prevModalEl) || new bootstrap.Modal(prevModalEl);
                prevModal.hide();
            } else {
                $('#previewTemplateModal').modal('hide');
            }
            useTemplate(id, name);
        };

        if (typeof bootstrap !== 'undefined') {
            var prevModal = new bootstrap.Modal(document.getElementById('previewTemplateModal'));
            prevModal.show();
        } else {
            $('#previewTemplateModal').modal('show');
        }
    }
</script>
@endsection
