@extends('layouts.admin')

@section('page_title', 'محادثات WhatsApp')
@section('page_description', 'إدارة ومتابعة محادثات WhatsApp البوت')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card admin-card p-4">
            <div class="text-muted small mb-1">إجمالي المحادثات</div>
            <div class="fs-3 fw-semibold">{{ number_format($stats['total']) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card admin-card p-4">
            <div class="text-muted small mb-1">نشطة (AI يرد)</div>
            <div class="fs-3 fw-semibold text-success">{{ number_format($stats['active']) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card admin-card p-4">
            <div class="text-muted small mb-1">تحت إشراف بشري</div>
            <div class="fs-3 fw-semibold text-warning">{{ number_format($stats['human_handover']) }}</div>
        </div>
    </div>
</div>

<div class="card admin-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">المحادثات</h2>
        <a href="{{ route('admin.chatbot-settings.edit') }}" class="btn btn-outline-primary btn-sm">⚙️ إعدادات WhatsApp</a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="q" class="form-control" placeholder="بحث برقم أو اسم..." value="{{ request('q') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">كل الحالات</option>
                <option value="active" @selected(request('status') === 'active')>نشطة</option>
                <option value="human_handover" @selected(request('status') === 'human_handover')>Human Handover</option>
                <option value="closed" @selected(request('status') === 'closed')>مغلقة</option>
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">بحث</button>
        </div>
    </form>

    @if($conversations->isEmpty())
        <div class="text-muted text-center py-5">
            <div style="font-size:3rem">💬</div>
            <p>لا توجد محادثات بعد. تأكد من إعداد WhatsApp Webhook.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>العميل</th>
                        <th>آخر رسالة</th>
                        <th>الحالة</th>
                        <th>الـ AI</th>
                        <th>المسؤول</th>
                        <th>إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($conversations as $conv)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $conv->contact_name ?: 'غير معروف' }}</div>
                                <div class="text-muted small">{{ $conv->wa_id }}</div>
                            </td>
                            <td>
                                <div class="small text-muted">{{ $conv->last_message_at?->diffForHumans() }}</div>
                                @if($conv->messages->first())
                                    <div class="small text-truncate" style="max-width:200px">
                                        {{ $conv->messages->first()->body }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($conv->status === 'active')
                                    <span class="badge bg-success-subtle text-success-emphasis">نشطة</span>
                                @elseif($conv->status === 'human_handover')
                                    <span class="badge bg-warning-subtle text-warning-emphasis">Human Handover</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">مغلقة</span>
                                @endif
                            </td>
                            <td>
                                @if($conv->ai_active)
                                    <span class="badge bg-primary-subtle text-primary-emphasis">🤖 مفعّل</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-muted">⏸️ موقوف</span>
                                @endif
                            </td>
                            <td>{{ $conv->assignedUser?->name ?? '—' }}</td>
                            <td>
                                <a href="{{ route('admin.whatsapp.conversations.show', $conv) }}" class="btn btn-sm btn-outline-primary">فتح</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $conversations->links() }}</div>
    @endif
</div>
@endsection
