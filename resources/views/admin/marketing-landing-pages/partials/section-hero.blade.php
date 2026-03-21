<div class="card admin-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">{{ __('admin.hero_section') }}</h2>
        <div class="form-check">
            <input type="hidden" name="sections[hero][enabled]" value="0">
            <input type="checkbox" class="form-check-input" id="hero-enabled" name="sections[hero][enabled]" value="1" @checked(old('sections.hero.enabled', $sections['hero']['enabled'] ?? true))>
            <label class="form-check-label" for="hero-enabled">{{ __('admin.active') }}</label>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">{{ __('admin.hero_eyebrow_en') }}</label><input type="text" name="sections[hero][eyebrow_en]" class="form-control" value="{{ old('sections.hero.eyebrow_en', $sections['hero']['eyebrow_en'] ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.hero_eyebrow_ar') }}</label><input type="text" name="sections[hero][eyebrow_ar]" class="form-control" value="{{ old('sections.hero.eyebrow_ar', $sections['hero']['eyebrow_ar'] ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.hero_title_en') }}</label><input type="text" name="sections[hero][title_en]" class="form-control" value="{{ old('sections.hero.title_en', $sections['hero']['title_en'] ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.hero_title_ar') }}</label><input type="text" name="sections[hero][title_ar]" class="form-control" value="{{ old('sections.hero.title_ar', $sections['hero']['title_ar'] ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.hero_subtitle_en') }}</label><textarea name="sections[hero][subtitle_en]" class="form-control" rows="3">{{ old('sections.hero.subtitle_en', $sections['hero']['subtitle_en'] ?? '') }}</textarea></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.hero_subtitle_ar') }}</label><textarea name="sections[hero][subtitle_ar]" class="form-control" rows="3">{{ old('sections.hero.subtitle_ar', $sections['hero']['subtitle_ar'] ?? '') }}</textarea></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.primary_button_text_en') }}</label><input type="text" name="sections[hero][primary_button_text_en]" class="form-control" value="{{ old('sections.hero.primary_button_text_en', $sections['hero']['primary_button_text_en'] ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.primary_button_text_ar') }}</label><input type="text" name="sections[hero][primary_button_text_ar]" class="form-control" value="{{ old('sections.hero.primary_button_text_ar', $sections['hero']['primary_button_text_ar'] ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.primary_button_url') }}</label><input type="text" name="sections[hero][primary_button_url]" class="form-control" value="{{ old('sections.hero.primary_button_url', $sections['hero']['primary_button_url'] ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.secondary_button_url') }}</label><input type="text" name="sections[hero][secondary_button_url]" class="form-control" value="{{ old('sections.hero.secondary_button_url', $sections['hero']['secondary_button_url'] ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.secondary_button_text_en') }}</label><input type="text" name="sections[hero][secondary_button_text_en]" class="form-control" value="{{ old('sections.hero.secondary_button_text_en', $sections['hero']['secondary_button_text_en'] ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.secondary_button_text_ar') }}</label><input type="text" name="sections[hero][secondary_button_text_ar]" class="form-control" value="{{ old('sections.hero.secondary_button_text_ar', $sections['hero']['secondary_button_text_ar'] ?? '') }}"></div>
        <div class="col-12">
            <label class="form-label">{{ __('admin.background_image') }}</label>
            <input type="file" name="hero_background_image" class="form-control" accept="image/*">
            @if(!empty($sections['hero']['background_image']))
                <div class="small text-muted mt-2">{{ $sections['hero']['background_image'] }}</div>
            @endif
        </div>
    </div>
</div>
