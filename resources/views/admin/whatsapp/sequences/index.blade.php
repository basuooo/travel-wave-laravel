@extends('layouts.admin')

@section('title', 'متتابعات المتابعة التلقائية — Sequences')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">Follow-up Sequences</h3>
            <p class="text-muted small mb-0">إنشاء متتابعات إرسال أوتوماتيكية متعددة الأيام مع الإيقاف الذكي عند الرد (Smart Stop)</p>
        </div>
        <button class="btn btn-primary font-weight-bold" data-bs-toggle="modal" data-bs-target="#createSequenceModal">
            ➕ إنشاء Sequence جديد
        </button>
    </div>

    @include('admin.whatsapp.nav')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($sequences as $seq)
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                        <span>🔄 {{ $seq->name }}</span>
                        <span class="badge bg-success">Active</span>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">{{ $seq->description ?: 'لا يوجد وصف' }}</p>
                        
                        <div class="mb-2">
                            <span class="badge bg-light text-dark border me-1">Stop on Reply: {{ $seq->smart_stop_on_reply ? 'Yes' : 'No' }}</span>
                            <span class="badge bg-light text-dark border me-1">Stop on Convert: {{ $seq->smart_stop_on_convert ? 'Yes' : 'No' }}</span>
                        </div>

                        <h6 class="fw-bold mt-3 mb-2">خطوات المتتابعة (Steps):</h6>
                        <div class="list-group list-group-flush border-top border-bottom mb-3">
                            @foreach($seq->steps as $step)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-primary me-2">Day {{ $step->delay_days }}</span>
                                        <span class="small font-monospace text-truncate d-inline-block" style="max-width: 280px;">{{ $step->message_content }}</span>
                                    </div>
                                    <span class="small text-muted">Step {{ $step->step_number }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer bg-white text-end">
                        <form action="{{ route('admin.whatsapp.sequences.destroy', $seq->id) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف هذه المتتابعة؟')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">حذف المتتابعة</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm p-5 text-center text-muted">
                    لا توجد متتابعات معرفة حالياً. اضغط "إنشاء Sequence جديد" لإضافة متتابعات متابعة.
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Create Sequence -->
<div class="modal fade" id="createSequenceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('admin.whatsapp.sequences.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">إنشاء Sequence متابعة جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">اسم المتتابعة</label>
                    <input type="text" name="name" class="form-control" required placeholder="مثال: متابعة عملاء التأشيرات الجدد">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">وصف المتتابعة</label>
                    <input type="text" name="description" class="form-control" placeholder="وصف مقتضب لهدف المتتابعة">
                </div>

                <div class="mb-3 d-flex gap-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="smart_stop_on_reply" value="1" checked id="chkStopReply">
                        <label class="form-check-label fw-bold small" for="chkStopReply">
                            إيقاف المتابعة تلقائياً إذا رد العميل (Smart Stop on Reply)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="smart_stop_on_convert" value="1" checked id="chkStopConvert">
                        <label class="form-check-label fw-bold small" for="chkStopConvert">
                            إيقاف المتابعة تلقائياً عند تحول العميل إلى Customer
                        </label>
                    </div>
                </div>

                <h6 class="fw-bold border-bottom pb-2 mb-3">خطوات الرسائل والجدولة الزمانية:</h6>
                <div id="stepsContainer">
                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">تأخير الأيام (Day Delay)</label>
                            <input type="number" name="steps[0][delay_days]" class="form-control" value="0" required placeholder="0 = فوراً">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label small fw-bold">نص الرسالة (Message 1)</label>
                            <input type="text" name="steps[0][content]" class="form-control" required placeholder="الرسالة الأولى...">
                        </div>
                    </div>
                    <div class="row g-2 mb-3 align-items-center">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">تأخير الأيام (Day Delay)</label>
                            <input type="number" name="steps[1][delay_days]" class="form-control" value="2" required placeholder="2 = بعد يومين">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label small fw-bold">نص الرسالة (Message 2)</label>
                            <input type="text" name="steps[1][content]" class="form-control" required placeholder="الرسالة الثانية بعد يومين...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary">حفظ وإطلاق المتتابعة</button>
            </div>
        </form>
    </div>
</div>
@endsection
