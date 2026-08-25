@extends('layouts.admin')

@section('title', 'مكتبة قوالب الرسائل — Message Templates')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">Message Templates Library</h3>
            <p class="text-muted small mb-0">إدارة القوالب والرسائل المعرفة مسبقاً لاستخدامها في الحملات والمتتابعات</p>
        </div>
        <button class="btn btn-primary font-weight-bold" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
            ➕ إنشاء قالب جديد
        </button>
    </div>

    @include('admin.whatsapp.nav')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3">
        @forelse($templates as $tpl)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                        <span>{{ $tpl->name }}</span>
                        <span class="badge bg-secondary">{{ strtoupper($tpl->category) }}</span>
                    </div>
                    <div class="card-body">
                        <div class="p-2 bg-light rounded small font-monospace mb-3" style="white-space: pre-wrap; min-height: 100px;">{{ $tpl->content }}</div>
                        <div class="text-muted text-xxs">تم الإنشاء بواسطة: {{ $tpl->creator?->name ?? 'Admin' }}</div>
                    </div>
                    <div class="card-footer bg-white text-end">
                        <form action="{{ route('admin.whatsapp.templates.destroy', $tpl->id) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف هذا القالب؟')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">حذف القالب</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm p-5 text-center text-muted">
                    لا توجد قوالب رسائل معرفة حالياً. اضغط "إنشاء قالب جديد" لإضافة قوالب.
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Create Template -->
<div class="modal fade" id="createTemplateModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.whatsapp.templates.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">إنشاء قالب جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">اسم القالب</label>
                    <input type="text" name="name" class="form-control" required placeholder="مثال: متابعة عميل الفيزا">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">تصنيف القالب (Category)</label>
                    <select name="category" class="form-select" required>
                        <option value="Follow-up">Follow-up</option>
                        <option value="Visa">Visa</option>
                        <option value="Offers">Offers</option>
                        <option value="Customer Service">Customer Service</option>
                        <option value="Reminder">Reminder</option>
                        <option value="Existing Customer">Existing Customer</option>
                        <option value="Lead Follow-up">Lead Follow-up</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">محتوى القالب</label>
                    <textarea name="content" class="form-control" rows="5" required placeholder="أهلاً بك @{{name}}، نود تذكيرك بـ..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary">حفظ القالب</button>
            </div>
        </form>
    </div>
</div>
@endsection
