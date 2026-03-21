<div class="card border js-repeatable-row">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="fw-semibold">{{ __('admin.item') }} #{{ is_numeric($index) ? $index + 1 : $index }}</div>
            <button type="button" class="btn btn-sm btn-outline-danger js-remove-row">{{ __('admin.delete') }}</button>
        </div>
        <div class="row g-3">
            @foreach($fields as $field)
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.field_' . $field . '_en') }}</label>
                    <input type="text" name="{{ $group }}[{{ $index }}][{{ $field }}_en]" class="form-control" value="{{ $item[$field . '_en'] ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.field_' . $field . '_ar') }}</label>
                    <input type="text" name="{{ $group }}[{{ $index }}][{{ $field }}_ar]" class="form-control" value="{{ $item[$field . '_ar'] ?? '' }}">
                </div>
            @endforeach
            <div class="col-md-6">
                <label class="form-label">{{ __('admin.sort_order') }}</label>
                <input type="number" min="0" class="form-control" name="{{ $group }}[{{ $index }}][sort_order]" value="{{ $item['sort_order'] ?? ((is_numeric($index) ? $index : 0) + 1) }}">
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <div class="form-check">
                    <input type="hidden" name="{{ $group }}[{{ $index }}][is_active]" value="0">
                    <input type="checkbox" class="form-check-input" id="{{ md5($group . $index) }}" name="{{ $group }}[{{ $index }}][is_active]" value="1" @checked(($item['is_active'] ?? true))>
                    <label class="form-check-label" for="{{ md5($group . $index) }}">{{ __('admin.active') }}</label>
                </div>
            </div>
        </div>
    </div>
</div>
