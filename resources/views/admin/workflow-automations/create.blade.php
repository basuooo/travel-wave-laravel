@extends('layouts.admin')

@section('title', __('admin.workflow_ui_create_title'))
@section('page_title', __('admin.workflow_ui_create_title'))
@section('page_description', __('admin.workflow_ui_page_desc'))

@section('content')
<div class="container-fluid py-4">
    <form method="post" action="{{ $formAction }}">
        @csrf
        @include('admin.workflow-automations._form')
    </form>
</div>
@endsection
