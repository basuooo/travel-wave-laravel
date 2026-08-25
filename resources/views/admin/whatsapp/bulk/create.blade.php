@extends('layouts.admin')

@section('title', 'إنشاء حملة جماعية — Bulk Campaign')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1 text-primary">🚀 Bulk Campaign Wizard</h3>
            <p class="text-muted small mb-0">إنشاء حملة إرسال جماعية لقائمة أرقام مع التخصيص والجدولة والخلفية</p>
        </div>
    </div>

    @include('admin.whatsapp.nav')

    <form action="{{ route('admin.whatsapp.bulk.store') }}" method="POST" id="bulkWizardForm">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8">
                <!-- Step 1 & 2: Audience & Account Selection -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">
                        1 & 2. حساب WhatsApp المرتبط والجمهور المستهدف (Audience)
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">اسم الحملة الجماعية</label>
                            <input type="text" name="name" class="form-control" placeholder="مثال: عروض العمرة لشهر رمضان" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">اختر رقم WhatsApp المرتبط</label>
                            <select name="whatsapp_account_id" class="form-select" required>
                                <option value="">-- اختر الحساب --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->phone_number }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">أدخل أو الصق الأرقام (Paste Numbers or Upload)</label>
                            <textarea name="raw_numbers" id="raw_numbers" class="form-control font-monospace" rows="6" required placeholder="أدخل الأرقام هنا (رقم في كل سطر أو مفصولة بـ فاصلة):&#10;محمد | +201000000001&#10;علي | +34600000002"></textarea>
                            <div class="form-text small">سيقوم النظام بتنظيف الأرقام وتوحيد كود الدولة وحذف التكرار تلقائياً عند الحفظ.</div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Message Builder -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">
                        3. محتوى الرسالة وتخصيص المتغيرات (Message Builder)
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">اختر من مكتبة Templates</label>
                            <select id="bulkTemplateSelect" class="form-select mb-2">
                                <option value="">-- اختر قالب محدد --</option>
                                @foreach($templates as $tpl)
                                    <option value="{{ $tpl->content }}">{{ $tpl->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">نص الرسالة</label>
                            <textarea name="message_content" id="bulk_message_content" class="form-control" rows="6" required placeholder="مرحباً {{name}}، يسعدنا تقديم عرض خاص بخصوص {{service}}..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Scheduling & Throttling -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">
                        4. إعدادات الفاصل الزمني والجدولة والحدود اليومية
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">نوع Delay الإرسال</label>
                                <select name="interval_type" class="form-select">
                                    <option value="random">Random Delay (30s - 90s)</option>
                                    <option value="fixed">Fixed Delay (60s)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">أقل فاصل زمني (ثواني)</label>
                                <input type="number" name="interval_min_sec" class="form-control" value="30" min="5">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">أقصى فاصل زمني (ثواني)</label>
                                <input type="number" name="interval_max_sec" class="form-control" value="90" min="5">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">الحد اليومي للإرسال (Daily Limit)</label>
                                <input type="number" name="daily_limit" class="form-control" placeholder="مثال: 500 رسالة يومياً">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">توقيت البدء</label>
                                <select name="schedule_type" class="form-select">
                                    <option value="now">بدء الإرسال في الخلفية فوراً</option>
                                    <option value="scheduled">جدولة لبدء في تاريخ ووقت محدد</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Live Message Preview Screen -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4 sticky-top" style="top: 20px;">
                    <div class="card-header bg-success text-white fw-bold">
                        📱 معاينة حية للرسالة (WhatsApp Live Preview)
                    </div>
                    <div class="card-body bg-light">
                        <div class="p-3 bg-white rounded shadow-sm border position-relative" style="min-height: 200px;">
                            <div class="d-flex align-items-center mb-2 pb-2 border-bottom">
                                <div class="rounded-circle bg-success text-white p-2 me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    WA
                                </div>
                                <div>
                                    <div class="fw-bold small">معاينة الرسالة للعميل</div>
                                    <div class="text-muted text-xxs">الآن</div>
                                </div>
                            </div>

                            <div id="livePreviewText" class="small" style="white-space: pre-wrap;">
                                مرحباً [اسم العميل]، يسعدنا تقديم عرض خاص بخصوص خدماتنا...
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-center p-3">
                        <button type="submit" class="btn btn-primary btn-lg w-100 font-weight-bold">
                            Confirm & Launch Campaign
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('bulkTemplateSelect').addEventListener('change', function() {
    if(this.value) {
        document.getElementById('bulk_message_content').value = this.value;
        updatePreview();
    }
});

document.getElementById('bulk_message_content').addEventListener('input', updatePreview);

function updatePreview() {
    const val = document.getElementById('bulk_message_content').value;
    const preview = val.replace(/\{\{name\}\}/g, 'أحمد محمود')
                       .replace(/\{\{phone\}\}/g, '+201012345678')
                       .replace(/\{\{service\}\}/g, 'تأشيرة إسبانيا')
                       .replace(/\{\{country\}\}/g, 'إسبانيا')
                       .replace(/\{\{employee\}\}/g, 'خدمة العملاء');

    document.getElementById('livePreviewText').innerText = preview || 'أدخل نص الرسالة للفي المعاينة الحية...';
}
</script>
@endsection
