@extends('layouts.app')

@php
    $isEn = app()->getLocale() === 'en';
    $bgColor = $form ? $form->thankYouBgColor() : '#ffffff';
    $textColor = $form ? $form->thankYouTextColor() : '#212529';
    $title = $form ? $form->thankYouTitle() : ($isEn ? 'Thank You! Your Request Has Been Received' : 'شكراً لك! تم استلام طلبك بنجاح 🎉');
    $message = $form ? $form->thankYouMessage() : ($isEn ? 'Our sales team will contact you shortly.' : 'تم استلام بياناتك وسيتم التواصل معك في أقرب وقت عبر فريق المبيعات.');
    $customHtml = $form ? $form->thankYouCustomHtml() : null;
    $customCss = $form ? $form->thankYouCustomCss() : null;
@endphp

@section('page_title', $title)

@section('content')
@if($customCss)
    <style>
        {!! $customCss !!}
    </style>
@endif

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 text-center position-relative overflow-hidden" style="background-color: {{ $bgColor }}; color: {{ $textColor }};">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle shadow-sm" style="width: 80px; height: 80px; font-size: 40px;">
                        ✓
                    </div>
                </div>

                <h1 class="h2 fw-extrabold mb-3" style="color: {{ $textColor }};">{{ $title }}</h1>
                <p class="fs-5 mb-4 text-secondary leading-relaxed">{{ $message }}</p>

                <!-- Custom HTML Container if specified -->
                @if($customHtml)
                    <div class="custom-thank-you-html my-4 p-3 rounded-3 border bg-light text-start">
                        {!! $customHtml !!}
                    </div>
                @endif

                <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
                    @if(isset($visaRecord) && $visaRecord)
                        <a href="{{ route('visa-database.public-preview', $visaRecord->country?->slug ?: $visaRecord->id) }}" class="btn btn-outline-primary fw-bold px-4 py-2 rounded-3">
                            ⬅️ {{ $isEn ? 'Back to Visa Details' : 'العودة لتفاصيل التأشيرة' }}
                        </a>
                    @endif

                    <a href="{{ route('visa-database.public-catalog') }}" class="btn btn-primary fw-bold px-4 py-2 rounded-3">
                        🌐 {{ $isEn ? 'Explore Visa Catalog' : 'استكشاف دليل التأشيرات' }}
                    </a>

                    @php
                        $catalogSetting = \App\Models\PublicCatalogSetting::getSettings();
                        $waPhone = $catalogSetting->whatsapp_phone ?: '201000000000';
                    @endphp
                    <a href="https://wa.me/{{ $waPhone }}?text={{ rawurlencode('مرحباً، قمت بتسجيل طلب تأشيرة وأرغب في المتابعة.') }}" target="_blank" class="btn btn-success fw-bold px-4 py-2 rounded-3">
                        💬 {{ $isEn ? 'Chat via WhatsApp' : 'تواصل عبر الواتساب' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
