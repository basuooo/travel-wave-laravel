@extends('layouts.admin')

@section('page_title', __('admin.crm_task_edit'))
@section('page_description', $task->title)

@section('content')
<div class="card admin-card p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="h5 mb-1">{{ __('admin.crm_task_edit') }}</h2>
            <p class="text-muted mb-0">{{ $task->title }}</p>
        </div>
    </div>

    <form method="POST" action="{{ $formAction }}">
        @csrf
        @method($formMethod)
        @include('admin.crm.tasks._form', ['submitLabel' => __('admin.update')])
    </form>
</div>
@endsection
