@extends('layouts.admin')

@section('page_title', __('admin.convert_to_customer'))
@section('page_description', __('admin.customer_conversion_desc'))

@section('content')
<div class="card admin-card p-4">
    <form method="post" action="{{ $formAction }}">
        @csrf
        <h2 class="h5 mb-3">{{ __('admin.customer_profile') }}</h2>
        @include('admin.crm.customers._form')
        <div class="d-flex justify-content-end gap-2 mt-4">
            @if($lead)
                <a href="{{ route('admin.crm.leads.show', $lead) }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
            @else
                <a href="{{ route('admin.crm.customers.index') }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
            @endif
            <button class="btn btn-primary">{{ __('admin.convert_to_customer') }}</button>
        </div>
    </form>
</div>
@endsection
