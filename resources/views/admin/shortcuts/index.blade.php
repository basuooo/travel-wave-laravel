@extends('layouts.admin')

@section('page_title', '⚡ صفحة الاختصارات (Shortcuts)')
@section('page_description', 'وصول سريع ومباشر لكافة أقسام وخصائص النظام المحددة وفقاً لصلاحيات حسابك.')

@section('content')
<style>
.shortcut-card {
    transition: all 0.25s cubic-bezier(0.165, 0.84, 0.44, 1);
    border-radius: 14px;
    border: 1px solid rgba(0, 0, 0, 0.08);
}
.shortcut-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.12) !important;
    border-color: #0d6efd;
}
.shortcut-icon-box {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.7rem;
}
.category-divider {
    border: 0;
    height: 3px;
    background: linear-gradient(to right, #0d6efd, #6c757d, transparent);
    opacity: 0.25;
    margin: 2.5rem 0;
}
</style>

<!-- Top Header Banner -->
<div class="card admin-card border-0 bg-primary text-white p-4 mb-4 shadow-sm rounded-4 position-relative overflow-hidden">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 position-relative" style="z-index: 2;">
        <div>
            <h2 class="h3 fw-extrabold mb-1 text-white d-flex align-items-center gap-2">
                <span>⚡</span> <span>صفحة الاختصارات التفاعلية</span>
            </h2>
            <p class="mb-0 text-white-50 fs-6">
                قوائم واختصارات سريعة مقسمة حسب الأقسام للوصول المباشر للأدوات المعتمدة في النظام.
            </p>
        </div>
        @if($canManageShortcuts)
            <button type="button" class="btn btn-light text-primary fw-bold px-4 py-2 shadow-sm rounded-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#configureShortcutsModal">
                <span>⚙️</span> <span>تحديد الاختصارات والخيارات المتاحة</span>
            </button>
        @endif
    </div>
    <!-- Background overlay decoration -->
    <div style="position: absolute; right: -20px; bottom: -30px; font-size: 10rem; opacity: 0.1; line-height: 1; pointer-events: none;">⚡</div>
</div>

<!-- Active User Shortcuts Grouped by Category -->
@if(count($groupedShortcuts) > 0)
    @php $catIndex = 0; @endphp
    @foreach($groupedShortcuts as $catKey => $category)
        @if($catIndex > 0)
            <!-- Horizontal Divider between Sections as requested -->
            <hr class="category-divider">
        @endif
        @php $catIndex++; @endphp

        <div class="mb-4">
            <!-- Category Section Header -->
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="fs-3">{{ $category['meta']['icon'] ?? '📌' }}</span>
                <div>
                    <h3 class="h4 fw-bold text-dark mb-0">{{ $category['meta']['title_ar'] }}</h3>
                    @if(!empty($category['meta']['description']))
                        <div class="text-muted small">{{ $category['meta']['description'] }}</div>
                    @endif
                </div>
                <span class="badge text-bg-primary fs-6 ms-auto px-3 py-2">
                    {{ count($category['items']) }} اختصار متاح
                </span>
            </div>

            <!-- Cards Grid for this Category -->
            <div class="row g-3">
                @foreach($category['items'] as $shortcut)
                    <div class="col-md-6 col-lg-4">
                        <div class="card admin-card shortcut-card h-100 p-4 shadow-sm bg-white position-relative">
                            <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                                <div class="shortcut-icon-box bg-{{ $shortcut['badge_color'] ?? 'primary' }} bg-opacity-10 text-{{ $shortcut['badge_color'] ?? 'primary' }}">
                                    {{ $shortcut['icon'] ?? '🔗' }}
                                </div>
                                <span class="badge text-bg-light border text-secondary px-3 py-1">
                                    {{ $shortcut['permission'] ?: 'عام للكل' }}
                                </span>
                            </div>

                            <h4 class="h5 fw-bold text-dark mb-2">{{ $shortcut['title_ar'] }}</h4>
                            <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.5;">
                                {{ $shortcut['description_ar'] }}
                            </p>

                            <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                                <span class="small text-muted fw-semibold">وصول مباشر 🚀</span>
                                <a href="{{ $shortcut['url'] }}" class="btn btn-sm btn-{{ $shortcut['badge_color'] ?? 'primary' }} fw-bold px-4 rounded-3 d-inline-flex align-items-center gap-1">
                                    فتح الاختصار ⬅️
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@else
    <div class="card admin-card p-5 text-center shadow-sm rounded-4">
        <div class="fs-1 mb-3 text-muted">⚡</div>
        <h4 class="fw-bold text-dark mb-2">لا توجد اختصارات متاحة حالياً</h4>
        <p class="text-muted mb-4">إما أنه لم يتم تفعيل اختصارات بالنظام بعد، أو لا تملك الصلاحيات الكافية لرؤية الخيارات المحددة.</p>
        @if($canManageShortcuts)
            <div>
                <button type="button" class="btn btn-primary fw-bold px-4 py-2" data-bs-toggle="modal" data-bs-target="#configureShortcutsModal">
                    ⚙️ اضغط هنا لتحديد وتخصيص الخيارات والخصائص
                </button>
            </div>
        @endif
    </div>
@endif

<!-- Admin Configuration Modal -->
@if($canManageShortcuts)
<div class="modal fade" id="configureShortcutsModal" tabindex="-1" aria-labelledby="configureShortcutsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <form method="post" action="{{ route('admin.shortcuts.update') }}" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-primary text-white p-4">
                <div>
                    <h5 class="modal-title fw-bold" id="configureShortcutsModalLabel">⚙️ تحديد وتخصيص كافة الاختصارات والخيارات بالنظام</h5>
                    <div class="small text-white-50 mt-1">تحديد الاختصارات التي تظهر لكل قسم. (ملاحظة: سيتم حجب أي اختيار عن البائع تلقائياً إذا كان لا يمتلك الصلاحية المطلوبة).</div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-3 shadow-sm border">
                    <span class="fw-bold text-dark fs-6">التحكم في تحديد كافة الاختصارات دفعة واحدة:</span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-primary fw-bold" onclick="toggleAllSystemShortcuts(true)">✅ تحديد الكل في كل الأقسام</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary fw-bold" onclick="toggleAllSystemShortcuts(false)">❌ إلغاء الكل</button>
                    </div>
                </div>

                <!-- Accordion / Sections for each category -->
                <div class="accordion" id="shortcutsCategoryAccordion">
                    @foreach($registryByCat as $catKey => $catData)
                        <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm overflow-hidden">
                            <h2 class="accordion-header" id="heading_{{ $catKey }}">
                                <button class="accordion-button bg-white text-dark fw-bold fs-6 py-3 px-4 shadow-none d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ $catKey }}" aria-expanded="true" aria-controls="collapse_{{ $catKey }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fs-4">{{ $catData['meta']['icon'] }}</span>
                                        <span>{{ $catData['meta']['title_ar'] }}</span>
                                        <span class="badge text-bg-primary ms-2">{{ count($catData['items']) }} خيار فرعي</span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse_{{ $catKey }}" class="accordion-collapse collapse show" aria-labelledby="heading_{{ $catKey }}">
                                <div class="accordion-body p-4 bg-white border-top">
                                    <div class="d-flex justify-content-end mb-3">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" onclick="toggleCategoryShortcuts('{{ $catKey }}', true)">تحديد قسم {{ $catData['meta']['title_ar'] }}</button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="toggleCategoryShortcuts('{{ $catKey }}', false)">إلغاء هذا القسم</button>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        @foreach($catData['items'] as $key => $item)
                                            <div class="col-md-6 col-lg-4">
                                                <div class="form-check form-switch p-3 bg-light rounded-3 border h-100">
                                                    <input class="form-check-input shortcut-checkbox shortcut-cat-{{ $catKey }} ms-0 me-2" type="checkbox" name="shortcuts[]" value="{{ $key }}" id="shortcut_check_{{ $key }}" @checked(in_array($key, $enabledKeys))>
                                                    <label class="form-check-label fw-bold text-dark cursor-pointer w-100" for="shortcut_check_{{ $key }}">
                                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                                            <span>
                                                                <span class="fs-5 me-1">{{ $item['icon'] }}</span>
                                                                <span>{{ $item['title_ar'] }}</span>
                                                            </span>
                                                        </div>
                                                        <div class="small text-muted fw-normal mt-1" style="font-size: 0.85rem; line-height: 1.4;">{{ $item['description_ar'] }}</div>
                                                        <div class="mt-2">
                                                            <span class="badge text-bg-secondary small">الصلاحية: {{ $item['permission'] ?: 'متاح للكل' }}</span>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer bg-white p-3 border-top">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary fw-bold px-5">حفظ التحديدات والاختصارات 💾</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAllSystemShortcuts(state) {
    document.querySelectorAll('.shortcut-checkbox').forEach(cb => cb.checked = state);
}
function toggleCategoryShortcuts(catKey, state) {
    document.querySelectorAll('.shortcut-cat-' + catKey).forEach(cb => cb.checked = state);
}
</script>
@endif
@endsection
