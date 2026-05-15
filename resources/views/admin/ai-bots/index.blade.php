@extends('layouts.admin')

@section('page_title', 'إدارة البوتات الذكية (Multi-Bot)')

@section('content')
<div class="card admin-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h5 mb-1">قائمة البوتات</h2>
            <p class="text-muted mb-0 small">يمكنك إنشاء أكثر من بوت بتعليمات مختلفة ومزودين مختلفين</p>
        </div>
        <a href="{{ route('admin.ai-bots.create') }}" class="btn btn-primary">➕ إضافة بوت جديد</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>اسم البوت</th>
                    <th>المزود (Provider)</th>
                    <th>الحالة</th>
                    <th>آخر تحديث</th>
                    <th class="text-end">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bots as $bot)
                <tr>
                    <td>
                        <div class="fw-bold">{{ $bot->name }}</div>
                        <div class="text-muted small">Key: <code>{{ $bot->key }}</code></div>
                    </td>
                    <td>
                        @php
                            $colors = ['openai' => 'success', 'gemini' => 'primary', 'deepseek' => 'info', 'claude' => 'warning'];
                            $color = $colors[$bot->provider] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $color }}">{{ strtoupper($bot->provider) }}</span>
                    </td>
                    <td>
                        <form method="post" action="{{ route('admin.ai-bots.toggle', $bot) }}">
                            @csrf
                            @method('PATCH')
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" onchange="this.form.submit()" @checked($bot->enabled)>
                                <label class="form-check-label">{{ $bot->enabled ? 'مفعل' : 'معطل' }}</label>
                            </div>
                        </form>
                    </td>
                    <td>{{ $bot->updated_at->diffForHumans() }}</td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.ai-bots.edit', $bot) }}" class="btn btn-sm btn-outline-primary">تعديل</a>
                            @if($bot->key !== 'default')
                            <form method="post" action="{{ route('admin.ai-bots.destroy', $bot) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا البوت؟')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">حذف</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
