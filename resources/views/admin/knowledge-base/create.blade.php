@extends('layouts.admin')

@section('page_title', __('admin.kb_add_article'))
@section('page_description', __('admin.kb_create_desc'))

@section('content')
<div class="card admin-card p-4">
    <form method="POST" action="{{ $formAction }}">
        @csrf
        @include('admin.knowledge-base._form')
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('admin.knowledge-base.index') }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
            <button class="btn btn-primary">{{ __('admin.create') }}</button>
        </div>
    </form>
</div>
@endsection
