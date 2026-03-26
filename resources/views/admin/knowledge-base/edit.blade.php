@extends('layouts.admin')

@section('page_title', __('admin.kb_edit_article'))
@section('page_description', $article->title)

@section('content')
<div class="card admin-card p-4">
    <form method="POST" action="{{ $formAction }}">
        @csrf
        @method($formMethod)
        @include('admin.knowledge-base._form')
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('admin.knowledge-base.show', $article) }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
            <button class="btn btn-primary">{{ __('admin.update') }}</button>
        </div>
    </form>
</div>
@endsection
