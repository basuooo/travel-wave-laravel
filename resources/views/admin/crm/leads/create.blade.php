@extends('layouts.admin')

@section('page_title', __('admin.crm_add_lead'))
@section('page_description', __('admin.crm_add_lead_desc'))

@section('content')
<div class="card admin-card p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="h5 mb-1">{{ __('admin.crm_add_lead') }}</h2>
            <p class="text-muted mb-0">{{ __('admin.crm_add_lead_desc') }}</p>
        </div>
        <a href="{{ route('admin.crm.leads.index') }}" class="btn btn-outline-secondary">{{ __('admin.back') }}</a>
    </div>

    <form method="post" action="{{ route('admin.crm.leads.store') }}" class="row g-3">
        @csrf

        <div class="col-md-6">
            <label class="form-label">{{ __('admin.full_name') }}</label>
            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name') }}" required>
            @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('admin.phone') }}</label>
            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
            <div class="form-text">{{ __('admin.crm_manual_phone_required_help') }}</div>
            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('admin.whatsapp_number') }}</label>
            <input type="text" name="whatsapp_number" class="form-control @error('whatsapp_number') is-invalid @enderror" value="{{ old('whatsapp_number') }}">
            @error('whatsapp_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('admin.email') }}</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        @if($statuses->isNotEmpty())
            <div class="col-md-6">
                <label class="form-label">{{ __('admin.status') }}</label>
                <select name="crm_status_id" class="form-select @error('crm_status_id') is-invalid @enderror">
                    <option value="">{{ __('admin.crm_default_new_status') }}</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}" @selected((string) old('crm_status_id', $defaultStatus?->id) === (string) $status->id)>{{ $status->localizedName() }}</option>
                    @endforeach
                </select>
                @error('crm_status_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        @endif

        <div class="col-md-6">
            <label class="form-label">{{ __('admin.country') }}</label>
            <input type="text" name="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country') }}">
            @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('admin.destination') }}</label>
            <input type="text" name="destination" class="form-control @error('destination') is-invalid @enderror" value="{{ old('destination') }}">
            @error('destination')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <label class="form-label">{{ __('admin.notes') }}</label>
            <textarea name="admin_notes" rows="5" class="form-control @error('admin_notes') is-invalid @enderror">{{ old('admin_notes') }}</textarea>
            @error('admin_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <div class="alert alert-light border mb-0">
                <strong>{{ __('admin.source') }}:</strong> {{ __('admin.crm_manual_source_label') }}
            </div>
        </div>

        <div class="col-12 d-flex flex-wrap gap-2 justify-content-end">
            <a href="{{ route('admin.crm.leads.index') }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('admin.crm_save_lead') }}</button>
        </div>
    </form>
</div>
@endsection
