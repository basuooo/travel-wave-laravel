<div class="border rounded-4 p-3 form-info-item-row">
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Title EN</label>
            <input type="text" name="settings[info_items][{{ $index }}][title_en]" class="form-control" value="{{ $item['title_en'] ?? '' }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Title AR</label>
            <input type="text" dir="rtl" name="settings[info_items][{{ $index }}][title_ar]" class="form-control text-end" value="{{ $item['title_ar'] ?? '' }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">Value EN</label>
            <input type="text" name="settings[info_items][{{ $index }}][value_en]" class="form-control" value="{{ $item['value_en'] ?? '' }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">Value AR</label>
            <input type="text" dir="rtl" name="settings[info_items][{{ $index }}][value_ar]" class="form-control text-end" value="{{ $item['value_ar'] ?? '' }}">
        </div>
        <div class="col-md-1">
            <label class="form-label">Order</label>
            <input type="number" name="settings[info_items][{{ $index }}][sort_order]" class="form-control" value="{{ $item['sort_order'] ?? '' }}">
        </div>
        <div class="col-md-1">
            <div class="form-check mt-4 pt-2">
                <input type="hidden" name="settings[info_items][{{ $index }}][is_active]" value="0">
                <input type="checkbox" name="settings[info_items][{{ $index }}][is_active]" value="1" class="form-check-input" id="info-active-{{ $index }}" @checked(!isset($item['is_active']) || !empty($item['is_active']))>
                <label class="form-check-label" for="info-active-{{ $index }}">Active</label>
            </div>
        </div>
        <div class="col-12 d-flex justify-content-end">
            <button type="button" class="btn btn-outline-danger remove-info-item-row">Remove</button>
        </div>
    </div>
</div>
