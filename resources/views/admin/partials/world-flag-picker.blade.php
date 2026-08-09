@php
    $currentVal = $currentValue ?? '';
    $pickerId = 'flag_picker_' . \Illuminate\Support\Str::random(6);
    $allWorldFlags = [
        ['code' => 'eu', 'name_ar' => 'الاتحاد الأوروبي (شنغن)', 'name_en' => 'European Union (Schengen)'],
        ['code' => 'fr', 'name_ar' => 'فرنسا', 'name_en' => 'France'],
        ['code' => 'eg', 'name_ar' => 'مصر', 'name_en' => 'Egypt'],
        ['code' => 'sa', 'name_ar' => 'السعودية', 'name_en' => 'Saudi Arabia'],
        ['code' => 'ae', 'name_ar' => 'الإمارات', 'name_en' => 'United Arab Emirates'],
        ['code' => 'tr', 'name_ar' => 'تركيا', 'name_en' => 'Turkey'],
        ['code' => 'gb', 'name_ar' => 'المملكة المتحدة (بريطانيا)', 'name_en' => 'United Kingdom'],
        ['code' => 'us', 'name_ar' => 'الولايات المتحدة (أمريكا)', 'name_en' => 'United States'],
        ['code' => 'it', 'name_ar' => 'إيطاليا', 'name_en' => 'Italy'],
        ['code' => 'es', 'name_ar' => 'إسبانيا', 'name_en' => 'Spain'],
        ['code' => 'de', 'name_ar' => 'ألمانيا', 'name_en' => 'Germany'],
        ['code' => 'jp', 'name_ar' => 'اليابان', 'name_en' => 'Japan'],
        ['code' => 'cn', 'name_ar' => 'الصين', 'name_en' => 'China'],
        ['code' => 'ca', 'name_ar' => 'كندا', 'name_en' => 'Canada'],
        ['code' => 'au', 'name_ar' => 'أستراليا', 'name_en' => 'Australia'],
        ['code' => 'ge', 'name_ar' => 'جورجيا', 'name_en' => 'Georgia'],
        ['code' => 'az', 'name_ar' => 'أذربيجان', 'name_en' => 'Azerbaijan'],
        ['code' => 'am', 'name_ar' => 'أرمينيا', 'name_en' => 'Armenia'],
        ['code' => 'om', 'name_ar' => 'عُمان', 'name_en' => 'Oman'],
        ['code' => 'qa', 'name_ar' => 'قطر', 'name_en' => 'Qatar'],
        ['code' => 'kw', 'name_ar' => 'الكويت', 'name_en' => 'Kuwait'],
        ['code' => 'bh', 'name_ar' => 'البحرين', 'name_en' => 'Bahrain'],
        ['code' => 'jo', 'name_ar' => 'الأردن', 'name_en' => 'Jordan'],
        ['code' => 'lb', 'name_ar' => 'لبنان', 'name_en' => 'Lebanon'],
        ['code' => 'iq', 'name_ar' => 'العراق', 'name_en' => 'Iraq'],
        ['code' => 'ma', 'name_ar' => 'المغرب', 'name_en' => 'Morocco'],
        ['code' => 'tn', 'name_ar' => 'تونس', 'name_en' => 'Tunisia'],
        ['code' => 'dz', 'name_ar' => 'الجزائر', 'name_en' => 'Algeria'],
        ['code' => 'ru', 'name_ar' => 'روسيا', 'name_en' => 'Russia'],
        ['code' => 'th', 'name_ar' => 'تايلاند', 'name_en' => 'Thailand'],
        ['code' => 'my', 'name_ar' => 'ماليزيا', 'name_en' => 'Malaysia'],
        ['code' => 'sg', 'name_ar' => 'سنغافورة', 'name_en' => 'Singapore'],
        ['code' => 'id', 'name_ar' => 'إندونيسيا', 'name_en' => 'Indonesia'],
        ['code' => 'in', 'name_ar' => 'الهند', 'name_en' => 'India'],
        ['code' => 'pk', 'name_ar' => 'باكستان', 'name_en' => 'Pakistan'],
        ['code' => 'ch', 'name_ar' => 'سويسرا', 'name_en' => 'Switzerland'],
        ['code' => 'nl', 'name_ar' => 'هولندا', 'name_en' => 'Netherlands'],
        ['code' => 'se', 'name_ar' => 'السويد', 'name_en' => 'Sweden'],
        ['code' => 'no', 'name_ar' => 'النرويج', 'name_en' => 'Norway'],
        ['code' => 'fi', 'name_ar' => 'فنلندا', 'name_en' => 'Finland'],
        ['code' => 'dk', 'name_ar' => 'الدنمارك', 'name_en' => 'Denmark'],
        ['code' => 'gr', 'name_ar' => 'اليونان', 'name_en' => 'Greece'],
        ['code' => 'pt', 'name_ar' => 'البرتغال', 'name_en' => 'Portugal'],
        ['code' => 'at', 'name_ar' => 'النمسا', 'name_en' => 'Austria'],
        ['code' => 'be', 'name_ar' => 'بلجيكا', 'name_en' => 'Belgium'],
        ['code' => 'ie', 'name_ar' => 'أيرلندا', 'name_en' => 'Ireland'],
        ['code' => 'cz', 'name_ar' => 'التشيك', 'name_en' => 'Czechia'],
        ['code' => 'hu', 'name_ar' => 'المجر', 'name_en' => 'Hungary'],
        ['code' => 'pl', 'name_ar' => 'بولندا', 'name_en' => 'Poland'],
        ['code' => 'ro', 'name_ar' => 'رومانيا', 'name_en' => 'Romania'],
        ['code' => 'bg', 'name_ar' => 'بلغاريا', 'name_en' => 'Bulgaria'],
        ['code' => 'sk', 'name_ar' => 'سلوفاكيا', 'name_en' => 'Slovakia'],
        ['code' => 'si', 'name_ar' => 'سلوفينيا', 'name_en' => 'Slovenia'],
        ['code' => 'hr', 'name_ar' => 'كرواتيا', 'name_en' => 'Croatia'],
        ['code' => 'cy', 'name_ar' => 'قبرص', 'name_en' => 'Cyprus'],
        ['code' => 'mt', 'name_ar' => 'مالطا', 'name_en' => 'Malta'],
        ['code' => 'is', 'name_ar' => 'أيسلندا', 'name_en' => 'Iceland'],
        ['code' => 'lu', 'name_ar' => 'لوكسمبورغ', 'name_en' => 'Luxembourg'],
        ['code' => 'ee', 'name_ar' => 'إستونيا', 'name_en' => 'Estonia'],
        ['code' => 'lv', 'name_ar' => 'لاتفيا', 'name_en' => 'Latvia'],
        ['code' => 'lt', 'name_ar' => 'ليتوانيا', 'name_en' => 'Lithuania'],
        ['code' => 'za', 'name_ar' => 'جنوب إفريقيا', 'name_en' => 'South Africa'],
        ['code' => 'mx', 'name_ar' => 'المكسيك', 'name_en' => 'Mexico'],
        ['code' => 'br', 'name_ar' => 'البرازيل', 'name_en' => 'Brazil'],
        ['code' => 'ar', 'name_ar' => 'الأرجنتين', 'name_en' => 'Argentina'],
        ['code' => 'cl', 'name_ar' => 'تشيلي', 'name_en' => 'Chile'],
        ['code' => 'co', 'name_ar' => 'كولومبيا', 'name_en' => 'Colombia'],
        ['code' => 'pe', 'name_ar' => 'بيرو', 'name_en' => 'Peru'],
        ['code' => 'nz', 'name_ar' => 'نيوزيلندا', 'name_en' => 'New Zealand'],
        ['code' => 'me', 'name_ar' => 'الجبل الأسود (مونتينيغرو)', 'name_en' => 'Montenegro'],
        ['code' => 'al', 'name_ar' => 'ألبانيا', 'name_en' => 'Albania'],
        ['code' => 'mk', 'name_ar' => 'مقدونيا الشمالية', 'name_en' => 'North Macedonia'],
        ['code' => 'rs', 'name_ar' => 'صربيا', 'name_en' => 'Serbia'],
        ['code' => 'ba', 'name_ar' => 'البوسنة والهرسك', 'name_en' => 'Bosnia and Herzegovina'],
        ['code' => 'md', 'name_ar' => 'مولدوفا', 'name_en' => 'Moldova'],
        ['code' => 'by', 'name_ar' => 'بيلاروسيا', 'name_en' => 'Belarus'],
        ['code' => 'ua', 'name_ar' => 'أوكرانيا', 'name_en' => 'Ukraine'],
        ['code' => 'vn', 'name_ar' => 'فيتنام', 'name_en' => 'Vietnam'],
        ['code' => 'ph', 'name_ar' => 'الفلبين', 'name_en' => 'Philippines'],
        ['code' => 'kr', 'name_ar' => 'كوريا الجنوبية', 'name_en' => 'South Korea'],
    ];

    $presetUrl = str_starts_with($currentVal, 'http://') || str_starts_with($currentVal, 'https://') ? $currentVal : '';
    $localImagePath = (!empty($currentVal) && !$presetUrl) ? asset('storage/' . $currentVal) : '';
@endphp

<div class="tw-flag-picker-container" id="{{ $pickerId }}">
    <input type="hidden" name="preset_flag_url" class="js-preset-flag-input" value="{{ old('preset_flag_url', $presetUrl) }}">

    <div class="d-flex align-items-center gap-2 mb-2">
        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold js-open-flag-modal">
            🚩 اختر علم الدولة جاهزاً من القائمة
        </button>
        <span class="text-muted small">أو ارفع صورة خاصة أدناه</span>
    </div>

    <!-- PREVIEW BOX -->
    <div class="card p-2 mb-2 bg-light border js-flag-preview-box" style="max-width: 280px; {{ empty($presetUrl) && empty($localImagePath) ? 'display:none;' : '' }}">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ $presetUrl ?: $localImagePath }}" alt="Flag" class="js-flag-preview-img border rounded" style="height: 32px; width: 48px; object-fit: contain; background: #fff;">
                <span class="small fw-bold js-flag-preview-name text-dark">
                    {{ $presetUrl ? 'علم مختار' : 'صورة مرفوعة' }}
                </span>
            </div>
            <button type="button" class="btn-close btn-sm js-clear-flag" title="إزالة العلم"></button>
        </div>
    </div>

    <!-- CUSTOM FILE UPLOAD FALLBACK -->
    <input type="file" class="form-control form-control-sm" name="{{ $fileInputName ?? 'flag_image' }}" accept="image/*,.svg">

    <!-- FLAG SELECTION MODAL -->
    <div class="modal fade js-flag-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy text-white">
                    <h5 class="modal-title fw-bold">🚩 اختر علم الدولة (World Flags Selector)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <input type="text" class="form-control form-control-lg text-end js-flag-search-input" dir="rtl" placeholder="🔍 ابحث عن اسم الدولة بالعربي أو الإنجليزي (مثل: فرنسا، مصر، السعودية، ألمانيا)...">
                    </div>
                    <div class="row g-2 js-flag-grid">
                        @foreach($allWorldFlags as $flag)
                            @php($flagUrl = "https://flagcdn.com/w80/{$flag['code']}.png")
                            <div class="col-6 col-sm-4 col-md-3 js-flag-card-col" data-search="{{ mb_strtolower($flag['name_ar'] . ' ' . $flag['name_en']) }}">
                                <button type="button" class="btn btn-outline-light text-dark w-100 p-2 text-start border rounded-3 d-flex align-items-center gap-2 js-select-flag-btn" data-url="{{ $flagUrl }}" data-name="{{ $flag['name_ar'] }}">
                                    <img src="{{ $flagUrl }}" alt="{{ $flag['name_ar'] }}" style="width: 36px; height: 24px; object-fit: contain; background: #fff;" class="border rounded-1">
                                    <span class="small fw-bold text-truncate" dir="rtl">{{ $flag['name_ar'] }}</span>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('{{ $pickerId }}');
    if (!container) return;

    const openBtn = container.querySelector('.js-open-flag-modal');
    const modalEl = container.querySelector('.js-flag-modal');
    const searchInput = container.querySelector('.js-flag-search-input');
    const flagCols = container.querySelectorAll('.js-flag-card-col');
    const selectBtns = container.querySelectorAll('.js-select-flag-btn');
    const hiddenInput = container.querySelector('.js-preset-flag-input');
    const previewBox = container.querySelector('.js-flag-preview-box');
    const previewImg = container.querySelector('.js-flag-preview-img');
    const previewName = container.querySelector('.js-flag-preview-name');
    const clearBtn = container.querySelector('.js-clear-flag');

    const bsModal = new bootstrap.Modal(modalEl);

    openBtn.addEventListener('click', function () {
        bsModal.show();
    });

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            flagCols.forEach(col => {
                const text = col.getAttribute('data-search') || '';
                if (!query || text.includes(query)) {
                    col.style.display = 'block';
                } else {
                    col.style.display = 'none';
                }
            });
        });
    }

    selectBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const url = this.getAttribute('data-url');
            const name = this.getAttribute('data-name');

            hiddenInput.value = url;
            previewImg.src = url;
            previewName.textContent = name;
            previewBox.style.display = 'block';

            bsModal.hide();
        });
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            hiddenInput.value = '';
            previewBox.style.display = 'none';
        });
    }
});
</script>
