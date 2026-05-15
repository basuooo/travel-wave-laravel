@extends('layouts.admin')

@section('page_title', 'تعديل البوت: ' . $bot->name)

@section('content')
<div class="card admin-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h5 mb-0">تعديل بيانات البوت</h2>
        <span class="badge bg-light text-dark">Key: {{ $bot->key }}</span>
    </div>
    
    <form method="post" action="{{ route('admin.ai-bots.update', $bot) }}">
        @csrf
        @method('PUT')
        @include('admin.ai-bots._form')

        <div class="mt-4 pt-3 border-top d-flex gap-2">
            <button class="btn btn-primary px-4">حفظ التغييرات</button>
            <a href="{{ route('admin.ai-bots.index') }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection
