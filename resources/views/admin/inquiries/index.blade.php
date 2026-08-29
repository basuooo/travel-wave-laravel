@extends('layouts.admin')

@section('page_title', 'قسم النماذج والعملاء - فورـم لـيـد')
@section('page_description', 'عرض ومتابعة كافة العملاء والليدز المسجلة عبر النماذج والموقع الإلكتروني حصراً.')

@section('content')
<!-- Nav Tabs -->
<ul class="nav nav-tabs border-bottom mb-4">
    <li class="nav-item">
        <a class="nav-link fs-6 fw-bold px-4 py-2 text-secondary" href="{{ route('admin.forms.index') }}">
            📋 إدارة النماذج (Forms)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active fs-6 fw-bold px-4 py-2 text-primary border-primary border-bottom-0" href="{{ route('admin.forms.submissions') }}">
            📥 فورـم لـيـد (Form Leads)
        </a>
    </li>
</ul>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    @foreach($stats as $label => $count)
        <div class="col-md-3">
            <div class="card admin-card p-3 shadow-sm border-start border-4 border-primary">
                <div class="text-muted text-uppercase small fw-bold mb-1">{{ $label }}</div>
                <div class="h3 mb-0 fw-extrabold text-dark">{{ number_format($count) }}</div>
            </div>
        </div>
    @endforeach
</div>

<!-- Filter Shell -->
<div class="card admin-card p-4 mb-4 shadow-sm">
    <form method="get" action="{{ route('admin.forms.submissions') }}" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label fw-bold">بحث عام</label>
            <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="اسم العميل، رقم الهاتف، أو اسم الفورم...">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold">اسم الفورم</label>
            <select class="form-select" name="form">
                <option value="">جميع النماذج</option>
                @foreach($forms as $form)
                    <option value="{{ $form->id }}" @selected((string) request('form') === (string) $form->id)>{{ $form->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold">الحالة</label>
            <select class="form-select" name="status">
                <option value="">الكل</option>
                <option value="new" @selected(request('status')==='new')>جديد (New)</option>
                <option value="contacted" @selected(request('status')==='contacted')>تم التواصل (Contacted)</option>
                <option value="closed" @selected(request('status')==='closed')>مغلق / تم (Closed)</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold">تاريخ التسجيل</label>
            <input type="date" class="form-control" name="date" value="{{ request('date') }}">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary fw-bold w-100">بحث وتصفية</button>
            <a href="{{ route('admin.forms.submissions') }}" class="btn btn-outline-secondary" title="إعادة تصفية">🔄</a>
        </div>
    </form>
</div>

<!-- Form Leads Table -->
<div class="card admin-card p-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="h5 fw-bold mb-0">📥 قائمة الليدز المسجلة عبر النماذج (Form Leads)</h4>
        <span class="badge bg-primary fs-6 px-3 py-2">عدد النتائج: {{ $items->total() }}</span>
    </div>

    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead class="table-light">
                <tr>
                    <th style="min-width: 140px;">اسم الفورم</th>
                    <th style="min-width: 150px;">تاريخ التسجيل</th>
                    <th style="min-width: 180px;">اسم العميل</th>
                    <th style="min-width: 140px;">رقم الهاتف</th>
                    <th style="min-width: 200px;">بيانات عامة باختصار</th>
                    <th class="text-end" style="min-width: 160px;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', $item->phone ?? '');
                        if (!str_starts_with($cleanPhone, '20') && strlen($cleanPhone) === 11) {
                            $cleanPhone = '20' . $cleanPhone;
                        }
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-bold text-primary">{{ $item->form_name ?: ($item->form?->name ?: 'نموذج عام') }}</div>
                            <div class="text-muted small">{{ $item->form_category ?: ($item->type ?: 'عام') }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->created_at ? $item->created_at->format('Y-m-d') : '-' }}</div>
                            <div class="text-muted small">{{ $item->created_at ? $item->created_at->format('h:i A') : '' }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->full_name ?: 'بدون اسم' }}</div>
                            @if($item->email)
                                <div class="text-muted small">{{ $item->email }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark" dir="ltr">{{ $item->phone ?: '-' }}</div>
                            @if($cleanPhone)
                                <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="badge text-bg-success text-decoration-none small mt-1 d-inline-flex align-items-center gap-1">
                                    💬 واتساب
                                </a>
                            @endif
                        </td>
                        <td>
                            <div class="mb-1">
                                <span class="badge bg-secondary">{{ $item->crmServiceType?->name_ar ?: ucfirst($item->type ?: 'عام') }}</span>
                                @if($item->source_page)
                                    @php
                                        $rawSource = $item->source_page;
                                        $sourceUrl = (str_starts_with($rawSource, 'http://') || str_starts_with($rawSource, 'https://'))
                                            ? $rawSource
                                            : url(ltrim($rawSource, '/'));
                                    @endphp
                                    <a href="{{ $sourceUrl }}" target="_blank" rel="noopener noreferrer" class="badge text-bg-light border text-decoration-none text-primary d-inline-flex align-items-center gap-1" title="فتح صفحة المصدر في نافذة جديدة: {{ $sourceUrl }}">
                                        <i class="bi bi-box-arrow-up-right small"></i>
                                        <span>{{ ltrim($rawSource, '/') }}</span>
                                    </a>
                                @endif
                            </div>
                            <div>
                                @if($item->crmStatus)
                                    <span class="badge" style="background-color: {{ $item->crmStatus->color ?: '#6c757d' }}; color: #fff;">
                                        {{ $item->crmStatus->name_ar ?: $item->crmStatus->name_en }}
                                    </span>
                                @else
                                    <span class="badge {{ $item->status === 'new' ? 'text-bg-warning' : ($item->status === 'contacted' ? 'text-bg-info' : 'text-bg-success') }}">
                                        {{ ucfirst($item->status ?: 'جديد') }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.crm.leads.show', $item->id) }}" class="btn btn-sm btn-success fw-bold d-inline-flex align-items-center gap-1" title="فتح وتعديل الليد في الـ CRM">
                                    ✏️ فتح / تعديل
                                </a>
                                <a href="{{ route('admin.inquiries.show', $item->id) }}" class="btn btn-sm btn-outline-primary fw-bold" title="عرض التفاصيل الكاملة لإجابات الفورم">
                                    👁️ التفاصيل
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <div class="fs-2 mb-2">📥</div>
                            <div>لا توجد ليدز مسجلة عبر النماذج حالياً.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>
@endsection
