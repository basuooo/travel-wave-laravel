@extends('layouts.admin')

@section('page_title', __('admin.crm_task_create'))
@section('page_description', __('admin.crm_tasks_desc'))

@section('content')
<div class="card admin-card p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="h5 mb-1">{{ __('admin.crm_task_create') }}</h2>
            <p class="text-muted mb-0">{{ __('admin.crm_tasks_desc') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ $formAction }}">
        @csrf
        @include('admin.crm.tasks._form', ['submitLabel' => __('admin.add_task')])
    </form>
</div>
@endsection
