<div class="border rounded-4 p-3 form-field-row">
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Field Key</label>
            <div class="input-group">
                <input type="text" name="fields[{{ $index }}][field_key]" class="form-control field-key-input" value="{{ $field['field_key'] ?? '' }}" placeholder="full_name">
                <button type="button" class="btn btn-outline-secondary copy-field-key" title="Copy Key">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label">Field Type</label>
            <select name="fields[{{ $index }}][type]" class="form-select">
                @foreach($fieldTypeOptions as $key => $label)
                    <option value="{{ $key }}" @selected(($field['type'] ?? 'text') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Label EN</label>
            <input type="text" name="fields[{{ $index }}][label_en]" class="form-control" value="{{ $field['label_en'] ?? '' }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Label AR</label>
            <input type="text" dir="rtl" name="fields[{{ $index }}][label_ar]" class="form-control text-end" value="{{ $field['label_ar'] ?? '' }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Placeholder EN</label>
            <input type="text" name="fields[{{ $index }}][placeholder_en]" class="form-control" value="{{ $field['placeholder_en'] ?? '' }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Placeholder AR</label>
            <input type="text" dir="rtl" name="fields[{{ $index }}][placeholder_ar]" class="form-control text-end" value="{{ $field['placeholder_ar'] ?? '' }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Default Value</label>
            <input type="text" name="fields[{{ $index }}][default_value]" class="form-control" value="{{ $field['default_value'] ?? '' }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Sort Order</label>
            <input type="number" name="fields[{{ $index }}][sort_order]" class="form-control" value="{{ $field['sort_order'] ?? '' }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Help Text EN</label>
            <textarea name="fields[{{ $index }}][help_text_en]" class="form-control" rows="2">{{ $field['help_text_en'] ?? '' }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Help Text AR</label>
            <textarea name="fields[{{ $index }}][help_text_ar]" class="form-control text-end" dir="rtl" rows="2">{{ $field['help_text_ar'] ?? '' }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Validation Rule</label>
            <input type="text" name="fields[{{ $index }}][validation_rule]" class="form-control" value="{{ $field['validation_rule'] ?? '' }}" placeholder="nullable|string|max:255">
        </div>
        <div class="col-md-3">
            <label class="form-label text-primary">Depends on Field (Key)</label>
            <input type="text" name="fields[{{ $index }}][depends_on_field]" class="form-control border-primary" value="{{ $field['depends_on_field'] ?? '' }}" placeholder="e.g. service_type" list="field-keys-list">
            <div class="form-text text-primary small">Key of the parent field.</div>
        </div>
        <div class="col-md-3">
            <label class="form-label text-primary">Depends on Value(s)</label>
            <input type="text" name="fields[{{ $index }}][depends_on_value]" class="form-control border-primary" value="{{ $field['depends_on_value'] ?? '' }}" placeholder="e.g. visa,work_permit">
            <div class="form-text text-primary small">Triggers. Use comma (,) for multiple.</div>
        </div>
        <div class="col-md-12 field-options-wrapper" style="{{ ($field['type'] ?? 'text') === 'select' ? '' : 'display: none;' }}">
            <label class="form-label mb-2">Dropdown Options</label>
            <div class="row g-2 js-dual-tags-wrapper">
                <div class="col-md-6">
                    <label class="form-label small text-muted mb-1">Options EN (Press Tab/Enter to add)</label>
                    <div class="form-control d-flex flex-wrap gap-1 align-items-center js-tags-container-en" style="min-height: 38px; cursor: text;">
                        <input type="text" class="border-0 shadow-none js-tags-input flex-grow-1" style="min-width: 120px; outline: none; background: transparent;" placeholder="e.g. Egypt, Saudi Arabia">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted mb-1">Options AR (Press Tab/Enter to add)</label>
                    <div class="form-control d-flex flex-wrap gap-1 align-items-center js-tags-container-ar" style="min-height: 38px; cursor: text;">
                        <input type="text" class="border-0 shadow-none js-tags-input flex-grow-1" style="min-width: 120px; outline: none; background: transparent;" placeholder="مثال: مصر, السعودية" dir="rtl">
                    </div>
                </div>
                <textarea name="fields[{{ $index }}][options_text]" class="d-none js-dual-hidden-textarea">{{ $field['options_text'] ?? '' }}</textarea>
                <div class="col-12 form-text">Add the English and Arabic translations for each option. The system will link them together automatically. You can paste a comma-separated list.</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-check mt-4 pt-2">
                <input type="hidden" name="fields[{{ $index }}][is_required]" value="0">
                <input type="checkbox" name="fields[{{ $index }}][is_required]" value="1" class="form-check-input" id="field-required-{{ $index }}" @checked(!empty($field['is_required']))>
                <label class="form-check-label" for="field-required-{{ $index }}">Required</label>
                <div class="form-text">If enabled, this field must be filled before submission.</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-check mt-4 pt-2">
                <input type="hidden" name="fields[{{ $index }}][is_enabled]" value="0">
                <input type="checkbox" name="fields[{{ $index }}][is_enabled]" value="1" class="form-check-input" id="field-enabled-{{ $index }}" @checked(!isset($field['is_enabled']) || !empty($field['is_enabled']))>
                <label class="form-check-label" for="field-enabled-{{ $index }}">Enabled</label>
                <div class="form-text">Disable it to hide the field from the frontend form completely.</div>
            </div>
        </div>
        <div class="col-md-6 d-flex align-items-end justify-content-end">
            <button type="button" class="btn btn-outline-danger remove-field-row">Remove Field</button>
        </div>
    </div>
</div>
