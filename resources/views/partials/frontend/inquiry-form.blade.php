@php($source = $source ?? request()->path())
@php($type = $type ?? 'general')
@php($config = $config ?? [])
@php($visibleFields = array_values(array_unique(array_merge(['full_name', 'phone'], $config['visible_fields'] ?? ['email', 'travel_date', 'message']))))
<div class="tw-form-card p-4 {{ $className ?? '' }}">
    @if(!empty($config['title']) || !empty($config['subtitle']))
        <div class="mb-4">
            @if(!empty($config['title']))
                <h3 class="h4 mb-2">{{ $config['title'] }}</h3>
            @endif
            @if(!empty($config['subtitle']))
                <p class="text-muted mb-0">{{ $config['subtitle'] }}</p>
            @endif
        </div>
    @endif

    <form method="post" action="{{ route('inquiries.store') }}" class="row g-3">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="hidden" name="source_page" value="{{ $source }}">
        <input type="hidden" name="preferred_language" value="{{ app()->getLocale() }}">
        @if(!empty($config['success_message']))
            <input type="hidden" name="success_message" value="{{ $config['success_message'] }}">
        @endif
        @if(!empty($config['default_service_type']))
            <input type="hidden" name="service_type" value="{{ $config['default_service_type'] }}">
        @endif
        @if(!empty($destination))
            <input type="hidden" name="destination" value="{{ $destination }}">
        @endif

        <div class="col-md-6">
            <label class="form-label">{{ __('ui.full_name') }}</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('ui.phone') }}</label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        @if(in_array('email', $visibleFields, true))
            <div class="col-md-6">
                <label class="form-label">{{ __('ui.email') }}</label>
                <input type="email" name="email" class="form-control">
            </div>
        @endif
        @if(in_array('travel_date', $visibleFields, true))
            <div class="col-md-6">
                <label class="form-label">{{ __('ui.travel_date') }}</label>
                <input type="date" name="travel_date" class="form-control">
            </div>
        @endif
        @if(in_array('message', $visibleFields, true))
            <div class="col-12">
                <label class="form-label">{{ __('ui.message') }}</label>
                <textarea name="message" rows="4" class="form-control"></textarea>
            </div>
        @endif
        <div class="col-12">
            <button class="btn btn-primary tw-btn-primary px-4 tw-form-submit">{{ $config['submit_text'] ?? __('ui.inquire_now') }}</button>
        </div>
    </form>
</div>
