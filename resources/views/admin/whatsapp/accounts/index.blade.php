@extends('layouts.admin')

@section('title', 'إدارة أرقام WhatsApp')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">WhatsApp Accounts</h3>
            <p class="text-muted small mb-0">إدارة أرقام الواتساب المرتبطة بالنظام وتحديد الموظف المسؤول</p>
        </div>
        <button class="btn btn-primary font-weight-bold" data-bs-toggle="modal" data-bs-target="#createAccountModal">
            ➕ إضافة رقم جديد
        </button>
    </div>

    @include('admin.whatsapp.nav')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>اسم الرقم</th>
                            <th>رقم الهاتف</th>
                            <th>حالة الاتصال</th>
                            <th>نوع الاستخدام</th>
                            <th>الموظف المسؤول</th>
                            <th>القسم / الفرع</th>
                            <th>المحادثات</th>
                            <th>مرسل / فاشل</th>
                            <th>آخر اتصال</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $acc)
                            <tr>
                                <td>{{ $acc->id }}</td>
                                <td class="fw-bold">{{ $acc->name }}</td>
                                <td>
                                    <span class="font-monospace text-primary fw-bold">{{ $acc->phone_number }}</span>
                                </td>
                                <td>
                                    @if($acc->status === 'connected')
                                        <span class="badge bg-success">🟢 Connected</span>
                                    @else
                                        <span class="badge bg-danger">🔴 Disconnected</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ strtoupper($acc->usage_type) }}</span>
                                </td>
                                <td>{{ $acc->assignedUser?->name ?? 'غير معين' }}</td>
                                <td>{{ $acc->department_branch ?? 'الرئيسي' }}</td>
                                <td><span class="badge bg-info text-dark">{{ $acc->conversations_count }}</span></td>
                                <td>
                                    <span class="text-success font-monospace fw-bold">{{ $acc->sent_count }}</span> / 
                                    <span class="text-danger font-monospace fw-bold">{{ $acc->failed_count }}</span>
                                </td>
                                <td class="small text-muted">{{ $acc->last_connected_at ? $acc->last_connected_at->format('Y-m-d H:i') : '-' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <form action="{{ route('admin.whatsapp.accounts.toggle-connect', $acc->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @if($acc->status === 'connected')
                                                <button class="btn btn-outline-warning" title="Disconnect">Disconnect</button>
                                            @else
                                                <button class="btn btn-outline-success" title="Connect">Connect</button>
                                            @endif
                                        </form>

                                        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#qrModal{{ $acc->id }}" title="QR Code">
                                            📷 QR
                                        </button>

                                        <a href="{{ route('admin.whatsapp.conversations.index', ['account_id' => $acc->id]) }}" class="btn btn-outline-info" title="View Conversations">
                                            💬
                                        </a>

                                        <form action="{{ route('admin.whatsapp.accounts.destroy', $acc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت تأكد من حذف هذا الرقم؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger" title="Delete">🗑️</button>
                                        </form>
                                    </div>

                                    <!-- QR Code Modal -->
                                    <div class="modal fade" id="qrModal{{ $acc->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-center">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">مسح QR Code للربط - {{ $acc->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body py-4">
                                                    <div class="p-3 bg-light d-inline-block rounded border mb-3">
                                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=WHATSAPP_CONNECT_{{ $acc->id }}" alt="QR Code" width="220" height="220">
                                                    </div>
                                                    <p class="text-muted small">افتح تطبيق الواتساب على هاتفك وأختر "الأجهزة المرتبطة" لربط الحساب بالنظام تلقائياً.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">لا توجد أرقام واتساب مضافة حالياً. اضغط "إضافة رقم جديد" للبدء.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create Account -->
<div class="modal fade" id="createAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.whatsapp.accounts.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">إضافة رقم WhatsApp جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">اسم الرقم في النظام</label>
                    <input type="text" name="name" class="form-check-input form-control" placeholder="مثال: واتساب خدمة المبيعات - مصر" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">رقم الهاتف</label>
                    <input type="text" name="phone_number" class="form-control" placeholder="مثال: +201012345678" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">نوع الاستخدام (Usage Type)</label>
                    <select name="usage_type" class="form-select" required>
                        <option value="both">Both (Retargeting & Bulk)</option>
                        <option value="retargeting">Retargeting Only</option>
                        <option value="bulk">Bulk Campaigns Only</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">الموظف المسؤول</label>
                    <select name="assigned_user_id" class="form-select">
                        <option value="">-- اختر موظف --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">القسم / الفرع</label>
                    <input type="text" name="department_branch" class="form-control" placeholder="مثال: فرع القاهرة / مبيعات الفيزا">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Meta Phone Number ID (اختياري / للربط المباشر)</label>
                    <input type="text" name="phone_number_id" class="form-control" placeholder="Phone Number ID">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Permanent Access Token (اختياري)</label>
                    <textarea name="access_token" class="form-control" rows="2" placeholder="Bearer Token..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary font-weight-bold">حفظ وإضافة</button>
            </div>
        </form>
    </div>
</div>
@endsection
