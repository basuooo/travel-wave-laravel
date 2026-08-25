@extends('layouts.admin')

@section('title', 'إعدادات WhatsApp والقائمة السوداء — Blacklist')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">WhatsApp Settings & Blacklist</h3>
            <p class="text-muted small mb-0">إدارة القائمة السوداء الموحدة (Do Not Contact) لمنع الإرسال التلقائي للعملاء المستبعدين</p>
        </div>
        <button class="btn btn-danger font-weight-bold" data-bs-toggle="modal" data-bs-target="#addBlacklistModal">
            🚫 إضافة رقم للقائمة السوداء
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
        <div class="card-header bg-white fw-bold">
            🚫 القائمة السوداء المركزية (Centralized Blacklist)
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>رقم الهاتف المسجل</th>
                            <th>الرقم المنظم (Normalized Phone)</th>
                            <th>سبب الإضافة (Reason)</th>
                            <th>الموظف الذي أضاف الرقم</th>
                            <th>تاريخ الإضافة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blacklist as $b)
                            <tr>
                                <td>{{ $b->id }}</td>
                                <td><span class="font-monospace fw-bold text-danger">{{ $b->phone }}</span></td>
                                <td><span class="font-monospace text-muted">{{ $b->normalized_phone }}</span></td>
                                <td>{{ $b->reason }}</td>
                                <td>{{ $b->addedBy?->name ?? 'System / Opt-out' }}</td>
                                <td class="small text-muted">{{ $b->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <form action="{{ route('admin.whatsapp.settings.blacklist.destroy', $b->id) }}" method="POST" class="d-inline" onsubmit="return confirm('إزالة هذا الرقم من القائمة السوداء والسماح بالإرسال له؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-success">إزالة من Blacklist</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">القائمة السوداء فارغة حالياً. لا يوجد أرقام محظورة.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Blacklist -->
<div class="modal fade" id="addBlacklistModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.whatsapp.settings.blacklist.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">إضافة رقم للقائمة السوداء</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control" required placeholder="+201012345678">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">سبب الإضافة إلى Blacklist</label>
                    <input type="text" name="reason" class="form-control" placeholder="طلب عدم التواصل / إلغاء اشتراك">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-danger font-weight-bold">إضافة وحظر الإرسال</button>
            </div>
        </form>
    </div>
</div>
@endsection
