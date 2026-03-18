@php($source = $source ?? request()->path())
@php($type = $type ?? 'general')
<div class="tw-form-card p-4">
    <form method="post" action="{{ route('inquiries.store') }}" class="row g-3">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="hidden" name="source_page" value="{{ $source }}">
        <input type="hidden" name="preferred_language" value="{{ app()->getLocale() }}">
        <div class="col-md-6">
            <label class="form-label">{{ __('ui.full_name') }}</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('ui.phone') }}</label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('ui.email') }}</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('ui.destination') }}</label>
            <input type="text" name="destination" value="{{ $destination ?? '' }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('ui.travel_date') }}</label>
            <input type="date" name="travel_date" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('ui.return_date') }}</label>
            <input type="date" name="return_date" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('ui.travelers_count') }}</label>
            <input type="number" name="travelers_count" min="1" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">{{ __('ui.message') }}</label>
            <textarea name="message" rows="4" class="form-control"></textarea>
        </div>
        <div class="col-12">
            <button class="btn btn-primary tw-btn-primary px-4 tw-form-submit">{{ __('ui.inquire_now') }}</button>
        </div>
    </form>
</div>
