<section class="container position-relative">
    <div class="tw-visa-hub-search-card">
        <form class="row g-3 align-items-end js-premium-service-filter" data-default-url="{{ $search['default_url'] ?? '#' }}">
            @foreach(($search['fields'] ?? []) as $field)
                <div class="col-lg">
                    <label class="form-label" for="service-search-{{ $field['name'] }}">{{ $field['label'] ?? '' }}</label>
                    <select id="service-search-{{ $field['name'] }}" class="form-select js-premium-service-select">
                        <option value="">{{ $field['placeholder'] ?? ($field['label'] ?? '') }}</option>
                        @foreach(($field['options'] ?? []) as $option)
                            <option value="{{ $option['url'] ?? '' }}">{{ $option['label'] ?? '' }}</option>
                        @endforeach
                    </select>
                </div>
            @endforeach
            <div class="col-lg-2">
                <button type="submit" class="btn btn-primary tw-btn-primary w-100">{{ $search['button'] ?? ($search['button_text'] ?? 'Search') }}</button>
            </div>
        </form>
    </div>
</section>
