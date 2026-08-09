@extends('layouts.app')

@section('title', $landingPage->seo_title_ar ?: ($landingPage->seo_title_en ?: $landingPage->title_ar))
@section('meta_description', $landingPage->seo_description_ar ?: $landingPage->seo_description_en)

@push('styles')
@if($landingPage->custom_css)
<style>
    {!! $landingPage->custom_css !!}
</style>
@endif
@endpush

@section('content')
<!-- Public Landing Page Renderer Engine -->
<div class="landing-page-public-render" id="lpPublicContainer">
    @php
        $elements = data_get($landingPage->structure ?? [], 'elements', []);
    @endphp

    @forelse($elements as $element)
        @php
            $type = $element['type'] ?? 'hero';
            $content = $element['content'] ?? [];
            $style = $element['style'] ?? [];
            $responsive = $element['responsive'] ?? [];
            
            $hideClasses = [];
            if (!empty($responsive['hide_desktop'])) $hideClasses[] = 'd-lg-none';
            if (!empty($responsive['hide_tablet'])) $hideClasses[] = 'd-md-none';
            if (!empty($responsive['hide_mobile'])) $hideClasses[] = 'd-sm-none';
            $hideClassStr = implode(' ', array_unique($hideClasses));
        @endphp

        <div class="lp-element-block {{ $hideClassStr }}" style="background-color: {{ $style['background_color'] ?? 'transparent' }}; color: {{ $style['text_color'] ?? 'inherit' }};">
            @if($type === 'hero' || $type === 'heading')
                <div class="container py-5 text-center">
                    <h1 class="display-4 fw-bold mb-3">{{ $content['title_ar'] ?? ($content['title_en'] ?? $landingPage->title_ar) }}</h1>
                    @if(!empty($content['subtitle_ar']) || !empty($content['subtitle_en']))
                        <p class="lead mb-4">{{ $content['subtitle_ar'] ?? $content['subtitle_en'] }}</p>
                    @endif
                    @if(!empty($content['cta_text_ar']) || !empty($content['cta_text_en']))
                        <a href="{{ $content['cta_link'] ?? '#lead_form_section' }}" class="btn btn-primary btn-lg px-5 fw-bold shadow">
                            {{ $content['cta_text_ar'] ?? $content['cta_text_en'] }}
                        </a>
                    @endif
                </div>
            @elseif($type === 'lead_form')
                <div class="container py-5" id="lead_form_section">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card shadow-lg border-0 rounded-4 p-4 p-md-5">
                                <h3 class="fw-bold text-center mb-4">{{ $content['title_ar'] ?? 'تواصل معنا واستلم تفاصيل العرض' }}</h3>
                                
                                <form method="post" action="{{ route('inquiries.store') }}">
                                    @csrf
                                    <input type="hidden" name="type" value="general">
                                    <input type="hidden" name="marketing_landing_page_id" value="{{ $landingPage->id }}">
                                    <input type="hidden" name="source_page" value="{{ $landingPage->internal_name }}">

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">الاسم الكامل *</label>
                                            <input type="text" name="full_name" class="form-control form-control-lg" placeholder="أدخل اسمك" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">رقم الجوال / الواتساب *</label>
                                            <input type="tel" name="phone" class="form-control form-control-lg" placeholder="05xxxxxxxx" required>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">البريد الإلكتروني</label>
                                            <input type="email" name="email" class="form-control form-control-lg" placeholder="name@example.com">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">ملاحظات أو تفاصيل إضافية</label>
                                            <textarea name="message" class="form-control" rows="3" placeholder="اكتب استفسارك هنا..."></textarea>
                                        </div>
                                        <div class="col-12 mt-4">
                                            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow py-3">
                                                إرسال الطلب الآن
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="container py-4">
                    <h3>{{ $content['title_ar'] ?? ($content['title_en'] ?? '') }}</h3>
                </div>
            @endif
        </div>
    @empty
        <div class="container py-5 text-center">
            <h2 class="fw-bold">{{ $landingPage->title_ar ?: $landingPage->title_en }}</h2>
            <p class="text-muted">{{ $landingPage->seo_description_ar }}</p>
        </div>
    @endforelse
</div>
@endsection

@push('scripts')
@if($landingPage->custom_js)
<script>
    {!! $landingPage->custom_js !!}
</script>
@endif
@endpush
