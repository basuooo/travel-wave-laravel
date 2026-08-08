@php($sections = $page->sections ?? [])
<form method="post" enctype="multipart/form-data" action="{{ $formAction }}">
    @csrf
    @if(($formMethod ?? 'POST') !== 'POST')
        @method($formMethod)
    @endif

    <div class="card admin-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
            <div>
                <h2 class="h5 mb-1">Core Page Content</h2>
                <p class="text-muted mb-0">Manage titles, slug, hero content, intro content, and activation state.</p>
            </div>
            @if(!empty($page->id) && $page->frontendUrl())
                <a href="{{ $page->frontendUrl() }}" class="btn btn-outline-secondary" target="_blank" rel="noopener noreferrer">View Page</a>
            @endif
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Key</label>
                <input class="form-control" name="key" value="{{ old('key', $page->key) }}" {{ !empty($page->id) && $page->isCorePage() ? 'readonly' : '' }}>
                <div class="form-text">Used internally. Core page keys stay locked.</div>
            </div>
            <div class="col-md-4"><label class="form-label">Title EN</label><input class="form-control" name="title_en" value="{{ old('title_en', $page->title_en) }}"></div>
            <div class="col-md-4"><label class="form-label">Title AR</label><input class="form-control text-end" dir="rtl" name="title_ar" value="{{ old('title_ar', $page->title_ar) }}"></div>
            <div class="col-md-4"><label class="form-label">Slug</label><input class="form-control" name="slug" value="{{ old('slug', $page->slug) }}"></div>
            <div class="col-md-4"><label class="form-label">Hero Badge EN</label><input class="form-control" name="hero_badge_en" value="{{ old('hero_badge_en', $page->hero_badge_en) }}"></div>
            <div class="col-md-4"><label class="form-label">Hero Badge AR</label><input class="form-control text-end" dir="rtl" name="hero_badge_ar" value="{{ old('hero_badge_ar', $page->hero_badge_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Hero Title EN</label><input class="form-control" name="hero_title_en" value="{{ old('hero_title_en', $page->hero_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Hero Title AR</label><input class="form-control text-end" dir="rtl" name="hero_title_ar" value="{{ old('hero_title_ar', $page->hero_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Hero Subtitle EN</label><textarea class="form-control" name="hero_subtitle_en" rows="3">{{ old('hero_subtitle_en', $page->hero_subtitle_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Hero Subtitle AR</label><textarea class="form-control text-end" dir="rtl" name="hero_subtitle_ar" rows="3">{{ old('hero_subtitle_ar', $page->hero_subtitle_ar) }}</textarea></div>
            <div class="col-md-4"><label class="form-label">Primary CTA EN</label><input class="form-control" name="hero_primary_cta_text_en" value="{{ old('hero_primary_cta_text_en', $page->hero_primary_cta_text_en) }}"></div>
            <div class="col-md-4"><label class="form-label">Primary CTA AR</label><input class="form-control text-end" dir="rtl" name="hero_primary_cta_text_ar" value="{{ old('hero_primary_cta_text_ar', $page->hero_primary_cta_text_ar) }}"></div>
            <div class="col-md-4"><label class="form-label">Primary CTA URL</label><input class="form-control" name="hero_primary_cta_url" value="{{ old('hero_primary_cta_url', $page->hero_primary_cta_url) }}"></div>
            <div class="col-md-4"><label class="form-label">Secondary CTA EN</label><input class="form-control" name="hero_secondary_cta_text_en" value="{{ old('hero_secondary_cta_text_en', $page->hero_secondary_cta_text_en) }}"></div>
            <div class="col-md-4"><label class="form-label">Secondary CTA AR</label><input class="form-control text-end" dir="rtl" name="hero_secondary_cta_text_ar" value="{{ old('hero_secondary_cta_text_ar', $page->hero_secondary_cta_text_ar) }}"></div>
            <div class="col-md-4"><label class="form-label">Secondary CTA URL</label><input class="form-control" name="hero_secondary_cta_url" value="{{ old('hero_secondary_cta_url', $page->hero_secondary_cta_url) }}"></div>
            <div class="col-md-6"><label class="form-label">Intro Title EN</label><input class="form-control" name="intro_title_en" value="{{ old('intro_title_en', $page->intro_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Intro Title AR</label><input class="form-control text-end" dir="rtl" name="intro_title_ar" value="{{ old('intro_title_ar', $page->intro_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Intro Body EN</label><textarea class="form-control" name="intro_body_en" rows="4">{{ old('intro_body_en', $page->intro_body_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Intro Body AR</label><textarea class="form-control text-end" dir="rtl" name="intro_body_ar" rows="4">{{ old('intro_body_ar', $page->intro_body_ar) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Meta Title EN</label><input class="form-control" name="meta_title_en" value="{{ old('meta_title_en', $page->meta_title_en) }}"></div>
            <div class="col-md-6"><label class="form-label">Meta Title AR</label><input class="form-control text-end" dir="rtl" name="meta_title_ar" value="{{ old('meta_title_ar', $page->meta_title_ar) }}"></div>
            <div class="col-md-6"><label class="form-label">Meta Description EN</label><textarea class="form-control" name="meta_description_en" rows="3">{{ old('meta_description_en', $page->meta_description_en) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Meta Description AR</label><textarea class="form-control text-end" dir="rtl" name="meta_description_ar" rows="3">{{ old('meta_description_ar', $page->meta_description_ar) }}</textarea></div>
            <div class="col-md-6"><label class="form-label">Hero Image</label><input type="file" class="form-control" name="hero_image"></div>
            <div class="col-md-3 d-flex align-items-end"><div class="form-check pb-2"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="page_is_active" @checked(old('is_active', $page->is_active))><label class="form-check-label" for="page_is_active">Active / Published</label></div></div>
        </div>
    </div>

    @php($pageKey = old('key', $page->key))

    @if($pageKey === 'home')
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">Homepage Services</h2>
            @for($i = 0; $i < 6; $i++)
                <div class="row g-3 mb-4 border-bottom pb-3">
                    <div class="col-md-2"><label class="form-label">Icon</label><input class="form-control" name="services_icon[]" value="{{ $sections['services'][$i]['icon'] ?? '' }}"></div>
                    <div class="col-md-5"><label class="form-label">Title EN</label><input class="form-control" name="services_title_en[]" value="{{ $sections['services'][$i]['title_en'] ?? '' }}"></div>
                    <div class="col-md-5"><label class="form-label">Title AR</label><input class="form-control text-end" dir="rtl" name="services_title_ar[]" value="{{ $sections['services'][$i]['title_ar'] ?? '' }}"></div>
                    <div class="col-md-6"><label class="form-label">Description EN</label><textarea class="form-control" name="services_text_en[]" rows="2">{{ $sections['services'][$i]['text_en'] ?? '' }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Description AR</label><textarea class="form-control text-end" dir="rtl" name="services_text_ar[]" rows="2">{{ $sections['services'][$i]['text_ar'] ?? '' }}</textarea></div>
                </div>
            @endfor
        </div>
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">Why Choose Travel Wave</h2>
            @php($homeWhyChooseSection = data_get($sections, 'why_choose_travel_wave', []))
            <div class="row g-3 mb-4">
                <div class="col-md-6"><label class="form-label">Section Title EN</label><input class="form-control" name="why_choose_travel_wave_title_en" value="{{ old('why_choose_travel_wave_title_en', data_get($homeWhyChooseSection, 'title_en', '')) }}"></div>
                <div class="col-md-6"><label class="form-label">Section Title AR</label><input class="form-control text-end" dir="rtl" name="why_choose_travel_wave_title_ar" value="{{ old('why_choose_travel_wave_title_ar', data_get($homeWhyChooseSection, 'title_ar', '')) }}"></div>
                <div class="col-md-6"><label class="form-label">Section Subtitle EN</label><textarea class="form-control" name="why_choose_travel_wave_subtitle_en" rows="2">{{ old('why_choose_travel_wave_subtitle_en', data_get($homeWhyChooseSection, 'subtitle_en', '')) }}</textarea></div>
                <div class="col-md-6"><label class="form-label">Section Subtitle AR</label><textarea class="form-control text-end" dir="rtl" name="why_choose_travel_wave_subtitle_ar" rows="2">{{ old('why_choose_travel_wave_subtitle_ar', data_get($homeWhyChooseSection, 'subtitle_ar', '')) }}</textarea></div>
            </div>
            @php($whyChooseIconSuggestions = [
                ['value' => 'material-symbols:travel-explore-rounded', 'label' => 'Travel Explore'],
                ['value' => 'material-symbols:verified-outline', 'label' => 'Verified'],
                ['value' => 'material-symbols:attach-money', 'label' => 'Money'],
                ['value' => 'material-symbols:groups-2', 'label' => 'Group'],
                ['value' => 'material-symbols:flash-on', 'label' => 'Flash'],
                ['value' => 'material-symbols:star-rate', 'label' => 'Star'],
            ])
            <div class="alert alert-light border small mb-4">
                <strong>Icon selection:</strong> choose from the recommended icons below or type a custom Iconify name.
            </div>
            @for($i = 0; $i < 6; $i++)
                <div class="row g-3 mb-4 border-bottom pb-3">
                    <div class="col-md-3">
                        <label class="form-label">Icon</label>
                        <div class="border rounded-3 p-2 bg-light tw-why-choose-icon-group">
                            @php($currentIconValue = old('why_choose_travel_wave_items.' . $i . '.icon', data_get($homeWhyChooseSection, 'items.' . $i . '.icon', 'material-symbols:travel-explore-rounded')))
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="tw-admin-icon-preview">
                                    <iconify-icon icon="{{ $currentIconValue }}" width="18" height="18"></iconify-icon>
                                </span>
                                <select class="form-select form-select-sm" data-icon-select onchange="var group=this.closest('.tw-why-choose-icon-group'); var input=group.querySelector('input[data-icon-input]'); var preview=group.querySelector('.tw-admin-icon-preview'); input.value=this.value; preview.innerHTML='<iconify-icon icon=\'' + this.value + '\' width=\'18\' height=\'18\'></iconify-icon>';">
                                    <option value="">Choose icon</option>
                                    @foreach($whyChooseIconSuggestions as $suggestion)
                                        <option value="{{ $suggestion['value'] }}" @selected($currentIconValue === $suggestion['value'])>{{ $suggestion['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input class="form-control form-control-sm" name="why_choose_travel_wave_items[{{ $i }}][icon]" value="{{ $currentIconValue }}" placeholder="material-symbols:travel-explore-rounded" autocomplete="off" data-icon-input oninput="var group=this.closest('.tw-why-choose-icon-group'); var preview=group.querySelector('.tw-admin-icon-preview'); var value=this.value.trim(); preview.innerHTML=value ? '<iconify-icon icon=\'' + value + '\' width=\'18\' height=\'18\'></iconify-icon>' : '<span class=\'text-muted\'>★</span>';">
                            <div class="form-text mt-2">Choose a preset or type a custom Iconify name.</div>
                        </div>
                    </div>
                    <div class="col-md-3"><label class="form-label">Title EN</label><input class="form-control" name="why_choose_travel_wave_items[{{ $i }}][title_en]" value="{{ old('why_choose_travel_wave_items.' . $i . '.title_en', data_get($homeWhyChooseSection, 'items.' . $i . '.title_en', '')) }}"></div>
                    <div class="col-md-3"><label class="form-label">Title AR</label><input class="form-control text-end" dir="rtl" name="why_choose_travel_wave_items[{{ $i }}][title_ar]" value="{{ old('why_choose_travel_wave_items.' . $i . '.title_ar', data_get($homeWhyChooseSection, 'items.' . $i . '.title_ar', '')) }}"></div>
                    <div class="col-md-2"><label class="form-label">Sort Order</label><input type="number" class="form-control" name="why_choose_travel_wave_items[{{ $i }}][sort_order]" value="{{ old('why_choose_travel_wave_items.' . $i . '.sort_order', data_get($homeWhyChooseSection, 'items.' . $i . '.sort_order', $i + 1)) }}"></div>
                    <div class="col-md-2 d-flex align-items-end"><div class="form-check pb-2"><input type="hidden" name="why_choose_travel_wave_items[{{ $i }}][is_active]" value="0"><input class="form-check-input" type="checkbox" name="why_choose_travel_wave_items[{{ $i }}][is_active]" value="1" @checked(old('why_choose_travel_wave_items.' . $i . '.is_active', data_get($homeWhyChooseSection, 'items.' . $i . '.is_active', true)))><label class="form-check-label">Enabled</label></div></div>
                    <div class="col-md-12"><label class="form-label">Description EN</label><textarea class="form-control" name="why_choose_travel_wave_items[{{ $i }}][text_en]" rows="2">{{ old('why_choose_travel_wave_items.' . $i . '.text_en', data_get($homeWhyChooseSection, 'items.' . $i . '.text_en', '')) }}</textarea></div>
                    <div class="col-md-12"><label class="form-label">Description AR</label><textarea class="form-control text-end" dir="rtl" name="why_choose_travel_wave_items[{{ $i }}][text_ar]" rows="2">{{ old('why_choose_travel_wave_items.' . $i . '.text_ar', data_get($homeWhyChooseSection, 'items.' . $i . '.text_ar', '')) }}</textarea></div>
                </div>
            @endfor
            <style>
                .tw-why-choose-icon-group {
                    min-height: 100%;
                }
                .tw-admin-icon-preview {
                    width: 2.35rem;
                    height: 2.35rem;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 0.75rem;
                    color: #ff8c32;
                    background: linear-gradient(135deg, rgba(255, 140, 50, 0.18), rgba(255, 140, 50, 0.32));
                    flex-shrink: 0;
                }
            </style>
        </div>
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">Why Choose Us / How It Works</h2>
            @for($i = 0; $i < 5; $i++)
                <div class="row g-3 mb-4 border-bottom pb-3">
                    <div class="col-md-6"><label class="form-label">Why Title EN</label><input class="form-control" name="why_title_en[]" value="{{ $sections['why_choose_us'][$i]['title_en'] ?? '' }}"></div>
                    <div class="col-md-6"><label class="form-label">Why Title AR</label><input class="form-control text-end" dir="rtl" name="why_title_ar[]" value="{{ $sections['why_choose_us'][$i]['title_ar'] ?? '' }}"></div>
                    <div class="col-md-6"><label class="form-label">Why Text EN</label><textarea class="form-control" name="why_text_en[]" rows="2">{{ $sections['why_choose_us'][$i]['text_en'] ?? '' }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Why Text AR</label><textarea class="form-control text-end" dir="rtl" name="why_text_ar[]" rows="2">{{ $sections['why_choose_us'][$i]['text_ar'] ?? '' }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Step Title EN</label><input class="form-control" name="steps_title_en[]" value="{{ $sections['how_it_works'][$i]['title_en'] ?? '' }}"></div>
                    <div class="col-md-6"><label class="form-label">Step Title AR</label><input class="form-control text-end" dir="rtl" name="steps_title_ar[]" value="{{ $sections['how_it_works'][$i]['title_ar'] ?? '' }}"></div>
                    <div class="col-md-6"><label class="form-label">Step Text EN</label><textarea class="form-control" name="steps_text_en[]" rows="2">{{ $sections['how_it_works'][$i]['text_en'] ?? '' }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Step Text AR</label><textarea class="form-control text-end" dir="rtl" name="steps_text_ar[]" rows="2">{{ $sections['how_it_works'][$i]['text_ar'] ?? '' }}</textarea></div>
                </div>
            @endfor
        </div>
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">Promo, Inquiry, and Final CTA</h2>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Promo Title EN</label><input class="form-control" name="promo_title_en" value="{{ $sections['promo']['title_en'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label">Promo Title AR</label><input class="form-control text-end" dir="rtl" name="promo_title_ar" value="{{ $sections['promo']['title_ar'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label">Promo Text EN</label><textarea class="form-control" name="promo_text_en" rows="2">{{ $sections['promo']['text_en'] ?? '' }}</textarea></div>
                <div class="col-md-6"><label class="form-label">Promo Text AR</label><textarea class="form-control text-end" dir="rtl" name="promo_text_ar" rows="2">{{ $sections['promo']['text_ar'] ?? '' }}</textarea></div>
                <div class="col-md-4"><label class="form-label">Promo Button EN</label><input class="form-control" name="promo_button_en" value="{{ $sections['promo']['button_en'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label">Promo Button AR</label><input class="form-control text-end" dir="rtl" name="promo_button_ar" value="{{ $sections['promo']['button_ar'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label">Promo URL</label><input class="form-control" name="promo_url" value="{{ $sections['promo']['url'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label">Inquiry Title EN</label><input class="form-control" name="inquiry_title_en" value="{{ $sections['inquiry']['title_en'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label">Inquiry Title AR</label><input class="form-control text-end" dir="rtl" name="inquiry_title_ar" value="{{ $sections['inquiry']['title_ar'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label">Inquiry Text EN</label><textarea class="form-control" name="inquiry_text_en" rows="2">{{ $sections['inquiry']['text_en'] ?? '' }}</textarea></div>
                <div class="col-md-6"><label class="form-label">Inquiry Text AR</label><textarea class="form-control text-end" dir="rtl" name="inquiry_text_ar" rows="2">{{ $sections['inquiry']['text_ar'] ?? '' }}</textarea></div>
                <div class="col-md-6"><label class="form-label">Final CTA Title EN</label><input class="form-control" name="final_cta_title_en" value="{{ $sections['final_cta']['title_en'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label">Final CTA Title AR</label><input class="form-control text-end" dir="rtl" name="final_cta_title_ar" value="{{ $sections['final_cta']['title_ar'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label">Final CTA Text EN</label><textarea class="form-control" name="final_cta_text_en" rows="2">{{ $sections['final_cta']['text_en'] ?? '' }}</textarea></div>
                <div class="col-md-6"><label class="form-label">Final CTA Text AR</label><textarea class="form-control text-end" dir="rtl" name="final_cta_text_ar" rows="2">{{ $sections['final_cta']['text_ar'] ?? '' }}</textarea></div>
                <div class="col-md-4"><label class="form-label">Final CTA Button EN</label><input class="form-control" name="final_cta_button_en" value="{{ $sections['final_cta']['button_en'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label">Final CTA Button AR</label><input class="form-control text-end" dir="rtl" name="final_cta_button_ar" value="{{ $sections['final_cta']['button_ar'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label">Final CTA URL</label><input class="form-control" name="final_cta_url" value="{{ $sections['final_cta']['url'] ?? '' }}"></div>
            </div>
        </div>
    @elseif(in_array($pageKey, ['visas', 'domestic', 'flights', 'hotels'], true))
        @include('admin.pages.partials.service-sections', ['sections' => $sections])
    @elseif(in_array($pageKey, ['about', 'contact'], true))
        @include('admin.pages.partials.content-sections', ['sections' => $sections])
    @else
        <div class="card admin-card p-4 mb-4">
            <h2 class="h5 mb-3">Feature Blocks, FAQs, and CTA</h2>
            @for($i = 0; $i < 5; $i++)
                <div class="row g-3 mb-4 border-bottom pb-3">
                    <div class="col-md-6"><label class="form-label">Feature Title EN</label><input class="form-control" name="feature_title_en[]" value="{{ $sections['feature_blocks'][$i]['title_en'] ?? '' }}"></div>
                    <div class="col-md-6"><label class="form-label">Feature Title AR</label><input class="form-control text-end" dir="rtl" name="feature_title_ar[]" value="{{ $sections['feature_blocks'][$i]['title_ar'] ?? '' }}"></div>
                    <div class="col-md-6"><label class="form-label">Feature Text EN</label><textarea class="form-control" name="feature_text_en[]" rows="2">{{ $sections['feature_blocks'][$i]['text_en'] ?? '' }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Feature Text AR</label><textarea class="form-control text-end" dir="rtl" name="feature_text_ar[]" rows="2">{{ $sections['feature_blocks'][$i]['text_ar'] ?? '' }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">FAQ Question EN</label><input class="form-control" name="faq_question_en[]" value="{{ $sections['faqs'][$i]['question_en'] ?? '' }}"></div>
                    <div class="col-md-6"><label class="form-label">FAQ Question AR</label><input class="form-control text-end" dir="rtl" name="faq_question_ar[]" value="{{ $sections['faqs'][$i]['question_ar'] ?? '' }}"></div>
                    <div class="col-md-6"><label class="form-label">FAQ Answer EN</label><textarea class="form-control" name="faq_answer_en[]" rows="2">{{ $sections['faqs'][$i]['answer_en'] ?? '' }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">FAQ Answer AR</label><textarea class="form-control text-end" dir="rtl" name="faq_answer_ar[]" rows="2">{{ $sections['faqs'][$i]['answer_ar'] ?? '' }}</textarea></div>
                </div>
            @endfor
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">CTA Title EN</label><input class="form-control" name="cta_title_en" value="{{ $sections['cta']['title_en'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label">CTA Title AR</label><input class="form-control text-end" dir="rtl" name="cta_title_ar" value="{{ $sections['cta']['title_ar'] ?? '' }}"></div>
                <div class="col-md-6"><label class="form-label">CTA Text EN</label><textarea class="form-control" name="cta_text_en" rows="2">{{ $sections['cta']['text_en'] ?? '' }}</textarea></div>
                <div class="col-md-6"><label class="form-label">CTA Text AR</label><textarea class="form-control text-end" dir="rtl" name="cta_text_ar" rows="2">{{ $sections['cta']['text_ar'] ?? '' }}</textarea></div>
                <div class="col-md-4"><label class="form-label">CTA Button EN</label><input class="form-control" name="cta_button_en" value="{{ $sections['cta']['button_en'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label">CTA Button AR</label><input class="form-control text-end" dir="rtl" name="cta_button_ar" value="{{ $sections['cta']['button_ar'] ?? '' }}"></div>
                <div class="col-md-4"><label class="form-label">CTA URL</label><input class="form-control" name="cta_url" value="{{ $sections['cta']['url'] ?? '' }}"></div>
            </div>
        </div>
    @endif

    <button class="btn btn-primary">{{ $submitLabel ?? 'Save Page' }}</button>
</form>

<script>
document.addEventListener('click', function (event) {
    const addButton = event.target.closest('[data-repeater-add]');
    const removeButton = event.target.closest('[data-repeater-remove]');

    if (addButton) {
        const key = addButton.getAttribute('data-repeater-add');
        const list = document.querySelector('[data-repeater-list="' + key + '"]');
        const lastItem = list?.querySelector('[data-repeater-item]:last-child');

        if (!list || !lastItem) {
            return;
        }

        const clone = lastItem.cloneNode(true);
        clone.querySelectorAll('input, textarea, select').forEach((input) => {
            if (input.type === 'checkbox') {
                input.checked = input.defaultChecked;
            } else {
                input.value = '';
            }
        });
        list.appendChild(clone);
        syncPageRepeaterNames(list);
    }

    if (removeButton) {
        const list = removeButton.closest('[data-repeater-list]');
        const item = removeButton.closest('[data-repeater-item]');

        if (item && list) {
            if (list.querySelectorAll('[data-repeater-item]').length > 1) {
                item.remove();
            } else {
                item.querySelectorAll('input, textarea, select').forEach((input) => {
                    if (input.type === 'checkbox') {
                        input.checked = false;
                    } else {
                        input.value = '';
                    }
                });
            }

            syncPageRepeaterNames(list);
        }
    }
});

function syncPageRepeaterNames(list) {
    if (!list) {
        return;
    }

    list.querySelectorAll('[data-repeater-item]').forEach((item, index) => {
        item.querySelectorAll('input, textarea, select').forEach((input) => {
            if (!input.name) {
                return;
            }

            input.name = input.name.replace(/\[\d+\]/, '[' + index + ']');
        });
    });
}

document.querySelectorAll('[data-repeater-list]').forEach(syncPageRepeaterNames);
</script>
