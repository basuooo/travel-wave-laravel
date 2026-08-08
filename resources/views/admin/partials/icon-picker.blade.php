@php
    $val = old($name ?? 'icon', $value ?? '');
    $presets = \App\Support\IconLibrary::presets();
    $fieldDataAttr = $fieldDataAttr ?? null;
    $placeholder = $placeholder ?? 'أدخل كود الإيقونة (Iconify / SVG / FontAwesome)';
    $isPreset = false;
    foreach ($presets as $p) {
        if ($p['value'] === strtolower(trim((string)$val))) {
            $isPreset = true;
            break;
        }
    }
@endphp

<div class="tw-icon-picker-group">
    <div class="d-flex align-items-center justify-content-between mb-1">
        @if(!empty($label))
            <label class="form-label font-weight-bold mb-0">{{ $label }}</label>
        @else
            <span></span>
        @endif
        <a href="https://icon-sets.iconify.design/" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="text-decoration-none d-inline-flex align-items-center gap-1 small text-primary fw-semibold"
           title="تصفح وابحث عن أكواد الأيقونات في مكتبة Iconify الرسمية">
            <span>🌐 مكتبة أكواد الأيقونات (Iconify)</span> ↗
        </a>
    </div>
    <div class="d-flex align-items-center gap-2 mb-2">
        <div class="tw-admin-icon-preview border rounded-3 bg-light d-flex align-items-center justify-content-center p-2" 
             data-icon-preview 
             style="width: 42px; height: 42px; min-width: 42px; color: #ff8c32; font-size: 1.25rem;">
            {!! \App\Support\IconLibrary::render($val, 'sparkles') !!}
        </div>
        <select class="form-select form-select-sm tw-icon-select" data-icon-select>
            <option value="">-- اختر إيقونة جاهزة (Select Preset) --</option>
            @foreach($presets as $p)
                <option value="{{ $p['value'] }}" @selected($isPreset && strtolower(trim((string)$val)) === $p['value'])>
                    {{ $p['label'] }}
                </option>
            @endforeach
            <option value="custom" @selected(!$isPreset && !empty($val))>✏️ كود مخصص / Iconify (Custom Code)</option>
        </select>
    </div>
    <input type="text"
           class="form-control form-control-sm tw-icon-input"
           @if(!empty($name)) name="{{ $name }}" @endif
           value="{{ $val }}"
           placeholder="{{ $placeholder }}"
           data-icon-input
           @if(!empty($fieldDataAttr)) data-field="{{ $fieldDataAttr }}" @endif
           autocomplete="off">
    <div class="form-text small text-muted mt-1 d-flex align-items-center justify-content-between flex-wrap gap-1">
        <span>اختر إيقونة جاهزة أو أدخل كود مخصص (Iconify / SVG / FontAwesome).</span>
        <a href="https://icon-sets.iconify.design/" target="_blank" rel="noopener noreferrer" class="text-decoration-none fw-semibold">
            🌐 البحث عن أكواد الأيقونات ↗
        </a>
    </div>
</div>
