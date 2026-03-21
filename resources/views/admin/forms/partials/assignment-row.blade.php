<div class="border rounded-4 p-3 form-assignment-row">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Assignment Target</label>
            <select name="assignments[{{ $index }}][assignment_target]" class="form-select">
                <option value="">Select target</option>
                @foreach($assignmentTargets as $group => $options)
                    <optgroup label="{{ $group }}">
                        @foreach($options as $value => $label)
                            <option value="{{ $value }}" @selected(($assignment['assignment_target'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Display Position</label>
            <select name="assignments[{{ $index }}][display_position]" class="form-select">
                @foreach($positionOptions as $value => $label)
                    <option value="{{ $value }}" @selected(($assignment['display_position'] ?? 'bottom') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1">
            <label class="form-label">Order</label>
            <input type="number" name="assignments[{{ $index }}][sort_order]" class="form-control" value="{{ $assignment['sort_order'] ?? '' }}">
        </div>
        <div class="col-md-1">
            <div class="form-check mt-4 pt-2">
                <input type="hidden" name="assignments[{{ $index }}][is_active]" value="0">
                <input type="checkbox" name="assignments[{{ $index }}][is_active]" value="1" class="form-check-input" id="assignment-active-{{ $index }}" @checked(!isset($assignment['is_active']) || !empty($assignment['is_active']))>
                <label class="form-check-label" for="assignment-active-{{ $index }}">Active</label>
            </div>
        </div>
        <div class="col-md-1 d-flex align-items-end justify-content-end">
            <button type="button" class="btn btn-outline-danger remove-assignment-row">Remove</button>
        </div>
    </div>
</div>
