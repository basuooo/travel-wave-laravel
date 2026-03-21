<div class="card admin-card p-4 mb-4">
    <h2 class="h5 mb-3">{{ __('admin.cta_section') }}</h2>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">{{ __('admin.section_title_en') }}</label><input type="text" name="sections[cta][title_en]" class="form-control" value="{{ old('sections.cta.title_en', $sections['cta']['title_en'] ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.section_title_ar') }}</label><input type="text" name="sections[cta][title_ar]" class="form-control" value="{{ old('sections.cta.title_ar', $sections['cta']['title_ar'] ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.section_subtitle_en') }}</label><textarea name="sections[cta][description_en]" class="form-control" rows="3">{{ old('sections.cta.description_en', $sections['cta']['description_en'] ?? '') }}</textarea></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.section_subtitle_ar') }}</label><textarea name="sections[cta][description_ar]" class="form-control" rows="3">{{ old('sections.cta.description_ar', $sections['cta']['description_ar'] ?? '') }}</textarea></div>
        <div class="col-md-3"><label class="form-label">{{ __('admin.primary_button_text_en') }}</label><input type="text" name="sections[cta][primary_button_text_en]" class="form-control" value="{{ old('sections.cta.primary_button_text_en', $sections['cta']['primary_button_text_en'] ?? '') }}"></div>
        <div class="col-md-3"><label class="form-label">{{ __('admin.primary_button_text_ar') }}</label><input type="text" name="sections[cta][primary_button_text_ar]" class="form-control" value="{{ old('sections.cta.primary_button_text_ar', $sections['cta']['primary_button_text_ar'] ?? '') }}"></div>
        <div class="col-md-3"><label class="form-label">{{ __('admin.secondary_button_text_en') }}</label><input type="text" name="sections[cta][secondary_button_text_en]" class="form-control" value="{{ old('sections.cta.secondary_button_text_en', $sections['cta']['secondary_button_text_en'] ?? '') }}"></div>
        <div class="col-md-3"><label class="form-label">{{ __('admin.secondary_button_text_ar') }}</label><input type="text" name="sections[cta][secondary_button_text_ar]" class="form-control" value="{{ old('sections.cta.secondary_button_text_ar', $sections['cta']['secondary_button_text_ar'] ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.primary_button_url') }}</label><input type="text" name="sections[cta][primary_button_url]" class="form-control" value="{{ old('sections.cta.primary_button_url', $sections['cta']['primary_button_url'] ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.secondary_button_url') }}</label><input type="text" name="sections[cta][secondary_button_url]" class="form-control" value="{{ old('sections.cta.secondary_button_url', $sections['cta']['secondary_button_url'] ?? '') }}"></div>
        <div class="col-md-6">
            <label class="form-label">{{ __('admin.background_image') }}</label>
            <input type="file" name="cta_background_image" class="form-control" accept="image/*">
            @if(!empty($sections['cta']['background_image']))
                <div class="small text-muted mt-2">{{ $sections['cta']['background_image'] }}</div>
            @endif
        </div>
        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
                <input type="hidden" name="sections[cta][enabled]" value="0">
                <input type="checkbox" class="form-check-input" id="cta-enabled" name="sections[cta][enabled]" value="1" @checked(old('sections.cta.enabled', $sections['cta']['enabled'] ?? true))>
                <label class="form-check-label" for="cta-enabled">{{ __('admin.active') }}</label>
            </div>
        </div>
    </div>
</div>
