@extends('layouts.admin')

@section('title', 'تفاصيل موعد السفارة - ' . $item->country_name)

@section('content')
<div class="container-fluid py-3">

    {{-- Breadcrumb / Navigation --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('admin.embassy-appointments.index') }}" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-1">
                <iconify-icon icon="lucide:arrow-right"></iconify-icon> العودة لجدول المواعيد
            </a>
            <h1 class="h3 fw-bold mb-0">
                🏛️ {{ $item->country_name }} — {{ $item->visa_type }} ({{ $item->appointment_center }} - {{ $item->appointment_type }})
            </h1>
        </div>
        <div class="d-flex gap-2">
            @if($item->status !== 'available_now')
                <form method="POST" action="{{ route('admin.embassy-appointments.toggle-available-now', $item) }}">
                    @csrf
                    <button type="submit" class="btn btn-success shadow-sm">
                        🟢 مواعيد متاحة الآن
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.embassy-appointments.toggle-no-availability', $item) }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        🔴 إغلاق المواعيد
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- Main Info --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0">📌 تفاصيل الموعد</h5>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">الدولة:</span>
                            <span class="fw-bold">{{ $item->country_name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">نوع التأشيرة:</span>
                            <span class="fw-semibold">{{ $item->visa_type }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">مركز التقديم:</span>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $item->appointment_center }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">نوع الموعد:</span>
                            <span class="badge bg-info bg-opacity-10 text-info">{{ $item->appointment_type }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">الحالة الحالية:</span>
                            <span class="badge {{ $item->status_badge_class }}">
                                {{ $item->status_icon }} {{ $item->status_label }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">أقرب موعد متاح:</span>
                            <span class="fw-bold text-dark">{{ $item->earliest_date ?: 'غير محدد' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">آخر تحديث:</span>
                            <span class="fw-semibold">{{ $item->formatted_last_updated }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">تم التحديث بواسطة:</span>
                            <span class="fw-semibold">{{ $item->updatedBy?->name ?: 'النظام' }}</span>
                        </li>
                        @if($item->booking_link)
                            <li class="list-group-item px-0 pt-3">
                                <a href="{{ $item->booking_link }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                    🔗 فتح رابط الحجز المباشر
                                </a>
                            </li>
                        @endif
                    </ul>

                    @if($item->notes)
                        <div class="mt-3 p-3 bg-light rounded border">
                            <small class="fw-semibold d-block text-muted mb-1">📝 ملاحظات:</small>
                            <p class="mb-0 small text-dark">{{ $item->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Waiting Leads Counter Card --}}
            <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                <div class="card-body p-4 text-center">
                    <div class="fs-1 fw-bold text-info mb-1">{{ number_format($waitingLeadsCount) }}</div>
                    <div class="fw-semibold text-dark mb-1">عملاء تنتظر فتح مواعيد السفارة ⏳</div>
                    <small class="text-muted d-block">عملاء حاليين بقاعدة البيانات مطابقة لدولة وشروط هذا الموعد</small>
                </div>
            </div>
        </div>

        {{-- Affected Leads & Activity Logs --}}
        <div class="col-12 col-lg-8">

            {{-- Affected Leads Notification List --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">🔔 تنبيهات العملاء والبائعين لموسم المواعيد الحالية</h5>
                    @if($latestEvent)
                        <span class="badge bg-light text-dark border">حدث رقم #{{ $latestEvent->id }} ({{ $latestEvent->created_at->format('Y-m-d H:i') }})</span>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($notificationsBySeller->isNotEmpty())
                        <div class="p-3">
                            @foreach($notificationsBySeller as $sellerId => $notifs)
                                @php($seller = $notifs->first()?->seller)
                                <div class="card border mb-3">
                                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                                        <div class="fw-bold text-dark">
                                            👤 البائع: {{ $seller?->name ?: 'غير معين' }} ({{ $notifs->count() }} عميل)
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover align-middle mb-0">
                                            <thead class="table-light fs-8">
                                                <tr>
                                                    <th>اسم العميل</th>
                                                    <th>رقم الهاتف</th>
                                                    <th>حالة التنبيه</th>
                                                    <th>نتيجة الاتصال</th>
                                                    <th>تاريخ التنبيه</th>
                                                    <th>الإجراء</th>
                                                </tr>
                                            </thead>
                                            <tbody class="small">
                                                @foreach($notifs as $n)
                                                    <tr>
                                                        <td class="fw-semibold">{{ $n->lead?->full_name }}</td>
                                                        <td>{{ $n->lead?->phone }}</td>
                                                        <td>
                                                            @if($n->status === 'contacted')
                                                                <span class="badge bg-success">تم الاتصال ✅</span>
                                                            @elseif($n->status === 'snoozed')
                                                                <span class="badge bg-warning text-dark">مؤجل ⏱️ (حتى {{ $n->snoozed_until?->format('H:i') }})</span>
                                                            @else
                                                                <span class="badge bg-primary">في الانتظار 🔔</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $n->contact_result_label ?: '—' }}</td>
                                                        <td class="text-muted">{{ $n->created_at->format('Y-m-d H:i') }}</td>
                                                        <td>
                                                            <a href="{{ route('admin.crm.leads.show', $n->inquiry_id) }}" class="btn btn-xs btn-outline-primary" target="_blank">
                                                                عرض العميل
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            لا توجد تنبيهات صادرة لهذا الموعد حالياً. عند النقر على 🟢 "مواعيد متاحة الآن" يتم إنشاء التنبيهات تلقائيًا.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Audit Logs History --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0">📜 سجل تغييرات الموعد (History Log)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th>التاريخ والوقت</th>
                                    <th>المستخدم</th>
                                    <th>الإجراء</th>
                                    <th>الحالة السابقة</th>
                                    <th>الحالة الجديدة</th>
                                    <th>أقرب موعد</th>
                                    <th>ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($item->logs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('Y-m-d — h:i A') }}</td>
                                        <td class="fw-semibold">{{ $log->user_name ?: 'النظام' }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $log->action }}</span>
                                        </td>
                                        <td>
                                            @if($log->old_status)
                                                <span class="text-muted">{{ $log->old_status }}</span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold text-primary">{{ $log->new_status }}</span>
                                        </td>
                                        <td>{{ $log->new_earliest_date ?: '—' }}</td>
                                        <td class="text-muted">{{ $log->notes ?: '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">لا يوجد سجل تغييرات سابق.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
