@extends('layouts.admin')

@section('page_title', __('admin.chatbot_knowledge_edit'))
@section('page_description', __('admin.chatbot_knowledge_edit_desc'))

@section('content')
<form method="post" action="{{ $formAction }}">
    @csrf
    @method($formMethod)
    <div class="card admin-card p-4 mb-4">
        <h2 class="h5 mb-3">{{ __('admin.chatbot_knowledge_edit') }}</h2>
        @include('admin.chatbot-knowledge._form')
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary px-4">{{ __('admin.update') }}</button>
        <a href="{{ route('admin.chatbot-knowledge.index') }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
    </div>
</form>
@endsection
