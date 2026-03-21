<div class="card admin-card p-4 mb-4">
    <h2 class="h5 mb-3">{{ __('admin.form_section') }}</h2>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label">{{ __('admin.section_title_en') }}</label><input type="text" name="sections[form][title_en]" class="form-control" value="{{ old('sections.form.title_en', $sections['form']['title_en'] ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.section_title_ar') }}</label><input type="text" name="sections[form][title_ar]" class="form-control" value="{{ old('sections.form.title_ar', $sections['form']['title_ar'] ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.section_subtitle_en') }}</label><textarea name="sections[form][subtitle_en]" class="form-control" rows="3">{{ old('sections.form.subtitle_en', $sections['form']['subtitle_en'] ?? '') }}</textarea></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.section_subtitle_ar') }}</label><textarea name="sections[form][subtitle_ar]" class="form-control" rows="3">{{ old('sections.form.subtitle_ar', $sections['form']['subtitle_ar'] ?? '') }}</textarea></div>
        <div class="col-12">
            <input type="hidden" name="sections[form][enabled]" value="0">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="form-enabled" name="sections[form][enabled]" value="1" @checked(old('sections.form.enabled', $sections['form']['enabled'] ?? true))>
                <label class="form-check-label" for="form-enabled">{{ __('admin.active') }}</label>
            </div>
        </div>
    </div>
</div>
