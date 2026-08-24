<!-- Embassy Appointments Seller Popup Modal -->
<div class="modal fade" id="embassySellerPopupModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">

            {{-- Header --}}
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2 mb-0">
                    <span class="fs-4">🔔</span>
                    <span>مواعيد سفارة متاحة الآن!</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body p-4" id="embassyPopupBody">
                <div class="text-center py-4" id="embassyPopupLoader">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                    <p class="mt-2 text-muted small">جاري التحقق من التنبيهات المتاحة...</p>
                </div>

                <div id="embassyPopupContent" style="display: none;">
                    {{-- Container for single or multi leads --}}
                    <div id="embassyLeadsContainer"></div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">إغلاق المؤقت</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const popupModalEl = document.getElementById('embassySellerPopupModal');
    if (!popupModalEl) return;

    const popupModal = bootstrap.Modal.getOrCreateInstance(popupModalEl);
    const popupContent = document.getElementById('embassyPopupContent');
    const popupLoader = document.getElementById('embassyPopupLoader');
    const leadsContainer = document.getElementById('embassyLeadsContainer');

    function checkPendingEmbassyPopups() {
        fetch("{{ route('admin.embassy-appointments.pending-popups') }}", {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.count > 0) {
                renderPopupItems(data.items);
                popupLoader.style.display = 'none';
                popupContent.style.display = 'block';
                popupModal.show();
            }
        })
        .catch(err => console.error('Embassy popup check error:', err));
    }

    function renderPopupItems(items) {
        let html = '';
        if (items.length === 1) {
            const item = items[0];
            html = `
                <div class="card border-success border-2 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="badge bg-success mb-1">🟢 مواعيد متاحة الآن</span>
                                <h4 class="fw-bold mb-0 text-dark">${item.lead_name}</h4>
                                <small class="text-muted">📞 ${item.lead_phone || 'بدون رقم'}</small>
                            </div>
                            <div class="text-end">
                                <span class="fs-2">🇪🇸</span>
                            </div>
                        </div>

                        <div class="row g-2 bg-light p-3 rounded mb-3 text-start dir-rtl">
                            <div class="col-6 col-md-3">
                                <small class="text-muted d-block">الدولة</small>
                                <strong class="text-dark">${item.country_name}</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <small class="text-muted d-block">التأشيرة</small>
                                <strong class="text-dark">${item.visa_type}</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <small class="text-muted d-block">المركز</small>
                                <strong class="text-dark">${item.appointment_center}</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <small class="text-muted d-block">نوع الموعد</small>
                                <strong class="text-dark">${item.appointment_type}</strong>
                            </div>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-success flex-grow-1 btnContactClick" data-notif-id="${item.id}">
                                📞 تم الاتصال بالعميل
                            </button>
                            <button type="button" class="btn btn-outline-warning flex-grow-1 btnSnoozeClick" data-notif-id="${item.id}">
                                ⏱️ تأجيل (Snooze)
                            </button>
                            <a href="${item.lead_url}" target="_blank" class="btn btn-primary">
                                👁️ فتح العميل
                            </a>
                        </div>

                        {{-- Sub-form for Contact Result --}}
                        <div class="contactFormBox mt-3 p-3 bg-light rounded border" id="contactBox_${item.id}" style="display: none;">
                            <h6 class="fw-bold mb-2">نتيجة الاتصال بالعميل:</h6>
                            <select class="form-select form-select-sm mb-2" id="callResult_${item.id}">
                                <option value="agreed">🟢 العميل وافق</option>
                                <option value="no_answer">🟡 العميل لم يرد</option>
                                <option value="call_later">🔵 العميل طلب الاتصال لاحقًا</option>
                                <option value="not_ready">⚪ العميل غير جاهز</option>
                                <option value="refused">🔴 العميل رفض</option>
                                <option value="other">📝 أخرى</option>
                            </select>
                            <textarea class="form-control form-control-sm mb-2" id="callNotes_${item.id}" placeholder="ملاحظات اختيارية..."></textarea>
                            <button type="button" class="btn btn-sm btn-success w-100 btnSubmitContact" data-notif-id="${item.id}">
                                تأكيد حفظ نتيجة الاتصال
                            </button>
                        </div>

                        {{-- Sub-form for Snooze --}}
                        <div class="snoozeFormBox mt-3 p-3 bg-light rounded border" id="snoozeBox_${item.id}" style="display: none;">
                            <h6 class="fw-bold mb-2">اختر فترة التأجيل (Snooze):</h6>
                            <div class="d-flex gap-1 flex-wrap mb-2">
                                <button type="button" class="btn btn-xs btn-outline-secondary btnSnoozeOpt" data-notif-id="${item.id}" data-val="15">15 دقيقة</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary btnSnoozeOpt" data-notif-id="${item.id}" data-val="30">30 دقيقة</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary btnSnoozeOpt" data-notif-id="${item.id}" data-val="60">ساعة</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary btnSnoozeOpt" data-notif-id="${item.id}" data-val="120">ساعتين</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary btnSnoozeOpt" data-notif-id="${item.id}" data-val="tomorrow">غدًا</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // Multi-lead view
            html = `
                <div class="alert alert-info py-2 small mb-3">
                    لديك <strong>${items.length} عملاء</strong> لديهم مواعيد سفارات متاحة الآن:
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>العميل</th>
                                <th>الدولة</th>
                                <th>التأشيرة</th>
                                <th>المركز</th>
                                <th>النوع</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${items.map(item => `
                                <tr>
                                    <td class="fw-bold">
                                        <a href="${item.lead_url}" target="_blank" class="text-decoration-none text-dark">${item.lead_name}</a>
                                        <div class="text-muted fs-8">${item.lead_phone || ''}</div>
                                    </td>
                                    <td>${item.country_name}</td>
                                    <td><span class="badge bg-light text-dark border">${item.visa_type}</span></td>
                                    <td>${item.appointment_center}</td>
                                    <td><span class="badge bg-info bg-opacity-10 text-info">${item.appointment_type}</span></td>
                                    <td>
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-xs btn-success btnContactClick" data-notif-id="${item.id}">
                                                📞 تم الاتصال
                                            </button>
                                            <button type="button" class="btn btn-xs btn-warning btnSnoozeClick" data-notif-id="${item.id}">
                                                ⏱️ تأجيل
                                            </button>
                                            <a href="${item.lead_url}" target="_blank" class="btn btn-xs btn-outline-primary">
                                                👁️ عرض
                                            </a>
                                        </div>

                                        {{-- Inline Contact Box --}}
                                        <div class="contactFormBox mt-2 p-2 bg-light rounded border text-start" id="contactBox_${item.id}" style="display: none;">
                                            <select class="form-select form-select-sm mb-1 fs-8" id="callResult_${item.id}">
                                                <option value="agreed">🟢 وافق</option>
                                                <option value="no_answer">🟡 لم يرد</option>
                                                <option value="call_later">🔵 الاتصال لاحقًا</option>
                                                <option value="not_ready">⚪ غير جاهز</option>
                                                <option value="refused">🔴 رفض</option>
                                                <option value="other">📝 أخرى</option>
                                            </select>
                                            <input type="text" class="form-control form-control-sm mb-1 fs-8" id="callNotes_${item.id}" placeholder="ملاحظات...">
                                            <button type="button" class="btn btn-xs btn-success w-100 btnSubmitContact" data-notif-id="${item.id}">
                                                حفظ
                                            </button>
                                        </div>

                                        {{-- Inline Snooze Box --}}
                                        <div class="snoozeFormBox mt-2 p-2 bg-light rounded border text-start" id="snoozeBox_${item.id}" style="display: none;">
                                            <div class="d-flex gap-1 flex-wrap">
                                                <button type="button" class="btn btn-xs btn-outline-secondary btnSnoozeOpt" data-notif-id="${item.id}" data-val="15">15د</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary btnSnoozeOpt" data-notif-id="${item.id}" data-val="30">30د</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary btnSnoozeOpt" data-notif-id="${item.id}" data-val="60">ساعة</button>
                                                <button type="button" class="btn btn-xs btn-outline-secondary btnSnoozeOpt" data-notif-id="${item.id}" data-val="tomorrow">غداً</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        }

        leadsContainer.innerHTML = html;
        attachEvents();
    }

    function attachEvents() {
        document.querySelectorAll('.btnContactClick').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.notifId;
                const box = document.getElementById(`contactBox_${id}`);
                if (box) {
                    box.style.display = box.style.display === 'none' ? 'block' : 'none';
                }
            });
        });

        document.querySelectorAll('.btnSnoozeClick').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.notifId;
                const box = document.getElementById(`snoozeBox_${id}`);
                if (box) {
                    box.style.display = box.style.display === 'none' ? 'block' : 'none';
                }
            });
        });

        document.querySelectorAll('.btnSubmitContact').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.notifId;
                const result = document.getElementById(`callResult_${id}`).value;
                const notes = document.getElementById(`callNotes_${id}`).value;

                fetch(`/admin/embassy-appointments/notifications/${id}/contact`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        contact_result: result,
                        contact_notes: notes
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        checkPendingEmbassyPopups();
                    }
                });
            });
        });

        document.querySelectorAll('.btnSnoozeOpt').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.dataset.notifId;
                const val = this.dataset.val;

                fetch(`/admin/embassy-appointments/notifications/${id}/snooze`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        snooze_option: val
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        checkPendingEmbassyPopups();
                    }
                });
            });
        });
    }

    // Run check on page load for logged in sellers
    setTimeout(checkPendingEmbassyPopups, 1500);
});
</script>
