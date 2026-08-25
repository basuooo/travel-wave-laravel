@extends('layouts.admin')

@section('title', 'سجل الرسائل والتتبع التفصيلي — Message Logs')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">Message Logs</h3>
            <p class="text-muted small mb-0">سجل عمليات الإرسال والفشل وأسباب الأخطاء لكل رسالة فردية</p>
        </div>
    </div>

    @include('admin.whatsapp.nav')

    <!-- Logs Filter -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.whatsapp.logs.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="بحث باسم المستلم أو رقم الهاتف..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">-- جميع الحالات --</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="skipped_blacklist" {{ request('status') == 'skipped_blacklist' ? 'selected' : '' }}>Skipped Blacklist</option>
                        <option value="skipped_optout" {{ request('status') == 'skipped_optout' ? 'selected' : '' }}>Skipped Opt-out</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-secondary w-100">بحث</button>
                    <a href="{{ route('admin.whatsapp.logs.index') }}" class="btn btn-outline-secondary">إعادة ضبط</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>المستلم</th>
                            <th>رقم الهاتف</th>
                            <th>الحملة</th>
                            <th>WhatsApp Account</th>
                            <th>حالة التسليم</th>
                            <th>وقت الإرسال</th>
                            <th>سبب الفشل / الخطأ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td class="fw-bold">{{ $log->contact_name ?: 'Contact' }}</td>
                                <td><span class="font-monospace text-primary fw-bold">{{ $log->phone }}</span></td>
                                <td>{{ $log->campaign?->name ?? '-' }}</td>
                                <td>{{ $log->account?->name ?? '-' }}</td>
                                <td>
                                    @if($log->status === 'sent')
                                        <span class="badge bg-success">Sent</span>
                                    @elseif($log->status === 'failed')
                                        <span class="badge bg-danger">Failed</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $log->status }}</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $log->sent_at ? $log->sent_at->format('Y-m-d H:i:s') : '-' }}</td>
                                <td class="small text-danger">{{ $log->error_message ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">لا توجد سجلات مطابقة.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="p-3">
                    {{ $logs->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
