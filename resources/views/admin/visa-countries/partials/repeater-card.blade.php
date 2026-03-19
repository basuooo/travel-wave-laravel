<div class="card admin-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h5 mb-1">{{ $title }}</h2>
            <p class="text-muted mb-0">{{ $description }}</p>
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add="{{ $repeaterKey }}">{{ $buttonLabel }}</button>
    </div>

    @if(!empty($sectionFields))
        <div class="row g-3 mb-4">
            @foreach($sectionFields as $field)
                <div class="col-md-6">
                    <label class="form-label">{{ $field['label'] }}</label>
                    <input class="form-control {{ !empty($field['rtl']) ? 'text-end' : '' }}"
                           @if(!empty($field['rtl'])) dir="rtl" @endif
                           name="{{ $field['name'] }}"
                           value="{{ $field['value'] }}">
                </div>
            @endforeach
        </div>
    @endif

    @if(!empty($sectionTextareas))
        <div class="row g-3 mb-4">
            @foreach($sectionTextareas as $field)
                <div class="col-md-6">
                    <label class="form-label">{{ $field['label'] }}</label>
                    <textarea class="form-control {{ !empty($field['rtl']) ? 'text-end' : '' }}"
                              @if(!empty($field['rtl'])) dir="rtl" @endif
                              name="{{ $field['name'] }}"
                              rows="3">{{ $field['value'] }}</textarea>
                </div>
            @endforeach
        </div>
    @endif

    <div class="row gy-3" data-repeater-list="{{ $repeaterKey }}">
        @foreach($items as $index => $row)
            <div class="col-12" data-repeater-item>
                <div class="border rounded-4 p-3">
                    <div class="row g-3">
                        @foreach($fields as $field)
                            @php
                                $inputType = $field['type'] ?? 'text';
                                $value = $row[$field['key']] ?? '';
                            @endphp
                            <div class="col-md-{{ $inputType === 'textarea' ? 6 : ($inputType === 'checkbox' ? 2 : 4) }}">
                                @if($inputType === 'checkbox')
                                    <div class="form-check mt-4 pt-2">
                                        <input class="form-check-input" type="checkbox" data-field="{{ $field['key'] }}" name="{{ $inputName }}[{{ $index }}][{{ $field['key'] }}]" value="1" @checked($value)>
                                        <label class="form-check-label">{{ $field['label'] }}</label>
                                    </div>
                                @elseif($inputType === 'textarea')
                                    <label class="form-label">{{ $field['label'] }}</label>
                                    <textarea class="form-control {{ !empty($field['rtl']) ? 'text-end' : '' }}"
                                              data-field="{{ $field['key'] }}"
                                              @if(!empty($field['rtl'])) dir="rtl" @endif
                                              name="{{ $inputName }}[{{ $index }}][{{ $field['key'] }}]"
                                              rows="3">{{ $value }}</textarea>
                                @else
                                    <label class="form-label">{{ $field['label'] }}</label>
                                    <input class="form-control {{ !empty($field['rtl']) ? 'text-end' : '' }}"
                                           data-field="{{ $field['key'] }}"
                                           @if(!empty($field['rtl'])) dir="rtl" @endif
                                           type="{{ $inputType }}"
                                           name="{{ $inputName }}[{{ $index }}][{{ $field['key'] }}]"
                                           value="{{ $value }}"
                                           placeholder="{{ $field['placeholder'] ?? '' }}">
                                @endif
                            </div>
                        @endforeach
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger w-100" data-repeater-remove>Remove</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
