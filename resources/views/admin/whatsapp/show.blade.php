@extends('layouts.admin')

@section('page_title', 'محادثة: ' . ($conversation->contact_name ?: $conversation->wa_id))
@section('page_description', 'عرض وإدارة محادثة WhatsApp')

@section('content')
<div class="row g-4">
    {{-- Messages Panel --}}
    <div class="col-lg-8">
        <div class="card admin-card">
            <div class="card-header d-flex justify-content-between align-items-center p-3">
                <div>
                    <strong>{{ $conversation->contact_name ?: 'غير معروف' }}</strong>
                    <span class="text-muted ms-2 small">{{ $conversation->wa_id }}</span>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    @if($conversation->ai_active)
                        <span class="badge bg-primary-subtle text-primary-emphasis">🤖 AI مفعّل</span>
                    @else
                        <span class="badge bg-warning-subtle text-warning-emphasis">👤 Human Mode</span>
                    @endif
                    <form method="POST" action="{{ route('admin.whatsapp.conversations.toggle-ai', $conversation) }}">
                        @csrf
                        <button class="btn btn-sm {{ $conversation->ai_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                            {{ $conversation->ai_active ? '⏸️ إيقاف AI' : '▶️ تفعيل AI' }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Messages --}}
            <div class="p-3" style="min-height:400px; max-height:600px; overflow-y:auto; background:#f5f5f5;" id="messages-container">
                @foreach($messages as $msg)
                    @if($msg->direction === 'inbound')
                        <div class="d-flex mb-3">
                            <div style="background:#fff; border-radius:12px 12px 12px 0; padding:10px 14px; max-width:75%; box-shadow:0 1px 2px rgba(0,0,0,.1);">
                                <div>{{ $msg->body }}</div>
                                <div class="text-muted" style="font-size:.7rem; margin-top:4px;">{{ $msg->created_at->format('H:i') }}</div>
                            </div>
                        </div>
                    @else
                        <div class="d-flex justify-content-end mb-3">
                            <div style="background:#dcf8c6; border-radius:12px 12px 0 12px; padding:10px 14px; max-width:75%; box-shadow:0 1px 2px rgba(0,0,0,.1);">
                                <div>{{ $msg->body }}</div>
                                <div class="text-muted d-flex gap-2" style="font-size:.7rem; margin-top:4px;">
                                    {{ $msg->created_at->format('H:i') }}
                                    @if($msg->ai_provider) <span class="text-primary">🤖 {{ $msg->ai_provider }}</span> @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
                @if($messages->isEmpty())
                    <div class="text-center text-muted py-4">لا توجد رسائل بعد</div>
                @endif
            </div>

            {{-- Send Message Form --}}
            <div class="card-footer p-3">
                <form method="POST" action="{{ route('admin.whatsapp.conversations.send', $conversation) }}" class="d-flex gap-2">
                    @csrf
                    <input type="text" name="message" class="form-control text-end" dir="rtl" placeholder="اكتب رسالة..." required>
                    <button class="btn btn-success">إرسال 📤</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Info Panel --}}
    <div class="col-lg-4">
        {{-- Lead Info (AI Extracted) --}}
        @if($conversation->metadata && isset($conversation->metadata['lead_info']))
            <div class="card admin-card p-3 mb-3 border-start border-4 border-info">
                <h6 class="fw-semibold mb-3">🎯 استخراج الـ AI (تلقائي)</h6>
                @php $info = is_array($conversation->metadata['lead_info']) ? $conversation->metadata['lead_info'] : json_decode($conversation->metadata['lead_info'], true); @endphp
                @if($info)
                <dl class="row mb-0 small">
                    <dt class="col-5">الاهتمام</dt><dd class="col-7 text-primary fw-bold">{{ $info['interest'] ?? '—' }}</dd>
                    <dt class="col-5">الوظيفة</dt><dd class="col-7">{{ $info['job'] ?? '—' }}</dd>
                    <dt class="col-5">الميزانية</dt><dd class="col-7">{{ $info['budget'] ?? '—' }}</dd>
                    <dt class="col-5">النية</dt><dd class="col-7"><span class="badge bg-secondary">{{ $info['intent'] ?? '—' }}</span></dd>
                    <dt class="col-5">التقييم</dt>
                    <dd class="col-7">
                        <div class="progress" style="height: 6px; margin-top: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $info['qualification_score'] ?? 0 }}%"></div>
                        </div>
                        <span class="fw-bold">{{ $info['qualification_score'] ?? 0 }}%</span>
                    </dd>
                </dl>
                @endif
            </div>
        @endif

        <div class="card admin-card p-3 mb-3">
            <h6 class="fw-semibold mb-3">معلومات المحادثة</h6>
            <dl class="row mb-0 small">
                <dt class="col-5">الرقم</dt><dd class="col-7">{{ $conversation->wa_id }}</dd>
                <dt class="col-5">اللغة</dt><dd class="col-7">{{ $conversation->locale === 'ar' ? 'عربي' : 'English' }}</dd>
                <dt class="col-5">الحالة</dt><dd class="col-7">{{ $conversation->status }}</dd>
                <dt class="col-5">آخر نشاط</dt><dd class="col-7">{{ $conversation->last_message_at?->diffForHumans() }}</dd>
            </dl>
        </div>

        {{-- Assign --}}
        <div class="card admin-card p-3 mb-3">
            <h6 class="fw-semibold mb-2">تعيين لموظف</h6>
            <form method="POST" action="{{ route('admin.whatsapp.conversations.assign', $conversation) }}" class="d-flex gap-2">
                @csrf
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">— بدون تعيين —</option>
                    @foreach(\App\Models\User::orderBy('name')->get() as $user)
                        <option value="{{ $user->id }}" @selected($conversation->assigned_user_id === $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-outline-primary">حفظ</button>
            </form>
        </div>

        <a href="{{ route('admin.whatsapp.conversations.index') }}" class="btn btn-outline-secondary w-100">← رجوع للقائمة</a>
    </div>
</div>

<script>
    // Auto-scroll messages to bottom
    document.addEventListener('DOMContentLoaded', function() {
        var container = document.getElementById('messages-container');
        if (container) container.scrollTop = container.scrollHeight;
    });
</script>
@endsection
