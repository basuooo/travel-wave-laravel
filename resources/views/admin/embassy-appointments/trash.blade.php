@extends('layouts.admin')

@section('title', 'سلة محذوفات مواعيد السفارات - Deleted Embassy Appointments')

@section('content')
<div class="container-fluid py-3">

    {{-- Header Title & Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 fw-bold mb-1">🗑️ سلة المحذوفات (المواعيد المحذوفة)</h1>
            <p class="text-muted mb-0">عرض وإدارة مواعيد السفارات المحذوفة، ويمكنك استعادتها أو حذفها بشكل نهائي.</p>
        </div>
        <div>
            <a href="{{ route('admin.embassy-appointments.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1 shadow-sm">
                <iconify-icon icon="lucide:arrow-right" width="18"></iconify-icon>
                <span>العودة للمواعيد النشطة</span>
            </a>
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

    {{-- Trash List Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr>
                            <th class="ps-4">الدولة</th>
                            <th>نوع التأشيرة</th>
                            <th>مركز التقديم</th>
                            <th>نوع الموعد</th>
                            <th>حالة الموعد وقت الحذف</th>
                            <th>أقرب موعد متاح</th>
                            <th>تاريخ الحذف</th>
                            <th class="text-end pe-4">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($item->country && $item->country->flag_image)
                                            <img src="{{ asset('storage/' . $item->country->flag_image) }}" alt="" style="width: 24px; height: 16px; object-fit: cover;" class="rounded shadow-sm">
                                        @endif
                                        <span class="fw-bold text-dark">{{ $item->country ? ($item->country->name_ar ?: $item->country->name_en) : 'دولة غير معروفة' }}</span>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary text-white">{{ $item->visa_type }}</span></td>
                                <td><span class="fw-semibold text-dark">{{ $item->appointment_center }}</span></td>
                                <td><span class="badge bg-light text-dark border">{{ $item->appointment_type }}</span></td>
                                <td>
                                    @if($item->status === \App\Models\EmbassyAppointment::STATUS_AVAILABLE_NOW)
                                        <span class="badge bg-success">🟢 متاحة الآن</span>
                                    @elseif($item->status === \App\Models\EmbassyAppointment::STATUS_AVAILABLE_LATER)
                                        <span class="badge bg-warning text-dark">🟡 متاحة مستقبلاً</span>
                                    @elseif($item->status === \App\Models\EmbassyAppointment::STATUS_NO_AVAILABILITY)
                                        <span class="badge bg-danger">🔴 لا توجد مواعيد</span>
                                    @else
                                        <span class="badge bg-secondary">⚪ غير معروف</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">{{ $item->earliest_date ?: 'غير محدد' }}</span>
                                </td>
                                <td>
                                    <small class="text-muted" title="{{ $item->deleted_at }}">{{ $item->deleted_at ? $item->deleted_at->diffForHumans() : '-' }}</small>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-2">
                                        <form method="POST" action="{{ route('admin.embassy-appointments.restore', $item->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1 shadow-sm" title="استعادة الموعد للمواعيد النشطة">
                                                <iconify-icon icon="lucide:rotate-ccw" width="16"></iconify-icon>
                                                <span>استعادة 🔄</span>
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.embassy-appointments.force-delete', $item->id) }}" class="d-inline" onsubmit="return confirm('هل أنت تأكد من الحذف النهائي لهذا الموعد؟ لا يمكن التراجع عن هذا الإجراء.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 shadow-sm" title="حذف نهائي لا يمكن استعادته">
                                                <iconify-icon icon="lucide:trash-2" width="16"></iconify-icon>
                                                <span>حذف نهائي ❌</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <iconify-icon icon="lucide:trash-2" width="48" class="text-secondary opacity-50 mb-2"></iconify-icon>
                                    <p class="mb-0 fw-semibold">سلة المحذوفات فارغة حاليًا.</p>
                                    <small>لا توجد مواعيد سفارات محذوفة.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
