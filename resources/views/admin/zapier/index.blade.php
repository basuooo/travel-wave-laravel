@extends('layouts.admin')

@section('page_title', 'ربط Zapier السريع - 1-Click Zapier Integration')
@section('page_description', 'أسهل وأسرع طريقة لربط نظام Travel Wave مع Zapier بدون أكواد أو تعقيد')

@section('content')
<div class="container-fluid px-0">

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-4"></i>
            <div class="fw-bold">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('plain_text_token'))
        <div class="alert alert-warning border-2 border-warning p-4 mb-4 rounded-3 shadow-sm">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-exclamation-triangle-fill text-warning fs-3 me-2"></i>
                <h5 class="mb-0 text-dark fw-bold">احتفظ بمفتاح الـ API الخاص بك الآن!</h5>
            </div>
            <p class="text-muted small mb-3">هذا المفتاح يظهر <strong>مرة واحدة فقط</strong> لأسباب أمنية. انسخه وادخله في Zapier:</p>
            <div class="input-group mb-2">
                <input type="text" id="plainTokenInput" class="form-control font-monospace fw-bold fs-6 bg-white" value="{{ session('plain_text_token') }}" readonly>
                <button class="btn btn-warning fw-bold px-4" onclick="copyToClipboard('plainTokenInput', this)">
                    <i class="bi bi-clipboard me-1"></i> نسخ المفتاح
                </button>
            </div>
        </div>
    @endif

    {{-- Main Header Card --}}
    <div class="card admin-card border-0 mb-4 shadow-sm bg-gradient text-white" style="background: linear-gradient(135deg, #ff4a00 0%, #ff7300 100%);">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="bg-white rounded-circle p-3 me-3 text-danger d-flex align-items-center justify-content-center shadow-sm" style="width: 65px; height: 65px;">
                        <i class="bi bi-lightning-charge-fill fs-1" style="color: #ff4a00;"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1">الربط السريع مع Zapier (في 30 ثانية) 🚀</h3>
                        <p class="mb-0 text-white-50">لا تحتاج لأي خبرة برمجية أو منصات معقدة! استخدم طريقة الـ Webhook المباشرة لربط السيستم فوراً.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ⚡ METHOD 1 & METHOD 2 - FASTEST WAY --}}
    <div class="row g-4 mb-4">

        {{-- 🟢 OUTGOING: Travel Wave -> Zapier --}}
        <div class="col-lg-6">
            <div class="card admin-card h-100 shadow-sm border-top border-4 border-warning">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-arrow-up-right-circle-fill text-warning me-2 fs-5"></i>
                        1️⃣ إرسال البيانات من Travel Wave إلى Zapier
                    </h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <p class="text-muted small mb-3">
                            عند إضافة حجز أو عميل جديد في السيستم، سيتم إرساله تلقائياً إلى Zapier لربطه مع Google Sheets, WhatsApp, Gmail أو غيرها.
                        </p>

                        <div class="bg-light p-3 rounded-3 mb-4 border">
                            <h6 class="fw-bold text-dark mb-2">كيف تحصل على الرابط في 3 خطوات؟</h6>
                            <ol class="ps-3 mb-0 small text-muted">
                                <li class="mb-1">افتح موقع <a href="https://zapier.com/app/zaps" target="_blank" class="fw-bold text-decoration-none">Zapier.com</a> وأنشد Zap جديدة.</li>
                                <li class="mb-1">في خطوة الـ Trigger اختر التطبيق: <strong>Webhooks by Zapier</strong>.</li>
                                <li class="mb-1">اختر الإجراء: <strong>Catch Hook</strong> وانسخ الرابط الموفر لك، وضعْه بالمربع بالأسفل:</li>
                            </ol>
                        </div>

                        <form action="{{ route('admin.zapier.quick-webhook.save') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark small">رابط Zapier Catch Hook الخاصة بك:</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-link-45deg text-warning fs-5"></i></span>
                                    <input type="url" name="quick_webhook_url" class="form-control font-monospace" placeholder="https://hooks.zapier.com/hooks/catch/123456/abcdef/" value="{{ $quickWebhook?->target_url }}" required>
                                </div>
                                <div class="form-text small">ضع رابط الـ Catch Hook المنسوخ من Zapier لربطه مباشرة مع السيستم.</div>
                            </div>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-warning w-100 fw-bold py-2 shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> حفظ وتفعيل الإرسال السريع لـ Zapier
                        </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔵 INCOMING: Zapier -> Travel Wave --}}
        <div class="col-lg-6">
            <div class="card admin-card h-100 shadow-sm border-top border-4 border-primary">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-arrow-down-left-circle-fill text-primary me-2 fs-5"></i>
                        2️⃣ استقبال العملاء من Zapier إلى Travel Wave
                    </h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <p class="text-muted small mb-3">
                            إذا كان لديك إعلانات فيسبوك، Google Forms، أو Typeform وتريد أن يدخل العملاء الجدد إلى السيستم تلقائياً:
                        </p>

                        <div class="bg-light p-3 rounded-3 mb-4 border">
                            <h6 class="fw-bold text-dark mb-2">كيف تضبط استقبال العملاء في Zapier؟</h6>
                            <ol class="ps-3 mb-0 small text-muted">
                                <li class="mb-1">في خطوة الـ Action داخل Zapier اختر التطبيق: <strong>Webhooks by Zapier</strong>.</li>
                                <li class="mb-1">اختر نوع الطلب: <strong>POST</strong>.</li>
                                <li class="mb-1">ضع رابط الاستقبال الثابت الخاص بسيستمك أدناه:</li>
                            </ol>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">رابط استقبال العملاء الثابت الخاص بسيستمك:</label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary-subtle text-primary fw-bold">POST</span>
                                <input type="text" id="incomingWebhookUrl" class="form-control font-monospace bg-light fw-bold" value="{{ $baseUrl }}/api/v1/zapier/incoming-lead" readonly>
                                <button class="btn btn-primary fw-bold" onclick="copyToClipboard('incomingWebhookUrl', this)">
                                    <i class="bi bi-clipboard me-1"></i> نسخ
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-info py-2 px-3 mb-0 small">
                            <strong>أسماء الحقول المتاحة للإرسال:</strong>
                            <span class="font-monospace">full_name</span>, <span class="font-monospace">phone</span>, <span class="font-monospace">email</span>, <span class="font-monospace">destination</span>, <span class="font-monospace">message</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Advanced Auth Section (Collapsed / Optional) --}}
    <div class="card admin-card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-muted">
                <i class="bi bi-gear-wide-connected me-2"></i> الطريقة المتقدمة: مفاتيح الـ API للمطورين (اختياري)
            </h6>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#advancedApiSection" aria-expanded="false" aria-controls="advancedApiSection">
                <i class="bi bi-chevron-down"></i> عرض / إخفاء المفاتيح
            </button>
        </div>
        <div class="collapse" id="advancedApiSection">
            <div class="card-body border-top">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h6 class="fw-bold mb-3">توليد مفاتيح Access Token (Sanctum)</h6>
                        <form action="{{ route('admin.zapier.tokens.generate') }}" method="POST" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary btn-sm fw-bold">
                                <i class="bi bi-key-fill me-1"></i> توليد مفتاح API جديد
                            </button>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>اسم المفتاح</th>
                                        <th>التاريخ</th>
                                        <th class="text-end">حذف</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tokens as $token)
                                        <tr>
                                            <td class="small fw-bold">{{ $token->name }}</td>
                                            <td class="small text-muted">{{ $token->created_at->format('Y-m-d') }}</td>
                                            <td class="text-end">
                                                <form action="{{ route('admin.zapier.tokens.revoke', $token->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-link text-danger p-0">حذف</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-2 text-muted small">لا توجد مفاتيح API مسجلة</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <h6 class="fw-bold mb-3">مسارات الـ REST Hooks المتقدمة</h6>
                        <div class="small font-monospace bg-light p-3 rounded border">
                            <div><strong>Auth Test:</strong> GET {{ $baseUrl }}/api/v1/zapier/me</div>
                            <div class="mt-2"><strong>Subscribe:</strong> POST {{ $baseUrl }}/api/v1/zapier/subscribe</div>
                            <div class="mt-2"><strong>Unsubscribe:</strong> DELETE {{ $baseUrl }}/api/v1/zapier/unsubscribe</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function copyToClipboard(inputId, btn) {
    var copyText = document.getElementById(inputId);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    
    var originalText = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check-lg"></i> تم النسخ!';
    btn.classList.remove('btn-primary', 'btn-warning');
    btn.classList.add('btn-success');
    
    setTimeout(function() {
        btn.innerHTML = originalText;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-primary');
    }, 2000);
}
</script>
@endsection
