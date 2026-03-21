@php($source = $source ?? request()->path())
@php($type = $type ?? 'general')
@php($config = $config ?? [])
@php($visibleFields = array_values(array_unique(array_merge(['full_name', 'phone'], $config['visible_fields'] ?? ['email', 'travel_date', 'message']))))
@php($labels = $config['labels'] ?? [])
@php($placeholders = $config['placeholders'] ?? [])
@php($fieldOptions = $config['field_options'] ?? [])
@php($metaEventName = $config['meta_event_name'] ?? ($type === 'contact' ? 'Contact' : 'Lead'))
@php($metaPageName = $config['title'] ?? ($destination ?? $source))
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

    <form method="post" action="{{ route('inquiries.store') }}" class="row g-3" data-meta-event-name="{{ $metaEventName }}" data-meta-form-name="{{ $config['title'] ?? ($type . '-form') }}" data-meta-page-name="{{ $metaPageName }}" data-meta-destination="{{ $destination ?? '' }}" data-meta-service-type="{{ $config['default_service_type'] ?? '' }}">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="hidden" name="source_page" value="{{ $source }}">
        <input type="hidden" name="preferred_language" value="{{ app()->getLocale() }}">
        <input type="hidden" name="meta_event_id" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
        <input type="hidden" name="meta_event_name" value="{{ $metaEventName }}">
        @if(!empty($config['success_message']))
            <input type="hidden" name="success_message" value="{{ $config['success_message'] }}">
        @endif
        @if(!empty($config['default_service_type']) && !in_array('service_type', $visibleFields, true))
            <input type="hidden" name="service_type" value="{{ $config['default_service_type'] }}">
        @endif
        @if(!empty($destination) && !in_array('destination', $visibleFields, true))
            <input type="hidden" name="destination" value="{{ $destination }}">
        @endif

        <div class="col-md-6">
            <label class="form-label">{{ $labels['full_name'] ?? __('ui.full_name') }}</label>
            <input type="text" name="full_name" class="form-control" placeholder="{{ $placeholders['full_name'] ?? '' }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ $labels['phone'] ?? __('ui.phone') }}</label>
            <input type="text" name="phone" class="form-control" placeholder="{{ $placeholders['phone'] ?? '' }}" required>
        </div>
        @if(in_array('email', $visibleFields, true))
            <div class="col-md-6">
                <label class="form-label">{{ $labels['email'] ?? __('ui.email') }}</label>
                <input type="email" name="email" class="form-control" placeholder="{{ $placeholders['email'] ?? '' }}">
            </div>
        @endif
        @if(in_array('service_type', $visibleFields, true))
            <div class="col-md-6">
                <label class="form-label">{{ $labels['service_type'] ?? __('ui.service_type') }}</label>
                @if(!empty($fieldOptions['service_type']))
                    <select name="service_type" class="form-select">
                        <option value="">{{ $placeholders['service_type'] ?? ($labels['service_type'] ?? __('ui.service_type')) }}</option>
                        @foreach($fieldOptions['service_type'] as $option)
                            <option value="{{ $option['value'] ?? $option['label'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                @else
                    <input type="text" name="service_type" class="form-control" placeholder="{{ $placeholders['service_type'] ?? '' }}">
                @endif
            </div>
        @endif
        @if(in_array('destination', $visibleFields, true))
            <div class="col-md-6">
                <label class="form-label">{{ $labels['destination'] ?? __('ui.destination') }}</label>
                @if(!empty($fieldOptions['destination']))
                    <select name="destination" class="form-select">
                        <option value="">{{ $placeholders['destination'] ?? ($labels['destination'] ?? __('ui.destination')) }}</option>
                        @foreach($fieldOptions['destination'] as $option)
                            <option value="{{ $option['value'] ?? $option['label'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                @else
                    <input type="text" name="destination" class="form-control" value="{{ $destination ?? '' }}" placeholder="{{ $placeholders['destination'] ?? '' }}">
                @endif
            </div>
        @endif
        @if(in_array('travel_date', $visibleFields, true))
            <div class="col-md-6">
                <label class="form-label">{{ $labels['travel_date'] ?? __('ui.travel_date') }}</label>
                <input type="date" name="travel_date" class="form-control" placeholder="{{ $placeholders['travel_date'] ?? '' }}">
            </div>
        @endif
        @if(in_array('return_date', $visibleFields, true))
            <div class="col-md-6">
                <label class="form-label">{{ $labels['return_date'] ?? __('ui.return_date') }}</label>
                <input type="date" name="return_date" class="form-control" placeholder="{{ $placeholders['return_date'] ?? '' }}">
            </div>
        @endif
        @if(in_array('travelers_count', $visibleFields, true))
            <div class="col-md-6">
                <label class="form-label">{{ $labels['travelers_count'] ?? __('ui.travelers_count') }}</label>
                <input type="number" min="1" name="travelers_count" class="form-control" placeholder="{{ $placeholders['travelers_count'] ?? '' }}">
            </div>
        @endif
        @if(in_array('message', $visibleFields, true))
            <div class="col-12">
                <label class="form-label">{{ $labels['message'] ?? __('ui.message') }}</label>
                <textarea name="message" rows="4" class="form-control" placeholder="{{ $placeholders['message'] ?? '' }}"></textarea>
            </div>
        @endif
        <div class="col-12">
            <button class="btn btn-primary tw-btn-primary px-4 tw-form-submit">{{ $config['submit_text'] ?? __('ui.inquire_now') }}</button>
        </div>
    </form>
</div>
