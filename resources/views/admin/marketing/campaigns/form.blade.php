@extends('layouts.admin')

@section('page_title', $isEdit ? __('admin.edit_marketing_campaign') : __('admin.add_marketing_campaign'))
@section('page_description', __('admin.marketing_campaign_form_desc'))

@section('content')
<div class="card admin-card p-4">
    <form method="post" action="{{ $isEdit ? route('admin.marketing-campaigns.update', $campaign) : route('admin.marketing-campaigns.store') }}" class="row g-3">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="col-md-6">
            <label class="form-label">{{ __('admin.campaign_name') }}</label>
            <input class="form-control @error('display_name') is-invalid @enderror" name="display_name" value="{{ old('display_name', $campaign->display_name) }}" required>
            @error('display_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.campaign_code') }}</label>
            <input class="form-control @error('campaign_code') is-invalid @enderror" name="campaign_code" value="{{ old('campaign_code', $campaign->campaign_code) }}">
            @error('campaign_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin.status') }}</label>
            <select name="status" class="form-select">
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $campaign->status) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4"><label class="form-label">{{ __('admin.platform') }}</label><input class="form-control" name="platform" value="{{ old('platform', $campaign->platform) }}"></div>
        <div class="col-md-4"><label class="form-label">{{ __('admin.medium') }}</label><input class="form-control" name="utm_medium" value="{{ old('utm_medium', $campaign->utm_medium) }}"></div>
        <div class="col-md-4"><label class="form-label">{{ __('admin.campaign_type') }}</label><input class="form-control" name="campaign_type" value="{{ old('campaign_type', $campaign->campaign_type) }}"></div>
        <div class="col-md-4"><label class="form-label">{{ __('admin.marketing_objective') }}</label><input class="form-control" name="objective" value="{{ old('objective', $campaign->objective) }}"></div>
        <div class="col-md-4"><label class="form-label">{{ __('admin.owner') }}</label><select name="owner_user_id" class="form-select"><option value="">{{ __('admin.all') }}</option>@foreach($owners as $owner)<option value="{{ $owner->id }}" @selected((int) old('owner_user_id', $campaign->owner_user_id) === (int) $owner->id)>{{ $owner->name }}</option>@endforeach</select></div>
        <div class="col-md-4"><label class="form-label">{{ __('admin.external_campaign_id') }}</label><input class="form-control" name="external_campaign_id" value="{{ old('external_campaign_id', $campaign->external_campaign_id) }}"></div>

        <div class="col-md-4"><label class="form-label">{{ __('admin.start_date') }}</label><input type="date" class="form-control" name="start_date" value="{{ old('start_date', optional($campaign->start_date)->format('Y-m-d')) }}"></div>
        <div class="col-md-4"><label class="form-label">{{ __('admin.end_date') }}</label><input type="date" class="form-control" name="end_date" value="{{ old('end_date', optional($campaign->end_date)->format('Y-m-d')) }}"></div>
        <div class="col-md-4"><label class="form-label">{{ __('admin.budget') }}</label><input type="number" step="0.01" min="0" class="form-control" name="budget" value="{{ old('budget', $campaign->budget) }}"></div>

        <div class="col-md-6"><label class="form-label">{{ __('admin.base_url') }}</label><input type="url" class="form-control" name="base_url" value="{{ old('base_url', $campaign->base_url) }}"></div>
        <div class="col-md-6"><label class="form-label">UTM Source</label><input class="form-control" name="utm_source" value="{{ old('utm_source', $campaign->utm_source) }}"></div>
        <div class="col-md-4"><label class="form-label">UTM Campaign</label><input class="form-control" name="utm_campaign" value="{{ old('utm_campaign', $campaign->utm_campaign) }}"></div>
        <div class="col-md-4"><label class="form-label">UTM ID</label><input class="form-control" name="utm_id" value="{{ old('utm_id', $campaign->utm_id) }}"></div>
        <div class="col-md-4"><label class="form-label">UTM Term</label><input class="form-control" name="utm_term" value="{{ old('utm_term', $campaign->utm_term) }}"></div>
        <div class="col-md-6"><label class="form-label">UTM Content</label><input class="form-control" name="utm_content" value="{{ old('utm_content', $campaign->utm_content) }}"></div>

        @if($generatedUrl)
            <div class="col-12">
                <div class="alert alert-info mb-0">
                    <div class="fw-semibold">{{ __('admin.final_url') }}</div>
                    <div class="small text-break">{{ $generatedUrl }}</div>
                </div>
            </div>
        @endif

        <div class="col-12">
            <label class="form-label">{{ __('admin.notes') }}</label>
            <textarea class="form-control" name="notes" rows="4">{{ old('notes', $campaign->notes) }}</textarea>
        </div>

        <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary">{{ $isEdit ? __('admin.save_changes') : __('admin.save') }}</button>
            <a href="{{ route('admin.marketing-campaigns.index') }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
