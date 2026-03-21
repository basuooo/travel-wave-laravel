<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use App\Models\Inquiry;
use App\Models\LeadForm;
use App\Models\LeadFormAssignment;
use App\Models\LeadFormField;
use App\Models\MarketingLandingPage;
use App\Models\MarketingLandingPageEvent;
use App\Models\MapSection;
use App\Models\Page;
use App\Models\SeoMetaEntry;
use App\Models\SeoRedirect;
use App\Models\SeoSetting;
use App\Models\Setting;
use App\Models\TrackingIntegration;
use App\Models\User;
use App\Models\VisaCountry;
use App\Models\Destination;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TravelWaveFrontendTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_with_seeded_content(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('home'))->assertOk()->assertSee('Travel Wave');
        $this->get(route('visas.index'))->assertOk()->assertSee('France');
        $this->get(route('destinations.show', 'sharm-el-sheikh'))->assertOk()->assertSee('Sharm El Sheikh');
        $this->get(route('blog.index'))->assertOk()->assertSee('Travel insights');
    }

    public function test_about_and_contact_pages_render_premium_brand_layouts(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('tw-brand-page-hero', false)
            ->assertSee('tw-brand-stats-shell', false)
            ->assertSee('tw-brand-page-cta', false);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('premium-contact-form', false)
            ->assertSee('name="service_type"', false)
            ->assertSee('name="destination"', false)
            ->assertSee('tw-managed-map-frame', false)
            ->assertSee('<iframe', false);
    }

    public function test_domestic_flights_and_hotels_pages_render_premium_service_layouts(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('destinations.index'))
            ->assertOk()
            ->assertSee('السياحة الداخلية')
            ->assertSee('احجز رحلتك الآن');

        $this->get(route('flights'))
            ->assertOk()
            ->assertSee('حجوزات الطيران')
            ->assertSee('ابحث عن رحلة');

        $this->get(route('hotels'))
            ->assertOk()
            ->assertSee('حجوزات الفنادق')
            ->assertSee('ابحث الآن');
    }

    public function test_homepage_hero_slider_renders_three_seeded_slides(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(3, HeroSlide::query()->where('is_active', true)->count());

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('travelWaveHeroSlider')
            ->assertSee('Luxury journeys shaped around your next visa, flight, and stay')
            ->assertSee('Europe, Gulf, and Asia visa services with a clearer path')
            ->assertSee('Discover Egypt and beyond with polished travel packages')
            ->assertSee('--slide-desktop-image', false)
            ->assertSee('--slide-mobile-image', false);
    }

    public function test_homepage_hero_slider_uses_selected_layout_mode(): void
    {
        $this->seed(DatabaseSeeder::class);

        Setting::query()->first()->update([
            'hero_slider_layout_mode' => 'fullscreen-hero',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('tw-home-slider-mode-fullscreen-hero', false);
    }

    public function test_homepage_renders_smart_service_search_bar(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('js-home-service-search', false)
            ->assertSee('External Visas')
            ->assertSee('Domestic Trips')
            ->assertSee('European Union')
            ->assertSee(route('flights'), false)
            ->assertSee(route('hotels'), false);
    }

    public function test_image_only_slide_renders_without_overlay_or_empty_content_container(): void
    {
        $this->seed(DatabaseSeeder::class);

        HeroSlide::query()->update([
            'is_active' => false,
        ]);

        $slide = HeroSlide::query()->create([
            'image_path' => 'hero-slides/slide-1.svg',
            'headline_en' => null,
            'headline_ar' => 'عنوان عربي',
            'subtitle_en' => null,
            'subtitle_ar' => 'وصف عربي',
            'cta_text_en' => null,
            'cta_text_ar' => 'اعرف المزيد',
            'cta_link' => '/contact',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($slide->image_path)
            ->assertDontSee('tw-home-slide-overlay', false)
            ->assertDontSee('tw-home-slide-content', false)
            ->assertDontSee('عنوان عربي');

        $this->withSession(['locale' => 'ar'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('tw-home-slide-overlay', false)
            ->assertSee('tw-home-slide-content', false)
            ->assertSee('عنوان عربي');
    }

    public function test_admin_login_page_is_accessible(): void
    {
        $this->get(route('admin.login'))->assertOk()->assertSee('Travel Wave Admin');
    }

    public function test_header_and_footer_render_brand_logo(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('settings/travel-wave-logo.svg')
            ->assertSee('tw-footer-logo')
            ->assertSee('Popular Visa Destinations')
            ->assertSee('tw-home-destination-card', false)
            ->assertSee('tw-home-destination-flag', false)
            ->assertSee('js-home-destination-prev', false)
            ->assertSee('js-home-destination-next', false)
            ->assertSee('js-home-destination-carousel', false);
    }

    public function test_global_floating_whatsapp_button_renders_with_seeded_settings(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('tw-floating-whatsapp', false)
            ->assertSee('https://wa.me/201060500236', false)
            ->assertSee(rawurlencode('Hello, I want to ask about Travel Wave services'), false);
    }

    public function test_arabic_locale_switch_renders_rtl_layout(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->withSession(['locale' => 'ar'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('dir="rtl"', false);
    }

    public function test_visa_country_template_renders_structured_sections(): void
    {
        $this->seed(DatabaseSeeder::class);

        $country = VisaCountry::query()->where('slug', 'france-visa')->firstOrFail();

        $this->get(route('visas.country', $country))
            ->assertOk()
            ->assertSee('France Visa Services Built for Clarity and Confidence')
            ->assertSee('Why Choose Travel Wave')
            ->assertSee('Required Documents')
            ->assertSee('Application Steps')
            ->assertSee('Fees and Processing Time')
            ->assertSee('Talk to Travel Wave About Your France Visa')
            ->assertSee('id="visa-inquiry"', false)
            ->assertSee('tw-visa-inquiry-shell', false)
            ->assertSee('name="whatsapp_number"', false)
            ->assertSee('name="service_type"', false)
            ->assertSee('name="destination"', false);
    }

    public function test_domestic_destination_page_uses_full_dynamic_destination_template(): void
    {
        $this->seed(DatabaseSeeder::class);

        $destination = Destination::query()->where('slug', 'sharm-el-sheikh')->firstOrFail();

        $this->get(route('destinations.show', $destination))
            ->assertOk()
            ->assertSee('Sharm El Sheikh Trips with Travel Wave')
            ->assertSee('Top Highlights')
            ->assertSee('Included Services')
            ->assertSee('Pricing Overview')
            ->assertDontSee('Ask About Sharm El Sheikh');
    }

    public function test_visa_inquiry_is_stored_with_country_source_page(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->post(route('inquiries.store'), [
            'type' => 'visa',
            'full_name' => 'France Visa Lead',
            'phone' => '+20 100 000 0000',
            'email' => 'france@example.com',
            'travel_date' => now()->addMonth()->toDateString(),
            'message' => 'I need help with my France visa file.',
            'destination' => 'France',
            'service_type' => 'France Visa',
            'source_page' => 'France Visa',
            'success_message' => 'Custom success message',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Custom success message');

        $this->assertDatabaseHas('inquiries', [
            'full_name' => 'France Visa Lead',
            'type' => 'visa',
            'destination' => 'France',
            'service_type' => 'France Visa',
            'source_page' => 'France Visa',
        ]);

        $this->assertSame(1, Inquiry::query()->where('source_page', 'France Visa')->count());
    }

    public function test_admin_visa_country_editor_renders_new_template_fields(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $country = VisaCountry::query()->where('slug', 'france-visa')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.visa-countries.edit', $country))
            ->assertOk()
            ->assertSee('Quick Summary Cards')
            ->assertSee('Mobile Hero Image')
            ->assertSee('Why Choose Travel Wave')
            ->assertSee('Fees, Map, Inquiry, and Final CTA');
    }

    public function test_admin_destination_editor_renders_structured_cms_fields(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $destination = Destination::query()->where('slug', 'sharm-el-sheikh')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.destinations.edit', $destination))
            ->assertOk()
            ->assertSee('Quick Info Cards')
            ->assertSee('Included Services')
            ->assertSee('CTA and Form Section')
            ->assertSee('Section Visibility');
    }

    public function test_admin_forms_manager_pages_render(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.forms.index'))
            ->assertOk()
            ->assertSee('Forms Manager')
            ->assertSee('Create Form');

        $this->actingAs($admin)
            ->get(route('admin.forms.create'))
            ->assertOk()
            ->assertSee('Dynamic Fields')
            ->assertSee('Page Assignments');
    }

    public function test_admin_tracking_manager_pages_render(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.tracking-integrations.index'))
            ->assertOk()
            ->assertSee('Tracking Manager')
            ->assertSee('Create Tracking Integration');

        $this->actingAs($admin)
            ->get(route('admin.tracking-integrations.create'))
            ->assertOk()
            ->assertSee('Tracking ID / Code')
            ->assertSee('Visibility Rules');
    }

    public function test_admin_seo_manager_pages_render(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.seo.dashboard'))
            ->assertOk()
            ->assertSee('SEO Manager')
            ->assertSee('Sitemap Manager');

        $this->actingAs($admin)
            ->get(route('admin.seo.settings'))
            ->assertOk()
            ->assertSee('robots.txt')
            ->assertSee('Schema Manager');
    }

    public function test_admin_meta_conversion_api_settings_can_be_saved(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $response = $this->actingAs($admin)->put(route('admin.meta-conversion-api-settings.update'), [
            'meta_conversion_api_enabled' => '1',
            'meta_pixel_id' => '123456789012345',
            'meta_conversion_api_access_token' => 'meta-test-token',
            'meta_conversion_api_test_event_code' => 'TEST123',
            'meta_conversion_api_default_event_source_url' => 'https://travelwave.test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('settings', [
            'meta_conversion_api_enabled' => 1,
            'meta_pixel_id' => '123456789012345',
            'meta_conversion_api_access_token' => 'meta-test-token',
            'meta_conversion_api_test_event_code' => 'TEST123',
            'meta_conversion_api_default_event_source_url' => 'https://travelwave.test',
        ]);
    }

    public function test_admin_marketing_manager_pages_render(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.marketing-landing-pages.index'))
            ->assertOk()
            ->assertSee('Marketing Manager')
            ->assertSee('Create Landing Page');

        $this->actingAs($admin)
            ->get(route('admin.marketing-landing-pages.create'))
            ->assertOk()
            ->assertSee('Landing Page Name')
            ->assertSee('Benefits / Features');
    }

    public function test_seeded_marketing_demo_landing_page_is_visible_in_admin_and_frontend(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $landingPage = MarketingLandingPage::query()->where('slug', 'france-visa-campaign-demo')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.marketing-landing-pages.index'))
            ->assertOk()
            ->assertSee('France Visa Campaign Demo')
            ->assertSee('France Visa Lead Campaign');

        $this->get(route('marketing.landing-pages.show', $landingPage))
            ->assertOk()
            ->assertSee('France Visa 2026')
            ->assertSee('Quick France Visa Highlights')
            ->assertSee('Request Your France Visa Callback');
    }

    public function test_frontend_renders_tracking_integration_from_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        TrackingIntegration::query()->create([
            'name' => 'GA4 Main',
            'slug' => 'ga4-main',
            'integration_type' => TrackingIntegration::TYPE_GA4,
            'platform' => 'Google Analytics',
            'tracking_code' => 'G-TEST1234',
            'placement' => 'standard',
            'visibility_mode' => 'all',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('https://www.googletagmanager.com/gtag/js?id=G-TEST1234', false)
            ->assertSee("gtag('config', 'G-TEST1234');", false);
    }

    public function test_public_robots_and_sitemap_routes_render_from_seo_manager(): void
    {
        $this->seed(DatabaseSeeder::class);

        SeoSetting::query()->create([
            'robots_txt_content' => "User-agent: *\nAllow: /\nSitemap: " . url('/sitemap.xml'),
        ]);

        $this->get(route('seo.robots'))
            ->assertOk()
            ->assertSee('User-agent: *');

        $this->post(route('admin.login.store'), [
            'email' => 'admin@travelwave.test',
            'password' => 'password',
        ]);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $this->actingAs($admin)->post(route('admin.seo.sitemap.regenerate'))->assertRedirect();

        $this->get(route('seo.sitemap.index'))
            ->assertOk()
            ->assertSee('<?xml', false)
            ->assertSee('/sitemap-pages.xml', false);
    }

    public function test_seo_meta_entry_can_override_frontend_title_and_description(): void
    {
        $this->seed(DatabaseSeeder::class);

        $page = Page::query()->where('key', 'about')->firstOrFail();

        SeoMetaEntry::query()->create([
            'target_type' => 'page',
            'target_id' => $page->id,
            'meta_title_en' => 'Travel Wave About SEO Title',
            'meta_description_en' => 'SEO description override for the About page.',
            'canonical_url' => 'https://travelwave.test/about-seo',
            'robots_meta' => 'index,follow',
            'is_active' => true,
        ]);

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('<title>Travel Wave About SEO Title</title>', false)
            ->assertSee('SEO description override for the About page.', false)
            ->assertSee('https://travelwave.test/about-seo', false);
    }

    public function test_seo_redirect_manager_redirects_source_path(): void
    {
        $this->seed(DatabaseSeeder::class);

        SeoRedirect::query()->create([
            'source_path' => '/old-france-visa',
            'destination_url' => '/visa-country/france-visa',
            'redirect_type' => 301,
            'is_active' => true,
        ]);

        $this->get('/old-france-visa')
            ->assertRedirect('/visa-country/france-visa');
    }

    public function test_frontend_renders_meta_browser_and_server_tracking_bridge_when_capi_is_enabled(): void
    {
        $this->seed(DatabaseSeeder::class);

        Setting::query()->firstOrFail()->update([
            'meta_conversion_api_enabled' => true,
            'meta_pixel_id' => '123456789012345',
            'meta_conversion_api_access_token' => 'meta-token',
        ]);

        TrackingIntegration::query()->create([
            'name' => 'Meta Pixel Main',
            'slug' => 'meta-pixel-main',
            'integration_type' => TrackingIntegration::TYPE_META_PIXEL,
            'platform' => 'Meta',
            'tracking_code' => '123456789012345',
            'placement' => 'standard',
            'visibility_mode' => 'all',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee("window.twMetaTrackBrowser('PageView'", false)
            ->assertSee('tracking\/meta\/events', false)
            ->assertSee('pageViewEventId', false)
            ->assertSee('WhatsAppClick', false);
    }

    public function test_admin_search_returns_grouped_results_for_arabic_or_english_queries(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.search', ['q' => 'France']))
            ->assertOk()
            ->assertSee('France')
            ->assertSee('Visa Destinations');
    }

    public function test_marketing_landing_page_renders_form_and_tracking_and_stores_visit_event(): void
    {
        $this->seed(DatabaseSeeder::class);

        $form = LeadForm::query()->create([
            'name' => 'Campaign Lead Form',
            'slug' => 'campaign-lead-form',
            'form_category' => 'contact',
            'title_en' => 'Talk to Travel Wave',
            'title_ar' => 'تواصل مع ترافل ويف',
            'submit_text_en' => 'Send',
            'submit_text_ar' => 'أرسل',
            'success_message_en' => 'Done',
            'success_message_ar' => 'تم',
            'is_active' => true,
        ]);

        $form->fields()->createMany([
            ['field_key' => 'full_name', 'type' => 'text', 'label_en' => 'Full Name', 'label_ar' => 'الاسم الكامل', 'is_required' => true, 'is_enabled' => true, 'sort_order' => 1],
            ['field_key' => 'phone', 'type' => 'phone', 'label_en' => 'Phone', 'label_ar' => 'رقم الهاتف', 'is_required' => true, 'is_enabled' => true, 'sort_order' => 2],
            ['field_key' => 'message', 'type' => 'textarea', 'label_en' => 'Message', 'label_ar' => 'رسالتك', 'is_required' => false, 'is_enabled' => true, 'sort_order' => 3],
        ]);

        $tracking = TrackingIntegration::query()->create([
            'name' => 'Landing GA4',
            'slug' => 'landing-ga4',
            'integration_type' => TrackingIntegration::TYPE_GA4,
            'platform' => 'Google Analytics',
            'tracking_code' => 'G-LANDING1',
            'placement' => 'standard',
            'visibility_mode' => 'all',
            'is_active' => true,
        ]);

        $landingPage = MarketingLandingPage::query()->create([
            'internal_name' => 'France Campaign LP',
            'title_en' => 'France Visa Campaign',
            'title_ar' => 'حملة تأشيرة فرنسا',
            'slug' => 'france-campaign',
            'campaign_name' => 'France Leads',
            'ad_platform' => 'meta_ads',
            'status' => MarketingLandingPage::STATUS_PUBLISHED,
            'assigned_lead_form_id' => $form->id,
            'tracking_integration_ids' => [$tracking->id],
            'sections' => [
                'hero' => [
                    'enabled' => true,
                    'title_en' => 'France Visa in Clear Steps',
                    'title_ar' => 'تأشيرة فرنسا بخطوات أوضح',
                    'subtitle_en' => 'Campaign-focused landing page.',
                    'subtitle_ar' => 'صفحة هبوط مخصصة للحملة.',
                    'primary_button_text_en' => 'Start Now',
                    'primary_button_text_ar' => 'ابدأ الآن',
                    'primary_button_url' => '#marketing-form',
                ],
                'benefits' => [
                    'enabled' => true,
                    'title_en' => 'Why this page converts',
                    'title_ar' => 'لماذا هذه الصفحة فعالة',
                    'items' => [
                        ['title_en' => 'Fast review', 'title_ar' => 'مراجعة سريعة', 'text_en' => 'Organized support', 'text_ar' => 'دعم منظم', 'is_active' => true, 'sort_order' => 1],
                    ],
                ],
                'form' => [
                    'enabled' => true,
                    'title_en' => 'Request the next step',
                    'title_ar' => 'اطلب الخطوة التالية',
                ],
                'faq' => [
                    'enabled' => true,
                    'title_en' => 'Questions',
                    'title_ar' => 'الأسئلة',
                    'items' => [
                        ['question_en' => 'When do we apply?', 'question_ar' => 'متى نبدأ؟', 'answer_en' => 'As soon as the file is ready.', 'answer_ar' => 'بمجرد تجهيز الملف.', 'is_active' => true, 'sort_order' => 1],
                    ],
                ],
                'cta' => [
                    'enabled' => true,
                    'title_en' => 'Ready to start?',
                    'title_ar' => 'جاهز للبدء؟',
                    'primary_button_text_en' => 'Send request',
                    'primary_button_text_ar' => 'أرسل الطلب',
                    'primary_button_url' => '#marketing-form',
                ],
            ],
        ]);

        $this->get(route('marketing.landing-pages.show', $landingPage))
            ->assertOk()
            ->assertSee('France Visa in Clear Steps')
            ->assertSee('name="marketing_landing_page_id"', false)
            ->assertSee('https://www.googletagmanager.com/gtag/js?id=G-LANDING1', false);

        $this->assertDatabaseHas('marketing_landing_page_events', [
            'marketing_landing_page_id' => $landingPage->id,
            'event_type' => MarketingLandingPageEvent::TYPE_PAGE_VIEW,
        ]);
    }

    public function test_marketing_landing_page_form_submission_is_attributed_to_page(): void
    {
        $this->seed(DatabaseSeeder::class);

        $form = LeadForm::query()->create([
            'name' => 'LP Inquiry',
            'slug' => 'lp-inquiry',
            'form_category' => 'contact',
            'submit_text_en' => 'Send',
            'submit_text_ar' => 'أرسل',
            'success_message_en' => 'Managed form success',
            'success_message_ar' => 'تم الإرسال',
            'is_active' => true,
        ]);

        $form->fields()->createMany([
            ['field_key' => 'full_name', 'type' => 'text', 'label_en' => 'Full Name', 'label_ar' => 'الاسم', 'is_required' => true, 'is_enabled' => true, 'sort_order' => 1],
            ['field_key' => 'phone', 'type' => 'phone', 'label_en' => 'Phone', 'label_ar' => 'الهاتف', 'is_required' => true, 'is_enabled' => true, 'sort_order' => 2],
            ['field_key' => 'message', 'type' => 'textarea', 'label_en' => 'Message', 'label_ar' => 'الرسالة', 'is_required' => false, 'is_enabled' => true, 'sort_order' => 3],
        ]);

        $landingPage = MarketingLandingPage::query()->create([
            'internal_name' => 'General Campaign',
            'title_en' => 'General Campaign',
            'title_ar' => 'حملة عامة',
            'slug' => 'general-campaign',
            'status' => MarketingLandingPage::STATUS_PUBLISHED,
            'assigned_lead_form_id' => $form->id,
            'sections' => ['hero' => ['enabled' => true, 'title_en' => 'General Campaign']],
        ]);

        $response = $this->post(route('inquiries.store'), [
            'lead_form_id' => $form->id,
            'type' => 'contact',
            'source_page' => $landingPage->slug,
            'marketing_landing_page_id' => $landingPage->id,
            'preferred_language' => 'en',
            'success_message' => 'Managed form success',
            'full_name' => 'Landing Lead',
            'phone' => '+20 100 000 1111',
            'message' => 'Need details',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Managed form success');

        $this->assertDatabaseHas('inquiries', [
            'marketing_landing_page_id' => $landingPage->id,
            'full_name' => 'Landing Lead',
            'source_page' => $landingPage->slug,
        ]);

        $this->assertDatabaseHas('marketing_landing_page_events', [
            'marketing_landing_page_id' => $landingPage->id,
            'event_type' => MarketingLandingPageEvent::TYPE_FORM_SUBMIT,
        ]);
    }

    public function test_admin_maps_manager_pages_render(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.map-sections.index'))
            ->assertOk()
            ->assertSee('Maps Manager')
            ->assertSee('Create Map Section');

        $this->actingAs($admin)
            ->get(route('admin.map-sections.create'))
            ->assertOk()
            ->assertSee('Layout Type')
            ->assertSee('Page Assignments');
    }

    public function test_admin_floating_whatsapp_settings_page_renders(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.floating-whatsapp-settings.edit'))
            ->assertOk()
            ->assertSee('Floating WhatsApp Settings')
            ->assertSee('WhatsApp Number')
            ->assertSee('Page Visibility Rules');
    }

    public function test_floating_whatsapp_visibility_rules_can_hide_specific_page(): void
    {
        $this->seed(DatabaseSeeder::class);

        Setting::query()->firstOrFail()->update([
            'floating_whatsapp_visibility_mode' => 'exclude_selected',
            'floating_whatsapp_visibility_targets' => ['page_key|contact'],
        ]);

        $this->get(route('contact'))
            ->assertOk()
            ->assertDontSee('tw-floating-whatsapp', false);

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('tw-floating-whatsapp', false);
    }

    public function test_managed_map_renders_on_contact_page_from_dashboard_assignment(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('tw-managed-map-frame', false)
            ->assertSee('Visit Travel Wave or Use the Map as a Quick Reference')
            ->assertSee('<iframe', false);
    }

    public function test_managed_map_renders_on_assigned_visa_destination_page(): void
    {
        $this->seed(DatabaseSeeder::class);

        $country = VisaCountry::query()->where('slug', 'france-visa')->firstOrFail();

        $map = MapSection::query()->create([
            'name' => 'France Visa Map',
            'slug' => 'france-visa-map',
            'title_en' => 'Managed France Visa Support Map',
            'title_ar' => 'خريطة دعم تأشيرة فرنسا المُدارة',
            'subtitle_en' => 'Visit our office or use this location as a quick reference for your next step.',
            'subtitle_ar' => 'زر مكتبنا أو استخدم هذا الموقع كمرجع سريع لخطوتك التالية.',
            'embed_code' => '<iframe src="https://www.google.com/maps?q=France%20Visa%20Support&output=embed" width="100%" height="320" style="border:0;" loading="lazy"></iframe>',
            'layout_type' => 'split',
            'height' => 320,
            'background_style' => 'soft',
            'spacing_preset' => 'normal',
            'rounded_corners' => true,
            'is_active' => true,
        ]);

        $map->assignments()->create([
            'assignment_type' => 'visa_country',
            'target_id' => $country->id,
            'display_position' => 'after_faq',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->get(route('visas.country', $country))
            ->assertOk()
            ->assertSee('Managed France Visa Support Map')
            ->assertSee('tw-managed-map-frame', false)
            ->assertSee('France%20Visa%20Support', false);
    }

    public function test_managed_form_renders_on_assigned_page_and_submission_is_stored(): void
    {
        $this->seed(DatabaseSeeder::class);

        $form = LeadForm::query()->create([
            'name' => 'About Lead Form',
            'slug' => 'about-lead-form',
            'form_category' => 'contact',
            'title_en' => 'Talk to Travel Wave',
            'title_ar' => 'تواصل مع Travel Wave',
            'submit_text_en' => 'Send Request',
            'submit_text_ar' => 'أرسل الطلب',
            'success_message_en' => 'Managed form success',
            'success_message_ar' => 'تم الإرسال',
            'is_active' => true,
        ]);

        $form->fields()->createMany([
            [
                'field_key' => 'full_name',
                'type' => 'text',
                'label_en' => 'Full Name',
                'label_ar' => 'الاسم',
                'is_required' => true,
                'is_enabled' => true,
                'sort_order' => 1,
            ],
            [
                'field_key' => 'phone',
                'type' => 'phone',
                'label_en' => 'Phone',
                'label_ar' => 'رقم الهاتف',
                'is_required' => true,
                'is_enabled' => true,
                'sort_order' => 2,
            ],
            [
                'field_key' => 'service_type',
                'type' => 'select',
                'label_en' => 'Service Type',
                'label_ar' => 'نوع الخدمة',
                'options' => [
                    ['value' => 'visa', 'label_en' => 'Visa', 'label_ar' => 'تأشيرة'],
                ],
                'is_required' => true,
                'is_enabled' => true,
                'sort_order' => 3,
            ],
            [
                'field_key' => 'message',
                'type' => 'textarea',
                'label_en' => 'Message',
                'label_ar' => 'الرسالة',
                'is_required' => false,
                'is_enabled' => true,
                'sort_order' => 4,
            ],
        ]);

        $assignment = $form->assignments()->create([
            'assignment_type' => 'page_key',
            'target_key' => 'about',
            'display_position' => 'below_hero',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Talk to Travel Wave')
            ->assertSee('name="lead_form_id"', false)
            ->assertSee('name="service_type"', false);

        $response = $this->post(route('inquiries.store'), [
            'lead_form_id' => $form->id,
            'lead_form_assignment_id' => $assignment->id,
            'type' => 'contact',
            'source_page' => 'about',
            'display_position' => 'below_hero',
            'preferred_language' => 'en',
            'success_message' => 'Managed form success',
            'full_name' => 'Managed Lead',
            'phone' => '+20 100 222 3333',
            'service_type' => 'visa',
            'message' => 'Need help with the next step.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Managed form success');

        $this->assertDatabaseHas('inquiries', [
            'lead_form_id' => $form->id,
            'lead_form_assignment_id' => $assignment->id,
            'form_name' => 'About Lead Form',
            'source_page' => 'about',
            'display_position' => 'below_hero',
            'full_name' => 'Managed Lead',
        ]);
    }

    public function test_managed_visa_form_can_render_split_layout_with_page_specific_info(): void
    {
        $this->seed(DatabaseSeeder::class);

        $country = VisaCountry::query()->where('slug', 'france-visa')->firstOrFail();

        $form = LeadForm::query()->create([
            'name' => 'Visa Split Form',
            'slug' => 'visa-split-form',
            'form_category' => 'visa',
            'title_en' => 'Start Your Visa Request',
            'title_ar' => 'ابدأ طلب التأشيرة',
            'submit_text_en' => 'Send Request',
            'submit_text_ar' => 'أرسل الطلب',
            'is_active' => true,
            'settings' => [
                'layout_variant' => 'visa_split',
            ],
        ]);

        $form->fields()->createMany([
            [
                'field_key' => 'full_name',
                'type' => 'text',
                'label_en' => 'Full Name',
                'label_ar' => 'الاسم الكامل',
                'is_required' => true,
                'is_enabled' => true,
                'sort_order' => 1,
            ],
            [
                'field_key' => 'phone',
                'type' => 'phone',
                'label_en' => 'Phone Number',
                'label_ar' => 'رقم الهاتف',
                'is_required' => true,
                'is_enabled' => true,
                'sort_order' => 2,
            ],
            [
                'field_key' => 'whatsapp_number',
                'type' => 'text',
                'label_en' => 'WhatsApp Number',
                'label_ar' => 'رقم واتساب',
                'is_required' => false,
                'is_enabled' => true,
                'sort_order' => 3,
            ],
            [
                'field_key' => 'email',
                'type' => 'email',
                'label_en' => 'Email Address',
                'label_ar' => 'البريد الإلكتروني',
                'is_required' => false,
                'is_enabled' => true,
                'sort_order' => 4,
            ],
            [
                'field_key' => 'service_type',
                'type' => 'text',
                'label_en' => 'Visa Type',
                'label_ar' => 'نوع التأشيرة',
                'is_required' => false,
                'is_enabled' => true,
                'default_value' => 'Short-Stay Schengen',
                'sort_order' => 5,
            ],
            [
                'field_key' => 'destination',
                'type' => 'text',
                'label_en' => 'Country',
                'label_ar' => 'الدولة',
                'is_required' => false,
                'is_enabled' => true,
                'default_value' => 'France',
                'sort_order' => 6,
            ],
            [
                'field_key' => 'travel_date',
                'type' => 'date',
                'label_en' => 'Travel Date',
                'label_ar' => 'تاريخ السفر',
                'is_required' => false,
                'is_enabled' => true,
                'sort_order' => 7,
            ],
            [
                'field_key' => 'message',
                'type' => 'textarea',
                'label_en' => 'Your Message',
                'label_ar' => 'رسالتك',
                'is_required' => false,
                'is_enabled' => true,
                'sort_order' => 8,
            ],
        ]);

        $form->assignments()->create([
            'assignment_type' => 'visa_country',
            'target_id' => $country->id,
            'display_position' => 'bottom',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->get(route('visas.country', $country))
            ->assertOk()
            ->assertSee('tw-visa-inquiry-shell', false)
            ->assertSee('tw-visa-inquiry-panel-info', false)
            ->assertSee('tw-visa-inquiry-panel-form', false)
            ->assertSee('Short-Stay Schengen')
            ->assertSee('15 to 30 working days')
            ->assertSee('name="whatsapp_number"', false)
            ->assertSee('name="service_type"', false)
            ->assertSee('name="destination"', false);
    }

    public function test_inquiry_submission_sends_meta_conversion_api_event_without_breaking_lead_storage(): void
    {
        Http::fake([
            'graph.facebook.com/*' => Http::response(['events_received' => 1], 200),
        ]);

        $this->seed(DatabaseSeeder::class);

        Setting::query()->firstOrFail()->update([
            'meta_conversion_api_enabled' => true,
            'meta_pixel_id' => '123456789012345',
            'meta_conversion_api_access_token' => 'meta-token',
            'meta_conversion_api_test_event_code' => 'TEST123',
        ]);

        $response = $this->post(route('inquiries.store'), [
            'type' => 'visa',
            'full_name' => 'Meta Lead',
            'phone' => '+20 106 050 0236',
            'email' => 'meta@example.com',
            'destination' => 'France',
            'service_type' => 'Short-Stay Schengen',
            'source_page' => 'france-visa-campaign-demo',
            'meta_event_id' => 'meta-event-123',
            'meta_event_name' => 'Lead',
            'message' => 'Need help with my file.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('inquiries', [
            'full_name' => 'Meta Lead',
            'destination' => 'France',
            'service_type' => 'Short-Stay Schengen',
        ]);

        Http::assertSent(function ($request) {
            $payload = $request->data();

            return str_contains((string) $request->url(), '/123456789012345/events')
                && ($payload['test_event_code'] ?? null) === 'TEST123'
                && ($payload['data'][0]['event_name'] ?? null) === 'Lead'
                && ($payload['data'][0]['event_id'] ?? null) === 'meta-event-123'
                && ($payload['data'][0]['action_source'] ?? null) === 'website'
                && filled($payload['data'][0]['user_data']['em'] ?? null)
                && ($payload['data'][0]['custom_data']['destination'] ?? null) === 'France';
        });
    }

    public function test_admin_can_update_visa_country_without_explicit_excerpt_fields(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $country = VisaCountry::query()->where('slug', 'france-visa')->firstOrFail();

        $response = $this->actingAs($admin)->put(route('admin.visa-countries.update', $country), [
            'visa_category_id' => $country->visa_category_id,
            'name_en' => $country->name_en,
            'name_ar' => $country->name_ar,
            'slug' => $country->slug,
            'overview_en' => 'Updated overview text for excerpt generation.',
            'overview_ar' => 'نص محدث لاختبار توليد المقتطف.',
            'detailed_description_en' => 'Detailed description fallback.',
            'detailed_description_ar' => 'وصف تفصيلي احتياطي.',
            'visa_type_en' => 'Short-Stay Schengen Visa',
            'visa_type_ar' => 'تأشيرة شنغن قصيرة الإقامة',
            'stay_duration_en' => 'Up to 90 days within 180 days',
            'stay_duration_ar' => 'حتى 90 يوماً خلال 180 يوماً',
            'processing_time_en' => '15 to 30 working days',
            'processing_time_ar' => 'من 15 إلى 30 يوم عمل',
            'why_choose_items' => [
                [
                    'title_en' => 'Document review',
                    'title_ar' => 'مراجعة المستندات',
                    'description_en' => 'Support with checking the file.',
                    'description_ar' => 'دعم في مراجعة الملف.',
                    'sort_order' => 1,
                    'is_active' => 1,
                ],
            ],
            'document_items' => [
                [
                    'name_en' => 'Passport',
                    'name_ar' => 'جواز السفر',
                    'description_en' => 'Valid passport.',
                    'description_ar' => 'جواز سفر ساري.',
                    'sort_order' => 1,
                    'is_active' => 1,
                ],
            ],
            'hero_overlay_opacity' => '0.45',
            'sort_order' => $country->sort_order,
            'is_active' => '1',
            'is_featured' => '1',
        ]);

        $response->assertRedirect(route('admin.visa-countries.index'));
        $response->assertSessionHasNoErrors();

        $country->refresh();

        $this->assertNotNull($country->excerpt_en);
        $this->assertNotNull($country->excerpt_ar);
        $this->assertStringContainsString('Visa type:', $country->excerpt_en);
        $this->assertStringContainsString('Expected processing time:', $country->excerpt_en);
        $this->assertStringNotContainsString('...', $country->excerpt_en);
    }

    public function test_france_visa_page_uses_curated_excerpt_content(): void
    {
        $this->seed(DatabaseSeeder::class);

        $country = VisaCountry::query()->where('slug', 'france-visa')->firstOrFail();

        $this->assertSame(
            "- France visa usually falls under the short-stay Schengen category.\n- Suitable for tourism, family visits, and selected business travel.\n- It usually allows stays of up to 90 days within 180 days.\n- Processing often takes around 15 to 30 working days depending on season and file completeness.\n- Travel Wave helps review documents, align bookings, and organize the file more clearly.",
            $country->excerpt_en
        );

        $this->assertSame(
            "- تأشيرة فرنسا تندرج غالبًا ضمن شنغن قصيرة الإقامة.\n- مناسبة للسياحة والزيارات العائلية وبعض رحلات الأعمال.\n- تسمح عادة بإقامة تصل إلى 90 يومًا خلال 180 يومًا.\n- مدة المعالجة غالبًا من 15 إلى 30 يوم عمل حسب الموسم واكتمال الملف.\n- تساعدك Travel Wave في مراجعة المستندات وتنظيم الحجوزات وتجهيز الملف بشكل أوضح وأكثر احترافية.",
            $country->excerpt_ar
        );

        $this->get(route('visas.country', $country))
            ->assertOk()
            ->assertSee('France visa usually falls under the short-stay Schengen category.', false);

        return;

        $this->assertSame(
            'تأشيرة فرنسا من أكثر تأشيرات شنغن طلبًا للسياحة والزيارات العائلية وبعض رحلات الأعمال، وتسمح عادة بإقامة تصل إلى 90 يومًا خلال 180 يومًا. تساعدك Travel Wave في تجهيز الملف بشكل منظم، ومراجعة المستندات، وتنسيق الحجوزات، وشرح خطوات التقديم والرسوم والمدة المتوقعة للمعالجة بطريقة أوضح وأسهل.',
            $country->excerpt_ar
        );

        $this->get(route('visas.country', $country))
            ->assertOk()
            ->assertSee('France visa usually falls under the short-stay Schengen category.', false);
    }

    public function test_header_footer_and_country_strip_admin_modules_render(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.header-settings.edit'))
            ->assertOk()
            ->assertSee('Header Styling');

        $this->actingAs($admin)
            ->get(route('admin.footer-settings.edit'))
            ->assertOk()
            ->assertSee('Footer Quick Links');

        $this->actingAs($admin)
            ->get(route('admin.home-country-strip.index'))
            ->assertOk()
            ->assertSee('Country Items')
            ->assertSee('Section Subtitle EN')
            ->assertSee('Autoplay Interval (ms)')
            ->assertSee('Transition Speed (ms)');
    }

    public function test_header_settings_can_be_saved_without_reuploading_logo(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $setting = Setting::query()->firstOrFail();
        $existingLogoPath = $setting->logo_path;

        $response = $this->actingAs($admin)->put(route('admin.header-settings.update'), [
            'header_background_color' => '#0A1E45',
            'header_text_color' => '#FFFFFF',
            'header_link_color' => '#FFFFFF',
            'header_hover_color' => '#FF8A00',
            'header_active_link_color' => '#FF8A00',
            'header_button_color' => '#FF8A00',
            'header_button_text_color' => '#0A1E45',
            'logo_width' => 240,
            'logo_height' => 72,
            'mobile_logo_width' => 160,
            'header_vertical_padding' => 12,
            'header_logo_enabled' => '1',
            'header_is_sticky' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertSame($existingLogoPath, $setting->fresh()->logo_path);
        $this->assertSame(240, $setting->fresh()->logo_width);
    }

    public function test_uploaded_header_logo_is_saved_and_rendered_on_frontend(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $setting = Setting::query()->firstOrFail();
        $logo = UploadedFile::fake()->create('travel-wave-new-logo.png', 24, 'image/png');

        $response = $this->actingAs($admin)->put(route('admin.header-settings.update'), [
            'header_background_color' => '#0A1E45',
            'header_text_color' => '#FFFFFF',
            'header_link_color' => '#FFFFFF',
            'header_hover_color' => '#FF8A00',
            'header_active_link_color' => '#FF8A00',
            'header_button_color' => '#FF8A00',
            'header_button_text_color' => '#0A1E45',
            'logo_width' => 240,
            'mobile_logo_width' => 160,
            'header_vertical_padding' => 12,
            'header_logo_enabled' => '1',
            'header_is_sticky' => '1',
            'logo' => $logo,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $newPath = $setting->fresh()->logo_path;

        $this->assertNotNull($newPath);
        $this->assertNotSame('settings/travel-wave-logo.svg', $newPath);
        Storage::disk('public')->assertExists($newPath);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('/storage/' . $newPath, false);
    }

    public function test_header_uses_main_logo_path_instead_of_footer_logo_path(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        Storage::disk('public')->put('settings/header-logo.png', 'header');
        Storage::disk('public')->put('settings/footer-logo.png', 'footer');

        Setting::query()->firstOrFail()->update([
            'logo_path' => 'settings/header-logo.png',
            'footer_logo_path' => 'settings/footer-logo.png',
        ]);

        $response = $this->get(route('home'))
            ->assertOk()
            ->assertSee('/storage/settings/header-logo.png', false);

        $content = $response->getContent();
        $this->assertStringContainsString('settings/header-logo.png', $content);
        $this->assertStringContainsString('settings/footer-logo.png', $content);
    }

    public function test_header_logo_handles_storage_prefixed_saved_paths(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        Storage::disk('public')->put('settings/prefixed-logo.png', 'header');

        Setting::query()->firstOrFail()->update([
            'logo_path' => 'storage/settings/prefixed-logo.png',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('/storage/settings/prefixed-logo.png', false)
            ->assertDontSee('/storage/storage/settings/prefixed-logo.png', false);
    }

    public function test_header_falls_back_to_brand_text_when_logo_file_is_missing(): void
    {
        $this->seed(DatabaseSeeder::class);

        Setting::query()->firstOrFail()->update([
            'logo_path' => 'settings/missing-logo.svg',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Travel Wave')
            ->assertDontSee('settings/missing-logo.svg');
    }

    public function test_hero_slide_mobile_image_falls_back_to_desktop_image(): void
    {
        $this->seed(DatabaseSeeder::class);

        HeroSlide::query()->firstOrFail()->update([
            'mobile_image_path' => null,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee("--slide-mobile-image: url('http://localhost/storage/hero-slides/slide-1.svg')", false);
    }

    public function test_home_destinations_carousel_outputs_dashboard_behavior_settings(): void
    {
        $this->seed(DatabaseSeeder::class);

        Setting::query()->firstOrFail()->update([
            'home_destinations_autoplay' => false,
            'home_destinations_interval' => 4800,
            'home_destinations_speed' => 900,
            'home_destinations_pause_on_hover' => false,
            'home_destinations_loop' => false,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-autoplay="false"', false)
            ->assertSee('data-interval="4800"', false)
            ->assertSee('data-speed="900"', false)
            ->assertSee('data-pause-on-hover="false"', false)
            ->assertSee('data-loop="false"', false);
    }
}
