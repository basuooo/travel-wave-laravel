@extends('layouts.admin')

@section('title', 'قاعدة بيانات WhatsApp Contacts')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">WhatsApp Contacts</h3>
            <p class="text-muted small mb-0">قاعدة بيانات جميع جهات الاتصال المسجلة بالواتساب والمرتبطة بالـ CRM</p>
        </div>
        <button class="btn btn-primary font-weight-bold" data-bs-toggle="modal" data-bs-target="#createContactModal">
            ➕ إضافة جهة اتصال
        </button>
    </div>

    @include('admin.whatsapp.nav')

    <!-- Filters Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.whatsapp.contacts.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="بحث بالاسم أو رقم الهاتف..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="account_id" class="form-select">
                        <option value="">-- جميع أرقام WhatsApp --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }} ({{ $acc->phone_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="opt_out_status" class="form-select">
                        <option value="">-- جميع حالات التفضيل --</option>
                        <option value="opted_in" {{ request('opt_out_status') == 'opted_in' ? 'selected' : '' }}>Opted In</option>
                        <option value="opted_out" {{ request('opt_out_status') == 'opted_out' ? 'selected' : '' }}>Opted Out</option>
                        <option value="do_not_contact" {{ request('opt_out_status') == 'do_not_contact' ? 'selected' : '' }}>Do Not Contact</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-secondary w-100">فلترة</button>
                    <a href="{{ route('admin.whatsapp.contacts.index') }}" class="btn btn-outline-secondary">إعادة ضبط</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Contacts Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>الاسم</th>
                            <th>رقم الهاتف</th>
                            <th>حساب WhatsApp المرتبط</th>
                            <th>الموظف المسؤول</th>
                            <th>حالة CRM</th>
                            <th>الخدمة / الدولة</th>
                            <th>حالة التفضيل (Opt-out)</th>
                            <th>عدد المحادثات</th>
                            <th>تاريخ أول / آخر تواصل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contacts as $c)
                            <tr>
                                <td class="fw-bold">{{ $c->name ?: 'بدون اسم' }}</td>
                                <td><span class="font-monospace text-primary fw-bold">{{ $c->phone }}</span></td>
                                <td>{{ $c->account?->name ?? '-' }}</td>
                                <td>{{ $c->assignedUser?->name ?? 'غير معين' }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ strtoupper($c->status_in_crm ?? 'Lead') }}</span>
                                </td>
                                <td>
                                    <span class="small">{{ $c->service ?? '-' }}</span> / 
                                    <span class="small fw-bold">{{ $c->country ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($c->opt_out_status === 'opted_in')
                                        <span class="badge bg-success">Opted In</span>
                                    @elseif($c->opt_out_status === 'do_not_contact')
                                        <span class="badge bg-danger">Do Not Contact</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ $c->opt_out_status }}</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-secondary">{{ $c->conversation_count }}</span></td>
                                <td class="small text-muted">
                                    {{ $c->last_contact_at ? $c->last_contact_at->format('Y-m-d') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">لا توجد جهات اتصال مطابقة.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($contacts->hasPages())
                <div class="p-3">
                    {{ $contacts->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Create Contact -->
<div class="modal fade" id="createContactModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.whatsapp.contacts.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">إضافة جهة اتصال WhatsApp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">الاسم</label>
                    <input type="text" name="name" class="form-control" required placeholder="اسم العميل">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control" required placeholder="+34600000000">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">WhatsApp Account</label>
                    <select name="whatsapp_account_id" class="form-select">
                        <option value="">-- اختر الحساب --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">الخدمة المطلوبة</label>
                    <input type="text" name="service" class="form-control" placeholder="تأشيرة إسبانيا">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">الدولة</label>
                    <input type="text" name="country" class="form-control" placeholder="Spain">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary">حفظ جهة الاتصال</button>
            </div>
        </form>
    </div>
</div>
@endsection
