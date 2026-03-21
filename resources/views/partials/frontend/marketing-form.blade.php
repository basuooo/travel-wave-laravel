@php
    $visibleFields = $form->fields->where('is_enabled', true)->values();
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $title = data_get($formSection, 'title_' . $locale) ?: data_get($formSection, 'title_en') ?: $form->localized('title') ?: $form->name;
    $subtitle = data_get($formSection, 'subtitle_' . $locale) ?: data_get($formSection, 'subtitle_en') ?: $form->localized('subtitle');
    $submitText = $form->localized('submit_text') ?: __('ui.inquire_now');
    $successMessage = $form->localized('success_message');
    $metaEventName = 'Lead';
@endphp

<div class="tw-card tw-marketing-form-card p-4 p-lg-5">
    @if($title || $subtitle)
        <div class="mb-4">
            @if($title)
                <h2 class="tw-section-title h3 mb-2">{{ $title }}</h2>
            @endif
            @if($subtitle)
                <p class="text-muted mb-0">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <form method="post" action="{{ route('inquiries.store') }}" class="row g-3 premium-contact-form" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" data-meta-event-name="{{ $metaEventName }}" data-meta-form-name="{{ $form->name }}" data-meta-page-name="{{ $landingPage->localized('title') ?: $landingPage->internal_name }}" data-meta-destination="{{ $landingPage->campaign_name }}" data-meta-service-type="{{ $form->form_category ?? '' }}">
        @csrf
        <input type="hidden" name="lead_form_id" value="{{ $form->id }}">
        <input type="hidden" name="type" value="{{ $form->form_category ?: 'general' }}">
        <input type="hidden" name="source_page" value="{{ $landingPage->slug }}">
        <input type="hidden" name="marketing_landing_page_id" value="{{ $landingPage->id }}">
        <input type="hidden" name="preferred_language" value="{{ $locale }}">
        <input type="hidden" name="meta_event_id" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
        <input type="hidden" name="meta_event_name" value="{{ $metaEventName }}">
        @if($successMessage)
            <input type="hidden" name="success_message" value="{{ $successMessage }}">
        @endif

        @foreach($visibleFields as $field)
            @php
                $fieldKey = $field->field_key;
                $label = $field->localized('label') ?: \Illuminate\Support\Str::headline(str_replace('_', ' ', $fieldKey));
                $placeholder = $field->localized('placeholder');
                $help = $field->localized('help_text');
                $defaultValue = old($fieldKey, $field->default_value);
                $colClass = $field->type === 'textarea' ? 'col-12' : 'col-md-6';
                $inputType = $field->type === 'phone' ? 'text' : $field->type;
            @endphp

            @if($field->type === 'hidden')
                <input type="hidden" name="{{ $fieldKey }}" value="{{ $defaultValue }}">
                @continue
            @endif

            <div class="{{ $colClass }}">
                <label class="form-label">{{ $label }} @if($field->is_required)<span class="text-danger">*</span>@endif</label>
                @if($field->type === 'textarea')
                    <textarea name="{{ $fieldKey }}" rows="6" class="form-control" placeholder="{{ $placeholder }}" @required($field->is_required)>{{ $defaultValue }}</textarea>
                @elseif($field->type === 'select')
                    <select name="{{ $fieldKey }}" class="form-select" @required($field->is_required)>
                        <option value="">{{ $placeholder ?: $label }}</option>
                        @foreach($field->options ?? [] as $option)
                            @php($optionLabel = $locale === 'ar' ? ($option['label_ar'] ?? $option['label_en'] ?? $option['value']) : ($option['label_en'] ?? $option['label_ar'] ?? $option['value']))
                            <option value="{{ $option['value'] ?? '' }}" @selected((string) $defaultValue === (string) ($option['value'] ?? ''))>{{ $optionLabel }}</option>
                        @endforeach
                    </select>
                @else
                    <input type="{{ $inputType }}" name="{{ $fieldKey }}" class="form-control" value="{{ $defaultValue }}" placeholder="{{ $placeholder }}" @required($field->is_required)>
                @endif

                @if($help)
                    <div class="form-text">{{ $help }}</div>
                @endif
            </div>
        @endforeach

        <div class="col-12">
            <button class="btn btn-primary tw-btn-primary px-4">{{ $submitText }}</button>
        </div>
    </form>
</div>
