@extends('layouts.admin')

@section('page_title', '⚡ صفحة الاختصارات (Shortcuts)')
@section('page_description', 'وصول سريع ومباشر لأهم الأقسام والمهام في النظام.')

@section('content')
<style>
.shortcut-card {
    transition: all 0.25s ease-in-out;
    border-radius: 14px;
    border: 1px solid rgba(0, 0, 0, 0.08);
}
.shortcut-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1) !important;
    border-color: #0d6efd;
}
.shortcut-icon-box {
    width: 54px;
    height: 54px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
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
                وصول سريع ومباشر للأدوات والأقسام المعتمدة في النظام وفقاً لصلاحيات حسابك.
            </p>
        </div>
        @if($canManageShortcuts)
            <button type="button" class="btn btn-light text-primary fw-bold px-4 py-2 shadow-sm rounded-3 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#configureShortcutsModal">
                <span>⚙️</span> <span>تخصيص وتحديد الاختصارات</span>
            </button>
        @endif
    </div>
    <!-- Background overlay decoration -->
    <div style="position: absolute; right: -20px; bottom: -30px; font-size: 10rem; opacity: 0.1; line-height: 1; pointer-events: none;">⚡</div>
</div>

<!-- Active User Shortcuts Grid -->
@if(count($userShortcuts) > 0)
    <div class="row g-3">
        @foreach($userShortcuts as $shortcut)
            <div class="col-md-6 col-lg-4">
                <div class="card admin-card shortcut-card h-100 p-4 shadow-sm bg-white position-relative">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                        <div class="shortcut-icon-box bg-{{ $shortcut['badge_color'] ?? 'primary' }} bg-opacity-10 text-{{ $shortcut['badge_color'] ?? 'primary' }}">
                            {{ $shortcut['icon'] ?? '🔗' }}
                        </div>
                        <span class="badge text-bg-{{ $shortcut['badge_color'] ?? 'secondary' }} rounded-pill px-3 py-1">
                            {{ $shortcut['category'] ?? 'عام' }}
                        </span>
                    </div>

                    <h3 class="h5 fw-bold text-dark mb-2">{{ $shortcut['title_ar'] }}</h3>
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
@else
    <div class="card admin-card p-5 text-center shadow-sm rounded-4">
        <div class="fs-1 mb-3 text-muted">⚡</div>
        <h4 class="fw-bold text-dark mb-2">لا توجد اختصارات متاحة حالياً</h4>
        <p class="text-muted mb-4">إما أنه لم يتم تفعيل اختصارات بالنظام بعد، أو لا تملك الصلاحيات الكافية لرؤية الاختصارات المحددة.</p>
        @if($canManageShortcuts)
            <div>
                <button type="button" class="btn btn-primary fw-bold px-4 py-2" data-bs-toggle="modal" data-bs-target="#configureShortcutsModal">
                    ⚙️ اضغط هنا لتخصيص وتحديد الاختصارات
                </button>
            </div>
        @endif
    </div>
@endif

<!-- Admin Configuration Modal -->
@if($canManageShortcuts)
<div class="modal fade" id="configureShortcutsModal" tabindex="-1" aria-labelledby="configureShortcutsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form method="post" action="{{ route('admin.shortcuts.update') }}" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-primary text-white p-4">
                <h5 class="modal-title fw-bold" id="configureShortcutsModalLabel">⚙️ تحديد الاختصارات المتاحة في النظام</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-4">
                    اختر الخصائص والأدوات التي ترغب في ظهورها بصفحة الاختصارات للنظام. (ملاحظة: سيتم حجب أي اختصار تلقائياً عن البائع أو المستخدم إذا كان لا يمتلك الصلاحية المطلوبة).
                </p>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold text-dark">قائمة الاختصارات المتاحة:</span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAllShortcuts(true)">تحديد الكل</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAllShortcuts(false)">إلغاء الكل</button>
                    </div>
                </div>

                <div class="row g-3">
                    @foreach($allRegistry as $key => $item)
                        <div class="col-md-6">
                            <div class="form-check form-switch p-3 bg-light rounded-3 border h-100">
                                <input class="form-check-input shortcut-checkbox ms-0 me-2" type="checkbox" name="shortcuts[]" value="{{ $key }}" id="shortcut_check_{{ $key }}" @checked(in_array($key, $enabledKeys))>
                                <label class="form-check-label fw-bold text-dark cursor-pointer" for="shortcut_check_{{ $key }}">
                                    <span class="fs-5 me-1">{{ $item['icon'] }}</span>
                                    <span>{{ $item['title_ar'] }}</span>
                                    <div class="small text-muted fw-normal mt-1">{{ $item['description_ar'] }}</div>
                                    <span class="badge text-bg-secondary text-white mt-2 small">الصلاحية: {{ $item['permission'] ?: 'عام للكل' }}</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer bg-light p-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary fw-bold px-4">حفظ الاختصارات المحددة 💾</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAllShortcuts(state) {
    document.querySelectorAll('.shortcut-checkbox').forEach(cb => cb.checked = state);
}
</script>
@endif
@endsection
