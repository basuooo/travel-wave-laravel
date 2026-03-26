@extends('layouts.admin')

@section('page_title', $customer->full_name)
@section('page_description', __('admin.customer_edit_desc'))

@section('content')
<div class="card admin-card p-4">
    <form method="post" action="{{ $formAction }}">
        @csrf
        @method($formMethod)
        <h2 class="h5 mb-3">{{ __('admin.customer_profile') }}</h2>
        @include('admin.crm.customers._form')
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('admin.crm.customers.show', $customer) }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
            <button class="btn btn-primary">{{ __('admin.update') }}</button>
        </div>
    </form>
</div>
@endsection
