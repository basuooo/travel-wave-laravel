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

                        {{-- Country --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">الدولة / السفارة <span class="text-danger">*</span></label>
                            <select name="visa_country_id" id="modal_visa_country_id" class="form-select" required>
                                <option value="">اختر الدولة...</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c->id }}">{{ $c->name_ar ?: $c->name_en }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Visa Type --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">نوع التأشيرة <span class="text-danger">*</span></label>
                            <input type="text" name="visa_type" id="modal_visa_type" class="form-control" placeholder="مثال: Tourist / سياحة" value="سياحة" required>
                        </div>

                        {{-- Appointment Center --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">مركز التقديم <span class="text-danger">*</span></label>
                            <input type="text" name="appointment_center" id="modal_appointment_center" class="form-control" placeholder="مثال: BLS, VFS, TLS, السفارة مباشرة" value="BLS" required>
                        </div>

                        {{-- Appointment Type --}}
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

                        {{-- Status --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">حالة الموعد <span class="text-danger">*</span></label>
                            <select name="status" id="modal_status" class="form-select" required>
                                <option value="no_availability">🔴 لا توجد مواعيد حاليًا (No Availability)</option>
                                <option value="available_now">🟢 مواعيد متاحة الآن (Available Now - يرسل تنبيهات للبائعين)</option>
                                <option value="available_later">🟡 مواعيد متاحة بتاريخ مستقبلي (Available Later)</option>
                                <option value="unknown">⚪ غير معروف / لم يتم التحديث (Unknown)</option>
                            </select>
                        </div>

                        {{-- Earliest Available Date --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">أقرب موعد متاح (اختياري)</label>
                            <input type="date" name="earliest_date" id="modal_earliest_date" class="form-control">
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
