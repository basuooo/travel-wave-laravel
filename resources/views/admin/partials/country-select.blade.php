@php
    use App\Support\WorldCountries;

    $name = $name ?? 'country';
    $currentVal = $value ?? '';
    $id = $id ?? 'country_select_' . \Illuminate\Support\Str::random(6);
    $placeholder = $placeholder ?? __('admin.home_search_country') ?? 'اختر الدولة...';
    $required = !empty($required);
    $class = $class ?? '';

    $allCountries = WorldCountries::all();
    $matchedCountry = WorldCountries::findMatch($currentVal);
    $isCustomValue = !empty($currentVal) && !$matchedCountry;

    // Determine displayed text and flag for trigger
    if ($matchedCountry) {
        $selectedText = $matchedCountry['name_ar'];
        $selectedFlag = $matchedCountry['flag'];
        $inputValue = $matchedCountry['name_ar'];
    } elseif ($isCustomValue) {
        $selectedText = $currentVal;
        $selectedFlag = '📍';
        $inputValue = $currentVal;
    } else {
        $selectedText = '';
        $selectedFlag = '';
        $inputValue = '';
    }
@endphp

<div class="dropdown tw-country-select-wrapper {{ $class }}" id="{{ $id }}" data-country-select>
    <input type="hidden" name="{{ $name }}" class="js-country-hidden-input" value="{{ $inputValue }}" @if($required) required @endif>

    <button type="button" 
            class="form-select text-start d-flex align-items-center justify-content-between gap-2 js-country-trigger" 
            data-bs-toggle="dropdown" 
            aria-expanded="false"
            style="cursor: pointer; background-color: var(--bs-body-bg, #fff);">
        <span class="js-country-trigger-text text-truncate">
            @if(!empty($selectedText))
                <span class="me-1">{{ $selectedFlag }}</span> <strong>{{ $selectedText }}</strong>
            @else
                <span class="text-muted">{{ $placeholder }}</span>
            @endif
        </span>
    </button>

    <div class="dropdown-menu shadow-lg p-2 w-100 js-country-dropdown" style="min-width: 240px; max-width: 100%; z-index: 1055;">
        <div class="mb-2 sticky-top bg-body pt-1 pb-1 border-bottom">
            <input type="text" 
                   class="form-control form-control-sm js-country-search-input" 
                   placeholder="🔍 ابحث عن الدولة (بالعربي أو EN)..." 
                   dir="rtl" 
                   autocomplete="off">
        </div>

        <div class="js-country-options-list" style="max-height: 240px; overflow-y: auto;">
            <!-- Option for empty selection -->
            <button type="button" 
                    class="dropdown-item py-2 px-2 d-flex align-items-center justify-content-between rounded js-country-option @if(empty($inputValue)) active @endif" 
                    data-val="" 
                    data-text="" 
                    data-flag="">
                <span class="text-muted small">-- {{ __('admin.all') ?? 'بدون تحديد' }} --</span>
            </button>

            @if($isCustomValue)
                <!-- Custom value preserved option -->
                <button type="button" 
                        class="dropdown-item py-2 px-2 d-flex align-items-center justify-content-between rounded active js-country-option" 
                        data-val="{{ $currentVal }}" 
                        data-text="{{ $currentVal }}" 
                        data-flag="📍" 
                        data-search="{{ mb_strtolower($currentVal) }}">
                    <span><span class="me-1">📍</span> <strong>{{ $currentVal }}</strong> <small class="text-muted">(مُدخل سابقاً)</small></span>
                    <span class="badge bg-secondary">سابق</span>
                </button>
            @endif

            @foreach($allCountries as $country)
                @php
                    $isSel = $matchedCountry && ($matchedCountry['code'] === $country['code'] || $matchedCountry['name_ar'] === $country['name_ar']);
                    $searchData = mb_strtolower($country['name_ar'] . ' ' . $country['name_en'] . ' ' . $country['code']);
                @endphp
                <button type="button" 
                        class="dropdown-item py-2 px-2 d-flex align-items-center justify-content-between rounded js-country-option @if($isSel) active @endif" 
                        data-val="{{ $country['name_ar'] }}" 
                        data-text="{{ $country['name_ar'] }}" 
                        data-flag="{{ $country['flag'] }}" 
                        data-search="{{ $searchData }}">
                    <span>
                        <span class="me-2 fs-6">{{ $country['flag'] }}</span>
                        <span>{{ $country['name_ar'] }}</span>
                    </span>
                    <span class="text-muted small text-uppercase ms-2">{{ $country['name_en'] }}</span>
                </button>
            @endforeach
        </div>

        <div class="js-no-country-found p-2 text-center text-muted small d-none border-top mt-1">
            لا تظهر الدولة المطلوبة؟ <button type="button" class="btn btn-link btn-sm p-0 js-use-custom-search-val">استخدام النص المكتوب</button>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        initCountrySelects();
    });

    // Handle dynamic inclusions
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-country-select]')) {
            const wrapper = e.target.closest('[data-country-select]');
            if (!wrapper.dataset.initialized) {
                setupSelectInstance(wrapper);
            }
        }
    });

    function initCountrySelects() {
        document.querySelectorAll('[data-country-select]').forEach(function(wrapper) {
            setupSelectInstance(wrapper);
        });
    }

    function setupSelectInstance(wrapper) {
        if (wrapper.dataset.initialized) return;
        wrapper.dataset.initialized = 'true';

        const trigger = wrapper.querySelector('.js-country-trigger');
        const triggerText = wrapper.querySelector('.js-country-trigger-text');
        const dropdown = wrapper.querySelector('.js-country-dropdown');
        const searchInput = wrapper.querySelector('.js-country-search-input');
        const hiddenInput = wrapper.querySelector('.js-country-hidden-input');
        const optionsList = wrapper.querySelector('.js-country-options-list');
        const options = wrapper.querySelectorAll('.js-country-option');
        const noFoundBox = wrapper.querySelector('.js-no-country-found');
        const useCustomBtn = wrapper.querySelector('.js-use-custom-search-val');

        // Focus search input when dropdown opens
        if (trigger) {
            trigger.addEventListener('shown.bs.dropdown', function() {
                if (searchInput) {
                    searchInput.value = '';
                    filterOptions('');
                    searchInput.focus();
                }
            });
        }

        // Live search filtering
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                filterOptions(this.value.trim().toLowerCase());
            });

            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const visibleOption = optionsList.querySelector('.js-country-option:not(.d-none)');
                    if (visibleOption) {
                        selectOption(visibleOption);
                    } else if (searchInput.value.trim()) {
                        setCustomValue(searchInput.value.trim());
                    }
                }
            });
        }

        function filterOptions(query) {
            let visibleCount = 0;
            options.forEach(function(opt) {
                const searchStr = opt.getAttribute('data-search') || '';
                const val = opt.getAttribute('data-val') || '';
                if (!query || !val || searchStr.includes(query)) {
                    opt.classList.remove('d-none');
                    visibleCount++;
                } else {
                    opt.classList.add('d-none');
                }
            });

            if (noFoundBox) {
                if (visibleCount === 0 && query !== '') {
                    noFoundBox.classList.remove('d-none');
                } else {
                    noFoundBox.classList.add('d-none');
                }
            }
        }

        function selectOption(opt) {
            const val = opt.getAttribute('data-val') || '';
            const text = opt.getAttribute('data-text') || '';
            const flag = opt.getAttribute('data-flag') || '';

            hiddenInput.value = val;
            
            // Dispatch change event on hidden input so listeners pick it up
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));

            if (val) {
                triggerText.innerHTML = `<span class="me-1">${flag}</span> <strong>${text}</strong>`;
            } else {
                triggerText.innerHTML = `<span class="text-muted">${wrapper.getAttribute('data-placeholder') || 'اختر الدولة...'}</span>`;
            }

            options.forEach(o => o.classList.remove('active'));
            opt.classList.add('active');

            // Close Bootstrap dropdown instance
            closeDropdown();
        }

        function setCustomValue(customVal) {
            hiddenInput.value = customVal;
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            triggerText.innerHTML = `<span class="me-1">📍</span> <strong>${customVal}</strong>`;
            closeDropdown();
        }

        function closeDropdown() {
            if (window.bootstrap && window.bootstrap.Dropdown) {
                const bsDropdown = bootstrap.Dropdown.getInstance(trigger);
                if (bsDropdown) bsDropdown.hide();
            } else {
                dropdown.classList.remove('show');
            }
        }

        // Click option handler
        options.forEach(function(opt) {
            opt.addEventListener('click', function(e) {
                e.preventDefault();
                selectOption(this);
            });
        });

        if (useCustomBtn) {
            useCustomBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (searchInput && searchInput.value.trim()) {
                    setCustomValue(searchInput.value.trim());
                }
            });
        }
    }
})();
</script>
@endpush
@endonce
