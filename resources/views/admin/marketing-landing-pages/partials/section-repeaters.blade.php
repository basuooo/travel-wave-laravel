<div class="card admin-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">{{ __('admin.' . $titleKey) }}</h2>
        <button type="button" class="btn btn-sm btn-outline-primary js-add-row" data-target="#{{ $sectionKey }}-rows" data-template="{{ $sectionKey }}-row-template">{{ __('admin.add_item') }}</button>
    </div>
    <div class="row g-3 mb-3">
        <div class="col-md-6"><label class="form-label">{{ __('admin.section_title_en') }}</label><input type="text" name="sections[{{ $sectionKey }}][title_en]" class="form-control" value="{{ old('sections.' . $sectionKey . '.title_en', $sections[$sectionKey]['title_en'] ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.section_title_ar') }}</label><input type="text" name="sections[{{ $sectionKey }}][title_ar]" class="form-control" value="{{ old('sections.' . $sectionKey . '.title_ar', $sections[$sectionKey]['title_ar'] ?? '') }}"></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.section_subtitle_en') }}</label><textarea name="sections[{{ $sectionKey }}][subtitle_en]" class="form-control" rows="2">{{ old('sections.' . $sectionKey . '.subtitle_en', $sections[$sectionKey]['subtitle_en'] ?? '') }}</textarea></div>
        <div class="col-md-6"><label class="form-label">{{ __('admin.section_subtitle_ar') }}</label><textarea name="sections[{{ $sectionKey }}][subtitle_ar]" class="form-control" rows="2">{{ old('sections.' . $sectionKey . '.subtitle_ar', $sections[$sectionKey]['subtitle_ar'] ?? '') }}</textarea></div>
        @if(!empty($backgroundUpload))
            <div class="col-md-6">
                <label class="form-label">{{ __('admin.background_image') }}</label>
                <input type="file" name="{{ $backgroundUpload }}" class="form-control" accept="image/*">
                @if(!empty($sections[$sectionKey]['background_image']))
                    <div class="small text-muted mt-2">{{ $sections[$sectionKey]['background_image'] }}</div>
                @endif
            </div>
        @endif
        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
                <input type="hidden" name="sections[{{ $sectionKey }}][enabled]" value="0">
                <input type="checkbox" class="form-check-input" id="{{ $sectionKey }}-enabled" name="sections[{{ $sectionKey }}][enabled]" value="1" @checked(old('sections.' . $sectionKey . '.enabled', $sections[$sectionKey]['enabled'] ?? true))>
                <label class="form-check-label" for="{{ $sectionKey }}-enabled">{{ __('admin.active') }}</label>
            </div>
        </div>
    </div>
    <div id="{{ $sectionKey }}-rows" class="d-grid gap-3">
        @foreach($rows as $index => $row)
            @include('admin.marketing-landing-pages.partials.simple-item-row', ['group' => 'sections[' . $sectionKey . '][items]', 'index' => $index, 'item' => $row, 'fields' => $fields])
        @endforeach
    </div>
</div>
