<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\CrmLeadSource;
use App\Models\CrmStatus;
use App\Models\Destination;
use App\Models\HeroSlide;
use App\Models\HomeCountryStripItem;
use App\Models\Inquiry;
use App\Models\LeadForm;
use App\Models\MarketingLandingPage;
use App\Models\MarketingLandingPageEvent;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\TrackingIntegration;
use App\Models\VisaCategory;
use App\Models\VisaCountry;
use App\Support\DestinationPageData;
use App\Support\LeadFormManager;
use App\Support\MapSectionManager;
use App\Support\MetaConversionApiService;
use App\Support\TrackingManager;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FrontendController extends Controller
{
    public function switchLocale(string $locale)
    {
        abort_unless(in_array($locale, ['en', 'ar'], true), 404);
        session(['locale' => $locale]);

        return back();
    }

    public function home()
    {
        $settings = Setting::query()->first();
        $homeSearchConfig = $this->homeSearchConfig();

        return view('frontend.home', [
            'page' => $this->page('home'),
            'managedForms' => $this->formsForContext(LeadFormManager::contextForPageKey('home')),
            'managedMaps' => $this->mapsForContext(MapSectionManager::contextForPageKey('home')),
            'heroSlides' => HeroSlide::where('is_active', true)->orderBy('sort_order')->limit(3)->get(),
            'heroSliderSettings' => $settings,
            'homeSearchConfig' => $homeSearchConfig,
            'homeCountryStripItems' => HomeCountryStripItem::where('is_active', true)
                ->where('show_on_homepage', true)
                ->with('visaCountry')
                ->orderBy('sort_order')
                ->get(),
            'featuredCountries' => VisaCountry::where('is_featured', true)->where('is_active', true)->with('category')->orderBy('sort_order')->limit(6)->get(),
            'featuredDestinations' => Destination::where('is_featured', true)->where('is_active', true)->orderBy('sort_order')->limit(6)->get(),
            'categories' => VisaCategory::where('is_active', true)->orderBy('sort_order')->get(),
            'testimonials' => Testimonial::where('is_active', true)->orderBy('sort_order')->get(),
            'posts' => BlogPost::where('is_published', true)->latest('published_at')->limit(3)->get(),
        ]);
    }

    public function visaIndex()
    {
        $categories = VisaCategory::where('is_active', true)
            ->with(['countries' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $featuredCountries = VisaCountry::where('is_featured', true)
            ->where('is_active', true)
            ->with('category')
            ->orderBy('sort_order')
            ->get();

        return view('frontend.services.premium-landing', [
            'page' => $this->page('visas'),
            'servicePage' => $this->visaServicePage($categories, $featuredCountries),
            'managedForms' => $this->formsForContext(LeadFormManager::contextForPageKey('visas.index')),
            'managedMaps' => $this->mapsForContext(MapSectionManager::contextForPageKey('visas.index')),
        ]);
    }

    public function visaCategory(VisaCategory $category)
    {
        return view('frontend.visas.category', [
            'category' => $category->load(['countries' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')]),
        ]);
    }

    public function visaCountry(VisaCountry $country)
    {
        abort_unless($country->is_active, 404);

        $country->load('category');

        return view('frontend.destination-pages.show', [
            'pageData' => DestinationPageData::fromVisaCountry($country),
            'managedForms' => $this->formsForContext(LeadFormManager::contextForVisaCountry($country)),
            'managedMaps' => $this->mapsForContext(MapSectionManager::contextForVisaCountry($country)),
        ]);
    }

    public function domesticIndex()
    {
        $destinations = Destination::where('is_active', true)->orderBy('sort_order')->get();

        return view('frontend.services.premium-landing', [
            'page' => $this->page('domestic'),
            'servicePage' => $this->domesticServicePage($destinations),
            'managedForms' => $this->formsForContext(LeadFormManager::contextForPageKey('destinations.index')),
            'managedMaps' => $this->mapsForContext(MapSectionManager::contextForPageKey('destinations.index')),
        ]);
    }

    public function destinationShow(Destination $destination)
    {
        abort_unless($destination->is_active, 404);

        return view('frontend.destination-pages.show', [
            'pageData' => DestinationPageData::fromDestination($destination),
            'managedForms' => $this->formsForContext(LeadFormManager::contextForDestination($destination)),
            'managedMaps' => $this->mapsForContext(MapSectionManager::contextForDestination($destination)),
        ]);
    }

    public function flights()
    {
        return view('frontend.services.premium-landing', [
            'page' => $this->page('flights'),
            'servicePage' => $this->flightServicePage(),
            'managedForms' => $this->formsForContext(LeadFormManager::contextForPageKey('flights')),
            'managedMaps' => $this->mapsForContext(MapSectionManager::contextForPageKey('flights')),
        ]);
    }

    public function hotels()
    {
        return view('frontend.services.premium-landing', [
            'page' => $this->page('hotels'),
            'servicePage' => $this->hotelServicePage(),
            'managedForms' => $this->formsForContext(LeadFormManager::contextForPageKey('hotels')),
            'managedMaps' => $this->mapsForContext(MapSectionManager::contextForPageKey('hotels')),
        ]);
    }

    public function about()
    {
        return view('frontend.pages.premium', [
            'page' => $this->page('about'),
            'contentPage' => $this->aboutPageContent(),
            'managedForms' => $this->formsForContext(LeadFormManager::contextForPageKey('about')),
            'managedMaps' => $this->mapsForContext(MapSectionManager::contextForPageKey('about')),
        ]);
    }

    public function contact()
    {
        return view('frontend.pages.premium', [
            'page' => $this->page('contact'),
            'contentPage' => $this->contactPageContent(),
            'managedForms' => $this->formsForContext(LeadFormManager::contextForPageKey('contact')),
            'managedMaps' => $this->mapsForContext(MapSectionManager::contextForPageKey('contact')),
        ]);
    }

    public function pageShow(Page $page)
    {
        abort_unless($page->is_active, 404);
        abort_if($page->isCorePage(), 404);

        return view('frontend.pages.standard', [
            'page' => $page,
            'managedForms' => $this->formsForContext(LeadFormManager::contextForPageKey('page:' . $page->key)),
            'managedMaps' => $this->mapsForContext(MapSectionManager::contextForPageKey('page:' . $page->key)),
        ]);
    }

    public function marketingLandingPage(MarketingLandingPage $landingPage, Request $request)
    {
        abort_unless($landingPage->status === MarketingLandingPage::STATUS_PUBLISHED, 404);

        $request->session()->start();

        MarketingLandingPageEvent::query()->create([
            'marketing_landing_page_id' => $landingPage->id,
            'event_type' => MarketingLandingPageEvent::TYPE_PAGE_VIEW,
            'session_key' => $request->session()->getId(),
            'source' => $landingPage->utm_source ?: $request->query('utm_source'),
            'medium' => $landingPage->utm_medium ?: $request->query('utm_medium'),
            'campaign' => $landingPage->utm_campaign ?: $request->query('utm_campaign'),
            'content' => $landingPage->utm_content ?: $request->query('utm_content'),
            'term' => $landingPage->utm_term ?: $request->query('utm_term'),
            'referrer' => $request->headers->get('referer'),
            'path' => $request->path(),
            'payload' => [
                'user_agent' => $request->userAgent(),
            ],
            'occurred_at' => now(),
        ]);

        return view('frontend.marketing.show', [
            'landingPage' => $landingPage->load('leadForm.fields'),
            'pageTrackingIntegrations' => TrackingIntegration::query()
                ->whereIn('id', $landingPage->tracking_integration_ids ?? [])
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'trackingContext' => array_merge(
                TrackingManager::contextFromRequest($request),
                ['page_key' => 'marketing.landing-page', 'marketing_landing_page_id' => $landingPage->id]
            ),
        ]);
    }

    public function trackMarketingLandingPageEvent(Request $request, MarketingLandingPage $landingPage)
    {
        abort_unless($landingPage->status === MarketingLandingPage::STATUS_PUBLISHED, 404);

        $data = $request->validate([
            'event_type' => ['required', 'in:cta_click,whatsapp_click,form_submit'],
            'payload' => ['nullable', 'array'],
            'meta_event_id' => ['nullable', 'string', 'max:100'],
        ]);

        $request->session()->start();

        MarketingLandingPageEvent::query()->create([
            'marketing_landing_page_id' => $landingPage->id,
            'event_type' => $data['event_type'],
            'session_key' => $request->session()->getId(),
            'source' => $landingPage->utm_source ?: $request->query('utm_source'),
            'medium' => $landingPage->utm_medium ?: $request->query('utm_medium'),
            'campaign' => $landingPage->utm_campaign ?: $request->query('utm_campaign'),
            'content' => $landingPage->utm_content ?: $request->query('utm_content'),
            'term' => $landingPage->utm_term ?: $request->query('utm_term'),
            'referrer' => $request->headers->get('referer'),
            'path' => $request->path(),
            'payload' => $data['payload'] ?? [],
            'occurred_at' => now(),
        ]);

        $this->sendMetaConversionEvent(
            $request,
            $data['event_type'] === MarketingLandingPageEvent::TYPE_WHATSAPP_CLICK ? 'WhatsAppClick' : 'Lead',
            [
                'event_id' => $data['meta_event_id'] ?? null,
                'custom_data' => [
                    'landing_page' => $landingPage->internal_name,
                    'campaign_name' => $landingPage->campaign_name,
                    'ad_platform' => $landingPage->ad_platform,
                    'event_type' => $data['event_type'],
                ],
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function trackMetaEvent(Request $request)
    {
        $data = $request->validate([
            'event_name' => ['required', 'string', 'max:100'],
            'event_id' => ['nullable', 'string', 'max:100'],
            'event_source_url' => ['nullable', 'url', 'max:500'],
            'user_data' => ['nullable', 'array'],
            'custom_data' => ['nullable', 'array'],
        ]);

        $sent = $this->sendMetaConversionEvent($request, $data['event_name'], [
            'event_id' => $data['event_id'] ?? null,
            'event_source_url' => $data['event_source_url'] ?? null,
            'user_data' => $data['user_data'] ?? [],
            'custom_data' => $data['custom_data'] ?? [],
        ]);

        return response()->json(['ok' => true, 'sent' => $sent]);
    }

    public function blogIndex()
    {
        return view('frontend.blog.index', [
            'page' => $this->page('blog'),
            'posts' => BlogPost::where('is_published', true)->latest('published_at')->paginate(9),
        ]);
    }

    public function blogShow(BlogPost $post)
    {
        abort_unless($post->is_published, 404);

        return view('frontend.blog.show', [
            'post' => $post->load('category'),
            'relatedPosts' => BlogPost::where('is_published', true)
                ->where('id', '!=', $post->id)
                ->latest('published_at')
                ->limit(3)
                ->get(),
        ]);
    }

    public function storeInquiry(Request $request)
    {
        if ($request->filled('lead_form_id')) {
            return $this->storeManagedFormInquiry($request);
        }

        $data = $request->validate([
            'type' => ['required', 'in:general,visa,destination,flights,hotels,contact'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'destination' => ['nullable', 'string', 'max:255'],
            'service_type' => ['nullable', 'string', 'max:255'],
            'travel_date' => ['nullable', 'date'],
            'return_date' => ['nullable', 'date'],
            'travelers_count' => ['nullable', 'integer'],
            'nights_count' => ['nullable', 'integer'],
            'accommodation_type' => ['nullable', 'string', 'max:255'],
            'estimated_budget' => ['nullable', 'string', 'max:255'],
            'preferred_language' => ['nullable', 'in:en,ar'],
            'source_page' => ['nullable', 'string', 'max:255'],
            'marketing_landing_page_id' => ['nullable', 'exists:marketing_landing_pages,id'],
            'success_message' => ['nullable', 'string', 'max:500'],
            'meta_event_id' => ['nullable', 'string', 'max:100'],
            'meta_event_name' => ['nullable', 'string', 'max:100'],
            'message' => ['nullable', 'string'],
        ]);

        $submittedData = array_filter([
            'whatsapp_number' => $data['whatsapp_number'] ?? null,
        ], fn ($value) => filled($value));

        $inquiry = Inquiry::create($data + [
            'preferred_language' => $data['preferred_language'] ?? app()->getLocale(),
            'submitted_data' => $submittedData ?: null,
            'status' => 'new',
            'country' => $data['nationality'] ?? null,
            'crm_status_id' => $this->defaultCrmStatusId(),
            'crm_source_id' => $this->resolveLeadSourceId($request, $data['type'] ?? null),
            'crm_status_updated_at' => now(),
            'crm_status_updated_by' => null,
            'status_1_updated_at' => now(),
            'lead_source' => $this->resolveLeadSource($request, $data['type'] ?? null),
            'campaign_name' => $request->input('utm_campaign'),
            'utm_source' => $request->input('utm_source'),
            'utm_campaign' => $request->input('utm_campaign'),
            'priority' => 'normal',
        ]);

        if (! empty($data['marketing_landing_page_id'])) {
            MarketingLandingPageEvent::query()->create([
                'marketing_landing_page_id' => (int) $data['marketing_landing_page_id'],
                'event_type' => MarketingLandingPageEvent::TYPE_FORM_SUBMIT,
                'session_key' => $request->session()->getId(),
                'path' => $request->path(),
                'occurred_at' => now(),
            ]);
        }

        $this->sendMetaConversionEvent($request, $data['meta_event_name'] ?? $this->defaultMetaEventNameForType($data['type']), [
            'event_id' => $data['meta_event_id'] ?? null,
            'user_data' => [
                'full_name' => $data['full_name'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
            ],
            'custom_data' => array_filter([
                'page_name' => $data['source_page'] ?? null,
                'form_name' => $data['type'] ?? null,
                'destination' => $data['destination'] ?? null,
                'service_type' => $data['service_type'] ?? null,
                'marketing_landing_page_id' => $data['marketing_landing_page_id'] ?? null,
                'inquiry_id' => $inquiry->id,
            ], fn ($value) => filled($value)),
        ]);

        return back()->with('success', $data['success_message'] ?? __('ui.inquiry_success'));
    }

    protected function storeManagedFormInquiry(Request $request)
    {
        $meta = $request->validate([
            'lead_form_id' => ['required', 'exists:lead_forms,id'],
            'lead_form_assignment_id' => ['nullable', 'exists:lead_form_assignments,id'],
            'marketing_landing_page_id' => ['nullable', 'exists:marketing_landing_pages,id'],
            'type' => ['nullable', 'string', 'max:255'],
            'source_page' => ['nullable', 'string', 'max:255'],
            'display_position' => ['nullable', 'string', 'max:50'],
            'preferred_language' => ['nullable', 'in:en,ar'],
            'success_message' => ['nullable', 'string', 'max:500'],
            'meta_event_id' => ['nullable', 'string', 'max:100'],
            'meta_event_name' => ['nullable', 'string', 'max:100'],
        ]);

        $form = LeadForm::query()
            ->with(['fields' => fn ($query) => $query->where('is_enabled', true)->orderBy('sort_order')])
            ->findOrFail($meta['lead_form_id']);

        abort_unless($form->is_active, 404);

        $fieldRules = [];

        foreach ($form->fields as $field) {
            $fieldRules[$field->field_key] = $this->rulesForManagedField($field->type, $field->validation_rule, $field->is_required, $field->options ?? []);
        }

        $submittedData = $request->validate($fieldRules);
        $country = $submittedData['country']
            ?? $submittedData['nationality']
            ?? $submittedData['from']
            ?? null;
        $destination = $submittedData['destination']
            ?? $submittedData['country']
            ?? $submittedData['to']
            ?? null;
        $serviceType = $submittedData['service_type']
            ?? $submittedData['visa_type']
            ?? null;
        $travelDate = $submittedData['travel_date']
            ?? $submittedData['departure_date']
            ?? $submittedData['check_in_date']
            ?? null;
        $returnDate = $submittedData['return_date']
            ?? $submittedData['check_out_date']
            ?? null;
        $travelersCount = $submittedData['travelers_count']
            ?? $submittedData['number_of_travelers']
            ?? $submittedData['number_of_guests']
            ?? null;

        $inquiry = Inquiry::create([
            'lead_form_id' => $form->id,
            'lead_form_assignment_id' => $meta['lead_form_assignment_id'] ?? null,
            'marketing_landing_page_id' => $meta['marketing_landing_page_id'] ?? null,
            'type' => $meta['type'] ?? ($form->form_category ?: 'general'),
            'form_name' => $form->name,
            'form_category' => $form->form_category,
            'full_name' => $submittedData['full_name'] ?? null,
            'phone' => $submittedData['phone'] ?? null,
            'whatsapp_number' => $submittedData['whatsapp_number'] ?? ($submittedData['phone'] ?? null),
            'email' => $submittedData['email'] ?? null,
            'nationality' => $submittedData['nationality'] ?? ($submittedData['from'] ?? null),
            'country' => $country,
            'destination' => $destination,
            'service_type' => $serviceType,
            'travel_date' => $travelDate,
            'return_date' => $returnDate,
            'travelers_count' => $travelersCount,
            'nights_count' => $submittedData['nights_count'] ?? ($submittedData['number_of_nights'] ?? null),
            'accommodation_type' => $submittedData['accommodation_type'] ?? ($submittedData['number_of_rooms'] ?? null),
            'preferred_language' => $meta['preferred_language'] ?? app()->getLocale(),
            'source_page' => $meta['source_page'] ?? request()->path(),
            'display_position' => $meta['display_position'] ?? null,
            'message' => $submittedData['message'] ?? ($submittedData['your_message'] ?? ($submittedData['subject'] ?? null)),
            'submitted_data' => $submittedData,
            'status' => 'new',
            'crm_status_id' => $this->defaultCrmStatusId(),
            'crm_source_id' => $this->resolveLeadSourceId($request, $meta['type'] ?? ($form->form_category ?: 'general')),
            'crm_status_updated_at' => now(),
            'crm_status_updated_by' => null,
            'status_1_updated_at' => now(),
            'lead_source' => $this->resolveLeadSource($request, $meta['type'] ?? ($form->form_category ?: 'general')),
            'campaign_name' => $request->input('utm_campaign'),
            'utm_source' => $request->input('utm_source'),
            'utm_campaign' => $request->input('utm_campaign'),
            'priority' => 'normal',
        ]);

        if (! empty($meta['marketing_landing_page_id'])) {
            MarketingLandingPageEvent::query()->create([
                'marketing_landing_page_id' => (int) $meta['marketing_landing_page_id'],
                'event_type' => MarketingLandingPageEvent::TYPE_FORM_SUBMIT,
                'session_key' => $request->session()->getId(),
                'path' => $request->path(),
                'occurred_at' => now(),
            ]);
        }

        $this->sendMetaConversionEvent($request, $meta['meta_event_name'] ?? $this->defaultMetaEventNameForType($form->form_category ?: 'general'), [
            'event_id' => $meta['meta_event_id'] ?? null,
            'user_data' => [
                'full_name' => $submittedData['full_name'] ?? null,
                'email' => $submittedData['email'] ?? null,
                'phone' => $submittedData['phone'] ?? null,
            ],
            'custom_data' => array_filter([
                'page_name' => $meta['source_page'] ?? request()->path(),
                'form_name' => $form->name,
                'form_category' => $form->form_category,
                'destination' => $submittedData['destination'] ?? ($submittedData['country'] ?? ($submittedData['to'] ?? null)),
                'service_type' => $submittedData['service_type'] ?? ($submittedData['visa_type'] ?? null),
                'display_position' => $meta['display_position'] ?? null,
                'marketing_landing_page_id' => $meta['marketing_landing_page_id'] ?? null,
                'inquiry_id' => $inquiry->id,
            ], fn ($value) => filled($value)),
        ]);

        return back()->with('success', $meta['success_message'] ?? ($form->localized('success_message') ?: __('ui.inquiry_success')));
    }

    protected function defaultMetaEventNameForType(?string $type): string
    {
        return match ($type) {
            'contact' => 'Contact',
            'registration' => 'CompleteRegistration',
            default => 'Lead',
        };
    }

    protected function defaultCrmStatusId(): ?int
    {
        return CrmStatus::query()
            ->where('slug', 'new-lead')
            ->value('id');
    }

    protected function resolveLeadSource(Request $request, ?string $fallback = null): ?string
    {
        return Arr::first([
            $request->input('utm_source'),
            $request->input('source'),
            $request->input('traffic_source'),
            $fallback,
            $request->headers->get('referer') ? 'referral' : null,
            'website',
        ], fn ($value) => filled($value));
    }

    protected function resolveLeadSourceId(Request $request, ?string $fallback = null): ?int
    {
        $label = $this->resolveLeadSource($request, $fallback);

        if (! filled($label)) {
            return null;
        }

        $normalized = mb_strtolower(trim((string) $label));

        return CrmLeadSource::query()
            ->whereRaw('LOWER(name_en) = ?', [$normalized])
            ->orWhereRaw('LOWER(name_ar) = ?', [$normalized])
            ->value('id');
    }

    protected function sendMetaConversionEvent(Request $request, string $eventName, array $payload = []): bool
    {
        return app(MetaConversionApiService::class)->track($eventName, $request, [
            'event_id' => $payload['event_id'] ?? (string) Str::uuid(),
            'event_source_url' => $payload['event_source_url'] ?? null,
            'user_data' => $payload['user_data'] ?? [],
            'custom_data' => $payload['custom_data'] ?? [],
        ]);
    }

    protected function rulesForManagedField(string $type, ?string $customRule, bool $required, array $options = []): array
    {
        if ($customRule) {
            $rules = collect(explode('|', $customRule))
                ->map(fn ($rule) => trim((string) $rule))
                ->filter()
                ->reject(fn ($rule) => in_array($rule, ['required', 'nullable', 'present', 'sometimes'], true))
                ->values()
                ->all();

            array_unshift($rules, $required ? 'required' : 'nullable');

            return $rules;
        }

        $rules = [$required ? 'required' : 'nullable'];

        $rules = match ($type) {
            'email' => [...$rules, 'email', 'max:255'],
            'date' => [...$rules, 'date'],
            'number' => [...$rules, 'integer'],
            'textarea' => [...$rules, 'string'],
            'select' => !empty($options)
                ? [...$rules, 'in:' . implode(',', collect($options)->pluck('value')->filter()->all())]
                : [...$rules, 'string', 'max:255'],
            default => [...$rules, 'string', 'max:255'],
        };

        return $rules;
    }

    protected function page(string $key): Page
    {
        return Page::where('key', $key)->where('is_active', true)->firstOrFail();
    }

    protected function formsForContext(array $context): array
    {
        return LeadFormManager::resolve($context);
    }

    protected function mapsForContext(array $context): array
    {
        return MapSectionManager::resolve($context);
    }

    protected function homeSearchConfig(): array
    {
        $regionDefinitions = [
            [
                'key' => 'european-union',
                'label_en' => 'European Union',
                'label_ar' => 'الاتحاد الأوروبي',
                'category_slugs' => ['european-union'],
            ],
            [
                'key' => 'arab-countries',
                'label_en' => 'Arab Countries',
                'label_ar' => 'الدول العربية',
                'category_slugs' => ['arab-countries', 'gcc', 'gulf'],
            ],
            [
                'key' => 'asia',
                'label_en' => 'Asia',
                'label_ar' => 'آسيا',
                'category_slugs' => ['asia'],
            ],
            [
                'key' => 'usa-canada',
                'label_en' => 'USA & Canada',
                'label_ar' => 'أمريكا وكندا',
                'category_slugs' => ['usa-canada', 'north-america'],
            ],
        ];

        $categories = VisaCategory::query()
            ->where('is_active', true)
            ->with(['countries' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->get()
            ->keyBy('slug');

        $visaRegions = collect($regionDefinitions)->map(function (array $region) use ($categories) {
            $countries = collect($region['category_slugs'])
                ->map(fn (string $slug) => $categories->get($slug))
                ->filter()
                ->flatMap(fn (VisaCategory $category) => $category->countries)
                ->unique('id')
                ->sortBy('sort_order')
                ->values()
                ->map(fn (VisaCountry $country) => [
                    'slug' => $country->slug,
                    'label_en' => $country->name_en,
                    'label_ar' => $country->name_ar,
                    'url' => route('visas.country', $country),
                ])
                ->all();

            return [
                'key' => $region['key'],
                'label_en' => $region['label_en'],
                'label_ar' => $region['label_ar'],
                'countries' => $countries,
            ];
        })->all();

        $domesticDestinations = Destination::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Destination $destination) => [
                'slug' => $destination->slug,
                'label_en' => $destination->title_en,
                'label_ar' => $destination->title_ar,
                'url' => route('destinations.show', $destination),
            ])
            ->all();

        return [
            'services' => [
                ['key' => 'visas', 'label_en' => 'External Visas', 'label_ar' => 'التأشيرات الخارجية'],
                ['key' => 'domestic', 'label_en' => 'Domestic Trips', 'label_ar' => 'الرحلات الداخلية'],
                ['key' => 'flights', 'label_en' => 'Flights', 'label_ar' => 'الطيران'],
                ['key' => 'hotels', 'label_en' => 'Hotels', 'label_ar' => 'الفنادق'],
            ],
            'visa_regions' => $visaRegions,
            'domestic_destinations' => $domesticDestinations,
            'direct_routes' => [
                'flights' => route('flights'),
                'hotels' => route('hotels'),
            ],
        ];
    }

    protected function aboutPageContent(): array
    {
        return [
            'direction' => 'rtl',
            'page_title' => 'من نحن | Travel Wave',
            'hero' => [
                'enabled' => true,
                'eyebrow' => 'Travel Wave',
                'title' => 'من نحن',
                'subtitle' => 'Travel Wave شريك موثوق في خدمات السفر والتأشيرات، نجمع بين التنظيم العملي، والمتابعة الواضحة، والتجربة الراقية التي تمنح العميل ثقة أكبر في كل خطوة.',
                'background_image' => asset('storage/hero-slides/slide-1.svg'),
                'breadcrumbs' => [
                    ['label' => 'الرئيسية', 'url' => route('home')],
                    ['label' => 'من نحن', 'url' => null],
                ],
            ],
            'story' => [
                'enabled' => true,
                'eyebrow' => 'هوية Travel Wave',
                'title' => 'رحلة مبنية على الوضوح والثقة',
                'description' => 'نؤمن في Travel Wave أن خدمات السفر والتأشيرات لا يجب أن تكون معقدة أو مرهقة. لذلك نصمم تجربة أكثر ترتيبًا ووضوحًا لمساعدة العملاء في التأشيرات الخارجية، والسياحة الداخلية، وحجوزات الطيران، وحجوزات الفنادق، مع اهتمام حقيقي بالتفاصيل وجودة الخدمة.',
                'image' => asset('storage/hero-slides/slide-2.svg'),
                'points' => [
                    'حلول متكاملة تبدأ من الاستفسار وحتى إتمام الخدمة.',
                    'لغة واضحة وخطوات عملية تناسب احتياج كل عميل.',
                    'تجربة أكثر احترافية في الحجوزات والملفات والمتابعة.',
                ],
            ],
            'mission' => [
                'enabled' => true,
                'eyebrow' => 'أساس العلامة',
                'title' => 'الرؤية والرسالة والقيم',
                'subtitle' => 'نصنع تجربة سفر أكثر رقيًا وتنظيمًا عبر خدمة واضحة، وتفاصيل مدروسة، واهتمام فعلي بما يحتاجه العميل.',
                'columns' => 'col-md-6 col-xl-4',
                'variant' => 'vision',
                'items' => [
                    [
                        'icon' => 'ر',
                        'title' => 'رؤيتنا',
                        'text' => 'أن تكون Travel Wave من الأسماء الأكثر ثقة في خدمات السفر والتأشيرات من خلال تجربة تجمع بين الفخامة والوضوح وسهولة التنفيذ.',
                    ],
                    [
                        'icon' => 'ر',
                        'title' => 'رسالتنا',
                        'text' => 'تبسيط رحلات العملاء وطلبات التأشيرات عبر تنظيم احترافي، وخيارات مناسبة، ودعم متواصل يرفع مستوى الثقة والراحة.',
                    ],
                    [
                        'icon' => 'ق',
                        'title' => 'قيمنا',
                        'text' => 'الوضوح، والالتزام، والدقة، وسرعة الاستجابة، والاهتمام بالتفاصيل التي تصنع فارقًا حقيقيًا في جودة الخدمة.',
                    ],
                ],
            ],
            'why_choose' => [
                'enabled' => true,
                'eyebrow' => 'لماذا نحن',
                'title' => 'لماذا يختارنا العملاء؟',
                'columns' => 'col-md-6 col-xl-4',
                'variant' => 'feature',
                'items' => [
                    ['icon' => 'خب', 'title' => 'خبرة في خدمات السفر والتأشيرات', 'text' => 'فهم أعمق لاحتياجات السفر والملفات والحجوزات يجعل التجربة أكثر سلاسة ووضوحًا.'],
                    ['icon' => 'مت', 'title' => 'متابعة دقيقة للملفات', 'text' => 'نهتم بالتفاصيل المهمة ونوضح للعميل ما ينقصه وما يحتاجه قبل أي خطوة أساسية.'],
                    ['icon' => 'حج', 'title' => 'تنظيم احترافي للحجوزات', 'text' => 'تنسيق أفضل بين الفنادق والطيران والخدمات المساندة بما يتوافق مع الهدف من الرحلة.'],
                    ['icon' => 'دع', 'title' => 'دعم مستمر للعملاء', 'text' => 'استجابة أوضح ومتابعة أكثر قربًا لتقليل التردد ورفع مستوى الثقة.'],
                    ['icon' => 'حل', 'title' => 'حلول متنوعة تناسب كل عميل', 'text' => 'نقترح خيارات مرنة تتناسب مع الميزانية، والوجهة، وطبيعة الخدمة المطلوبة.'],
                    ['icon' => 'سر', 'title' => 'سرعة ووضوح في الإجراءات', 'text' => 'نرتب الأولويات ونقدم خطوات عملية تساعد العميل على التحرك بشكل أسرع وأكثر راحة.'],
                ],
            ],
            'services' => [
                'enabled' => true,
                'eyebrow' => 'خدمات Travel Wave',
                'title' => 'نظرة على خدماتنا',
                'subtitle' => 'مجموعة خدمات متكاملة صممت لتمنح العميل تجربة سفر أكثر جودة وتنظيمًا من البداية وحتى آخر خطوة.',
                'columns' => 'col-md-6 col-xl-3',
                'variant' => 'service',
                'items' => [
                    ['icon' => 'تأ', 'title' => 'التأشيرات الخارجية', 'text' => 'مساعدة في تجهيز الملفات، ومراجعة المستندات، وتنسيق خطوات التقديم.', 'link_label' => 'استعرض الخدمة', 'url' => route('visas.index')],
                    ['icon' => 'سي', 'title' => 'السياحة الداخلية', 'text' => 'برامج محلية منظمة بترتيب أوضح للإقامة والتنقلات وخيارات الرحلة.', 'link_label' => 'استعرض الخدمة', 'url' => route('destinations.index')],
                    ['icon' => 'طي', 'title' => 'حجوزات الطيران', 'text' => 'حلول أكثر مرونة لحجز الرحلات المحلية والدولية ومتابعة بيانات الحجز.', 'link_label' => 'استعرض الخدمة', 'url' => route('flights')],
                    ['icon' => 'فن', 'title' => 'حجوزات الفنادق', 'text' => 'خيارات إقامة تناسب الوجهات المختلفة مع ترشيحات أكثر راحة ووضوحًا.', 'link_label' => 'استعرض الخدمة', 'url' => route('hotels')],
                ],
            ],
            'stats' => [
                'enabled' => true,
                'eyebrow' => 'مؤشرات الثقة',
                'title' => 'أرقام تعكس حجم الخبرة',
                'items' => [
                    ['value' => '+12K', 'label' => 'عميل تمت خدمته', 'text' => 'طلبات واستفسارات وخطط سفر تم التعامل معها باحترافية.'],
                    ['value' => '+35', 'label' => 'وجهة وخدمة نشطة', 'text' => 'بين التأشيرات، والرحلات، والخدمات المرتبطة بالسفر.'],
                    ['value' => '+8K', 'label' => 'طلبات تأشيرة', 'text' => 'ملفات جرى ترتيبها ومتابعتها بمستوى أوضح من التنظيم.'],
                    ['value' => '94%', 'label' => 'رضا العملاء', 'text' => 'مؤشر ثقة يعكس جودة التواصل والوضوح وسهولة المتابعة.'],
                ],
            ],
            'professionalism' => [
                'enabled' => true,
                'eyebrow' => 'احترافية العمل',
                'title' => 'فريق يعمل بعقلية الخدمة الراقية',
                'description' => 'نعتمد على أسلوب عمل يوازن بين السرعة والدقة والاهتمام بالتفاصيل، حتى يشعر العميل أن كل جزء من الرحلة أو الطلب يتم التعامل معه باحترافية واضحة.',
                'image' => asset('storage/hero-slides/slide-3.svg'),
                'points' => [
                    'فريق متخصص يفهم طبيعة الخدمات والملفات ومتطلبات التنفيذ.',
                    'تنسيق أوضح بين عناصر الخدمة المختلفة لتقليل التشتت.',
                    'اهتمام بالتفاصيل الصغيرة التي ترفع جودة التجربة بالكامل.',
                ],
                'reverse' => true,
            ],
            'cta' => [
                'enabled' => true,
                'eyebrow' => 'ابدأ رحلتك معنا',
                'title' => 'جاهز لتبدأ تجربة سفر أكثر وضوحًا واحترافية؟',
                'description' => 'تواصل مع Travel Wave ودعنا نساعدك في اختيار الخدمة المناسبة وترتيب الخطوة التالية بثقة أكبر.',
                'background_image' => asset('storage/hero-slides/slide-2.svg'),
                'buttons' => [
                    ['text' => 'ابدأ الآن', 'url' => route('contact'), 'variant' => 'primary'],
                    ['text' => 'تواصل معنا', 'url' => route('contact') . '#premium-contact-form', 'variant' => 'outline'],
                ],
            ],
        ];
    }

    protected function contactPageContent(): array
    {
        $settings = Setting::query()->first();
        $whatsAppUrl = $settings?->whatsapp_number
            ? 'https://wa.me/' . preg_replace('/\D+/', '', $settings->whatsapp_number)
            : route('contact');

        return [
            'direction' => 'rtl',
            'page_title' => 'تواصل معنا | Travel Wave',
            'hero' => [
                'enabled' => true,
                'eyebrow' => 'Travel Wave Support',
                'title' => 'تواصل معنا',
                'subtitle' => 'للاستفسارات، والحجوزات، وخدمات التأشيرات، وخطط السفر المختلفة، فريق Travel Wave جاهز لمساعدتك بخطوات أوضح واستجابة أسرع.',
                'background_image' => asset('storage/hero-slides/slide-2.svg'),
                'breadcrumbs' => [
                    ['label' => 'الرئيسية', 'url' => route('home')],
                    ['label' => 'تواصل معنا', 'url' => null],
                ],
            ],
            'contact_info' => [
                'enabled' => true,
                'eyebrow' => 'معلومات التواصل',
                'title' => 'اختر وسيلة التواصل الأنسب لك',
                'columns' => 'col-md-6 col-xl-4',
                'variant' => 'contact',
                'items' => array_values(array_filter([
                    $settings?->phone ? ['icon' => 'ها', 'title' => 'رقم الهاتف', 'text' => $settings->phone, 'meta' => $settings->secondary_phone, 'url' => 'tel:' . $settings->phone, 'link_label' => 'اتصل الآن'] : null,
                    $settings?->whatsapp_number ? ['icon' => 'وت', 'title' => 'واتساب', 'text' => $settings->whatsapp_number, 'meta' => 'رد أسرع للاستفسارات والمتابعة', 'url' => $whatsAppUrl, 'link_label' => 'راسلنا واتساب'] : null,
                    $settings?->contact_email ? ['icon' => 'بر', 'title' => 'البريد الإلكتروني', 'text' => $settings->contact_email, 'meta' => 'للطلبات والاستفسارات العامة', 'url' => 'mailto:' . $settings->contact_email, 'link_label' => 'أرسل بريدًا'] : null,
                    $settings?->localized('address') ? ['icon' => 'عن', 'title' => 'العنوان', 'text' => $settings->localized('address'), 'meta' => 'يسعدنا استقبال استفساراتك ومساعدتك', 'url' => '#contact-location', 'link_label' => 'اعرض الموقع'] : null,
                    $settings?->localized('working_hours') ? ['icon' => 'دو', 'title' => 'ساعات العمل', 'text' => $settings->localized('working_hours'), 'meta' => 'مواعيد الدعم والمتابعة المعتادة', 'url' => '#premium-contact-form', 'link_label' => 'أرسل طلبك'] : null,
                ])),
            ],
            'form' => [
                'enabled' => true,
                'eyebrow' => 'نموذج التواصل',
                'title' => 'أرسل استفسارك إلى Travel Wave',
                'subtitle' => 'اكتب تفاصيل طلبك وسنساعدك في اختيار الخدمة المناسبة وترتيب الخطوة التالية بطريقة أوضح.',
                'checklist' => [
                    'مساعدة في التأشيرات الخارجية ومتطلبات الملفات.',
                    'تنسيق حجوزات الرحلات الداخلية والطيران والفنادق.',
                    'متابعة أوضح للطلبات والاستفسارات الخاصة.',
                ],
                'type' => 'contact',
                'source' => 'Contact Us',
                'config' => [
                    'title' => 'ابدأ رسالتك الآن',
                    'subtitle' => 'املأ البيانات الأساسية وسيتواصل معك فريق Travel Wave.',
                    'submit_text' => 'أرسل الآن',
                    'success_message' => 'تم إرسال رسالتك بنجاح، وسيتواصل معك فريق Travel Wave قريبًا.',
                    'visible_fields' => ['email', 'service_type', 'destination', 'message'],
                    'labels' => [
                        'full_name' => 'الاسم',
                        'phone' => 'رقم الهاتف',
                        'email' => 'البريد الإلكتروني',
                        'service_type' => 'نوع الخدمة',
                        'destination' => 'الوجهة',
                        'message' => 'الرسالة / ملاحظات',
                    ],
                    'placeholders' => [
                        'full_name' => 'اكتب اسمك الكامل',
                        'phone' => 'اكتب رقم الهاتف',
                        'email' => 'example@email.com',
                        'destination' => 'مثال: فرنسا، شرم الشيخ، دبي',
                        'message' => 'اكتب تفاصيل طلبك أو استفسارك',
                    ],
                    'field_options' => [
                        'service_type' => [
                            ['value' => 'التأشيرات الخارجية', 'label' => 'التأشيرات الخارجية'],
                            ['value' => 'السياحة الداخلية', 'label' => 'السياحة الداخلية'],
                            ['value' => 'حجوزات الطيران', 'label' => 'حجوزات الطيران'],
                            ['value' => 'حجوزات الفنادق', 'label' => 'حجوزات الفنادق'],
                            ['value' => 'طلب مخصص', 'label' => 'طلب مخصص'],
                        ],
                    ],
                ],
            ],
            'quick_help' => [
                'enabled' => true,
                'eyebrow' => 'كيف نساعدك؟',
                'title' => 'يمكنك التواصل معنا بخصوص',
                'columns' => 'col-md-6 col-xl-4',
                'variant' => 'help',
                'items' => [
                    ['icon' => 'تأ', 'title' => 'الاستفسار عن التأشيرات', 'text' => 'مراجعة الطلبات والمستندات وخيارات التقديم والمتابعة.'],
                    ['icon' => 'دا', 'title' => 'حجز رحلات داخلية', 'text' => 'برامج محلية، وإقامة، وتنقلات، وخيارات تناسب طبيعة الرحلة.'],
                    ['icon' => 'طي', 'title' => 'حجز طيران', 'text' => 'مساعدة في اختيار المسارات المناسبة والبيانات الأساسية للحجز.'],
                    ['icon' => 'فن', 'title' => 'حجز فنادق', 'text' => 'ترشيحات إقامة مناسبة حسب الوجهة والميزانية ومستوى الراحة المطلوب.'],
                    ['icon' => 'مت', 'title' => 'متابعة الطلبات', 'text' => 'توضيح الخطوات التالية وتأكيد ما يلزم لاستكمال الخدمة بوضوح أكبر.'],
                ],
            ],
            'map' => [
                'enabled' => filled($settings?->map_iframe),
                'eyebrow' => 'الموقع',
                'title' => 'زورنا أو استخدم الموقع كمرجع',
                'description' => 'يمكنك استخدام بيانات الموقع للتواصل أو للوصول إلى المكتب عند الحاجة إلى زيارة مباشرة أو متابعة خاصة.',
                'embed_code' => $settings?->map_iframe,
                'details' => array_values(array_filter([
                    $settings?->localized('address') ? ['label' => 'العنوان', 'value' => $settings->localized('address')] : null,
                    $settings?->localized('working_hours') ? ['label' => 'ساعات العمل', 'value' => $settings->localized('working_hours')] : null,
                ])),
            ],
            'faq' => [
                'enabled' => true,
                'eyebrow' => 'إجابات سريعة',
                'title' => 'أسئلة شائعة حول التواصل',
                'items' => [
                    ['question' => 'ما أوقات العمل؟', 'answer' => $settings?->localized('working_hours') ?: 'تتوفر خدمة المتابعة خلال ساعات العمل الرسمية، مع استجابة سريعة للاستفسارات المتاحة.'],
                    ['question' => 'كيف أتواصل بسرعة؟', 'answer' => 'أسرع وسيلة للتواصل عادة تكون عبر الهاتف أو واتساب حسب طبيعة الاستفسار والخدمة المطلوبة.'],
                    ['question' => 'هل يمكن التواصل عبر واتساب؟', 'answer' => 'نعم، يمكن استخدام واتساب للاستفسارات السريعة وبدء الطلبات ومتابعة التفاصيل الأساسية.'],
                    ['question' => 'هل يمكن طلب خدمة مخصصة؟', 'answer' => 'نعم، يمكن إرسال طلب خاص أو خدمة مركبة وسيقوم فريق Travel Wave بتوضيح الخيارات المناسبة.'],
                    ['question' => 'متى يتم الرد على الاستفسارات؟', 'answer' => 'يتم الرد بأسرع وقت ممكن حسب توقيت الاستفسار ونوع الخدمة المطلوبة.'],
                ],
            ],
            'cta' => [
                'enabled' => true,
                'eyebrow' => 'ابدأ التواصل الآن',
                'title' => 'هل ترغب في رد أسرع وخطوة أوضح؟',
                'description' => 'تواصل مع Travel Wave الآن ودعنا نساعدك في ترتيب الخدمة المناسبة لك بثقة أكبر وتجربة أكثر راحة.',
                'background_image' => asset('storage/hero-slides/slide-1.svg'),
                'buttons' => [
                    ['text' => 'راسلنا الآن', 'url' => $whatsAppUrl, 'variant' => 'primary'],
                    ['text' => 'ابدأ طلبك', 'url' => '#premium-contact-form', 'variant' => 'outline'],
                ],
            ],
        ];
    }

    // Service page content lives here for now, separated from the shared Blade layout.
    // This makes it straightforward to move these arrays later into DB/admin/CMS storage.
    protected function domesticServicePage($destinations): array
    {
        $popular = $destinations->take(6)->values();

        return [
            'theme' => 'domestic',
            'title' => 'السياحة الداخلية',
            'hero' => [
                'badge' => 'رحلات داخلية بروح مميزة',
                'title' => 'السياحة الداخلية',
                'text' => 'استمتع بأفضل الوجهات المحلية مع برامج منظمة وتجارب مريحة تجمع بين الاسترخاء، الترفيه، وسهولة الحجز من خلال Travel Wave.',
                'primary_cta' => ['label' => 'احجز رحلتك الآن', 'url' => '#service-contact'],
                'secondary_cta' => ['label' => 'استعرض البرامج', 'url' => '#service-packages'],
                'image' => $popular->first()?->hero_image ? asset('storage/' . $popular->first()->hero_image) : null,
            ],
            'search' => [
                'fields' => [
                    ['name' => 'destination', 'label' => 'الوجهة', 'options' => $popular->map(fn ($item) => ['label' => $item->localized('title'), 'url' => route('destinations.show', $item)])->all()],
                    ['name' => 'duration', 'label' => 'مدة الرحلة', 'options' => [['label' => '3 أيام', 'url' => route('destinations.index')], ['label' => '4 أيام', 'url' => route('destinations.index')], ['label' => '5 أيام', 'url' => route('destinations.index')], ['label' => 'أسبوع كامل', 'url' => route('destinations.index')]]],
                    ['name' => 'trip_type', 'label' => 'نوع الرحلة', 'options' => [['label' => 'عائلية', 'url' => route('destinations.index')], ['label' => 'شواطئ', 'url' => route('destinations.index')], ['label' => 'استرخاء ومنتجعات', 'url' => route('destinations.index')], ['label' => 'ثقافية', 'url' => route('destinations.index')]]],
                ],
                'button' => 'ابحث الآن',
                'default_url' => route('destinations.index'),
            ],
            'popular' => [
                'eyebrow' => 'وجهات داخلية مميزة',
                'title' => 'أشهر الوجهات المحلية',
                'text' => 'وجهات مختارة بعناية تجمع بين الراحة والتنظيم والبرامج المناسبة للأفراد والعائلات.',
                'items' => $popular->map(fn ($item, $index) => [
                    'title' => $item->localized('title'),
                    'subtitle' => ['رحلات بحرية واستجمام', 'إجازات عائلية مرنة', 'برامج موسمية مميزة', 'إقامة مريحة وتجارب متنوعة', 'رحلات قصيرة وسريعة', 'وجهة مثالية للاسترخاء'][$index % 6],
                    'meta' => ['3 ليالٍ / 4 أيام', 'إقامة وتنقلات', 'أسعار تبدأ من 4,500 جنيه', 'حجوزات مؤكدة', 'عروض موسمية', 'برامج مرنة'][$index % 6],
                    'image' => $item->hero_image ? asset('storage/' . $item->hero_image) : null,
                    'button' => 'عرض التفاصيل',
                    'url' => route('destinations.show', $item),
                    'badge' => ['رحلة داخلية', 'الأكثر طلباً', 'شامل الإقامة', 'برنامج مرن'][$index % 4],
                ])->all(),
            ],
            'features_title' => 'لماذا تختار برامجنا الداخلية؟',
            'features' => [
                ['tag' => '01', 'title' => 'برامج متنوعة', 'text' => 'خيارات متعددة تناسب الأزواج والعائلات والمجموعات وبرامج الراحة السريعة.'],
                ['tag' => '02', 'title' => 'تنظيم كامل', 'text' => 'ترتيب شامل للإقامة والتنقلات والتفاصيل الأساسية لتجربة أكثر راحة.'],
                ['tag' => '03', 'title' => 'حجوزات مضمونة', 'text' => 'تأكيدات واضحة للفنادق والخدمات وفق البرنامج المختار.'],
                ['tag' => '04', 'title' => 'أسعار مناسبة', 'text' => 'باقات مدروسة تجمع بين القيمة والجودة والمرونة.'],
                ['tag' => '05', 'title' => 'متابعة مستمرة', 'text' => 'فريقنا يتابع معك من لحظة الحجز وحتى بدء الرحلة.'],
                ['tag' => '06', 'title' => 'عروض موسمية', 'text' => 'فرص متجددة على الوجهات المطلوبة في أفضل المواسم.'],
            ],
            'packages' => [
                'title' => 'برامج داخلية مميزة',
                'items' => [
                    ['title' => 'باقة شرم الشيخ الذهبية', 'meta' => '4 أيام / 3 ليالٍ', 'highlights' => ['فندق 5 نجوم', 'إفطار وعشاء', 'تنقلات داخلية'], 'price' => 'تبدأ من 6,900 جنيه', 'button' => 'عرض الباقة'],
                    ['title' => 'باقة الغردقة العائلية', 'meta' => '5 أيام / 4 ليالٍ', 'highlights' => ['برنامج عائلي', 'شاطئ خاص', 'أنشطة يومية'], 'price' => 'تبدأ من 8,250 جنيه', 'button' => 'عرض الباقة'],
                    ['title' => 'باقة الأقصر وأسوان', 'meta' => '6 أيام / 5 ليالٍ', 'highlights' => ['مزج ثقافي وترفيهي', 'إقامة مريحة', 'برنامج منظم'], 'price' => 'تبدأ من 9,800 جنيه', 'button' => 'عرض الباقة'],
                ],
            ],
            'steps' => ['اختر الوجهة', 'حدد البرنامج', 'أكمل الحجز', 'استلم التأكيد', 'استعد للرحلة'],
            'steps_title' => 'خطوات الحجز',
            'grid' => [
                'title' => 'وجهات سياحية داخلية',
                'items' => $destinations->take(10)->map(fn ($item) => [
                    'title' => $item->localized('title'),
                    'chip' => 'رحلة داخلية',
                    'text' => $item->localized('excerpt') ?: 'وجهة مريحة تناسب برامج الاستجمام والرحلات العائلية.',
                    'url' => route('destinations.show', $item),
                ])->all(),
            ],
            'quick_info' => [
                ['title' => 'أفضل أوقات السفر', 'value' => 'الربيع والصيف والعطلات الطويلة تعتبر من أفضل الفترات للبرامج الداخلية.'],
                ['title' => 'مدة البرامج', 'value' => 'من رحلات قصيرة لثلاثة أيام حتى برامج كاملة لأسبوع أو أكثر.'],
                ['title' => 'طرق الحجز', 'value' => 'عن طريق الهاتف أو الواتساب أو طلب الحجز المباشر من الصفحة.'],
                ['title' => 'العروض المتاحة', 'value' => 'عروض موسمية وبرامج مخصصة للمجموعات والعائلات.'],
            ],
            'cta' => [
                'eyebrow' => 'رحلتك القادمة تبدأ هنا',
                'title' => 'احجز رحلتك الداخلية القادمة الآن',
                'text' => 'استمتع ببرنامج محلي منظم وخيارات إقامة مميزة وتجربة حجز أكثر راحة مع Travel Wave.',
                'primary' => ['label' => 'احجز الآن', 'url' => '#service-contact'],
                'secondary' => ['label' => 'تواصل واتساب', 'url' => 'https://wa.me/201000000000'],
            ],
            'faqs' => [
                ['q' => 'ما أفضل الوجهات المتاحة؟', 'a' => 'تختلف الأفضلية حسب نوع الرحلة، لكن شرم الشيخ والغردقة والساحل الشمالي والأقصر من الوجهات الأكثر طلباً.'],
                ['q' => 'هل البرامج تشمل الإقامة؟', 'a' => 'نعم، كثير من البرامج تشمل الإقامة ويمكن توضيح مستوى الفندق والخدمات عند الاختيار.'],
                ['q' => 'هل يوجد رحلات عائلية؟', 'a' => 'نعم، تتوفر برامج مصممة خصيصاً للعائلات مع خيارات أكثر مرونة في الإقامة والأنشطة.'],
                ['q' => 'هل يمكن تعديل البرنامج؟', 'a' => 'في كثير من الحالات يمكن تكييف البرنامج وفق المدة والميزانية ونوع الرحلة.'],
                ['q' => 'ما طريقة الحجز؟', 'a' => 'يمكن طلب الحجز من خلال النموذج أو التواصل المباشر مع فريق Travel Wave.'],
            ],
            'contact' => [
                'title' => 'ابدأ حجز رحلتك الداخلية',
                'text' => 'أرسل بياناتك وسنساعدك في اختيار الوجهة والبرنامج الأنسب لك.',
                'checklist' => ['اقتراح أفضل برنامج', 'تنسيق الحجز والإقامة', 'متابعة حتى التأكيد النهائي'],
                'type' => 'destination',
                'source' => 'Domestic Tourism',
                'fields' => [
                    ['name' => 'full_name', 'label' => 'الاسم', 'type' => 'text', 'required' => true],
                    ['name' => 'phone', 'label' => 'رقم الهاتف', 'type' => 'text', 'required' => true],
                    ['name' => 'destination', 'label' => 'الوجهة المطلوبة', 'type' => 'select', 'options' => $destinations->take(10)->map(fn ($item) => ['value' => $item->localized('title'), 'label' => $item->localized('title')])->all()],
                    ['name' => 'travelers_count', 'label' => 'عدد الأفراد', 'type' => 'number'],
                    ['name' => 'travel_date', 'label' => 'تاريخ السفر', 'type' => 'date'],
                    ['name' => 'message', 'label' => 'ملاحظات', 'type' => 'textarea'],
                ],
            ],
        ];
    }

    // External visa page content is centralized here instead of being hardcoded in the template.
    protected function visaServicePage($categories, $featuredCountries): array
    {
        $countries = $categories->flatMap->countries->unique('id')->sortBy('sort_order')->values();
        $popularCountries = $featuredCountries->isNotEmpty() ? $featuredCountries->take(6)->values() : $countries->take(6)->values();
        $heroCountry = $popularCountries->first() ?: $countries->first();
        $heroImage = $heroCountry?->hero_image
            ? asset('storage/' . $heroCountry->hero_image)
            : ($heroCountry?->intro_image ? asset('storage/' . $heroCountry->intro_image) : null);

        return [
            'page_title' => 'خدمات التأشيرات الخارجية',
            'direction' => 'rtl',
            'hero' => [
                'enabled' => true,
                'badge' => 'منصة تأشيرات احترافية',
                'title' => 'خدمات التأشيرات الخارجية',
                'subtitle' => 'نجعل رحلة التقديم أوضح وأسهل من أول استشارة حتى تجهيز الملف والحجوزات والمتابعة، بأسلوب احترافي يليق بعلامة Travel Wave.',
                'background_image' => $heroImage,
                'buttons' => [
                    ['label' => 'ابدأ الآن', 'url' => '#service-form', 'variant' => 'primary'],
                    ['label' => 'استعرض الوجهات', 'url' => '#service-featured', 'variant' => 'outline'],
                ],
                'metrics' => [
                    ['value' => '+24', 'label' => 'وجهة متاحة', 'text' => 'خيارات واسعة لتأشيرات أوروبا وآسيا والعالم العربي وأمريكا الشمالية.'],
                    ['value' => '15-30', 'label' => 'يوم عمل', 'text' => 'مدد معالجة تقريبية أوضح حسب كل وجهة واكتمال الملف.'],
                    ['value' => '360°', 'label' => 'دعم كامل', 'text' => 'مراجعة ملف، حجوزات، إرشاد، ومتابعة حتى مرحلة التقديم.'],
                ],
            ],
            'search_box' => [
                'enabled' => true,
                'default_url' => route('visas.index'),
                'button' => 'ابحث الآن',
                'fields' => [
                    ['name' => 'service_type', 'label' => 'نوع الخدمة', 'placeholder' => 'اختر نوع الخدمة', 'options' => [['label' => 'تأشيرة سياحية', 'url' => route('visas.index')], ['label' => 'زيارة عائلية', 'url' => route('visas.index')], ['label' => 'تأشيرة أعمال', 'url' => route('visas.index')], ['label' => 'حجز موعد ومتابعة', 'url' => route('visas.index')]]],
                    ['name' => 'destination', 'label' => 'الوجهة', 'placeholder' => 'اختر الوجهة', 'options' => $countries->map(fn ($country) => ['label' => $country->localized('name'), 'url' => route('visas.country', $country)])->all()],
                    ['name' => 'visa_type', 'label' => 'نوع التأشيرة', 'placeholder' => 'اختر نوع التأشيرة', 'options' => [['label' => 'شنغن قصيرة الإقامة', 'url' => route('visas.index')], ['label' => 'زيارة عائلية', 'url' => route('visas.index')], ['label' => 'رحلات أعمال', 'url' => route('visas.index')], ['label' => 'متعددة السفرات', 'url' => route('visas.index')]]],
                ],
            ],
            'featured_section' => [
                'enabled' => true,
                'section_id' => 'service-featured',
                'eyebrow' => 'الوجهات الأكثر طلبًا',
                'title' => 'أشهر وجهات التأشيرات',
                'subtitle' => 'بطاقات مختارة لوجهات يطلبها العملاء باستمرار مع معلومات سريعة ورابط مباشر للتفاصيل.',
                'slider' => ['autoplay' => true, 'interval' => 3600],
                'items' => $popularCountries->map(fn ($country, $index) => [
                    'title' => $country->localized('name'),
                    'subtitle' => $country->localized('visa_type') ?: 'خدمة تأشيرة خارجية',
                    'meta' => $country->localized('processing_time') ?: 'حوالي 15 إلى 30 يوم عمل',
                    'badge' => ['الأكثر طلبًا', 'شنغن', 'إقامة قصيرة', 'متابعة احترافية'][$index % 4],
                    'image' => $country->hero_image ? asset('storage/' . $country->hero_image) : ($country->intro_image ? asset('storage/' . $country->intro_image) : null),
                    'button' => 'عرض التفاصيل',
                    'url' => route('visas.country', $country),
                ])->all(),
            ],
            'features_section' => [
                'enabled' => true,
                'eyebrow' => 'لماذا Travel Wave',
                'title' => 'لماذا يختارنا العملاء في خدمات التأشيرات',
                'subtitle' => 'خدمة واضحة ومرتبة تمنح العميل ثقة أعلى وتجربة أكثر احترافية في كل خطوة.',
                'items' => [
                    ['tag' => '01', 'title' => 'مراجعة المستندات', 'text' => 'فحص الملف وتحديد النواقص ونقاط التحسين قبل التقديم.'],
                    ['tag' => '02', 'title' => 'متابعة الملف', 'text' => 'متابعة منظمة لكل مرحلة من التجهيز وحتى ما بعد التقديم.'],
                    ['tag' => '03', 'title' => 'تنسيق الحجوزات', 'text' => 'مساعدة في ترتيب الطيران والفنادق بما يدعم ملف الرحلة.'],
                    ['tag' => '04', 'title' => 'تنظيم برنامج الرحلة', 'text' => 'بناء تصور أوضح للرحلة والمدة والهدف بما يعزز الملف.'],
                    ['tag' => '05', 'title' => 'سرعة في التنفيذ', 'text' => 'خطوات أوضح وتجهيز أسرع للنماذج والمستندات الأساسية.'],
                    ['tag' => '06', 'title' => 'دعم كامل حتى التقديم', 'text' => 'إرشاد واضح وحجز موعد ومتابعة مستمرة حتى آخر خطوة.'],
                ],
            ],
            'cards_section' => [
                'enabled' => true,
                'eyebrow' => 'خدمات داعمة',
                'title' => 'باقات خدمة مرنة حسب احتياجك',
                'subtitle' => 'اختر مستوى الدعم الأنسب لرحلتك ونوع التأشيرة المطلوبة.',
                'items' => [
                    ['title' => 'مراجعة الملف قبل التقديم', 'meta' => 'للملفات الجاهزة التي تحتاج مراجعة دقيقة', 'highlights' => ['مراجعة المستندات', 'تنسيق بيانات الرحلة', 'ملاحظات واضحة قبل التقديم'], 'price' => 'يحدد حسب الوجهة وحجم الملف', 'button' => 'اطلب الخدمة', 'url' => '#service-form'],
                    ['title' => 'خدمة تجهيز كاملة', 'meta' => 'من أول خطوة حتى ترتيب المستندات الأساسية', 'highlights' => ['إرشاد كامل', 'تنظيم الملف', 'تنسيق الحجوزات والمتطلبات'], 'price' => 'خطة مرنة حسب نوع التأشيرة', 'button' => 'اطلب الخدمة', 'url' => '#service-form'],
                    ['title' => 'دعم المواعيد والمتابعة', 'meta' => 'لمن يحتاج دعماً عملياً في الموعد والإجراءات', 'highlights' => ['حجز موعد إذا أمكن', 'متابعة الخطوات', 'إجابات أوضح قبل التقديم'], 'price' => 'يحدد حسب الوجهة والمركز', 'button' => 'اطلب الخدمة', 'url' => '#service-form'],
                ],
            ],
            'steps_section' => [
                'enabled' => true,
                'eyebrow' => 'خطوات واضحة',
                'title' => 'كيف تسير الخدمة معنا',
                'subtitle' => 'رحلة منظمة تمنحك وضوحًا أكبر من أول تواصل وحتى مرحلة التقديم والمتابعة.',
                'items' => [
                    ['title' => 'اختر الوجهة', 'text' => 'نحدد معك الدولة المناسبة ونوع التأشيرة الأنسب لسبب السفر.'],
                    ['title' => 'أرسل المستندات', 'text' => 'تشاركنا المستندات الأساسية والبيانات المهمة الخاصة بالرحلة.'],
                    ['title' => 'مراجعة الملف', 'text' => 'نفحص الملف ونوضح المطلوب استكماله أو تحسينه قبل التقديم.'],
                    ['title' => 'حجز الموعد', 'text' => 'نرتب خطوة الموعد ونجهزك لما قبل التقديم حسب الوجهة.'],
                    ['title' => 'التقديم والمتابعة', 'text' => 'تستكمل التقديم بثقة مع متابعة أوضح لحالة الطلب والخطوات التالية.'],
                ],
            ],
            'grid_section' => [
                'enabled' => true,
                'eyebrow' => 'شبكة الوجهات',
                'title' => 'وجهات يمكنك البدء بها الآن',
                'subtitle' => 'مجموعة من أشهر الوجهات الخارجية التي يمكن إدارة ملفها من خلال Travel Wave.',
                'items' => $countries->take(10)->map(fn ($country) => ['title' => $country->localized('name'), 'chip' => $country->localized('visa_type') ?: 'خدمة تأشيرة', 'text' => $country->localized('excerpt') ?: 'خدمة متكاملة لتجهيز الملف وتوضيح الخطوات الأساسية قبل التقديم.', 'url' => route('visas.country', $country)])->all(),
            ],
            'quick_info_section' => [
                'enabled' => true,
                'eyebrow' => 'معلومات سريعة',
                'title' => 'أهم ما يحتاجه العميل قبل البدء',
                'items' => [
                    ['title' => 'مدة المعالجة', 'value' => 'غالبًا من 15 إلى 30 يوم عمل حسب الوجهة والموسم.', 'tone' => 'navy'],
                    ['title' => 'الرسوم', 'value' => 'تتحدد حسب الدولة، الرسوم القنصلية، ومستوى الخدمة المطلوب.', 'tone' => 'royal'],
                    ['title' => 'المستندات المطلوبة', 'value' => 'جواز سفر، صور، مستندات مالية، حجوزات، وأوراق داعمة حسب الحالة.', 'tone' => 'amber'],
                    ['title' => 'سهولة الملف', 'value' => 'ترتفع مع اكتمال البيانات وتناسق المستندات وخطة الرحلة.', 'tone' => 'slate'],
                ],
            ],
            'cta_section' => [
                'enabled' => true,
                'eyebrow' => 'ابدأ بثقة',
                'title' => 'ابدأ ملف التأشيرة بخطوات أوضح ودعم أكثر احترافية',
                'description' => 'نمنحك تجربة أكثر ترتيبًا وراحة من أول استشارة حتى تجهيز الملف والحجوزات وشرح الرسوم والمدة المتوقعة.',
                'buttons' => [
                    ['label' => 'احجز استشارتك الآن', 'url' => '#service-form', 'variant' => 'primary'],
                    ['label' => 'تواصل واتساب', 'url' => 'https://wa.me/201000000000', 'variant' => 'light-outline'],
                ],
            ],
            'faq_section' => [
                'enabled' => true,
                'eyebrow' => 'الأسئلة الشائعة',
                'title' => 'إجابات سريعة قبل أن تبدأ',
                'subtitle' => 'مجموعة أسئلة شائعة تساعد العميل على فهم ما ينتظره قبل تجهيز الملف.',
                'items' => [
                    ['question' => 'ما مدة استخراج التأشيرة؟', 'answer' => 'تختلف حسب الدولة والموسم واكتمال الملف، لكن كثيرًا من الوجهات تقع بين 15 و30 يوم عمل.'],
                    ['question' => 'ما الأوراق المطلوبة؟', 'answer' => 'يعتمد ذلك على الوجهة ونوع التأشيرة، لكن الأساس يشمل الجواز والصور والمستندات المالية والحجوزات.'],
                    ['question' => 'هل يوجد متابعة بعد التقديم؟', 'answer' => 'نعم، يتم إرشادك لما بعد التقديم مع متابعة أوضح للخطوات التالية عند الحاجة.'],
                    ['question' => 'هل يمكن المساعدة في الحجوزات؟', 'answer' => 'نعم، يمكن المساعدة في تنسيق الطيران والفنادق بما يناسب ملف الرحلة.'],
                    ['question' => 'ما أفضل وقت للتقديم؟', 'answer' => 'كلما كان التقديم مبكرًا كان أفضل، خصوصًا قبل المواسم المزدحمة أو عند محدودية المواعيد.'],
                ],
            ],
            'form_section' => [
                'enabled' => true,
                'eyebrow' => 'طلب استشارة',
                'title' => 'ابدأ معنا بطلب واضح وسريع',
                'subtitle' => 'اترك بياناتك وسيتواصل معك فريق Travel Wave لتحديد الخطوات المناسبة للوجهة ونوع التأشيرة.',
                'checklist' => ['استشارة أولية مباشرة', 'توجيه للمستندات والحجوزات', 'متابعة بعد إرسال الطلب'],
                'type' => 'visa',
                'source' => 'External Visa Services',
                'submit_text' => 'أرسل الطلب',
                'fields' => [
                    ['name' => 'full_name', 'label' => 'الاسم', 'type' => 'text', 'required' => true, 'placeholder' => 'اكتب الاسم الكامل'],
                    ['name' => 'phone', 'label' => 'رقم الهاتف', 'type' => 'text', 'required' => true, 'placeholder' => 'رقم الهاتف أو واتساب'],
                    ['name' => 'destination', 'label' => 'الوجهة', 'type' => 'select', 'placeholder' => 'اختر الوجهة', 'options' => $countries->map(fn ($country) => ['value' => $country->localized('name'), 'label' => $country->localized('name')])->all()],
                    ['name' => 'service_type', 'label' => 'نوع التأشيرة', 'type' => 'select', 'placeholder' => 'اختر النوع', 'options' => [['value' => 'تأشيرة سياحية', 'label' => 'تأشيرة سياحية'], ['value' => 'زيارة عائلية', 'label' => 'زيارة عائلية'], ['value' => 'تأشيرة أعمال', 'label' => 'تأشيرة أعمال'], ['value' => 'شنغن', 'label' => 'شنغن']]],
                    ['name' => 'message', 'label' => 'ملاحظات', 'type' => 'textarea', 'placeholder' => 'اكتب أي تفاصيل مهمة عن الرحلة أو الملف'],
                ],
            ],
        ];
    }

    protected function flightServicePage(): array
    {
        return [
            'theme' => 'flights',
            'title' => 'الطيران',
            'hero' => [
                'badge' => 'حلول حجز سريعة وموثوقة',
                'title' => 'حجوزات الطيران',
                'text' => 'نوفّر لك خيارات محلية ودولية بترتيب أسرع ودعم أوضح، لتصل إلى الرحلة المناسبة بالسعر والخدمة التي تناسبك.',
                'primary_cta' => ['label' => 'احجز رحلتك', 'url' => '#service-contact'],
                'secondary_cta' => ['label' => 'استعرض الوجهات', 'url' => '#service-popular'],
                'image' => null,
            ],
            'search' => [
                'fields' => [
                    ['name' => 'from', 'label' => 'من', 'options' => [['label' => 'القاهرة', 'url' => route('flights')], ['label' => 'الإسكندرية', 'url' => route('flights')], ['label' => 'جدة', 'url' => route('flights')], ['label' => 'دبي', 'url' => route('flights')]]],
                    ['name' => 'to', 'label' => 'إلى', 'options' => [['label' => 'جدة', 'url' => route('flights')], ['label' => 'دبي', 'url' => route('flights')], ['label' => 'إسطنبول', 'url' => route('flights')], ['label' => 'باريس', 'url' => route('flights')], ['label' => 'لندن', 'url' => route('flights')]]],
                    ['name' => 'travel_date', 'label' => 'تاريخ السفر', 'options' => [['label' => 'هذا الأسبوع', 'url' => route('flights')], ['label' => 'الأسبوع القادم', 'url' => route('flights')], ['label' => 'هذا الشهر', 'url' => route('flights')]]],
                    ['name' => 'travelers', 'label' => 'عدد المسافرين', 'options' => [['label' => '1 مسافر', 'url' => route('flights')], ['label' => '2 مسافر', 'url' => route('flights')], ['label' => '3 مسافرين', 'url' => route('flights')], ['label' => '4+ مسافرين', 'url' => route('flights')]]],
                ],
                'button' => 'ابحث عن رحلة',
                'default_url' => route('flights'),
            ],
            'popular' => [
                'eyebrow' => 'خطوط مطلوبة',
                'title' => 'أشهر مسارات الطيران',
                'text' => 'مسارات متكررة وحجوزات مناسبة للأفراد والعائلات ورحلات الأعمال.',
                'items' => collect([
                    ['title' => 'القاهرة → جدة', 'subtitle' => 'رحلات مباشرة وموسمية', 'meta' => 'أسعار تبدأ من 8,900 جنيه', 'badge' => 'الأكثر طلباً'],
                    ['title' => 'القاهرة → دبي', 'subtitle' => 'خيارات متنوعة على مدار الأسبوع', 'meta' => 'أسعار تبدأ من 9,500 جنيه', 'badge' => 'مرن'],
                    ['title' => 'القاهرة → الرياض', 'subtitle' => 'حلول مناسبة لرحلات العمل والزيارات', 'meta' => 'أسعار تبدأ من 8,700 جنيه', 'badge' => 'سريع'],
                    ['title' => 'القاهرة → إسطنبول', 'subtitle' => 'وجهة شائعة للسياحة والتسوق', 'meta' => 'أسعار تبدأ من 10,200 جنيه', 'badge' => 'رحلات يومية'],
                    ['title' => 'القاهرة → باريس', 'subtitle' => 'خيارات أوروبية مميزة', 'meta' => 'أسعار تبدأ من 16,800 جنيه', 'badge' => 'دولي'],
                    ['title' => 'القاهرة → لندن', 'subtitle' => 'رحلات منتظمة مع خيارات متعددة', 'meta' => 'أسعار تبدأ من 18,500 جنيه', 'badge' => 'ممتاز'],
                ])->map(fn ($item) => $item + ['button' => 'عرض التفاصيل', 'url' => '#service-contact', 'image' => null])->all(),
            ],
            'features_title' => 'لماذا تحجز الطيران معنا؟',
            'features' => [
                ['tag' => '01', 'title' => 'أفضل الخيارات', 'text' => 'نعرض لك المسارات والبدائل المناسبة حسب الموعد والميزانية.'],
                ['tag' => '02', 'title' => 'دعم سريع', 'text' => 'استجابة أسرع للاستفسارات والحجوزات والتعديلات الممكنة.'],
                ['tag' => '03', 'title' => 'أسعار تنافسية', 'text' => 'خيارات مدروسة ومناسبة للأفراد والعائلات والسفر المتكرر.'],
                ['tag' => '04', 'title' => 'متابعة الحجز', 'text' => 'نؤكد معك البيانات الأساسية ونرتب خطوات الحجز بوضوح.'],
                ['tag' => '05', 'title' => 'حجوزات مرنة', 'text' => 'مساعدة في اختيار الأنسب بين الرحلات المباشرة وغير المباشرة.'],
                ['tag' => '06', 'title' => 'خدمة موثوقة', 'text' => 'تنفيذ أدق للحجوزات مع وضوح أكبر في التفاصيل والمتطلبات.'],
            ],
            'packages' => [
                'title' => 'خدمات الطيران',
                'items' => [
                    ['title' => 'رحلات داخلية', 'meta' => 'للمدن المحلية والانتقالات السريعة', 'highlights' => ['مواعيد متنوعة', 'حجز واضح', 'خيارات اقتصادية'], 'price' => 'أسعار مرنة حسب الموعد', 'button' => 'اطلب الخدمة'],
                    ['title' => 'رحلات دولية', 'meta' => 'للسياحة والأعمال والزيارات', 'highlights' => ['وجهات متعددة', 'دعم في الاختيار', 'تنسيق أوضح'], 'price' => 'عروض حسب الوجهة', 'button' => 'اطلب الخدمة'],
                    ['title' => 'حجوزات عائلية وأعمال', 'meta' => 'تنسيق أفضل للمجموعات والملفات المنظمة', 'highlights' => ['أسماء وبيانات دقيقة', 'متابعة الحجز', 'حلول مرنة'], 'price' => 'تسعير حسب العدد والخدمة', 'button' => 'اطلب الخدمة'],
                ],
            ],
            'steps' => ['اختر خط السير', 'حدد الموعد', 'اختر عدد المسافرين', 'أكد البيانات', 'استلم الحجز'],
            'steps_title' => 'خطوات حجز الطيران',
            'grid' => [
                'title' => 'خدمات الطيران المتاحة',
                'items' => [
                    ['title' => 'رحلات داخلية', 'chip' => 'محلي', 'text' => 'حلول مرنة للانتقال بين المدن المحلية بسرعة ووضوح.', 'url' => '#service-contact'],
                    ['title' => 'رحلات دولية', 'chip' => 'دولي', 'text' => 'وجهات خارجية متعددة مع خيارات تناسب التوقيت والميزانية.', 'url' => '#service-contact'],
                    ['title' => 'حجوزات ذهاب وعودة', 'chip' => 'متكامل', 'text' => 'تنسيق أفضل لرحلات الذهاب والعودة في نفس الحجز.', 'url' => '#service-contact'],
                    ['title' => 'حجوزات عائلية', 'chip' => 'عائلة', 'text' => 'خيارات مناسبة للعائلات مع دعم أوضح للبيانات والحجوزات.', 'url' => '#service-contact'],
                    ['title' => 'حجوزات أعمال', 'chip' => 'أعمال', 'text' => 'حلول أسرع لرحلات العمل المتكررة والمواعيد الدقيقة.', 'url' => '#service-contact'],
                    ['title' => 'عروض موسمية', 'chip' => 'عروض', 'text' => 'متابعة أفضل للعروض المتاحة في الفترات المطلوبة.', 'url' => '#service-contact'],
                ],
            ],
            'quick_info' => [
                ['title' => 'سياسات الحجز', 'value' => 'تختلف حسب شركة الطيران ونوع التذكرة وسياسة التغيير أو الإلغاء.'],
                ['title' => 'الأمتعة', 'value' => 'يتم توضيح الأمتعة المسموح بها حسب المسار وشركة الطيران المختارة.'],
                ['title' => 'التعديلات', 'value' => 'بعض الحجوزات تسمح بالتعديل وفق شروط الناقل ونوع السعر.'],
                ['title' => 'العروض الخاصة', 'value' => 'تتوفر عروض على بعض الخطوط والمواعيد حسب التوفر الفعلي.'],
            ],
            'cta' => [
                'eyebrow' => 'احجز بسرعة واطمئنان',
                'title' => 'ابدأ حجز رحلة الطيران الآن',
                'text' => 'اختر مسارك ودع Travel Wave ترتب لك الحجز بصورة أوضح وأسرع مع متابعة أفضل للتفاصيل.',
                'primary' => ['label' => 'ابدأ الحجز الآن', 'url' => '#service-contact'],
                'secondary' => ['label' => 'تواصل واتساب', 'url' => 'https://wa.me/201000000000'],
            ],
            'faqs' => [
                ['q' => 'هل يوجد حجز داخلي ودولي؟', 'a' => 'نعم، تتوفر خدمات لحجوزات الطيران المحلية والدولية حسب الوجهة المطلوبة.'],
                ['q' => 'هل يمكن تعديل الحجز؟', 'a' => 'يعتمد ذلك على شركة الطيران ونوع التذكرة وسياسة التعديل المعتمدة.'],
                ['q' => 'هل توجد عروض على الرحلات؟', 'a' => 'تتوفر عروض على بعض المسارات والمواسم حسب التوفر الفعلي وقت الحجز.'],
                ['q' => 'هل أستطيع حجز رحلة لعائلة؟', 'a' => 'نعم، يمكن تنسيق حجوزات عائلية مع مراعاة عدد المسافرين ومتطلبات كل رحلة.'],
                ['q' => 'ما البيانات المطلوبة للحجز؟', 'a' => 'الاسم كما في الجواز أو الهوية، خط السير، الموعد، وعدد المسافرين هي أهم البيانات الأساسية.'],
            ],
            'contact' => [
                'title' => 'اطلب حجز الطيران المناسب',
                'text' => 'أرسل بياناتك وسنساعدك في اختيار الرحلة الأنسب ومتابعة الحجز.',
                'checklist' => ['ترشيح أنسب الرحلات', 'دعم في البيانات الأساسية', 'متابعة حتى تأكيد الحجز'],
                'type' => 'flights',
                'source' => 'Flights',
                'fields' => [
                    ['name' => 'full_name', 'label' => 'الاسم', 'type' => 'text', 'required' => true],
                    ['name' => 'phone', 'label' => 'رقم الهاتف', 'type' => 'text', 'required' => true],
                    ['name' => 'nationality', 'label' => 'من', 'type' => 'text'],
                    ['name' => 'destination', 'label' => 'إلى', 'type' => 'text'],
                    ['name' => 'travel_date', 'label' => 'تاريخ السفر', 'type' => 'date'],
                    ['name' => 'travelers_count', 'label' => 'عدد المسافرين', 'type' => 'number'],
                    ['name' => 'message', 'label' => 'ملاحظات', 'type' => 'textarea'],
                ],
            ],
        ];
    }

    protected function hotelServicePage(): array
    {
        return [
            'theme' => 'hotels',
            'title' => 'الفنادق',
            'hero' => [
                'badge' => 'إقامة أذكى وأكثر راحة',
                'title' => 'حجوزات الفنادق',
                'text' => 'نساعدك في حجز الإقامة المناسبة بمستوى راحة أعلى وخيارات متنوعة وأسعار مدروسة تناسب الرحلات الفردية والعائلية.',
                'primary_cta' => ['label' => 'احجز إقامتك الآن', 'url' => '#service-contact'],
                'secondary_cta' => ['label' => 'استعرض الفنادق', 'url' => '#service-popular'],
                'image' => null,
            ],
            'search' => [
                'fields' => [
                    ['name' => 'destination', 'label' => 'الوجهة', 'options' => [['label' => 'شرم الشيخ', 'url' => route('hotels')], ['label' => 'الغردقة', 'url' => route('hotels')], ['label' => 'دبي', 'url' => route('hotels')], ['label' => 'مكة', 'url' => route('hotels')], ['label' => 'إسطنبول', 'url' => route('hotels')], ['label' => 'باريس', 'url' => route('hotels')]]],
                    ['name' => 'check_in', 'label' => 'تاريخ الوصول', 'options' => [['label' => 'هذا الأسبوع', 'url' => route('hotels')], ['label' => 'الأسبوع القادم', 'url' => route('hotels')], ['label' => 'هذا الشهر', 'url' => route('hotels')]]],
                    ['name' => 'check_out', 'label' => 'تاريخ المغادرة', 'options' => [['label' => 'بعد 2 ليلة', 'url' => route('hotels')], ['label' => 'بعد 4 ليالٍ', 'url' => route('hotels')], ['label' => 'بعد أسبوع', 'url' => route('hotels')]]],
                    ['name' => 'rooms', 'label' => 'عدد الغرف', 'options' => [['label' => 'غرفة واحدة', 'url' => route('hotels')], ['label' => 'غرفتان', 'url' => route('hotels')], ['label' => '3 غرف', 'url' => route('hotels')]]],
                    ['name' => 'guests', 'label' => 'عدد النزلاء', 'options' => [['label' => '2 نزلاء', 'url' => route('hotels')], ['label' => '4 نزلاء', 'url' => route('hotels')], ['label' => '6+ نزلاء', 'url' => route('hotels')]]],
                ],
                'button' => 'ابحث الآن',
                'default_url' => route('hotels'),
            ],
            'popular' => [
                'eyebrow' => 'إقامات مختارة',
                'title' => 'فنادق ووجهات مميزة',
                'text' => 'مجموعة مختارة من الوجهات الفندقية المطلوبة بخيارات إقامة متنوعة ومرنة.',
                'items' => collect([
                    ['title' => 'فنادق شرم الشيخ', 'subtitle' => 'منتجعات شاطئية وإقامات مريحة', 'meta' => 'تقييمات مرتفعة وخيارات عائلية', 'badge' => 'شاطئي'],
                    ['title' => 'فنادق الغردقة', 'subtitle' => 'إقامة مناسبة للاسترخاء والأنشطة البحرية', 'meta' => 'خيارات متنوعة حسب الميزانية', 'badge' => 'الأكثر طلباً'],
                    ['title' => 'فنادق دبي', 'subtitle' => 'إقامة راقية في مواقع مميزة', 'meta' => 'خيارات أعمال وترفيه', 'badge' => 'فاخر'],
                    ['title' => 'فنادق مكة', 'subtitle' => 'حلول إقامة مريحة بقرب مناسب', 'meta' => 'حجوزات منظمة ومرنة', 'badge' => 'قرب أفضل'],
                    ['title' => 'فنادق إسطنبول', 'subtitle' => 'تنوع كبير في الفئات والمناطق', 'meta' => 'إقامات عائلية وفردية', 'badge' => 'متنوع'],
                    ['title' => 'فنادق باريس', 'subtitle' => 'خيارات إقامة مناسبة للرحلات الأوروبية', 'meta' => 'تأكيدات سريعة وخيارات متعددة', 'badge' => 'أوروبي'],
                ])->map(fn ($item) => $item + ['button' => 'عرض التفاصيل', 'url' => '#service-contact', 'image' => null])->all(),
            ],
            'features_title' => 'لماذا تحجز الفنادق معنا؟',
            'features' => [
                ['tag' => '01', 'title' => 'خيارات متنوعة', 'text' => 'مجموعة أوسع من الفنادق والمنتجعات والإقامات المناسبة لفئات مختلفة.'],
                ['tag' => '02', 'title' => 'أفضل الأسعار', 'text' => 'ترشيح الخيارات الأكثر توازناً بين السعر والموقع والخدمة.'],
                ['tag' => '03', 'title' => 'مواقع مميزة', 'text' => 'مساعدة في اختيار الفندق بحسب المنطقة والاحتياج الفعلي للرحلة.'],
                ['tag' => '04', 'title' => 'دعم في الحجز', 'text' => 'تأكيد أوضح لبيانات الحجز وتفاصيل الإقامة المطلوبة.'],
                ['tag' => '05', 'title' => 'إقامة مريحة', 'text' => 'حلول مناسبة للعائلات والأفراد والرحلات العملية والترفيهية.'],
                ['tag' => '06', 'title' => 'تأكيد سريع', 'text' => 'تجهيز أسرع للحجز حسب التوفر وخيارات الغرف المطلوبة.'],
            ],
            'packages' => [
                'title' => 'فئات الإقامة',
                'items' => [
                    ['title' => 'فنادق اقتصادية', 'meta' => 'حلول مناسبة للميزانيات العملية', 'highlights' => ['تكلفة أفضل', 'مواقع مناسبة', 'حجز واضح'], 'price' => 'أسعار تبدأ حسب الوجهة', 'button' => 'اطلب الخدمة'],
                    ['title' => 'فنادق 4 و5 نجوم', 'meta' => 'مستوى أعلى من الراحة والخدمة', 'highlights' => ['مرافق أفضل', 'خدمة مميزة', 'خيارات متنوعة'], 'price' => 'خيارات حسب الموسم', 'button' => 'اطلب الخدمة'],
                    ['title' => 'منتجعات وشقق فندقية', 'meta' => 'حلول عائلية وإقامات أطول', 'highlights' => ['مساحات أوسع', 'مرونة أكبر', 'خيارات عائلية'], 'price' => 'تسعير حسب المدة', 'button' => 'اطلب الخدمة'],
                ],
            ],
            'steps' => ['اختر الوجهة', 'حدد التواريخ', 'اختر نوع الإقامة', 'أكد الحجز', 'استلم التأكيد'],
            'steps_title' => 'خطوات حجز الفندق',
            'grid' => [
                'title' => 'أنواع الفنادق والإقامات',
                'items' => [
                    ['title' => 'فنادق اقتصادية', 'chip' => 'اقتصادي', 'text' => 'حلول إقامة مناسبة للرحلات القصيرة والميزانيات العملية.', 'url' => '#service-contact'],
                    ['title' => 'فنادق 4 نجوم', 'chip' => 'مريح', 'text' => 'توازن جيد بين السعر والموقع والخدمات الأساسية.', 'url' => '#service-contact'],
                    ['title' => 'فنادق 5 نجوم', 'chip' => 'فاخر', 'text' => 'إقامة راقية وتجربة أكثر فخامة وراحة.', 'url' => '#service-contact'],
                    ['title' => 'منتجعات', 'chip' => 'استجمام', 'text' => 'خيارات مناسبة للعطلات والاسترخاء والرحلات الشاطئية.', 'url' => '#service-contact'],
                    ['title' => 'شقق فندقية', 'chip' => 'مرن', 'text' => 'حلول أوسع للإقامات العائلية أو الطويلة نسبياً.', 'url' => '#service-contact'],
                    ['title' => 'إقامات عائلية', 'chip' => 'عائلي', 'text' => 'خيارات أكثر ملاءمة للعائلات وعدد النزلاء الأكبر.', 'url' => '#service-contact'],
                ],
            ],
            'quick_info' => [
                ['title' => 'أنواع الإقامة', 'value' => 'فنادق اقتصادية، 4 و5 نجوم، منتجعات، وشقق فندقية.'],
                ['title' => 'سياسة الحجز', 'value' => 'تختلف حسب الفندق ونوع السعر وشروط الإلغاء أو التعديل.'],
                ['title' => 'المزايا المتاحة', 'value' => 'تتغير حسب الفندق وتشمل الإفطار أو الإطلالة أو الموقع أو المرافق.'],
                ['title' => 'خيارات الدفع', 'value' => 'تتحدد بحسب الحجز والتأكيد وسياسة الفندق أو المزود.'],
            ],
            'cta' => [
                'eyebrow' => 'إقامتك تبدأ من هنا',
                'title' => 'احجز إقامتك الآن بثقة وراحة',
                'text' => 'استفد من خيارات فندقية أوضح وأكثر مرونة مع Travel Wave، ودع فريقنا يساعدك في اختيار الأنسب.',
                'primary' => ['label' => 'احجز الآن', 'url' => '#service-contact'],
                'secondary' => ['label' => 'تواصل واتساب', 'url' => 'https://wa.me/201000000000'],
            ],
            'faqs' => [
                ['q' => 'هل تتوفر فنادق اقتصادية وفاخرة؟', 'a' => 'نعم، تتوفر فئات متعددة تناسب الميزانيات المختلفة ونوع الرحلة المطلوبة.'],
                ['q' => 'هل يمكن الحجز لعائلات؟', 'a' => 'نعم، توجد خيارات مناسبة للعائلات من حيث الغرف والمساحة والخدمات.'],
                ['q' => 'هل يوجد فنادق في وجهات متعددة؟', 'a' => 'نعم، يمكن المساعدة في حجوزات محلية وخارجية في وجهات متنوعة.'],
                ['q' => 'هل الحجز مؤكد؟', 'a' => 'يتم توضيح حالة التوفر والتأكيد النهائي حسب الفندق المختار ووقت الحجز.'],
                ['q' => 'هل يمكن المساعدة في اختيار الفندق المناسب؟', 'a' => 'نعم، نرشح لك الخيارات الأنسب حسب الوجهة والميزانية وعدد النزلاء ونوع الرحلة.'],
            ],
            'contact' => [
                'title' => 'ابدأ طلب حجز الفندق',
                'text' => 'أرسل بيانات الإقامة المطلوبة وسنساعدك في اختيار الفندق الأنسب لك.',
                'checklist' => ['اقتراح أفضل خيارات الإقامة', 'مقارنة أوضح بين البدائل', 'متابعة حتى تأكيد الحجز'],
                'type' => 'hotels',
                'source' => 'Hotels',
                'fields' => [
                    ['name' => 'full_name', 'label' => 'الاسم', 'type' => 'text', 'required' => true],
                    ['name' => 'phone', 'label' => 'رقم الهاتف', 'type' => 'text', 'required' => true],
                    ['name' => 'destination', 'label' => 'الوجهة', 'type' => 'text'],
                    ['name' => 'travel_date', 'label' => 'تاريخ الوصول', 'type' => 'date'],
                    ['name' => 'return_date', 'label' => 'تاريخ المغادرة', 'type' => 'date'],
                    ['name' => 'accommodation_type', 'label' => 'عدد الغرف', 'type' => 'text'],
                    ['name' => 'travelers_count', 'label' => 'عدد النزلاء', 'type' => 'number'],
                    ['name' => 'message', 'label' => 'ملاحظات', 'type' => 'textarea'],
                ],
            ],
        ];
    }
}
