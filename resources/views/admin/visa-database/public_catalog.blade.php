<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دليل وحاسبة التأشيرات | Travel Wave</title>
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
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: #ffffff;
            padding: 3rem 0;
            border-radius: 0 0 2rem 2rem;
        }
        .visa-card {
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid #e2e8f0;
            transition: all 0.25s ease;
            height: 100%;
        }
        .visa-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08);
            border-color: #3b82f6;
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
        </div>
    </nav>

    <!-- Hero Header -->
    <div class="hero-banner mb-5 text-center">
        <div class="container">
            <span class="badge bg-primary px-3 py-2 rounded-pill fs-6 mb-3">دليل تأشيرات دول العالم</span>
            <h1 class="fw-extrabold display-5 mb-3">قاعدة بيانات ومتطلبات التأشيرات والفيزا</h1>
            <p class="text-white-50 fs-5 max-w-2xl mx-auto">تصفح الشروط والأوراق المطلوبة وأسعار التأشيرات لكل دول العالم بسهولة</p>
        </div>
    </div>

    <div class="container pb-5">
        <!-- Search & Filter Card -->
        <div class="card shadow-sm border-0 rounded-4 mb-5">
            <div class="card-body p-4">
                <form action="{{ route('visa-database.public-catalog') }}" method="GET" class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label fw-bold">بحث باسم الدولة أو نوع التأشيرة</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="اكتب اسم الدولة (مثال: فرنسا، المانيا، إسبانيا)..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">التصنيف</label>
                        <select name="category_id" class="form-select">
                            <option value="">جميع التصنيفات</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2"><i class="bi bi-filter me-1"></i> بحث</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Visa Cards Grid -->
        <div class="row g-4 mb-4">
            @forelse($records as $rec)
                <div class="col-lg-4 col-md-6">
                    <div class="visa-card p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h4 class="fw-bold mb-1 text-dark">{{ $rec->country?->name_ar ?: $rec->country?->name_en }}</h4>
                                    <span class="badge bg-light text-secondary border">{{ $rec->visa_type }}</span>
                                </div>
                                <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 rounded-pill fw-bold">
                                    @if($rec->price && $rec->price > 0)
                                        {{ number_format($rec->price, 0) }} {{ $rec->currency ?: 'EGP' }}
                                    @else
                                        تواصل للتحديد
                                    @endif
                                </span>
                            </div>

                            <ul class="list-unstyled small text-muted mb-4 space-y-2">
                                <li class="mb-2"><i class="bi bi-clock me-2 text-primary"></i> <strong>مدة العمل:</strong> {{ $rec->working_days ?: '15-20 يوم' }}</li>
                                <li class="mb-2"><i class="bi bi-cash me-2 text-primary"></i> <strong>رسوم السفارة:</strong> {{ $rec->embassy_fee ? $rec->embassy_fee . ' ' . $rec->embassy_fee_currency : 'غير محدد' }}</li>
                                <li class="mb-2"><i class="bi bi-building me-2 text-primary"></i> <strong>مكان التقديم:</strong> {{ is_array($rec->application_center) ? implode(', ', $rec->application_center) : ($rec->application_center ?: 'السفارة') }}</li>
                            </ul>
                        </div>

                        <div>
                            <a href="{{ route('visa-database.public-preview', $rec->id) }}" target="_blank" class="btn btn-outline-primary w-100 fw-bold rounded-3 py-2">
                                <i class="bi bi-eye-fill me-1"></i> معاينة الشروط والأوراق
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-search text-muted display-1"></i>
                    <h4 class="fw-bold mt-3 text-muted">لم يتم العثور على نتائج طابق بحثك</h4>
                    <p class="text-muted">جرب البحث بكلمة مختلفة أو اختر تصنيف آخر</p>
                </div>
            @forelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $records->links() }}
        </div>
    </div>
</body>
</html>
