<div class="modal fade" id="bulkCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.embassy-appointments.store-bulk') }}" method="POST" id="bulkForm">
                @csrf

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        🏛️ إضافة مواعيد سفارات دفعة واحدة (Bulk Add)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="mb-3 p-3 bg-light rounded border">
                        <label for="bulkImageInput" class="form-label fw-bold text-dark mb-1">
                            📷 رفع صورة المواعيد لاستخراجها تلقائيًا:
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="file" class="form-control" id="bulkImageInput" accept="image/*">
                            <button type="button" class="btn btn-outline-primary" id="processUploadedImageBtn">
                                🔍 قراءة الصورة وتعبئة الجدول
                            </button>
                        </div>
                        <div class="form-text small text-muted">اختر صورة المواعيد من جهازك ليتم قراءتها وتوليد الصفوف لك تلقائيًا.</div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <span class="fw-bold text-dark">جدول المواعيد المتعددة</span>
                            <span class="text-muted small ms-2">يمكنك رفع صورة المواعيد بالأعلى أو إضافة صفوف جديدة يدويًا.</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" id="addBulkRowBtn" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1">
                                ➕ <span>إضافة صف موعد جديد</span>
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                        <table class="table table-bordered align-middle text-center mb-0" id="bulkTable">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width: 25%;">الدولة <span class="text-danger">*</span></th>
                                    <th style="width: 15%;">نوع التأشيرة <span class="text-danger">*</span></th>
                                    <th style="width: 15%;">مركز التقديم <span class="text-danger">*</span></th>
                                    <th style="width: 12%;">نوع الموعد <span class="text-danger">*</span></th>
                                    <th style="width: 18%;">حالة الموعد <span class="text-danger">*</span></th>
                                    <th style="width: 15%;">أقرب موعد (الفترة)</th>
                                    <th style="width: 5%;">إزالة</th>
                                </tr>
                            </thead>
                            <tbody id="bulkTableBody">
                                {{-- Rows added dynamically --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer bg-light justify-content-between">
                    <span class="text-muted small" id="rowCountBadge">عدد المواعيد المدرجة: 0</span>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">حفظ جميع المواعيد (Bulk Save)</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
