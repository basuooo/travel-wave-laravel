@extends('layouts.admin')

@section('page_title', __('admin.accounting_add_treasury'))
@section('page_description', __('admin.accounting_treasuries_desc'))

@section('content')
<div class="card admin-card p-4">
    <form method="POST" action="{{ route('admin.accounting.treasuries.store') }}">
        @include('admin.accounting.treasuries._form', ['method' => 'POST'])
    </form>
</div>
@endsection
