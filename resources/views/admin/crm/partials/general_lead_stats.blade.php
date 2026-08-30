<div class="card admin-card p-3 mb-4 shadow-sm" id="general-lead-stats-container">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 border-bottom pb-2">
        <div>
            <h2 class="h5 fw-bold mb-0 text-primary d-flex align-items-center gap-2">
                <i class="bi bi-bar-chart-fill"></i> الإحصائيات العامة للعملاء المحتملين
            </h2>
            <small class="text-muted">عرض أعداد الـ Leads لجميع الحالات بناءً على فلتر الفترة الزمنية والفلاتر المحددة</small>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 px-3 py-2" id="stats-total-badge">
                الإجمالي: <strong id="stats-total-count">...</strong>
            </span>

            <!-- Date Period & Basis Filters -->
            <div class="d-flex align-items-center gap-1 bg-light border rounded px-2 py-1">
                <label for="stats-date-basis" class="form-label mb-0 fw-semibold small text-nowrap"><i class="bi bi-funnel me-1"></i>أساس الفلترة:</label>
                <select class="form-select form-select-sm fw-bold border-0 bg-transparent shadow-none" id="stats-date-basis" style="width: 175px;">
                    <option value="created_or_updated" selected>إنشاء أو تحديث بالفترة</option>
                    <option value="created_at">تاريخ الإنشاء فقط</option>
                    <option value="crm_status_updated_at">تاريخ تغيير الحالة فقط</option>
                    <option value="updated_at">تاريخ آخر تعديل فقط</option>
                </select>
            </div>

            <div class="d-flex align-items-center gap-1 bg-light border rounded px-2 py-1">
                <label for="stats-date-period" class="form-label mb-0 fw-semibold small text-nowrap"><i class="bi bi-calendar-event me-1"></i>الفترة الزمنية:</label>
                <select class="form-select form-select-sm fw-bold border-0 bg-transparent shadow-none" id="stats-date-period" style="width: 140px;">
                    <option value="today" selected>اليوم</option>
                    <option value="yesterday">أمس</option>
                    <option value="current_week">الأسبوع الحالي</option>
                    <option value="last_week">الأسبوع الماضي</option>
                    <option value="current_month">الشهر الحالي</option>
                    <option value="last_month">الشهر الماضي</option>
                    <option value="current_year">السنة الحالية</option>
                    <option value="last_year">السنة الماضية</option>
                    <option value="all">الكل</option>
                </select>
            </div>

            <!-- Filter Toggle Button -->
            <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" type="button" data-bs-toggle="collapse" data-bs-target="#stats-advanced-filters" aria-expanded="false">
                <i class="bi bi-funnel-fill"></i>
                <span>فلاتر العملاء</span>
                <span class="badge text-bg-primary rounded-pill d-none" id="active-filters-count">0</span>
            </button>
        </div>
    </div>

    <!-- Advanced Lead Filters Collapse Bar (Same filter options as Leads Index) -->
    <div class="collapse mb-3" id="stats-advanced-filters">
        <div class="p-3 bg-light-subtle rounded-3 border">
            <form id="stats-filter-form" onsubmit="return false;">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-semibold mb-1">{{ __('admin.search') }}</label>
                        <input type="text" class="form-control form-control-sm stats-filter-input" name="q" placeholder="{{ __('admin.crm_search_placeholder') }}">
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="form-label small fw-semibold mb-1">{{ __('admin.source') }}</label>
                        <select class="form-select form-select-sm stats-filter-input" name="crm_source_id">
                            <option value="">كل شيء (All)</option>
                            @if(isset($sources))
                                @foreach($sources as $source)
                                    <option value="{{ $source->id }}">{{ $source->localizedName() }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="form-label small fw-semibold mb-1">{{ __('admin.assigned_to') }}</label>
                        <select class="form-select form-select-sm stats-filter-input" name="assigned_user_id">
                            <option value="">كل شيء (All)</option>
                            @if(isset($canViewAllLeads) && $canViewAllLeads)
                                <option value="unassigned">{{ __('admin.crm_unassigned') }}</option>
                            @endif
                            @if(isset($users))
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-6">
                        <label class="form-label small fw-semibold mb-1">{{ __('admin.crm_service_type') }}</label>
                        <select class="form-select form-select-sm stats-filter-input" name="crm_service_type_id">
                            <option value="">كل شيء (All)</option>
                            @if(isset($serviceTypes))
                                @foreach($serviceTypes as $serviceType)
                                    <option value="{{ $serviceType->id }}">{{ $serviceType->localizedName() }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-semibold mb-1">{{ __('admin.created_date') }}</label>
                        <div class="input-group input-group-sm">
                            <input type="date" class="form-control stats-filter-input" name="created_from" title="تاريخ الإنشاء من">
                            <input type="date" class="form-control stats-filter-input" name="created_to" title="تاريخ الإنشاء إلى">
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-semibold mb-1">{{ __('admin.crm_last_status_change') }}</label>
                        <div class="input-group input-group-sm">
                            <input type="date" class="form-control stats-filter-input" name="changed_from" title="تغيير الحالة من">
                            <input type="date" class="form-control stats-filter-input" name="changed_to" title="تغيير الحالة إلى">
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-semibold mb-1">{{ __('admin.last_modified') }}</label>
                        <div class="input-group input-group-sm">
                            <input type="date" class="form-control stats-filter-input" name="updated_from" title="آخر تعديل من">
                            <input type="date" class="form-control stats-filter-input" name="updated_to" title="آخر تعديل إلى">
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label class="form-label small fw-semibold mb-1">{{ __('admin.comment') }}</label>
                        <input type="text" class="form-control form-control-sm stats-filter-input" name="admin_notes" placeholder="{{ __('admin.comment') }}">
                    </div>

                    <div class="col-md-3 col-sm-6 d-flex align-items-end">
                        <button type="button" class="btn btn-sm btn-outline-danger w-100" id="stats-reset-filters">
                            <i class="bi bi-x-circle me-1"></i> إعادة ضبط الفلاتر
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Status Cards Ultra-Compact Responsive Grid -->
    <div class="position-relative">
        <div id="stats-loading-spinner" class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-none d-flex align-items-center justify-content-center" style="z-index: 10; min-height: 80px;">
            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
            <span class="small fw-semibold text-muted">جاري تحديث الإحصائيات...</span>
        </div>

        <div class="general-lead-stats-grid" id="stats-cards-grid">
            @if(isset($allStatuses))
                @foreach($allStatuses as $status)
                    <div class="stat-mini-card" id="stat-card-{{ $status->id }}">
                        <div class="stat-mini-name text-truncate" title="{{ $status->localizedName() }}">
                            {{ $status->localizedName() }}
                        </div>
                        <div class="stat-mini-count fw-bold" id="stat-count-{{ $status->id }}">
                            0
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

<style>
.general-lead-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 8px;
}
.stat-mini-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 10px;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.stat-mini-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
    transform: translateY(-1px);
}
.stat-mini-name {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 600;
    line-height: 1.2;
    margin-bottom: 3px;
}
.stat-mini-count {
    font-size: 1.35rem;
    color: #0f172a;
    line-height: 1.1;
    font-weight: 700;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dateSelect = document.getElementById('stats-date-period');
    const dateBasisSelect = document.getElementById('stats-date-basis');
    const filterForm = document.getElementById('stats-filter-form');
    const grid = document.getElementById('stats-cards-grid');
    const loading = document.getElementById('stats-loading-spinner');
    const totalCountEl = document.getElementById('stats-total-count');
    const activeFiltersCount = document.getElementById('active-filters-count');
    const resetBtn = document.getElementById('stats-reset-filters');
    const endpoint = "{{ route('admin.crm.general-lead-stats') }}";

    // ALWAYS reset Date Filter to 'today' on initial page load / new session
    if (dateSelect) {
        dateSelect.value = 'today';
    }
    if (dateBasisSelect) {
        dateBasisSelect.value = 'created_or_updated';
    }

    let debounceTimer = null;

    function fetchGeneralStats() {
        if (!dateSelect) return;
        if (loading) loading.classList.remove('d-none');

        const params = new URLSearchParams();
        params.append('date_period', dateSelect.value);
        if (dateBasisSelect) {
            params.append('date_basis', dateBasisSelect.value);
        }

        if (filterForm) {
            const formData = new FormData(filterForm);
            let activeCount = 0;
            for (let [key, val] of formData.entries()) {
                if (val && val.trim() !== '') {
                    params.append(key, val.trim());
                    activeCount++;
                }
            }
            if (activeFiltersCount) {
                if (activeCount > 0) {
                    activeFiltersCount.textContent = activeCount;
                    activeFiltersCount.classList.remove('d-none');
                } else {
                    activeFiltersCount.classList.add('d-none');
                }
            }
        }

        fetch(`${endpoint}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (totalCountEl) {
                    totalCountEl.textContent = Number(data.total).toLocaleString();
                }
                data.statuses.forEach(st => {
                    const countEl = document.getElementById(`stat-count-${st.id}`);
                    if (countEl) {
                        countEl.textContent = Number(st.count).toLocaleString();
                    }
                });
            }
        })
        .catch(err => console.error('Error fetching general lead stats:', err))
        .finally(() => {
            if (loading) loading.classList.add('d-none');
        });
    }

    if (dateSelect) {
        dateSelect.addEventListener('change', fetchGeneralStats);
    }
    if (dateBasisSelect) {
        dateBasisSelect.addEventListener('change', fetchGeneralStats);
    }

    if (filterForm) {
        filterForm.querySelectorAll('.stats-filter-input').forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchGeneralStats, 350);
            });
            input.addEventListener('change', function() {
                clearTimeout(debounceTimer);
                fetchGeneralStats();
            });
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (filterForm) filterForm.reset();
            if (dateSelect) dateSelect.value = 'today';
            fetchGeneralStats();
        });
    }

    // Initial Load
    fetchGeneralStats();
});
</script>
