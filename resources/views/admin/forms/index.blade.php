@extends('layouts.admin')

@section('page_title', 'قسم النماذج والعملاء - إدارة النماذج')
@section('page_description', 'إنشاء وتعديل وإدارة النماذج الديناميكية وتعليمات الحقول وتخصيص الصفحات.')

@section('content')
<!-- Nav Tabs -->
<ul class="nav nav-tabs border-bottom mb-4">
    <li class="nav-item">
        <a class="nav-link active fs-6 fw-bold px-4 py-2 text-primary border-primary border-bottom-0" href="{{ route('admin.forms.index') }}">
            📋 إدارة النماذج (Forms)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link fs-6 fw-bold px-4 py-2 text-secondary" href="{{ route('admin.forms.submissions') }}">
            📥 فورـم لـيـد (Form Leads)
        </a>
    </li>
</ul>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 mb-1 fw-bold">النماذج المتاحة في النظام</h2>
        <p class="text-muted mb-0">إدارة النماذج التفاعلية وحقول الاستمارة وربطها بالصفحات المختلفة.</p>
    </div>
    <a href="{{ route('admin.forms.create') }}" class="btn btn-primary fw-bold">+ إنشاء نموذج جديد</a>
</div>

<div class="card admin-card p-4 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead class="table-light">
                <tr>
                    <th>اسم النموذج</th>
                    <th>النوع</th>
                    <th>الحالة</th>
                    <th>الصفحات المرتبطة</th>
                    <th>عدد التسجيلات</th>
                    <th>تاريخ الإنشاء</th>
                    <th class="text-end">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->name }}</div>
                            <div class="text-muted small">{{ $item->slug }}</div>
                        </td>
                        <td><span class="badge text-bg-light border">{{ ucfirst($item->form_category ?: 'عام') }}</span></td>
                        <td>
                            <span class="badge {{ $item->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ $item->is_active ? 'مفعل' : 'غير مفعل' }}
                            </span>
                        </td>
                        <td><span class="badge text-bg-info">{{ $item->assignments_count }}</span></td>
                        <td><span class="badge text-bg-primary fs-6">{{ $item->inquiries_count }}</span></td>
                        <td>{{ $item->created_at->format('Y-m-d') }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.forms.edit', $item) }}" class="btn btn-sm btn-outline-primary fw-bold">تعديل</a>
                                <form method="post" action="{{ route('admin.forms.duplicate', $item) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-secondary">نسخ</button>
                                </form>
                                <form method="post" action="{{ route('admin.forms.destroy', $item) }}" onsubmit="return confirm('هل تريد حذف هذا النموذج؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">لا توجد نماذج مضافة حالياً.</td>
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
