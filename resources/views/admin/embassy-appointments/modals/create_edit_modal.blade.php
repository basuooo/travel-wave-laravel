<div class="modal fade" id="createApptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form id="apptForm" method="POST" action="{{ route('admin.embassy-appointments.store') }}">
                @csrf
                <div id="methodContainer"></div>

                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="modalTitle">إضافة موعد سفارة جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-3">

                        {{-- Country with live search filter --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                                <span>الدولة / السفارة <span class="text-danger">*</span></span>
                                <small class="text-muted fw-normal">اكتب بالتصفية للبحث 🔍</small>
                            </label>
                            <div class="input-group input-group-sm mb-1">
                                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="countrySearchInput" class="form-control" placeholder="ابحث باسم الدولة (مثال: إسبانيا، إيطاليا)..." autocomplete="off">
                            </div>
                            <select name="visa_country_id" id="modal_visa_country_id" class="form-select" required size="4">
                                <option value="">اختر الدولة...</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c->id }}" data-search="{{ mb_strtolower(($c->name_ar ?? '') . ' ' . ($c->name_en ?? '')) }}">
                                        {{ $c->name_ar ?: $c->name_en }} ({{ $c->name_en }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Visa Type Dropdown --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">نوع التأشيرة <span class="text-danger">*</span></label>
                            <select name="visa_type" id="modal_visa_type" class="form-select" required>
                                <option value="سياحة">سياحة (Tourist)</option>
                                <option value="عمل / بيزنس">عمل / بيزنس (Business)</option>
                                <option value="دراسة">دراسة (Study)</option>
                                <option value="فيزا عمل">فيزا عمل (Work Visa)</option>
                                <option value="زيارة عائلية">زيارة عائلية (Family Visit)</option>
                                <option value="علاج">علاج (Medical)</option>
                                <option value="ترانزيت">ترانزيت (Transit)</option>
                                <option value="أخرى">أخرى (Other)</option>
                            </select>
                        </div>

                        {{-- Application Center Dropdown --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">مركز التقديم <span class="text-danger">*</span></label>
                            <select name="appointment_center" id="modal_appointment_center" class="form-select" required>
                                <option value="BLS">BLS (إسبانيا...)</option>
                                <option value="VFS">VFS Global (إيطاليا، فرنسا، ألمانيا...)</option>
                                <option value="TLS">TLScontact (المملكة المتحدة، فرنسا...)</option>
                                <option value="iOM">iOM (المجر وغيرها)</option>
                                <option value="السفارة مباشرة">السفارة مباشرة (Direct Embassy)</option>
                                <option value="مركز تقديم آخر">مركز تقديم آخر / أخرى</option>
                            </select>
                        </div>

                        {{-- Appointment Type Dropdown --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">نوع الموعد <span class="text-danger">*</span></label>
                            <select name="appointment_type" id="modal_appointment_type" class="form-select" required>
                                <option value="Regular">Regular (عادي)</option>
                                <option value="VIP">VIP</option>
                                <option value="Super VIP">Super VIP</option>
                                <option value="VVIP">VVIP</option>
                            </select>
                        </div>

                        <hr class="my-2">

                        {{-- Status Dropdown --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">حالة الموعد <span class="text-danger">*</span></label>
                            <select name="status" id="modal_status" class="form-select" required>
                                <option value="no_availability">🔴 لا توجد مواعيد حاليًا (No Availability)</option>
                                <option value="available_now">🟢 مواعيد متاحة الآن (Available Now - يرسل تنبيهات للبائعين)</option>
                                <option value="available_later">🟡 مواعيد متاحة بتاريخ مستقبلي (Available Later)</option>
                                <option value="unknown">⚪ غير معروف / لم يتم التحديث (Unknown)</option>
                            </select>
                        </div>

                        {{-- Earliest Available Date / Period (Text Input) --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">أقرب موعد متاح (اختياري)</label>
                            <input type="text" name="earliest_date" id="modal_earliest_date" class="form-control" placeholder="أدخل التقدير، مثال: أول شهر 9، منتصف سبتمبر، بداية أكتوبر...">
                            <div class="form-text text-muted fs-8">خانة نصية تتيح كتابة تقدير الفترة يدويًا (مثال: أول شهر 9...).</div>
                        </div>

                        {{-- Booking Link --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">رابط الحجز (اختياري)</label>
                            <input type="url" name="booking_link" id="modal_booking_link" class="form-control" placeholder="https://...">
                        </div>

                        {{-- Notes --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">ملاحظات إضافية (اختياري)</label>
                            <textarea name="notes" id="modal_notes" class="form-control" rows="3" placeholder="أدخل أي تفاصيل خاصة بالشروط أو الأوراق..."></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary px-4">حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>
