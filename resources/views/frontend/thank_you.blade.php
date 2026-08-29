@php
    $isEn = app()->getLocale() === 'en';
    $dir = $isEn ? 'ltr' : 'rtl';
    $bgColor = $form ? $form->thankYouBgColor() : '#ffffff';
    $textColor = $form ? $form->thankYouTextColor() : '#212529';
    $title = $form ? $form->thankYouTitle() : ($isEn ? 'Thank You! Your Request Has Been Received' : 'شكراً لك! تم استلام طلبك بنجاح 🎉');
    $message = $form ? $form->thankYouMessage() : ($isEn ? 'Our sales team will contact you shortly.' : 'تم استلام بياناتك وسيتم التواصل معك في أقرب وقت عبر فريق المبيعات.');
    $customHtml = $form ? $form->thankYouCustomHtml() : null;
    $customCss = $form ? $form->thankYouCustomCss() : null;

    $countryName = (isset($visaRecord) && $visaRecord && $visaRecord->country)
        ? ($isEn && $visaRecord->country->name_en ? $visaRecord->country->name_en : ($visaRecord->country->name_ar ?: $visaRecord->country->name_en))
        : '';
    $catalogSetting = \App\Models\PublicCatalogSetting::getSettings();
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | {{ $isEn ? 'Visa Database' : 'دليل التأشيرات' }}</title>
    <!-- Bootstrap 5 CSS -->
    @if($isEn)
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    @else
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    @endif
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1.5rem;
        }
        .thank-you-card {
            background-color: {{ $bgColor }};
            color: {{ $textColor }};
            border-radius: 1.5rem;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.12);
            border: 1px solid #e2e8f0;
        }
        .success-icon-badge {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 45px;
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
        }
        .brand-logo-img {
            max-width: 180px;
            max-height: 55px;
            object-fit: contain;
        }
        @if($customCss)
            {!! $customCss !!}
        @endif
    </style>
</head>
<body>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            <div class="thank-you-card p-4 p-md-5 text-center position-relative overflow-hidden">
                
                @if(!empty($catalogSetting->logo_path))
                    <div class="mb-4 text-center">
                        <img src="{{ asset('storage/' . $catalogSetting->logo_path) }}" alt="Logo" class="brand-logo-img">
                    </div>
                @endif

                <div class="mb-4">
                    <div class="success-icon-badge">
                        <i class="bi bi-check-lg"></i>
                    </div>
                </div>

                <h1 class="h2 fw-bold mb-3" style="color: {{ $textColor }};">{{ $title }}</h1>
                <p class="fs-5 mb-4 text-secondary leading-relaxed" style="max-width: 600px; margin: 0 auto;">{{ $message }}</p>

                <!-- Custom HTML Container if specified -->
                @if($customHtml)
                    <div class="custom-thank-you-html my-4 p-3 rounded-3 border bg-light text-start">
                        {!! $customHtml !!}
                    </div>
                @endif

                <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
                    @if(isset($visaRecord) && $visaRecord)
                        <a href="{{ route('visa-database.public-preview', $visaRecord->country?->slug ?: $visaRecord->id) }}" class="btn btn-outline-primary fw-bold px-4 py-2 rounded-3">
                            <i class="bi bi-arrow-right me-1"></i> {{ $isEn ? 'Back to Visa Details' : 'العودة لتفاصيل تأشيرة ' . $countryName }}
                        </a>
                    @endif

                    <a href="{{ route('visa-database.public-catalog') }}" class="btn btn-primary fw-bold px-4 py-2 rounded-3">
                        <i class="bi bi-globe me-1"></i> {{ $isEn ? 'Explore Visa Catalog' : 'استكشاف دليل التأشيرات' }}
                    </a>

                    @php
                        $waPhone = $catalogSetting->whatsapp_phone ?: '201000000000';
                        $waText = rawurlencode('مرحباً، قمت بتسجيل طلب تأشيرة ' . ($countryName ? '(' . $countryName . ')' : '') . ' وأرغب في المتابعة.');
                    @endphp
                    <a href="https://wa.me/{{ $waPhone }}?text={{ $waText }}" target="_blank" class="btn btn-success fw-bold px-4 py-2 rounded-3">
                        <i class="bi bi-whatsapp me-1"></i> {{ $isEn ? 'Chat via WhatsApp' : 'تواصل عبر الواتساب للمتابعة' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
