@extends('layouts.admin')

@section('page_title', 'إضافة بوت جديد')

@section('content')
<div class="card admin-card p-4">
    <h2 class="h5 mb-4">بيانات البوت الجديد</h2>
    
    <form method="post" action="{{ route('admin.ai-bots.store') }}">
        @csrf
        @include('admin.ai-bots._form')

        <div class="mt-4 pt-3 border-top d-flex gap-2">
            <button class="btn btn-primary px-4">إضافة البوت</button>
            <a href="{{ route('admin.ai-bots.index') }}" class="btn btn-outline-secondary">إلغاء</a>
        </div>
    </form>
</div>
@endsection
