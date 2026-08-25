@extends('layouts.admin')

@section('title', 'مواعيد السفارات - Embassy Appointments')

@section('content')
<div class="container-fluid py-3">

    {{-- Header Title & Action --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 fw-bold mb-1">🏛️ مواعيد السفارات (Embassy Appointments)</h1>
            <p class="text-muted mb-0">إدارة ومتابعة فتح مواعيد السفارات وتنبيه المبيعات تلقائيًا للعملاء المنتظرين.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.embassy-appointments.trash') }}" class="btn btn-outline-danger d-inline-flex align-items-center gap-1 shadow-sm">
                <iconify-icon icon="lucide:trash-2" width="18"></iconify-icon>
                <span>سلة المحذوفات</span>
                @if(isset($trashedCount) && $trashedCount > 0)
                    <span class="badge bg-danger rounded-pill ms-1">{{ $trashedCount }}</span>
                @endif
            </a>
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#createApptModal">
                <iconify-icon icon="lucide:plus-circle" width="18"></iconify-icon>
                <span>إضافة موعد سفارة جديد</span>
            </button>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <strong>✅ تم بنجاح:</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <strong>⚠️ تنبيه:</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold">إجمالي المواعيد</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ number_format($summary['total']) }}</div>
                    <div class="small text-muted">سفارة / مركز</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm h-100 bg-success bg-opacity-10 border-start border-success border-4">
                <div class="card-body p-3">
                    <div class="text-success fw-bold small">🟢 متاحة الآن</div>
                    <div class="fs-2 fw-bold text-success mt-1">{{ number_format($summary['available_now']) }}</div>
                    <div class="small text-muted">موعد مفتوح</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm h-100 bg-warning bg-opacity-10 border-start border-warning border-4">
                <div class="card-body p-3">
                    <div class="text-warning text-darken-2 fw-bold small">🟡 متاحة مستقبلاً</div>
                    <div class="fs-2 fw-bold text-warning text-darken-2 mt-1">{{ number_format($summary['available_later']) }}</div>
                    <div class="small text-muted">بتاريخ قادم</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm h-100 bg-danger bg-opacity-10 border-start border-danger border-4">
                <div class="card-body p-3">
                    <div class="text-danger fw-bold small">🔴 لا توجد مواعيد</div>
                    <div class="fs-2 fw-bold text-danger mt-1">{{ number_format($summary['no_availability']) }}</div>
                    <div class="small text-muted">مغلقة حالياً</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm h-100 bg-secondary bg-opacity-10 border-start border-secondary border-4">
                <div class="card-body p-3">
                    <div class="text-secondary fw-bold small">⚪ غير معروفة</div>
                    <div class="fs-2 fw-bold text-secondary mt-1">{{ number_format($summary['unknown']) }}</div>
                    <div class="small text-muted">لم تُحدث بعد</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card border-0 shadow-sm h-100 bg-info bg-opacity-10 border-start border-info border-4">
                <div class="card-body p-3">
                    <div class="text-info fw-bold small">⏳ العملاء المنتظرون</div>
                    <div class="fs-2 fw-bold text-info mt-1">{{ number_format($summary['waiting_leads']) }}</div>
                    <div class="small text-muted">Lead يتابع الموعد</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.embassy-appointments.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold">بحث باسم الدولة</label>
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="مثال: إسبانيا، France..." value="{{ request('q') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold">الدولة</label>
                    <select name="visa_country_id" class="form-select form-select-sm">
                        <option value="">جميع الدول</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->id }}" {{ request('visa_country_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name_ar ?: $c->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold">حالة الموعد</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">جميع الحالات</option>
                        <option value="available_now" {{ request('status') === 'available_now' ? 'selected' : '' }}>🟢 متاحة الآن</option>
                        <option value="available_later" {{ request('status') === 'available_later' ? 'selected' : '' }}>🟡 متاحة مستقبلاً</option>
                        <option value="no_availability" {{ request('status') === 'no_availability' ? 'selected' : '' }}>🔴 لا توجد مواعيد</option>
                        <option value="unknown" {{ request('status') === 'unknown' ? 'selected' : '' }}>⚪ غير معروف</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold">نوع الموعد</label>
                    <select name="appointment_type" class="form-select form-select-sm">
                        <option value="">الكل</option>
                        <option value="Regular" {{ request('appointment_type') === 'Regular' ? 'selected' : '' }}>Regular</option>
                        <option value="VIP" {{ request('appointment_type') === 'VIP' ? 'selected' : '' }}>VIP</option>
                        <option value="Super VIP" {{ request('appointment_type') === 'Super VIP' ? 'selected' : '' }}>Super VIP</option>
                        <option value="VVIP" {{ request('appointment_type') === 'VVIP' ? 'selected' : '' }}>VVIP</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                        <iconify-icon icon="lucide:search"></iconify-icon> فلترة
                    </button>

                    <a href="{{ route('admin.embassy-appointments.index') }}" class="btn btn-sm btn-light border">
                        إعادة ضبط
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 20%;">الدولة</th>
                            <th style="width: 12%;">نوع التأشيرة</th>
                            <th style="width: 12%;">مركز التقديم</th>
                            <th style="width: 10%;">نوع الموعد</th>
                            <th style="width: 14%;">الحالة الحالية</th>
                            <th style="width: 12%;">أقرب موعد</th>
                            <th style="width: 10%;">العملاء المنتظرون</th>
                            <th style="width: 10%;">آخر تحديث</th>
                            <th class="pe-3 text-end" style="width: 220px;">الإجراءات السريعة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $appt)
                            <tr>
                                <td class="ps-3 fw-bold">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($appt->country?->flag_image)
                                            <img src="{{ asset($appt->country->flag_image) }}" alt="" style="width: 24px; height: 16px; object-fit: cover;" class="rounded-1 border">
                                        @else
                                            <span>🌍</span>
                                        @endif
                                        <span>{{ $appt->country_name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-normal">{{ $appt->visa_type }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary fw-semibold">{{ $appt->appointment_center }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $appt->appointment_type === 'VIP' ? 'bg-warning text-dark' : 'bg-info bg-opacity-10 text-info' }} fw-semibold">
                                        {{ $appt->appointment_type }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $appt->status_badge_class }} fs-7 px-2 py-1">
                                        {{ $appt->status_icon }} {{ $appt->status_label }}
                                    </span>
                                </td>
                                <td class="small">
                                    @if($appt->earliest_date)
                                        <span class="fw-bold text-dark">{{ $appt->earliest_date }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($appt->waiting_leads_count > 0)
                                        <span class="badge bg-primary rounded-pill px-2 py-1 fs-7">
                                            {{ $appt->waiting_leads_count }} عميل ⏳
                                        </span>
                                    @else
                                        <span class="text-muted small">0</span>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    @if($appt->last_updated_at)
                                        <div>{{ $appt->last_updated_at->format('Y-m-d') }}</div>
                                        <div class="fs-8 text-secondary">{{ $appt->last_updated_at->diffForHumans() }}</div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="pe-3 text-end">
                                    <div class="d-inline-flex gap-1">
                                        <form method="POST" action="{{ route('admin.embassy-appointments.update-quick-status', $appt) }}" class="d-inline">
                                            @csrf
                                            <select name="status" class="form-select form-select-sm fw-bold border-0 shadow-sm pe-4" onchange="this.form.submit()" style="cursor: pointer; display: inline-block; width: auto; font-size: 0.8rem;
                                                background-color: {{ $appt->status === 'available_now' ? '#d1e7dd' : ($appt->status === 'available_later' ? '#fff3cd' : ($appt->status === 'no_availability' ? '#f8d7da' : '#e2e3e5')) }};
                                                color: {{ $appt->status === 'available_now' ? '#0f5132' : ($appt->status === 'available_later' ? '#664d03' : ($appt->status === 'no_availability' ? '#842029' : '#41464b')) }};"
                                                title="تغيير الحالة السريعة للموعد">
                                                <option value="available_now" {{ $appt->status === 'available_now' ? 'selected' : '' }}>🟢 متاحة الآن</option>
                                                <option value="available_later" {{ $appt->status === 'available_later' ? 'selected' : '' }}>🟡 متاحة مستقبلاً</option>
                                                <option value="no_availability" {{ $appt->status === 'no_availability' ? 'selected' : '' }}>🔴 لا توجد مواعيد</option>
                                                <option value="unknown" {{ $appt->status === 'unknown' ? 'selected' : '' }}>⚪ غير معروف</option>
                                            </select>
                                        </form>

                                        <button type="button" class="btn btn-sm btn-light border editApptBtn"
                                            data-id="{{ $appt->id }}"
                                            data-country-id="{{ $appt->visa_country_id }}"
                                            data-visa-type="{{ $appt->visa_type }}"
                                            data-center="{{ $appt->appointment_center }}"
                                            data-appt-type="{{ $appt->appointment_type }}"
                                            data-status="{{ $appt->status }}"
                                            data-earliest-date="{{ $appt->earliest_date }}"
                                            data-notes="{{ $appt->notes }}"
                                            data-booking-link="{{ $appt->booking_link }}"
                                            title="تعديل البيانات">
                                            ✏️
                                        </button>

                                        <a href="{{ route('admin.embassy-appointments.show', $appt) }}" class="btn btn-sm btn-light border" title="التفاصيل وسجل التغييرات">
                                            👁️
                                        </a>

                                        <form method="POST" action="{{ route('admin.embassy-appointments.destroy', $appt) }}" class="d-inline" onsubmit="return confirm('هل أنت تأكد من رغبتك في حذف سجل هذا الموعد بالكامل؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف سجل الموعد">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <iconify-icon icon="lucide:inbox" width="48" class="text-secondary opacity-50 mb-2"></iconify-icon>
                                    <p class="mb-0 fw-semibold">لا توجد مواعيد سفارات مسجلة حاليًا.</p>
                                    <small>اضغط على زر "إضافة موعد سفارة جديد" بالأعلى لإضافة موعد جديدة.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($items->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $items->links() }}
            </div>
        @endif
    </div>

</div>

@endsection

@push('modals')
    @include('admin.embassy-appointments.modals.create_edit_modal')
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('createApptModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        const form = document.getElementById('apptForm');
        const modalTitle = document.getElementById('modalTitle');
        const methodContainer = document.getElementById('methodContainer');

        function cleanArabic(str) {
            if (!str) return '';
            return str.toString().toLowerCase()
                .replace(/[أإآٱ]/g, 'ا')
                .replace(/ة/g, 'ه')
                .replace(/ى/g, 'ي')
                .trim();
        }

        const countrySearchInput = document.getElementById('countrySearchInput');
        const countrySelect = document.getElementById('modal_visa_country_id');

        if (countrySearchInput && countrySelect) {
            countrySearchInput.addEventListener('input', function () {
                const val = cleanArabic(this.value);
                let firstMatch = null;
                Array.from(countrySelect.options).forEach(opt => {
                    if (!opt.value) return;
                    const searchData = cleanArabic(opt.dataset.search || opt.textContent);
                    if (!val || searchData.includes(val)) {
                        opt.style.display = '';
                        if (!firstMatch && val) firstMatch = opt;
                    } else {
                        opt.style.display = 'none';
                    }
                });
                if (firstMatch && val) {
                    countrySelect.value = firstMatch.value;
                }
            });
        }

        function setSelectValueOrAdd(selectId, val) {
            const select = document.getElementById(selectId);
            if (!select) return;
            if (!val) {
                select.selectedIndex = 0;
                return;
            }
            let exists = Array.from(select.options).some(opt => opt.value === val);
            if (!exists) {
                const newOpt = new Option(val, val, true, true);
                select.add(newOpt);
            }
            select.value = val;
        }

        document.querySelectorAll('.editApptBtn').forEach(btn => {
            btn.addEventListener('click', function () {
                const data = this.dataset;
                modalTitle.textContent = 'تعديل موعد سفارة';
                form.action = `/admin/embassy-appointments/${data.id}`;
                methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';

                if (countrySearchInput) countrySearchInput.value = '';
                if (countrySelect) {
                    Array.from(countrySelect.options).forEach(opt => opt.style.display = '');
                    if (data.countryId) {
                        countrySelect.value = String(data.countryId);
                    }
                }

                setSelectValueOrAdd('modal_visa_type', data.visaType);
                setSelectValueOrAdd('modal_appointment_center', data.center);
                document.getElementById('modal_appointment_type').value = data.apptType;
                document.getElementById('modal_status').value = data.status;
                document.getElementById('modal_earliest_date').value = data.earliestDate || '';
                document.getElementById('modal_notes').value = data.notes || '';
                document.getElementById('modal_booking_link').value = data.bookingLink || '';

                modal.show();
            });
        });

        modalEl.addEventListener('hidden.bs.modal', function () {
            modalTitle.textContent = 'إضافة موعد سفارة جديد';
            form.action = "{{ route('admin.embassy-appointments.store') }}";
            methodContainer.innerHTML = '';
            form.reset();
            if (countrySearchInput) countrySearchInput.value = '';
            if (countrySelect) {
                Array.from(countrySelect.options).forEach(opt => opt.style.display = '');
            }
        });
    }
});
</script>
@endpush
