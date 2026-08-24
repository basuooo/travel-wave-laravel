@extends('layouts.admin')

@section('title', 'Visa Management / قاعدة بيانات التأشيرات')

@section('content')
<div class="container-fluid px-3 py-2">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-passport text-primary me-2"></i>Visa Database / إدارة التأشيرات</h3>
            <p class="text-muted small mb-0">قاعدة بيانات منظمة وتفاعلية لجميع تأشيرات دول العالم مع إدارة الأسعار، رسوم السفارة، والأوراق المطلوبة.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#manageCategoriesModal">
                <i class="bi bi-tags me-1"></i> إدارة التصنيفات
            </button>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVisaModal">
                <i class="bi bi-plus-circle me-1"></i> إضافة تأشيرة جديدة
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search & Filters Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body bg-light rounded-3 p-3">
            <form method="GET" action="{{ route('admin.visa-database.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold mb-1">بحث بالاسم أو نوع التأشيرة</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="اسم الدولة / نوع التأشيرة..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">التصنيف</label>
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">جميع التصنيفات</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">الدولة</label>
                    <select name="country_id" class="form-select form-select-sm">
                        <option value="">جميع الدول</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->id }}" {{ request('country_id') == $c->id ? 'selected' : '' }}>{{ $c->name_ar }} ({{ $c->name_en }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">نوع التأشيرة</label>
                    <select name="visa_type" class="form-select form-select-sm">
                        <option value="">جميع الأنواع</option>
                        @foreach($distinctVisaTypes as $vt)
                            <option value="{{ $vt }}" {{ request('visa_type') == $vt ? 'selected' : '' }}>{{ $vt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">مكان التقديم</label>
                    <select name="application_center" class="form-select form-select-sm">
                        <option value="">جميع المراكز</option>
                        <option value="VFS" {{ request('application_center') == 'VFS' ? 'selected' : '' }}>VFS</option>
                        <option value="TLS" {{ request('application_center') == 'TLS' ? 'selected' : '' }}>TLS</option>
                        <option value="BLS" {{ request('application_center') == 'BLS' ? 'selected' : '' }}>BLS</option>
                        <option value="Almaviva" {{ request('application_center') == 'Almaviva' ? 'selected' : '' }}>Almaviva / المافيفا</option>
                        <option value="السفارة مباشرة" {{ request('application_center') == 'السفارة مباشرة' ? 'selected' : '' }}>السفارة مباشرة</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label small fw-bold mb-1">البصمة</label>
                    <select name="is_biometrics_required" class="form-select form-select-sm">
                        <option value="">الكل</option>
                        <option value="1" {{ request('is_biometrics_required') === '1' ? 'selected' : '' }}>نعم</option>
                        <option value="0" {{ request('is_biometrics_required') === '0' ? 'selected' : '' }}>لا</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label small fw-bold mb-1">المقابلة</label>
                    <select name="is_interview_required" class="form-select form-select-sm">
                        <option value="">الكل</option>
                        <option value="1" {{ request('is_interview_required') === '1' ? 'selected' : '' }}>نعم</option>
                        <option value="0" {{ request('is_interview_required') === '0' ? 'selected' : '' }}>لا</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold mb-1">الحالة</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">جميع الحالات</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active / نشطة</option>
                        <option value="temporarily_unavailable" {{ request('status') == 'temporarily_unavailable' ? 'selected' : '' }}>متوقفة مؤقتاً</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير متاحة</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i> تصفية</button>
                    <a href="{{ route('admin.visa-database.index') }}" class="btn btn-outline-secondary btn-sm w-100"><i class="bi bi-x-circle me-1"></i> إلغاء</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr class="text-secondary small">
                            <th>الدولة</th>
                            <th>التصنيف</th>
                            <th>نوع التأشيرة</th>
                            <th>سعر العميل</th>
                            <th>رسوم السفارة</th>
                            <th>أيام العمل</th>
                            <th>مدة التأشيرة</th>
                            <th>مكان التقديم</th>
                            <th class="text-center">البصمة</th>
                            <th class="text-center">المقابلة</th>
                            <th>الحالة</th>
                            <th class="text-end px-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $rec)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fs-4">{{ $rec->country?->flag_image ?: '🌍' }}</span>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $rec->country?->name_ar ?: $rec->country?->name_en }}</div>
                                            <div class="small text-muted">{{ $rec->country?->name_en }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php($catList = $rec->country?->categories)
                                    @if($catList && $catList->count() > 0)
                                        @foreach($catList as $c)
                                            <span class="badge bg-light text-dark border me-1">{{ $c->name_ar }}</span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-light text-muted border">{{ $rec->country?->category?->name_ar ?: 'غير محدد' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold text-primary">{{ $rec->visa_type }}</span>
                                </td>
                                <td>
                                    @if($rec->price !== null)
                                        <span class="fw-bold text-success">{{ number_format($rec->price, 0) }} {{ $rec->currency }}</span>
                                    @else
                                        <span class="text-muted small">غير محدد</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rec->embassy_fee)
                                        <div>
                                            <span class="fw-bold text-dark">{{ $rec->embassy_fee }} {{ $rec->embassy_fee_currency }}</span>
                                            @if($rec->embassy_fee_payment_method)
                                                <div class="small text-muted" style="font-size: 11px;">{{ Str::limit($rec->embassy_fee_payment_method, 20) }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">غير محدد</span>
                                    @endif
                                </td>
                                <td><span class="small">{{ $rec->working_days ?: 'غير محدد' }}</span></td>
                                <td><span class="small" title="{{ $rec->proposed_duration }}">{{ Str::limit($rec->proposed_duration, 25) ?: 'غير محدد' }}</span></td>
                                <td><span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">{{ $rec->application_centers_formatted }}</span></td>
                                <td class="text-center">
                                    @if($rec->is_biometrics_required)
                                        <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle me-1"></i>نعم</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary"><i class="bi bi-x-circle me-1"></i>لا</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($rec->is_interview_required)
                                        <span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle me-1"></i>نعم</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary"><i class="bi bi-x-circle me-1"></i>لا</span>
                                    @endif
                                </td>
                                <td>
                                    <!-- Toggle Status Dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-sm badge {{ $rec->status_badge_class }} dropdown-toggle border-0" type="button" data-bs-toggle="dropdown">
                                            {{ $rec->status_label }}
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end small">
                                            <li>
                                                <form action="{{ route('admin.visa-database.toggle-status', $rec) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="active">
                                                    <button class="dropdown-item text-success" type="submit"><i class="bi bi-check-circle me-2"></i>Active / نشطة</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.visa-database.toggle-status', $rec) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="temporarily_unavailable">
                                                    <button class="dropdown-item text-warning" type="submit"><i class="bi bi-pause-circle me-2"></i>متوقفة مؤقتاً</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.visa-database.toggle-status', $rec) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="inactive">
                                                    <button class="dropdown-item text-danger" type="submit"><i class="bi bi-x-circle me-2"></i>Inactive / غير متاحة</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td class="text-end px-3">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewModal{{ $rec->id }}" title="عرض التفاصيل">
                                            <i class="bi bi-eye"></i> التفاصيل
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $rec->id }}" title="تعديل">
                                            <i class="bi bi-pencil"></i> تعديل
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="showLogs({{ $rec->id }})" title="سجل التعديلات">
                                            <i class="bi bi-history"></i> Log
                                        </button>
                                        <form action="{{ route('admin.visa-database.destroy', $rec) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت تأكد من رغبتك في حذف هذا التسجيل؟');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="حذف"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                    لا توجد بيانات تأشيرات تتطابق مع خيارات البحث والتصفية.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($records->hasPages())
            <div class="card-footer bg-light p-3">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Render Item Modals outside of the table DOM tree to prevent z-index backdrop issue -->
@foreach($records as $rec)
    <!-- View Details Modal -->
    <div class="modal fade" id="viewModal{{ $rec->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-passport me-2"></i>تفاصيل تأشيرة {{ $rec->country?->name_ar }} — {{ $rec->visa_type }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-wrap" style="white-space: normal;">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border">
                                <div class="text-muted small">الدولة والتصنيف</div>
                                <div class="fw-bold fs-5 text-dark mb-1">{{ $rec->country?->name_ar }} ({{ $rec->country?->name_en }})</div>
                                <div>
                                    @foreach($rec->country?->categories ?? [] as $c)
                                        <span class="badge bg-secondary me-1">{{ $c->name_ar }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border">
                                <div class="text-muted small">سعر التأشيرة للعميل</div>
                                <div class="fw-bold fs-4 text-success">{{ $rec->price !== null ? number_format($rec->price, 0) . ' ' . $rec->currency : 'غير محدد' }}</div>
                                <div class="small text-muted">حالة الخدمة: <span class="badge {{ $rec->status_badge_class }}">{{ $rec->status_label }}</span></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2 border rounded text-center">
                                <div class="text-muted small">رسوم السفارة</div>
                                <div class="fw-bold">{{ $rec->embassy_fee ? $rec->embassy_fee . ' ' . $rec->embassy_fee_currency : 'غير محدد' }}</div>
                                <div class="small text-muted" style="font-size: 11px;">{{ $rec->embassy_fee_payment_method }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2 border rounded text-center">
                                <div class="text-muted small">أيام العمل</div>
                                <div class="fw-bold text-primary">{{ $rec->working_days ?: 'غير محدد' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2 border rounded text-center">
                                <div class="text-muted small">مكان التقديم</div>
                                <div class="fw-bold text-dark">{{ $rec->application_centers_formatted }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 border rounded text-center">
                                <div class="text-muted small">مدة التأشيرة المقترحة</div>
                                <div class="fw-bold">{{ $rec->proposed_duration ?: 'غير محدد' }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2 border rounded text-center">
                                <div class="text-muted small">البصمة مطلوبة</div>
                                <div class="fw-bold">{{ $rec->is_biometrics_required ? 'نعم' : 'لا' }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-2 border rounded text-center">
                                <div class="text-muted small">المقابلة مطلوبة</div>
                                <div class="fw-bold">{{ $rec->is_interview_required ? 'نعم' : 'لا' }}</div>
                            </div>
                        </div>
                    </div>

                    @if(filled($rec->required_documents))
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-2"><i class="bi bi-file-earmark-text text-primary me-2"></i>الأوراق المطلوبة</h6>
                            <div class="bg-light p-3 rounded border font-monospace small" style="white-space: pre-line;">{{ $rec->required_documents }}</div>
                        </div>
                    @endif

                    @if(filled($rec->notes))
                        <div class="mb-3">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-2"><i class="bi bi-info-circle text-warning me-2"></i>ملاحظات واستثناءات هامة</h6>
                            <div class="bg-warning-subtle text-warning-emphasis p-3 rounded border border-warning-subtle small" style="white-space: pre-line;">{{ $rec->notes }}</div>
                        </div>
                    @endif

                    <!-- Hidden element containing text for copying -->
                    <textarea id="copyContent{{ $rec->id }}" class="d-none">{{ $rec->toFormattedShareText() }}</textarea>
                </div>
                <div class="modal-footer bg-light justify-content-between">
                    <button type="button" class="btn btn-outline-success" onclick="copyToClipboard({{ $rec->id }})">
                        <i class="bi bi-clipboard-check me-1"></i> نسخ البيانات (WhatsApp / Email)
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق X</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal{{ $rec->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.visa-database.update', $rec) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>تعديل بيانات تأشيرة {{ $rec->country?->name_ar }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">الدولة</label>
                                <select name="visa_country_id" class="form-select" required>
                                    @foreach($countries as $c)
                                        <option value="{{ $c->id }}" {{ $rec->visa_country_id == $c->id ? 'selected' : '' }}>{{ $c->name_ar }} ({{ $c->name_en }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">نوع التأشيرة</label>
                                <input type="text" name="visa_type" class="form-control" value="{{ $rec->visa_type }}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">التصنيفات المرتطبة بالدولة</label>
                                <div class="d-flex flex-wrap gap-3 p-2 bg-light border rounded">
                                    @php($activeCatIds = $rec->country?->categories->pluck('id')->toArray() ?? [])
                                    @foreach($categories as $cat)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="category_ids[]" value="{{ $cat->id }}" id="catEdit_{{ $rec->id }}_{{ $cat->id }}" {{ in_array($cat->id, $activeCatIds) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="catEdit_{{ $rec->id }}_{{ $cat->id }}">{{ $cat->name_ar }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">سعر التأشيرة للعميل</label>
                                <input type="number" step="0.01" name="price" class="form-control" value="{{ $rec->price }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small">العملة</label>
                                <input type="text" name="currency" class="form-control" value="{{ $rec->currency ?: 'EGP' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">رسوم السفارة</label>
                                <input type="text" name="embassy_fee" class="form-control" value="{{ $rec->embassy_fee }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">عملة السفارة</label>
                                <input type="text" name="embassy_fee_currency" class="form-control" value="{{ $rec->embassy_fee_currency ?: 'EUR' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">طريقة دفع رسوم السفارة</label>
                                <input type="text" name="embassy_fee_payment_method" class="form-control" value="{{ $rec->embassy_fee_payment_method }}" placeholder="مثال: بالمصري داخل السفارة — Visa أو Cash">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">أيام العمل</label>
                                <input type="text" name="working_days" class="form-control" value="{{ $rec->working_days }}" placeholder="مثال: 15–20 يوم عمل">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">مدة التأشيرة المقترحة</label>
                                <input type="text" name="proposed_duration" class="form-control" value="{{ $rec->proposed_duration }}" placeholder="مثال: حسب قرار السفارة من أسبوع إلى 15 يوم">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">مدة الإقامة</label>
                                <input type="text" name="stay_duration" class="form-control" value="{{ $rec->stay_duration }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">مكان التقديم</label>
                                <div class="d-flex flex-wrap gap-2 pt-1">
                                    @php($currCenters = $rec->application_centers_list)
                                    @foreach(['VFS', 'TLS', 'BLS', 'Almaviva / المافيفا', 'السفارة مباشرة'] as $centerOpt)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="application_center[]" value="{{ $centerOpt }}" id="centerEdit_{{ $rec->id }}_{{ $loop->index }}" {{ in_array($centerOpt, $currCenters) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="centerEdit_{{ $rec->id }}_{{ $loop->index }}">{{ $centerOpt }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">البصمة مطلوبة؟</label>
                                <select name="is_biometrics_required" class="form-select">
                                    <option value="1" {{ $rec->is_biometrics_required ? 'selected' : '' }}>Yes / نعم</option>
                                    <option value="0" {{ ! $rec->is_biometrics_required ? 'selected' : '' }}>No / لا</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small">المقابلة مطلوبة؟</label>
                                <select name="is_interview_required" class="form-select">
                                    <option value="1" {{ $rec->is_interview_required ? 'selected' : '' }}>Yes / نعم</option>
                                    <option value="0" {{ ! $rec->is_interview_required ? 'selected' : '' }}>No / لا</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">الحالة</label>
                                <select name="status" class="form-select">
                                    <option value="active" {{ $rec->status == 'active' ? 'selected' : '' }}>Active / نشطة</option>
                                    <option value="temporarily_unavailable" {{ $rec->status == 'temporarily_unavailable' ? 'selected' : '' }}>Temporarily Unavailable / متوقفة مؤقتاً</option>
                                    <option value="inactive" {{ $rec->status == 'inactive' ? 'selected' : '' }}>Inactive / غير متاحة</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">الأوراق المطلوبة</label>
                                <textarea name="required_documents" class="form-control" rows="5">{{ $rec->required_documents }}</textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small">ملاحظات واستثناءات</label>
                                <textarea name="notes" class="form-control" rows="3">{{ $rec->notes }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Changes / حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- Add Visa Record Modal -->
<div class="modal fade" id="addVisaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.visa-database.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>إضافة تأشيرة جديدة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">الدولة</label>
                            <select name="visa_country_id" class="form-select" required>
                                <option value="">-- اختر الدولة --</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c->id }}">{{ $c->name_ar }} ({{ $c->name_en }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">نوع التأشيرة</label>
                            <input type="text" name="visa_type" class="form-control" value="سياحة" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">سعر التأشيرة للعميل</label>
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="6500">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small">العملة</label>
                            <input type="text" name="currency" class="form-control" value="EGP">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">رسوم السفارة</label>
                            <input type="text" name="embassy_fee" class="form-control" placeholder="90">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">عملة السفارة</label>
                            <input type="text" name="embassy_fee_currency" class="form-control" value="EUR">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">طريقة دفع رسوم السفارة</label>
                            <input type="text" name="embassy_fee_payment_method" class="form-control" placeholder="بالمصري داخل السفارة — Visa أو Cash">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">أيام العمل</label>
                            <input type="text" name="working_days" class="form-control" placeholder="15–20 يوم عمل">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">مدة التأشيرة المقترحة</label>
                            <input type="text" name="proposed_duration" class="form-control" placeholder="حسب قرار السفارة من أسبوع إلى 15 يوم">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">مكان التقديم</label>
                            <div class="d-flex flex-wrap gap-2 pt-1">
                                @foreach(['VFS', 'TLS', 'BLS', 'Almaviva / المافيفا', 'السفارة مباشرة'] as $centerOpt)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="application_center[]" value="{{ $centerOpt }}" id="centerAdd_{{ $loop->index }}">
                                        <label class="form-check-label small" for="centerAdd_{{ $loop->index }}">{{ $centerOpt }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">البصمة مطلوبة؟</label>
                            <select name="is_biometrics_required" class="form-select">
                                <option value="1" selected>Yes / نعم</option>
                                <option value="0">No / لا</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">المقابلة مطلوبة؟</label>
                            <select name="is_interview_required" class="form-select">
                                <option value="1" selected>Yes / نعم</option>
                                <option value="0">No / لا</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="active" selected>Active / نشطة</option>
                                <option value="temporarily_unavailable">Temporarily Unavailable / متوقفة مؤقتاً</option>
                                <option value="inactive">Inactive / غير متاحة</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">الأوراق المطلوبة</label>
                            <textarea name="required_documents" class="form-control" rows="4" placeholder="اكتب المستندات المطلوبة..."></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">ملاحظات واستثناءات</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="أية ملاحظات خاصة..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> حفظ التأشيرة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manage Categories Modal -->
<div class="modal fade" id="manageCategoriesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-tags me-2"></i>إدارة التصنيفات</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('admin.visa-database.categories.store') }}" method="POST" class="mb-4">
                    @csrf
                    <h6 class="fw-bold mb-2">إضافة تصنيف جديد</h6>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="text" name="name_ar" class="form-control form-control-sm" placeholder="الاسم بالعربية" required>
                        </div>
                        <div class="col-6">
                            <input type="text" name="name_en" class="form-control form-control-sm" placeholder="Name in English" required>
                        </div>
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-sm btn-success w-100"><i class="bi bi-plus me-1"></i> إضافة تصنيف</button>
                        </div>
                    </div>
                </form>

                <h6 class="fw-bold mb-2">التصنيفات الحالية ({{ $categories->count() }})</h6>
                <div class="list-group list-group-flush border rounded" style="max-height: 250px; overflow-y: auto;">
                    @foreach($categories as $cat)
                        <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                            <div>
                                <span class="fw-bold">{{ $cat->name_ar }}</span>
                                <span class="small text-muted ms-2">({{ $cat->name_en }})</span>
                            </div>
                            <span class="badge bg-light text-dark border">{{ $cat->pivotCountries()->count() ?: $cat->countries()->count() }} دولة</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<!-- Activity Log Modal -->
<div class="modal fade" id="activityLogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-clock-history me-2"></i>سجل التعديلات (Activity Log)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="logContent">
                    <div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(id) {
    var copyText = document.getElementById("copyContent" + id);
    if (!copyText) return;
    
    navigator.clipboard.writeText(copyText.value).then(function() {
        alert("✅ تم نسخ جميع بيانات التأشيرة إلى الحافظة بنجاح! يمكنك الآن لصقها في WhatsApp أو Email.");
    }, function(err) {
        // Fallback for older browsers
        copyText.classList.remove('d-none');
        copyText.select();
        document.execCommand("copy");
        copyText.classList.add('d-none');
        alert("✅ تم نسخ البيانات إلى الحافظة!");
    });
}

function showLogs(id) {
    var modal = new bootstrap.Modal(document.getElementById('activityLogModal'));
    var contentDiv = document.getElementById('logContent');
    contentDiv.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';
    modal.show();

    fetch('{{ url("admin/visa-database") }}/' + id + '/logs')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.logs.length > 0) {
                var html = '<div class="table-responsive"><table class="table table-sm table-striped align-middle"><thead><tr><th>التاريخ</th><th>المستخدم</th><th>العملية</th><th>التفاصيل</th></tr></thead><tbody>';
                data.logs.forEach(function(log) {
                    var date = new Date(log.created_at).toLocaleString('ar-EG');
                    html += '<tr><td>' + date + '</td><td><span class="badge bg-light text-dark border">' + (log.user_name || 'System') + '</span></td><td><span class="fw-bold">' + log.action + '</span></td><td class="small">' + log.description + '</td></tr>';
                });
                html += '</tbody></table></div>';
                contentDiv.innerHTML = html;
            } else {
                contentDiv.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-info-circle fs-3 d-block mb-2"></i>لا توجد تعديلات مسجلة سابقاً لهذه التأشيرة.</div>';
            }
        })
        .catch(err => {
            contentDiv.innerHTML = '<div class="alert alert-danger">حدث خطأ أثناء تحميل سجل التعديلات.</div>';
        });
}
</script>
@endsection
