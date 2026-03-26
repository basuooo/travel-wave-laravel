@extends('layouts.admin')

@section('page_title', __('admin.accounting_edit_treasury'))
@section('page_description', $item->name)

@section('content')
<div class="card admin-card p-4">
    <form method="POST" action="{{ route('admin.accounting.treasuries.update', $item) }}">
        @include('admin.accounting.treasuries._form', ['method' => 'PUT'])
    </form>
</div>
@endsection
