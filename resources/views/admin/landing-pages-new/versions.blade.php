@extends('layouts.admin')

@section('page_title', 'سجل النسخ: ' . $landingPage->internal_name)

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div>
                <h4 class="fw-bold mb-1">📜 سجل النسخ والاستعادة: {{ $landingPage->internal_name }}</h4>
                <p class="text-muted mb-0">يمكنك استعادة أي نسخة سابقة تم حفظها تلقائياً أو يدوياً.</p>
            </div>
            <a href="{{ route('admin.landing-pages-new.builder', $landingPage) }}" class="btn btn-outline-primary rounded-pill px-3">
                ← العودة للمحرر المرئي
            </a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>رقم النسخة</th>
                        <th>تسمية النسخة</th>
                        <th>تاريخ الحفظ</th>
                        <th>بواسطة</th>
                        <th class="text-end">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($versions as $ver)
                        <tr>
                            <td><span class="badge bg-navy fs-6">v{{ $ver->version_number }}</span></td>
                            <td class="fw-bold">{{ $ver->label ?: 'حفظ تلقائي' }}</td>
                            <td>{{ $ver->created_at->format('Y-m-d H:i:s') }} ({{ $ver->created_at->diffForHumans() }})</td>
                            <td>{{ $ver->createdBy?->name ?: 'النظام' }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.landing-pages-new.versions.restore', [$landingPage, $ver]) }}" onsubmit="return confirm('هل أنت تأكد من استعادة هذه النسخة؟')" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning fw-bold">
                                        ↺ استعادة هذه النسخة
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">لا توجد نسخ سابقة محفوظة لهذه الصفحة بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
