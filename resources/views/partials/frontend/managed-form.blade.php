@php
    $form = $assignment->form;
    $settings = $form->settings ?? [];
    $locale = app()->getLocale();
    $fallbackLocale = config('app.fallback_locale', 'en');
    $visibleFields = $form->fields->where('is_enabled', true)->values();
    $formTitle = $form->localized('title') ?: $form->name;
    $formSubtitle = $form->localized('subtitle');
    $submitText = $form->localized('submit_text') ?: __('ui.inquire_now');
    $successMessage = $form->localized('success_message');
    $layoutVariant = $settings['layout_type'] ?? $settings['layout_variant'] ?? 'standard';
    $contextData = $contextData ?? null;
    $contextType = $contextData['type'] ?? null;
    $isVisaSplit = $layoutVariant === 'visa_split' && $contextType === 'visa';
    $isSplitLayout = $isVisaSplit || $layoutVariant === 'split_details';
    $isRtl = app()->getLocale() === 'ar';
    $infoColumnClass = $isRtl ? 'col-lg-5 order-lg-2' : 'col-lg-5 order-lg-1';
    $formColumnClass = $isRtl ? 'col-lg-7 order-lg-1' : 'col-lg-7 order-lg-2';
    $metaEventName = match ($form->form_category) {
        'contact' => 'Contact',
        'registration' => 'CompleteRegistration',
        default => 'Lead',
    };
    $metaPageName = $sourcePage ?? request()->path();

    $settingText = function (string $key) use ($settings, $locale, $fallbackLocale) {
        return $settings[$key . '_' . $locale]
            ?? $settings[$key . '_' . $fallbackLocale]
            ?? $settings[$key]
            ?? null;
    };

    $mapLocalizedItem = function (array $item, string $baseKey) use ($locale, $fallbackLocale) {
        return $item[$baseKey . '_' . $locale]
            ?? $item[$baseKey . '_' . $fallbackLocale]
            ?? $item[$baseKey]
            ?? '';
    };

    $infoItems = collect($settings['info_items'] ?? [])
        ->filter(fn (array $item) => ($item['is_active'] ?? true))
        ->sortBy('sort_order')
        ->map(fn (array $item) => [
            'label' => $mapLocalizedItem($item, 'title'),
            'value' => $mapLocalizedItem($item, 'value'),
        ])
        ->filter(fn (array $item) => filled($item['label']) || filled($item['value']))
        ->values();

    if ($infoItems->isEmpty() && !empty($contextData)) {
        $infoItems = collect($contextData['form']['highlights'] ?? $contextData['quick_info']['items'] ?? [])
            ->map(fn (array $item) => [
                'label' => $item['label'] ?? '',
                'value' => $item['value'] ?? '',
            ])
            ->filter(fn (array $item) => filled($item['label']) || filled($item['value']))
            ->take(3)
            ->values();
    }

    $infoLabel = $settingText('info_label') ?: ($contextData['form']['section_label'] ?? __('ui.contact_us'));
    $infoHeading = $settingText('info_heading') ?: ($contextData['form']['title'] ?? $formTitle);
    $infoDescription = $settingText('info_description') ?: ($contextData['form']['subtitle'] ?? $formSubtitle);

    $renderDynamicFields = function () use ($visibleFields) {
        foreach ($visibleFields as $field) {
            $fieldKey = $field->field_key;
            $label = $field->localized('label') ?: \Illuminate\Support\Str::headline(str_replace('_', ' ', $fieldKey));
            $placeholder = $field->localized('placeholder');
            $help = $field->localized('help_text');
            $defaultValue = old($fieldKey, $field->default_value);
            $colClass = in_array($field->type, ['textarea'], true) ? 'col-12' : 'col-md-6';
            $requiredAttribute = $field->is_required ? 'required' : '';
            $inputType = $field->type === 'phone' ? 'text' : $field->type;

            if ($field->type === 'hidden') {
                echo '<input type="hidden" name="' . e($fieldKey) . '" value="' . e($defaultValue) . '">';
                continue;
            }

            echo '<div class="' . e($colClass) . '">';
            echo '<label class="form-label">' . e($label);
            if ($field->is_required) {
                echo ' <span class="text-danger">*</span>';
            }
            echo '</label>';

            if ($field->type === 'textarea') {
                echo '<textarea name="' . e($fieldKey) . '" rows="6" class="form-control" placeholder="' . e($placeholder) . '" ' . $requiredAttribute . '>' . e($defaultValue) . '</textarea>';
            } elseif ($field->type === 'select') {
                echo '<select name="' . e($fieldKey) . '" class="form-select" ' . $requiredAttribute . '>';
                echo '<option value="">' . e($placeholder ?: $label) . '</option>';

                foreach ($field->options ?? [] as $option) {
                    $optionLabel = app()->getLocale() === 'ar'
                        ? ($option['label_ar'] ?? $option['label_en'] ?? $option['value'])
                        : ($option['label_en'] ?? $option['label_ar'] ?? $option['value']);

                    $selected = (string) $defaultValue === (string) ($option['value'] ?? '') ? 'selected' : '';
                    echo '<option value="' . e($option['value'] ?? '') . '" ' . $selected . '>' . e($optionLabel) . '</option>';
                }

                echo '</select>';
            } else {
                $minAttribute = $field->type === 'number' ? 'min="0"' : '';
                echo '<input type="' . e($inputType) . '" name="' . e($fieldKey) . '" class="form-control" value="' . e($defaultValue) . '" placeholder="' . e($placeholder) . '" ' . $minAttribute . ' ' . $requiredAttribute . '>';
            }

            if ($help) {
                echo '<div class="form-text">' . e($help) . '</div>';
            }

            echo '</div>';
        }
    };
@endphp

@if($isSplitLayout)
    <div class="tw-visa-inquiry-shell {{ $isRtl ? 'tw-visa-inquiry-shell-rtl' : 'tw-visa-inquiry-shell-ltr' }} {{ $className ?? '' }}">
        <div class="row g-0">
            <div class="{{ $infoColumnClass }}">
                <div class="tw-visa-inquiry-panel tw-visa-inquiry-panel-info">
                    @if($infoLabel)
                        <div class="tw-visa-inquiry-label">{{ $infoLabel }}</div>
                    @endif
                    @if($infoHeading)
                        <h3 class="tw-section-title h2 mb-3">{{ $infoHeading }}</h3>
                    @endif
                    @if($infoDescription)
                        <p class="tw-visa-inquiry-copy mb-4">{{ $infoDescription }}</p>
                    @endif

                    @if($infoItems->isNotEmpty())
                        <div class="tw-visa-inquiry-info-list">
                            @foreach($infoItems as $item)
                                <div class="tw-visa-inquiry-info-card">
                                    <span class="tw-visa-inquiry-info-label">{{ $item['label'] }}</span>
                                    <strong class="tw-visa-inquiry-info-value">{{ $item['value'] }}</strong>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="{{ $formColumnClass }}">
                <div class="tw-visa-inquiry-panel tw-visa-inquiry-panel-form">
                    @if($formTitle || $formSubtitle)
                        <div class="tw-visa-inquiry-form-copy mb-4">
                            @if($formTitle)
                                <h3 class="h4 mb-2">{{ $formTitle }}</h3>
                            @endif
                            @if($formSubtitle)
                                <p class="text-muted mb-0">{{ $formSubtitle }}</p>
                            @endif
                        </div>
                    @endif

                    <form method="post" action="{{ route('inquiries.store') }}" class="row g-3 premium-contact-form {{ $isRtl ? 'text-end' : '' }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" data-meta-event-name="{{ $metaEventName }}" data-meta-form-name="{{ $form->name }}" data-meta-page-name="{{ $metaPageName }}" data-meta-destination="{{ $contextData['title'] ?? '' }}" data-meta-service-type="{{ $form->form_category ?? '' }}">
                        @csrf
                        <input type="hidden" name="lead_form_id" value="{{ $form->id }}">
                        <input type="hidden" name="lead_form_assignment_id" value="{{ $assignment->id }}">
                        <input type="hidden" name="type" value="{{ $form->form_category ?: 'general' }}">
                        <input type="hidden" name="source_page" value="{{ $sourcePage ?? request()->path() }}">
                        <input type="hidden" name="display_position" value="{{ $assignment->display_position }}">
                        <input type="hidden" name="preferred_language" value="{{ app()->getLocale() }}">
                        <input type="hidden" name="meta_event_id" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
                        <input type="hidden" name="meta_event_name" value="{{ $metaEventName }}">
                        @if($successMessage)
                            <input type="hidden" name="success_message" value="{{ $successMessage }}">
                        @endif

                        {!! $renderDynamicFields() !!}

                        <div class="col-12">
                            <button class="btn btn-primary tw-btn-primary tw-visa-inquiry-submit">{{ $submitText }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="tw-form-card p-4 {{ $className ?? '' }}">
        @if($formTitle || $formSubtitle)
            <div class="mb-4">
                @if($formTitle)
                    <h3 class="h4 mb-2">{{ $formTitle }}</h3>
                @endif
                @if($formSubtitle)
                    <p class="text-muted mb-0">{{ $formSubtitle }}</p>
                @endif
            </div>
        @endif

        <form method="post" action="{{ route('inquiries.store') }}" class="row g-3" data-meta-event-name="{{ $metaEventName }}" data-meta-form-name="{{ $form->name }}" data-meta-page-name="{{ $metaPageName }}" data-meta-destination="{{ $contextData['title'] ?? '' }}" data-meta-service-type="{{ $form->form_category ?? '' }}">
            @csrf
            <input type="hidden" name="lead_form_id" value="{{ $form->id }}">
            <input type="hidden" name="lead_form_assignment_id" value="{{ $assignment->id }}">
            <input type="hidden" name="type" value="{{ $form->form_category ?: 'general' }}">
            <input type="hidden" name="source_page" value="{{ $sourcePage ?? request()->path() }}">
            <input type="hidden" name="display_position" value="{{ $assignment->display_position }}">
            <input type="hidden" name="preferred_language" value="{{ app()->getLocale() }}">
            <input type="hidden" name="meta_event_id" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
            <input type="hidden" name="meta_event_name" value="{{ $metaEventName }}">
            @if($successMessage)
                <input type="hidden" name="success_message" value="{{ $successMessage }}">
            @endif

            {!! $renderDynamicFields() !!}

            <div class="col-12">
                <button class="btn btn-primary tw-btn-primary px-4 tw-form-submit">{{ $submitText }}</button>
            </div>
        </form>
    </div>
@endif
