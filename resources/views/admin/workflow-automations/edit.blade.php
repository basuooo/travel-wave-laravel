@extends('layouts.admin')

@section('title', __('admin.workflow_ui_edit_title'))
@section('page_title', __('admin.workflow_ui_edit_title'))
@section('page_description', $automation->name)

@section('content')
<div class="container-fluid py-4">
    <form method="post" action="{{ $formAction }}">
        @csrf
        @method('PUT')
        @include('admin.workflow-automations._form')
    </form>
</div>
@endsection
