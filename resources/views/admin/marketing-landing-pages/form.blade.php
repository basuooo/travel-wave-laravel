@extends('layouts.admin')

@php
    $benefitItems = old('sections.benefits.items', $sections['benefits']['items'] ?? []);
    $quickInfoItems = old('sections.quick_info.items', $sections['quick_info']['items'] ?? []);
    $testimonialItems = old('sections.testimonials.items', $sections['testimonials']['items'] ?? []);
    $faqItems = old('sections.faq.items', $sections['faq']['items'] ?? []);
@endphp

@section('page_title', $isEdit ? __('admin.edit_landing_page') : __('admin.create_landing_page'))
@section('page_description', __('admin.marketing_manager_desc'))

@section('content')
<form method="post" enctype="multipart/form-data" action="{{ $isEdit ? route('admin.marketing-landing-pages.update', $item) : route('admin.marketing-landing-pages.store') }}">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    @if($analytics)
        <div class="row g-3 mb-4">
            <div class="col-md-4 col-xl-2"><div class="card admin-card p-3"><div class="small text-muted">{{ __('admin.total_visits') }}</div><div class="h4 mb-0">{{ $analytics['visits'] }}</div></div></div>
            <div class="col-md-4 col-xl-2"><div class="card admin-card p-3"><div class="small text-muted">{{ __('admin.unique_visits') }}</div><div class="h4 mb-0">{{ $analytics['unique_visits'] }}</div></div></div>
            <div class="col-md-4 col-xl-2"><div class="card admin-card p-3"><div class="small text-muted">{{ __('admin.form_submissions') }}</div><div class="h4 mb-0">{{ $analytics['form_submissions'] }}</div></div></div>
            <div class="col-md-4 col-xl-2"><div class="card admin-card p-3"><div class="small text-muted">{{ __('admin.whatsapp_clicks') }}</div><div class="h4 mb-0">{{ $analytics['whatsapp_clicks'] }}</div></div></div>
            <div class="col-md-4 col-xl-2"><div class="card admin-card p-3"><div class="small text-muted">{{ __('admin.cta_clicks') }}</div><div class="h4 mb-0">{{ $analytics['cta_clicks'] }}</div></div></div>
            <div class="col-md-4 col-xl-2"><div class="card admin-card p-3"><div class="small text-muted">{{ __('admin.conversion_rate') }}</div><div class="h4 mb-0">{{ $analytics['conversion_rate'] }}%</div></div></div>
        </div>
    @endif

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.core_settings') }}</h2>
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">{{ __('admin.landing_page_name') }}</label><input type="text" name="internal_name" class="form-control" value="{{ old('internal_name', $item->internal_name) }}" required></div>
            <div class="col-md-4"><label class="form-label">{{ __('admin.public_title_en') }}</label><input type="text" name="title_en" class="form-control" value="{{ old('title_en', $item->title_en) }}" required></div>
            <div class="col-md-4"><label class="form-label">{{ __('admin.public_title_ar') }}</label><input type="text" name="title_ar" class="form-control" value="{{ old('title_ar', $item->title_ar) }}" required></div>
            <div class="col-md-4"><label class="form-label">{{ __('admin.slug_key') }}</label><input type="text" name="slug" class="form-control" value="{{ old('slug', $item->slug) }}" required></div>
            <div class="col-md-4"><label class="form-label">{{ __('admin.campaign_name') }}</label><input type="text" name="campaign_name" class="form-control" value="{{ old('campaign_name', $item->campaign_name) }}"></div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.status') }}</label>
                <select name="status" class="form-select">
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $item->status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">{{ __('admin.platform') }}</label>
                <select name="ad_platform" class="form-select">
                    <option value="">{{ __('admin.select_platform') }}</option>
                    @foreach($platforms as $value => $label)
                        <option value="{{ $value }}" @selected(old('ad_platform', $item->ad_platform) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4"><label class="form-label">{{ __('admin.campaign_type') }}</label><input type="text" name="campaign_type" class="form-control" value="{{ old('campaign_type', $item->campaign_type) }}"></div>
            <div class="col-md-4"><label class="form-label">{{ __('admin.traffic_source') }}</label><input type="text" name="traffic_source" class="form-control" value="{{ old('traffic_source', $item->traffic_source) }}"></div>
            <div class="col-md-6"><label class="form-label">{{ __('admin.target_audience_note') }}</label><textarea name="target_audience_note" class="form-control" rows="3">{{ old('target_audience_note', $item->target_audience_note) }}</textarea></div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.assigned_form') }}</label>
                <select name="assigned_lead_form_id" class="form-select">
                    <option value="">{{ __('admin.none_option') }}</option>
                    @foreach($forms as $form)
                        <option value="{{ $form->id }}" @selected((int) old('assigned_lead_form_id', $item->assigned_lead_form_id) === $form->id)>{{ $form->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ __('admin.assigned_tracking') }}</label>
                <select name="tracking_integration_ids[]" class="form-select" multiple size="4">
                    @foreach($trackingIntegrations as $integration)
                        <option value="{{ $integration->id }}" @selected(in_array($integration->id, old('tracking_integration_ids', $item->tracking_integration_ids ?? []), true))>{{ $integration->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.utm_and_metadata') }}</h2>
        <div class="row g-3">
            <div class="col-md-2"><label class="form-label">UTM Source</label><input type="text" name="utm_source" class="form-control" value="{{ old('utm_source', $item->utm_source) }}"></div>
            <div class="col-md-2"><label class="form-label">UTM Medium</label><input type="text" name="utm_medium" class="form-control" value="{{ old('utm_medium', $item->utm_medium) }}"></div>
            <div class="col-md-2"><label class="form-label">UTM Campaign</label><input type="text" name="utm_campaign" class="form-control" value="{{ old('utm_campaign', $item->utm_campaign) }}"></div>
            <div class="col-md-3"><label class="form-label">UTM Content</label><input type="text" name="utm_content" class="form-control" value="{{ old('utm_content', $item->utm_content) }}"></div>
            <div class="col-md-3"><label class="form-label">UTM Term</label><input type="text" name="utm_term" class="form-control" value="{{ old('utm_term', $item->utm_term) }}"></div>
            <div class="col-md-6"><label class="form-label">{{ __('admin.final_url') }}</label><input type="text" name="final_url" class="form-control" value="{{ old('final_url', $item->final_url) }}"></div>
            <div class="col-md-3"><label class="form-label">{{ __('admin.seo_title_en') }}</label><input type="text" name="seo_title_en" class="form-control" value="{{ old('seo_title_en', $item->seo_title_en) }}"></div>
            <div class="col-md-3"><label class="form-label">{{ __('admin.seo_title_ar') }}</label><input type="text" name="seo_title_ar" class="form-control" value="{{ old('seo_title_ar', $item->seo_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">{{ __('admin.seo_description_en') }}</label><textarea name="seo_description_en" class="form-control" rows="3">{{ old('seo_description_en', $item->seo_description_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">{{ __('admin.seo_description_ar') }}</label><textarea name="seo_description_ar" class="form-control" rows="3">{{ old('seo_description_ar', $item->seo_description_ar) }}</textarea></div>
            <div class="col-12"><label class="form-label">{{ __('admin.notes') }}</label><textarea name="notes" class="form-control" rows="3">{{ old('notes', $item->notes) }}</textarea></div>
        </div>
    </div>

    @include('admin.marketing-landing-pages.partials.section-hero', ['sections' => $sections])
    @include('admin.marketing-landing-pages.partials.section-repeaters', ['titleKey' => 'benefits_features', 'sectionKey' => 'benefits', 'fields' => ['title', 'text', 'meta'], 'rows' => $benefitItems, 'sections' => $sections])
    @include('admin.marketing-landing-pages.partials.section-repeaters', ['titleKey' => 'quick_info_section', 'sectionKey' => 'quick_info', 'fields' => ['label', 'value'], 'rows' => $quickInfoItems, 'sections' => $sections])
    @include('admin.marketing-landing-pages.partials.section-repeaters', ['titleKey' => 'testimonials', 'sectionKey' => 'testimonials', 'fields' => ['quote', 'author', 'role'], 'rows' => $testimonialItems, 'sections' => $sections, 'backgroundUpload' => 'testimonial_background_image'])
    @include('admin.marketing-landing-pages.partials.section-repeaters', ['titleKey' => 'faq_section', 'sectionKey' => 'faq', 'fields' => ['question', 'answer'], 'rows' => $faqItems, 'sections' => $sections])
    @include('admin.marketing-landing-pages.partials.section-cta', ['sections' => $sections])
    @include('admin.marketing-landing-pages.partials.section-form', ['sections' => $sections])

    <div class="d-flex gap-2">
        <button class="btn btn-primary">{{ $isEdit ? __('admin.update') : __('admin.create') }}</button>
        <a href="{{ route('admin.marketing-landing-pages.index') }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
    </div>
</form>

<template id="benefits-row-template">
    @include('admin.marketing-landing-pages.partials.simple-item-row', ['group' => 'sections[benefits][items]', 'index' => '__INDEX__', 'item' => [], 'fields' => ['title', 'text', 'meta']])
</template>
<template id="quick_info-row-template">
    @include('admin.marketing-landing-pages.partials.simple-item-row', ['group' => 'sections[quick_info][items]', 'index' => '__INDEX__', 'item' => [], 'fields' => ['label', 'value']])
</template>
<template id="testimonials-row-template">
    @include('admin.marketing-landing-pages.partials.simple-item-row', ['group' => 'sections[testimonials][items]', 'index' => '__INDEX__', 'item' => [], 'fields' => ['quote', 'author', 'role']])
</template>
<template id="faq-row-template">
    @include('admin.marketing-landing-pages.partials.simple-item-row', ['group' => 'sections[faq][items]', 'index' => '__INDEX__', 'item' => [], 'fields' => ['question', 'answer']])
</template>

<script>
document.addEventListener('click', function (event) {
    const trigger = event.target.closest('.js-add-row');
    if (!trigger) return;
    const target = document.querySelector(trigger.dataset.target);
    const template = document.getElementById(trigger.dataset.template);
    if (!target || !template) return;
    const index = target.children.length;
    target.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', index));
});

document.addEventListener('click', function (event) {
    const remove = event.target.closest('.js-remove-row');
    if (!remove) return;
    remove.closest('.js-repeatable-row')?.remove();
});
</script>
@endsection
