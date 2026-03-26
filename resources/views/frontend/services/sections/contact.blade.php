<section class="container py-5" id="service-form">
    <div class="row g-4 align-items-stretch">
        <div class="col-lg-5">
            <div class="tw-visa-hub-contact-copy h-100">
                @if(!empty($section['eyebrow']))
                    <span class="tw-visa-hub-section-pill">{{ $section['eyebrow'] }}</span>
                @endif
                <h2 class="tw-section-title h2 mt-3 mb-3">{{ $section['title'] ?? '' }}</h2>
                @if(!empty($section['subtitle']) || !empty($section['text']))
                    <p class="text-muted mb-4">{{ $section['subtitle'] ?? $section['text'] }}</p>
                @endif
                @if(!empty($section['checklist']))
                    <div class="tw-visa-hub-contact-list">
                        @foreach($section['checklist'] as $item)
                            <div>{{ $item }}</div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="col-lg-7">
            <div class="tw-form-card p-4 p-lg-5">
                <form method="POST" action="{{ route('inquiries.store') }}" class="row g-3">
                    @csrf
                    <input type="hidden" name="type" value="{{ $section['type'] ?? 'general' }}">
                    <input type="hidden" name="source_page" value="{{ $section['source'] ?? ($page->localized('title') ?? 'Travel Wave') }}">

                    @foreach(($section['fields'] ?? $servicePage['contact']['fields'] ?? []) as $field)
                        <div class="{{ ($field['type'] ?? 'text') === 'textarea' ? 'col-12' : 'col-md-6' }}">
                            <label class="form-label">{{ $field['label'] ?? '' }}</label>
                            @if(($field['type'] ?? 'text') === 'select')
                                <select name="{{ $field['name'] }}" class="form-select" @if(!empty($field['required'])) required @endif>
                                    <option value="">{{ $field['placeholder'] ?? __('ui.home_search_placeholder') }}</option>
                                    @foreach(($field['options'] ?? []) as $option)
                                        <option value="{{ $option['value'] ?? $option['label'] }}">{{ $option['label'] ?? '' }}</option>
                                    @endforeach
                                </select>
                            @elseif(($field['type'] ?? 'text') === 'textarea')
                                <textarea name="{{ $field['name'] }}" class="form-control" rows="5" placeholder="{{ $field['placeholder'] ?? '' }}" @if(!empty($field['required'])) required @endif></textarea>
                            @else
                                <input type="{{ $field['type'] ?? 'text' }}" name="{{ $field['name'] }}" class="form-control" placeholder="{{ $field['placeholder'] ?? '' }}" @if(!empty($field['required'])) required @endif>
                            @endif
                        </div>
                    @endforeach

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-lg tw-btn-primary">{{ $section['submit_text'] ?? __('ui.send_request') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
