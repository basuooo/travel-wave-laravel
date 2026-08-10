@extends('layouts.admin')

@section('page_title', '+ إنشاء Popup جديد')

@section('content')
<div class="container py-4 max-w-700">

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
            <div>
                <h4 class="fw-bold mb-1">🎯 إنشاء Popup جديد</h4>
                <p class="text-muted mb-0">حدد اسم ونوع وموزع الـ Popup للبدء في التصميم والضبط.</p>
            </div>
            <a href="{{ route('admin.popups.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-3">
                ← العودة للداشبورد
            </a>
        </div>

        <form action="{{ route('admin.popups.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="form-label fw-bold">اسم الـ Popup المرجعي <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control form-control-lg" placeholder="مثال: عرض تأشيرة فرنسا الجاري (خصم 20%)" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">موضع الـ Popup في الشاشة (Layout)</label>
                <select name="layout" class="form-select form-select-lg">
                    <option value="center" selected>منتصف الشاشة (Center Modal)</option>
                    <option value="top">شريط علوي (Top Banner)</option>
                    <option value="bottom">شريط سفلي (Bottom Bar)</option>
                    <option value="top_left">أعلى اليسار (Top Left)</option>
                    <option value="top_right">أعلى اليمين (Top Right)</option>
                    <option value="bottom_left">أسفل اليسار (Bottom Left)</option>
                    <option value="bottom_right">أسفل اليمين (Bottom Right)</option>
                    <option value="fullscreen">ملء الشاشة بالكامل (Full Screen Overlay)</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">نوع زناد الظهور الأساسي (Primary Trigger)</label>
                <select name="trigger_mode" class="form-select form-select-lg">
                    <option value="random_time" selected>🎲 توقيت عشوائي (Random Time - Min/Max Delay)</option>
                    <option value="delay">⏱️ تأخير زمني محدد (Time Delay)</option>
                    <option value="immediately">⚡ فور فتح الصفحة (Immediately)</option>
                    <option value="scroll">📜 عند التمرير بالصفحة (Scroll Percentage)</option>
                    <option value="exit_intent">🚪 عند محاولة خروج الزائر (Exit Intent)</option>
                    <option value="click">🖱️ عند النقر على عنصر أو زر (Click Element)</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">ربط نموذج من السيستم (اختياري)</label>
                <select name="assigned_lead_form_id" class="form-select form-select-lg">
                    <option value="">-- بدون نموذج (محتوى وزر فقط) --</option>
                    @foreach($forms as $form)
                        <option value="{{ $form->id }}">{{ $form->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="{{ route('admin.popups.dashboard') }}" class="btn btn-light rounded-pill px-4">إلغاء</a>
                <button type="submit" class="btn btn-success fw-bold rounded-pill px-5 shadow">
                    إنشاء والانتشار للمحرر ←
                </button>
            </div>
        </form>

    </div>

</div>
@endsection
