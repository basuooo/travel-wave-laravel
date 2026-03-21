<div class="border rounded-4 p-3 form-field-row">
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Field Key</label>
            <input type="text" name="fields[{{ $index }}][field_key]" class="form-control" value="{{ $field['field_key'] ?? '' }}" placeholder="full_name">
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
        <div class="col-md-6">
            <label class="form-label">Select Options</label>
            <textarea name="fields[{{ $index }}][options_text]" class="form-control" rows="2" placeholder="value|Label EN|Label AR">{{ $field['options_text'] ?? '' }}</textarea>
        </div>
        <div class="col-md-3">
            <div class="form-check mt-4 pt-2">
                <input type="hidden" name="fields[{{ $index }}][is_required]" value="0">
                <input type="checkbox" name="fields[{{ $index }}][is_required]" value="1" class="form-check-input" id="field-required-{{ $index }}" @checked(!empty($field['is_required']))>
                <label class="form-check-label" for="field-required-{{ $index }}">Required</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-check mt-4 pt-2">
                <input type="hidden" name="fields[{{ $index }}][is_enabled]" value="0">
                <input type="checkbox" name="fields[{{ $index }}][is_enabled]" value="1" class="form-check-input" id="field-enabled-{{ $index }}" @checked(!isset($field['is_enabled']) || !empty($field['is_enabled']))>
                <label class="form-check-label" for="field-enabled-{{ $index }}">Enabled</label>
            </div>
        </div>
        <div class="col-md-6 d-flex align-items-end justify-content-end">
            <button type="button" class="btn btn-outline-danger remove-field-row">Remove Field</button>
        </div>
    </div>
</div>
