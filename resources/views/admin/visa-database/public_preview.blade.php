<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>معاينة تفاصيل تأشيرة {{ $record->country?->name_ar ?: $record->country?->name_en }} | Travel Wave</title>
    <!-- Bootstrap 5 RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .hero-banner {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: #ffffff;
            padding: 2.5rem 0;
            border-radius: 0 0 1.5rem 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.3);
        }
        .info-card {
            background: #ffffff;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.05);
        }
        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }
        .price-badge {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            font-size: 1.5rem;
            font-weight: 800;
            padding: 0.5rem 1.25rem;
            border-radius: 0.75rem;
        }
        .documents-content h2, .documents-content h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1e3a8a;
            margin-top: 1rem;
            margin-bottom: 0.75rem;
        }
        .documents-content ul {
            padding-right: 1.25rem;
            margin-bottom: 1rem;
        }
        .documents-content li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        .btn-whatsapp {
            background-color: #25d366;
            color: #ffffff;
            font-weight: 700;
            border-radius: 0.75rem;
            padding: 0.75rem 1.5rem;
            border: none;
            transition: background-color 0.2s ease;
        }
        .btn-whatsapp:hover {
            background-color: #1eb956;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <!-- Header Navbar -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom py-2 sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-primary" href="{{ route('home') }}">
                <i class="bi bi-airplane-engines-fill fs-3"></i>
                <span class="fs-5">ترافيل ويف | Travel Wave</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('visa-database.public-catalog') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-grid-fill me-1"></i> دليل التأشيرات
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-banner mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-3 mb-lg-0">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <span class="badge bg-white text-primary px-3 py-2 rounded-pill fs-6 fw-bold">
                            <i class="bi bi-globe-europe-africa me-1"></i> {{ $record->country?->name_ar }}
                        </span>
                        @if($record->country?->name_en)
                            <span class="text-white-50 fs-6 fw-semibold">({{ $record->country->name_en }})</span>
                        @endif
                        <span class="badge bg-info text-dark px-3 py-2 rounded-pill fs-6">
                            {{ $record->visa_type }}
                        </span>
                    </div>
                    <h1 class="fw-extrabold display-6 mb-2">تفاصيل وإجراءات تأشيرة {{ $record->country?->name_ar }}</h1>
                    <p class="text-white-50 m-0">الدليل التوضيحي المعتمد لمتطلبات التقديم والرسوم وأوراق فيزا {{ $record->country?->name_ar }}</p>
                </div>
                <div class="col-lg-4 text-lg-start text-center">
                    <div class="d-inline-block text-start">
                        <div class="small text-white-50 mb-1 fw-bold">سعر الخدمة</div>
                        <div class="price-badge d-inline-block shadow-sm">
                            @if($record->price && $record->price > 0)
                                {{ number_format($record->price, 0) }} <small class="fs-6">{{ $record->currency ?: 'EGP' }}</small>
                            @else
                                <span class="fs-5 text-muted">غير محدد (تواصل معنا)</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container pb-5">
        <div class="row g-4 mb-4">
            <!-- Key Info Cards -->
            <div class="col-md-4 col-sm-6">
                <div class="info-card p-3 d-flex align-items-center gap-3">
                    <div class="icon-box bg-primary-subtle text-primary">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">أيام العمل</div>
                        <div class="fw-bold fs-6">{{ $record->working_days ?: '15–20 يوم عمل' }}</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="info-card p-3 d-flex align-items-center gap-3">
                    <div class="icon-box bg-success-subtle text-success">
                        <i class="bi bi-calendar2-check"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">مدة التأشيرة المقترحة</div>
                        <div class="fw-bold fs-6">{{ $record->proposed_duration ?: 'حسب قرار السفارة' }}</div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="info-card p-3 d-flex align-items-center gap-3">
                    <div class="icon-box bg-warning-subtle text-warning">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">رسوم السفارة</div>
                        <div class="fw-bold fs-6">
                            @if($record->embassy_fee)
                                {{ $record->embassy_fee }} {{ $record->embassy_fee_currency }}
                            @else
                                غير محدد
                            @endif
                        </div>
                        @if($record->embassy_fee_payment_method)
                            <div class="small text-muted" style="font-size: 0.75rem;">({{ $record->embassy_fee_payment_method }})</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="info-card p-3 d-flex align-items-center gap-3">
                    <div class="icon-box bg-info-subtle text-info">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">مكان التقديم (المركز)</div>
                        <div class="fw-bold fs-6">
                            @if($record->application_center_list && count($record->application_center_list) > 0)
                                @foreach($record->application_center_list as $center)
                                    <span class="badge bg-secondary me-1">{{ $center }}</span>
                                @endforeach
                            @else
                                <span class="badge bg-secondary">السفارة مباشرة</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="info-card p-3 d-flex align-items-center gap-3">
                    <div class="icon-box bg-danger-subtle text-danger">
                        <i class="bi bi-fingerprint"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">هل البصمة مطلوبة؟</div>
                        <div class="fw-bold fs-6">
                            @if($record->is_biometrics_required)
                                <span class="text-danger"><i class="bi bi-check-circle-fill me-1"></i> مطلوبة</span>
                            @else
                                <span class="text-success"><i class="bi bi-x-circle-fill me-1"></i> غير مطلوبة</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="info-card p-3 d-flex align-items-center gap-3">
                    <div class="icon-box bg-purple-subtle text-purple" style="background-color: #f3e8ff; color: #9333ea;">
                        <i class="bi bi-person-lines-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">هل المقابلة مطلوبة؟</div>
                        <div class="fw-bold fs-6">
                            @if($record->is_interview_required)
                                <span style="color: #9333ea;"><i class="bi bi-check-circle-fill me-1"></i> مطلوبة</span>
                            @else
                                <span class="text-success"><i class="bi bi-x-circle-fill me-1"></i> غير مطلوبة</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Documents Section -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                        <h4 class="fw-bold text-primary mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-text-fill"></i> الأوراق والوثائق المطلوبة للتقديم
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        @if($record->required_documents)
                            <div class="documents-content">
                                {!! nl2br(e($record->required_documents)) !!}
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle me-1"></i> يرجى التواصل مع فريق خدمة العملاء للحصول على قائمة الأوراق المحدثة الخاصة بتأشيرة {{ $record->country?->name_ar }}.
                            </div>
                        @endif
                    </div>
                </div>

                @if($record->notes)
                    <div class="card shadow-sm border-0 rounded-4 mb-4 border-start border-4 border-warning">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-warning-emphasis mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-triangle-fill"></i> ملاحظات وتوجيهات هامة
                            </h5>
                            <p class="mb-0 text-secondary leading-relaxed">{!! nl2br(e($record->notes)) !!}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar / Action Card -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 rounded-4 sticky-top" style="top: 90px;">
                    <div class="card-body p-4 text-center">
                        <div class="icon-box bg-success-subtle text-success mx-auto mb-3" style="width: 64px; height: 64px; font-size: 2rem;">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <h5 class="fw-bold mb-2">هل ترغب في البدء بالتقديم؟</h5>
                        <p class="text-muted small mb-4">فريقنا متواجد لمساعدتك في تجهيز الملف وحجز موعد السفارة فوراً.</p>

                        @php
                            $waMessage = rawurlencode("مرحباً ترافيل ويف، أود الاستفسار والتقديم على تأشيرة " . ($record->country?->name_ar ?: $record->country?->name_en) . " (" . $record->visa_type . ")");
                            $waPhone = "201000000000"; // Can be replaced by settings phone
                        @endphp

                        <a href="https://wa.me/{{ $waPhone }}?text={{ $waMessage }}" target="_blank" class="btn btn-whatsapp w-100 mb-3 shadow-sm">
                            <i class="bi bi-whatsapp me-1"></i> تواصل معنا عبر واتساب
                        </a>

                        <button onclick="copyShareLink()" class="btn btn-outline-secondary w-100 btn-sm">
                            <i class="bi bi-clipboard me-1"></i> نسخ رابط هذه المعاينة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
        <div id="copyToast" class="toast bg-dark text-white border-0 align-items-center" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fs-6">
                    <i class="bi bi-check-circle-fill text-success me-2"></i> تم نسخ الرابط بنجاح!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyShareLink() {
            navigator.clipboard.writeText(window.location.href).then(function() {
                var toastEl = document.getElementById('copyToast');
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            });
        }
    </script>
</body>
</html>
