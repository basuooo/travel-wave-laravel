@extends('layouts.admin')

@php
    $isAr = app()->getLocale() === 'ar';
@endphp

@section('title', $isAr ? 'قائمة الفانلات التفاعلية' : 'Interactive Funnels List')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 fw-bold mb-1">⚡ {{ $isAr ? 'الفانلات التفاعلية (Interactive Funnels)' : 'Interactive Funnels' }}</h1>
            <p class="text-muted mb-0">{{ $isAr ? 'إدارة كافة مسارات الفانل، وحالات النشر، والروابط المخصصة' : 'Manage all interactive funnels, publication status, and custom slugs' }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.funnels.templates.index') }}" class="btn btn-outline-primary fw-bold">
                🎨 {{ $isAr ? 'مكتبة القوالب' : 'Templates' }}
            </a>
            <a href="{{ route('admin.funnels.create') }}" class="btn btn-primary fw-bold">
                ➕ {{ $isAr ? 'إنشاء فانل جديد' : 'Create Funnel' }}
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.funnels.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="{{ $isAr ? 'بحث بالاسم أو الرابط...' : 'Search funnel name or slug...' }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">-- {{ $isAr ? 'جميع الحالات' : 'All Statuses' }} --</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>{{ $isAr ? 'منشور (Live)' : 'Published' }}</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>{{ $isAr ? 'مسودة (Draft)' : 'Draft' }}</option>
                        <option value="unpublished" {{ request('status') === 'unpublished' ? 'selected' : '' }}>{{ $isAr ? 'غير منشور' : 'Unpublished' }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-secondary w-100 fw-bold">{{ $isAr ? 'تصفية 🔍' : 'Filter 🔍' }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Funnels List -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ $isAr ? 'اسم الفانل' : 'Funnel Name' }}</th>
                        <th>{{ $isAr ? 'الرابط المباشر' : 'Slug / Public URL' }}</th>
                        <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                        <th>{{ $isAr ? 'القالب الأساسي' : 'Template' }}</th>
                        <th>{{ $isAr ? 'تاريخ الإنشاء' : 'Created' }}</th>
                        <th class="text-end">{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($funnels as $funnel)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $funnel->name }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark font-monospace border">
                                    /f/{{ $funnel->slug }}
                                </span>
                                <a href="{{ $funnel->publicUrl() }}" target="_blank" class="ms-1 text-decoration-none" title="{{ $isAr ? 'فتح الرابط العام' : 'Open Public URL' }}">🔗</a>
                            </td>
                            <td>
                                @if($funnel->status === 'published')
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold">{{ $isAr ? 'منشور (Live)' : 'Published' }}</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill fw-bold">{{ $isAr ? 'مسودة (Draft)' : 'Draft' }}</span>
                                @endif
                            </td>
                            <td>{{ $funnel->template?->name ?: ($isAr ? 'مخصص (من الصفر)' : 'Scratch') }}</td>
                            <td class="text-muted small">{{ $funnel->created_at->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('admin.funnels.builder', $funnel) }}" class="btn btn-sm btn-outline-primary" title="{{ $isAr ? 'فتح الـ Builder' : 'Open Builder' }}">
                                        ⚡ {{ $isAr ? 'تعديل البيلدر' : 'Builder' }}
                                    </a>
                                    <a href="{{ route('admin.funnels.analytics', $funnel) }}" class="btn btn-sm btn-outline-info" title="{{ $isAr ? 'الإحصائيات' : 'Analytics' }}">
                                        📊 {{ $isAr ? 'التحليلات' : 'Analytics' }}
                                    </a>
                                    @if($funnel->status === 'published')
                                        <form action="{{ route('admin.funnels.unpublish', $funnel) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-warning">{{ $isAr ? 'إلغاء النشر' : 'Unpublish' }}</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.funnels.publish', $funnel) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">{{ $isAr ? 'نشر' : 'Publish' }}</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.funnels.duplicate', $funnel) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">{{ $isAr ? 'نسخ' : 'Copy' }}</button>
                                    </form>
                                    <form action="{{ route('admin.funnels.destroy', $funnel) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $isAr ? 'هل أنت متأكد من حذف هذا الفانل؟' : 'Are you sure you want to delete this funnel?' }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">{{ $isAr ? 'لا توجد فانلات حالياً.' : 'No funnels found.' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($funnels->hasPages())
            <div class="card-footer bg-transparent py-3">
                {{ $funnels->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
