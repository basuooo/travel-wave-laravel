<div class="border rounded p-3 map-assignment-row">
    <div class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label">Assignment Target</label>
            <select name="assignments[{{ $index }}][assignment_target]" class="form-select">
                <option value="">Choose where this map should appear</option>
                @foreach($assignmentTargets as $groupLabel => $options)
                    <optgroup label="{{ $groupLabel }}">
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
        <div class="col-md-2">
            <label class="form-label">Order</label>
            <input type="number" name="assignments[{{ $index }}][sort_order]" class="form-control" value="{{ $assignment['sort_order'] ?? (is_numeric($index) ? ((int) $index + 1) : 1) }}">
        </div>
        <div class="col-md-1">
            <div class="form-check mt-4">
                <input type="hidden" name="assignments[{{ $index }}][is_active]" value="0">
                <input type="checkbox" name="assignments[{{ $index }}][is_active]" value="1" class="form-check-input" id="map-assignment-active-{{ $index }}" @checked(!isset($assignment['is_active']) || !empty($assignment['is_active']))>
            </div>
        </div>
        <div class="col-md-1 text-end">
            <button type="button" class="btn btn-outline-danger btn-sm remove-map-assignment">Remove</button>
        </div>
    </div>
</div>
