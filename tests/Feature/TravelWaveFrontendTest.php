<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use App\Models\Inquiry;
use App\Models\ChatbotInteraction;
use App\Models\ChatbotKnowledgeEntry;
use App\Models\ChatbotKnowledgeItem;
use App\Models\AccountingCustomerAccount;
use App\Models\CrmInformation;
use App\Models\CrmFollowUp;
use App\Models\LeadForm;
use App\Models\LeadFormAssignment;
use App\Models\LeadFormField;
use App\Models\MarketingLandingPage;
use App\Models\MarketingLandingPageEvent;
use App\Models\MapSection;
use App\Models\MediaAsset;
use App\Models\Page;
use App\Models\SeoMetaEntry;
use App\Models\SeoRedirect;
use App\Models\SeoSetting;
use App\Models\Setting;
use App\Models\TrackingIntegration;
use App\Models\UtmCampaign;
use App\Models\UtmVisit;
use App\Notifications\AdminDatabaseNotification;
use App\Models\Permission;
use App\Models\Role;
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

    public function test_footer_renders_social_links_as_icons_only_for_configured_platforms(): void
    {
        $this->seed(DatabaseSeeder::class);

        Setting::query()->firstOrFail()->update([
            'facebook_url' => 'https://facebook.com/travelwave',
            'instagram_url' => 'https://instagram.com/travelwave',
            'twitter_url' => 'https://x.com/travelwave',
            'youtube_url' => null,
            'tiktok_url' => 'https://tiktok.com/@travelwave',
            'linkedin_url' => 'https://linkedin.com/company/travelwave',
            'snapchat_url' => 'https://snapchat.com/add/travelwave',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('tw-footer-social-link', false)
            ->assertSee('https://facebook.com/travelwave', false)
            ->assertSee('https://instagram.com/travelwave', false)
            ->assertSee('https://x.com/travelwave', false)
            ->assertSee('https://tiktok.com/@travelwave', false)
            ->assertSee('https://linkedin.com/company/travelwave', false)
            ->assertSee('https://snapchat.com/add/travelwave', false)
            ->assertDontSee('YouTube</a>', false);
    }

    public function test_footer_whatsapp_number_opens_direct_whatsapp_link(): void
    {
        $this->seed(DatabaseSeeder::class);

        Setting::query()->firstOrFail()->update([
            'whatsapp_number' => '+20 106 050 0236',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('tw-footer-whatsapp-link', false)
            ->assertSee('https://wa.me/201060500236', false)
            ->assertSee('+20 106 050 0236');
    }

    public function test_footer_phone_numbers_render_as_clickable_tel_links(): void
    {
        $this->seed(DatabaseSeeder::class);

        Setting::query()->firstOrFail()->update([
            'phone' => '+20 100 123 4567',
            'secondary_phone' => '0100 765 4321',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('tel:+201001234567', false)
            ->assertSee('tel:01007654321', false)
            ->assertSee('+20 100 123 4567')
            ->assertSee('0100 765 4321');
    }

    public function test_header_phone_number_renders_as_clickable_tel_link(): void
    {
        $this->seed(DatabaseSeeder::class);

        Setting::query()->firstOrFail()->update([
            'phone' => '+20 106 050 0236',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('tw-navbar-phone', false)
            ->assertSee('tel:+201060500236', false)
            ->assertSee('+20 106 050 0236');
    }

    public function test_footer_settings_can_save_new_social_platform_urls(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.footer-settings.update'), [
            'twitter_url' => 'https://x.com/travelwave',
            'linkedin_url' => 'https://linkedin.com/company/travelwave',
            'snapchat_url' => 'https://snapchat.com/add/travelwave',
            'telegram_url' => 'https://t.me/travelwave',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('settings', [
            'twitter_url' => 'https://x.com/travelwave',
            'linkedin_url' => 'https://linkedin.com/company/travelwave',
            'snapchat_url' => 'https://snapchat.com/add/travelwave',
            'telegram_url' => 'https://t.me/travelwave',
        ]);
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

    public function test_pages_manager_supports_create_view_duplicate_and_trash_restore_for_custom_pages(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.pages.index'))
            ->assertOk()
            ->assertSee('Create New Page')
            ->assertSee('Duplicate')
            ->assertSee('Pages Trash');

        $this->actingAs($admin)->post(route('admin.pages.store'), [
            'key' => 'travel-tips',
            'title_en' => 'Travel Tips',
            'title_ar' => 'نصائح السفر',
            'slug' => 'travel-tips',
            'is_active' => '1',
        ])->assertRedirect();

        $page = Page::query()->where('key', 'travel-tips')->firstOrFail();
        $this->assertSame('travel-tips', $page->slug);
        $this->assertTrue($page->is_active);

        $this->get(route('pages.show', $page))
            ->assertOk()
            ->assertSee('Travel Tips');

        $this->actingAs($admin)->post(route('admin.pages.duplicate', $page))
            ->assertRedirect();

        $duplicate = Page::query()->where('key', 'travel_tips_copy')->firstOrFail();
        $this->assertSame('travel-tips-copy', $duplicate->slug);
        $this->assertFalse($duplicate->is_active);

        $this->actingAs($admin)->delete(route('admin.pages.destroy', $page))
            ->assertRedirect(route('admin.pages.index'));

        $this->assertSoftDeleted('pages', ['id' => $page->id]);

        $this->actingAs($admin)
            ->get(route('admin.pages.trash'))
            ->assertOk()
            ->assertSee('Travel Tips')
            ->assertSee('Restore')
            ->assertSee('Delete Permanently');

        $this->actingAs($admin)->post(route('admin.pages.restore', $page->id))
            ->assertRedirect(route('admin.pages.trash'));

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'deleted_at' => null,
        ]);
    }

    public function test_core_pages_can_be_viewed_and_moved_to_trash_then_deleted_permanently(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $about = Page::query()->where('key', 'about')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.pages.index'))
            ->assertOk()
            ->assertSee(route('about'), false);

        $this->actingAs($admin)
            ->delete(route('admin.pages.destroy', $about))
            ->assertRedirect(route('admin.pages.index'));

        $this->assertSoftDeleted('pages', ['id' => $about->id]);

        $this->actingAs($admin)
            ->get(route('about'))
            ->assertNotFound();

        $this->actingAs($admin)
            ->post(route('admin.pages.restore', $about->id))
            ->assertRedirect(route('admin.pages.trash'));

        $this->assertDatabaseHas('pages', [
            'id' => $about->id,
            'deleted_at' => null,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.pages.destroy', $about))
            ->assertRedirect(route('admin.pages.index'));

        $this->assertSoftDeleted('pages', ['id' => $about->id]);

        $this->actingAs($admin)
            ->delete(route('admin.pages.force-destroy', $about->id))
            ->assertRedirect(route('admin.pages.trash'));

        $this->assertDatabaseMissing('pages', ['id' => $about->id]);
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
            ->assertSee('GTM Container ID')
            ->assertSee('Visibility Rules')
            ->assertSee('TikTok Pixel')
            ->assertSee('Snap Pixel')
            ->assertSee('X / Twitter Pixel')
            ->assertSee('LinkedIn Insight Tag')
            ->assertSee('Pinterest Tag')
            ->assertSee('Google Ads Conversion Tracking')
            ->assertSee('Microsoft Clarity');
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

    public function test_frontend_renders_extended_tracking_tools_from_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        TrackingIntegration::query()->create([
            'name' => 'TikTok Pixel',
            'slug' => 'tiktok-pixel',
            'integration_type' => TrackingIntegration::TYPE_TIKTOK_PIXEL,
            'platform' => 'TikTok',
            'tracking_code' => 'C123ABC456DEF',
            'placement' => 'standard',
            'visibility_mode' => 'all',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        TrackingIntegration::query()->create([
            'name' => 'LinkedIn Insight',
            'slug' => 'linkedin-insight',
            'integration_type' => TrackingIntegration::TYPE_LINKEDIN_INSIGHT,
            'platform' => 'LinkedIn',
            'tracking_code' => '1234567',
            'placement' => 'standard',
            'visibility_mode' => 'all',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        TrackingIntegration::query()->create([
            'name' => 'Google Ads',
            'slug' => 'google-ads',
            'integration_type' => TrackingIntegration::TYPE_GOOGLE_ADS,
            'platform' => 'Google Ads',
            'tracking_code' => 'AW-123456789',
            'settings' => ['conversion_label' => 'AbCdEFgHiJkLmNoP'],
            'placement' => 'standard',
            'visibility_mode' => 'all',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        TrackingIntegration::query()->create([
            'name' => 'Microsoft Clarity',
            'slug' => 'microsoft-clarity',
            'integration_type' => TrackingIntegration::TYPE_MICROSOFT_CLARITY,
            'platform' => 'Microsoft',
            'tracking_code' => 'clarity123',
            'placement' => 'standard',
            'visibility_mode' => 'all',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee("ttq.load('C123ABC456DEF');", false)
            ->assertSee("window._linkedin_data_partner_ids.push('1234567');", false)
            ->assertSee('https://www.googletagmanager.com/gtag/js?id=AW-123456789', false)
            ->assertSee('(window, document, "clarity", "script", "clarity123");', false);
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

    public function test_managed_form_field_requirement_and_visibility_follow_dashboard_configuration(): void
    {
        $this->seed(DatabaseSeeder::class);

        $form = LeadForm::query()->create([
            'name' => 'Configurable Lead Form',
            'slug' => 'configurable-lead-form',
            'form_category' => 'contact',
            'title_en' => 'Configurable Form',
            'title_ar' => 'نموذج قابل للتحكم',
            'submit_text_en' => 'Send',
            'submit_text_ar' => 'أرسل',
            'success_message_en' => 'Managed form success',
            'success_message_ar' => 'تم الإرسال',
            'is_active' => true,
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
                'label_en' => 'Phone',
                'label_ar' => 'رقم الهاتف',
                'is_required' => true,
                'is_enabled' => true,
                'sort_order' => 2,
            ],
            [
                'field_key' => 'email',
                'type' => 'email',
                'label_en' => 'Email',
                'label_ar' => 'البريد الإلكتروني',
                'validation_rule' => 'required|email|max:255',
                'is_required' => false,
                'is_enabled' => true,
                'sort_order' => 3,
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
            ->assertSee('name="email"', false);

        $optionalResponse = $this->from(route('about'))->post(route('inquiries.store'), [
            'lead_form_id' => $form->id,
            'lead_form_assignment_id' => $assignment->id,
            'type' => 'contact',
            'source_page' => 'about',
            'display_position' => 'below_hero',
            'preferred_language' => 'en',
            'success_message' => 'Managed form success',
            'full_name' => 'Optional Email Lead',
            'phone' => '+20 100 555 0001',
        ]);

        $optionalResponse->assertRedirect(route('about'));
        $optionalResponse->assertSessionHas('success', 'Managed form success');
        $this->assertDatabaseHas('inquiries', [
            'lead_form_id' => $form->id,
            'full_name' => 'Optional Email Lead',
            'email' => null,
        ]);

        $emailField = LeadFormField::query()
            ->where('lead_form_id', $form->id)
            ->where('field_key', 'email')
            ->firstOrFail();

        $emailField->update([
            'is_required' => true,
            'is_enabled' => true,
        ]);

        $requiredResponse = $this->from(route('about'))->post(route('inquiries.store'), [
            'lead_form_id' => $form->id,
            'lead_form_assignment_id' => $assignment->id,
            'type' => 'contact',
            'source_page' => 'about',
            'display_position' => 'below_hero',
            'preferred_language' => 'en',
            'success_message' => 'Managed form success',
            'full_name' => 'Required Email Lead',
            'phone' => '+20 100 555 0002',
        ]);

        $requiredResponse->assertRedirect(route('about'));
        $requiredResponse->assertSessionHasErrors(['email']);

        $emailField->update([
            'is_required' => false,
            'is_enabled' => false,
        ]);

        $this->get(route('about'))
            ->assertOk()
            ->assertDontSee('name="email"', false);

        $hiddenResponse = $this->from(route('about'))->post(route('inquiries.store'), [
            'lead_form_id' => $form->id,
            'lead_form_assignment_id' => $assignment->id,
            'type' => 'contact',
            'source_page' => 'about',
            'display_position' => 'below_hero',
            'preferred_language' => 'en',
            'success_message' => 'Managed form success',
            'full_name' => 'Hidden Email Lead',
            'phone' => '+20 100 555 0003',
        ]);

        $hiddenResponse->assertRedirect(route('about'));
        $hiddenResponse->assertSessionHas('success', 'Managed form success');
        $this->assertDatabaseHas('inquiries', [
            'lead_form_id' => $form->id,
            'full_name' => 'Hidden Email Lead',
            'email' => null,
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

    public function test_country_items_manager_supports_view_duplicate_trash_restore_and_permanent_delete(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $item = \App\Models\HomeCountryStripItem::query()->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.home-country-strip.index'))
            ->assertOk()
            ->assertSee('Country Items Trash')
            ->assertSee('Duplicate');

        $this->assertNotSame('#', $item->resolvedUrl());

        $this->actingAs($admin)
            ->post(route('admin.home-country-strip.duplicate', $item))
            ->assertRedirect();

        $duplicate = \App\Models\HomeCountryStripItem::query()
            ->where('name_en', 'like', '%Copy')
            ->latest('id')
            ->firstOrFail();

        $this->assertFalse($duplicate->is_active);
        $this->assertFalse($duplicate->show_on_homepage);

        $this->actingAs($admin)
            ->delete(route('admin.home-country-strip.destroy', $item))
            ->assertRedirect(route('admin.home-country-strip.index'));

        $this->assertSoftDeleted('home_country_strip_items', ['id' => $item->id]);

        $this->actingAs($admin)
            ->get(route('admin.home-country-strip.trash'))
            ->assertOk()
            ->assertSee($item->displayName('en'))
            ->assertSee('Restore')
            ->assertSee('Delete Permanently');

        $this->actingAs($admin)
            ->post(route('admin.home-country-strip.restore', $item->id))
            ->assertRedirect(route('admin.home-country-strip.trash'));

        $this->assertDatabaseHas('home_country_strip_items', [
            'id' => $item->id,
            'deleted_at' => null,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.home-country-strip.destroy', $item))
            ->assertRedirect(route('admin.home-country-strip.index'));

        $this->assertSoftDeleted('home_country_strip_items', ['id' => $item->id]);

        $this->actingAs($admin)
            ->delete(route('admin.home-country-strip.force-destroy', $item->id))
            ->assertRedirect(route('admin.home-country-strip.trash'));

        $this->assertDatabaseMissing('home_country_strip_items', ['id' => $item->id]);
    }

    public function test_managed_content_modules_support_duplicate_and_trash_workflows(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $visaCategory = \App\Models\VisaCategory::query()->firstOrFail();
        $visaCountry = \App\Models\VisaCountry::query()->firstOrFail();
        $destination = Destination::query()->firstOrFail();
        $testimonial = \App\Models\Testimonial::query()->firstOrFail();
        $menuItem = \App\Models\MenuItem::query()->firstOrFail();
        $blogCategory = \App\Models\BlogCategory::query()->firstOrFail();
        $blogPost = \App\Models\BlogPost::query()->firstOrFail();

        $this->actingAs($admin)->get(route('admin.visa-categories.index'))->assertOk()->assertSee('Duplicate')->assertSee('Trash');
        $this->actingAs($admin)->get(route('admin.visa-countries.index'))->assertOk()->assertSee('Duplicate')->assertSee('Trash');
        $this->actingAs($admin)->get(route('admin.destinations.index'))->assertOk()->assertSee('Duplicate')->assertSee('Trash');
        $this->actingAs($admin)->get(route('admin.testimonials.index'))->assertOk()->assertSee('Duplicate')->assertSee('Trash');
        $this->actingAs($admin)->get(route('admin.menu-items.index'))->assertOk()->assertSee('Duplicate')->assertSee('Trash');
        $this->actingAs($admin)->get(route('admin.blog-categories.index'))->assertOk()->assertSee('Duplicate')->assertSee('Trash');
        $this->actingAs($admin)->get(route('admin.blog-posts.index'))->assertOk()->assertSee('Duplicate')->assertSee('Trash');

        $this->actingAs($admin)->post(route('admin.visa-categories.duplicate', $visaCategory))->assertRedirect();
        $this->actingAs($admin)->post(route('admin.visa-countries.duplicate', $visaCountry))->assertRedirect();
        $this->actingAs($admin)->post(route('admin.destinations.duplicate', $destination))->assertRedirect();
        $this->actingAs($admin)->post(route('admin.testimonials.duplicate', $testimonial))->assertRedirect();
        $this->actingAs($admin)->post(route('admin.menu-items.duplicate', $menuItem))->assertRedirect();
        $this->actingAs($admin)->post(route('admin.blog-categories.duplicate', $blogCategory))->assertRedirect();
        $this->actingAs($admin)->post(route('admin.blog-posts.duplicate', $blogPost))->assertRedirect();

        $this->actingAs($admin)->delete(route('admin.visa-categories.destroy', $visaCategory))->assertRedirect();
        $this->actingAs($admin)->delete(route('admin.visa-countries.destroy', $visaCountry))->assertRedirect();
        $this->actingAs($admin)->delete(route('admin.destinations.destroy', $destination))->assertRedirect();
        $this->actingAs($admin)->delete(route('admin.testimonials.destroy', $testimonial))->assertRedirect();
        $this->actingAs($admin)->delete(route('admin.menu-items.destroy', $menuItem))->assertRedirect();
        $this->actingAs($admin)->delete(route('admin.blog-categories.destroy', $blogCategory))->assertRedirect();
        $this->actingAs($admin)->delete(route('admin.blog-posts.destroy', $blogPost))->assertRedirect();

        $this->assertSoftDeleted('visa_categories', ['id' => $visaCategory->id]);
        $this->assertSoftDeleted('visa_countries', ['id' => $visaCountry->id]);
        $this->assertSoftDeleted('destinations', ['id' => $destination->id]);
        $this->assertSoftDeleted('testimonials', ['id' => $testimonial->id]);
        $this->assertSoftDeleted('menu_items', ['id' => $menuItem->id]);
        $this->assertSoftDeleted('blog_categories', ['id' => $blogCategory->id]);
        $this->assertSoftDeleted('blog_posts', ['id' => $blogPost->id]);

        $this->actingAs($admin)->post(route('admin.visa-categories.restore', $visaCategory->id))->assertRedirect();
        $this->actingAs($admin)->post(route('admin.visa-countries.restore', $visaCountry->id))->assertRedirect();
        $this->actingAs($admin)->post(route('admin.destinations.restore', $destination->id))->assertRedirect();
        $this->actingAs($admin)->post(route('admin.testimonials.restore', $testimonial->id))->assertRedirect();
        $this->actingAs($admin)->post(route('admin.menu-items.restore', $menuItem->id))->assertRedirect();
        $this->actingAs($admin)->post(route('admin.blog-categories.restore', $blogCategory->id))->assertRedirect();
        $this->actingAs($admin)->post(route('admin.blog-posts.restore', $blogPost->id))->assertRedirect();

        $this->assertDatabaseHas('visa_categories', ['id' => $visaCategory->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('visa_countries', ['id' => $visaCountry->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('destinations', ['id' => $destination->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('testimonials', ['id' => $testimonial->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('menu_items', ['id' => $menuItem->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('blog_categories', ['id' => $blogCategory->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('blog_posts', ['id' => $blogPost->id, 'deleted_at' => null]);

        $this->actingAs($admin)->delete(route('admin.blog-posts.destroy', $blogPost))->assertRedirect();
        $this->assertSoftDeleted('blog_posts', ['id' => $blogPost->id]);
        $this->actingAs($admin)->delete(route('admin.blog-posts.force-destroy', $blogPost->id))->assertRedirect();
        $this->assertDatabaseMissing('blog_posts', ['id' => $blogPost->id]);
    }

    public function test_hero_slide_form_renders_mobile_banner_safe_area_guidance(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.hero-slides.create'))
            ->assertOk()
            ->assertSee('Desktop Banner Image')
            ->assertSee('Mobile Banner Image')
            ->assertSee('1204 x 800')
            ->assertSee('900 x 1200')
            ->assertSee('Mobile safe area');
    }

    public function test_header_settings_can_be_saved_without_reuploading_logo(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $setting = Setting::query()->firstOrFail();
        $existingLogoPath = $setting->header_logo_path ?: $setting->logo_path;

        $response = $this->actingAs($admin)->put(route('admin.header-settings.update'), [
            'header_background_color' => '#0A1E45',
            'header_text_color' => '#FFFFFF',
            'header_link_color' => '#FFFFFF',
            'header_hover_color' => '#FF8A00',
            'header_active_link_color' => '#FF8A00',
            'header_button_color' => '#FF8A00',
            'header_button_text_color' => '#0A1E45',
            'header_logo_width' => 240,
            'header_logo_height' => 72,
            'header_mobile_logo_width' => 160,
            'header_vertical_padding' => 12,
            'header_logo_enabled' => '1',
            'header_is_sticky' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertSame($existingLogoPath, $setting->fresh()->header_logo_path ?: $setting->fresh()->logo_path);
        $this->assertSame(240, $setting->fresh()->header_logo_width);
    }

    public function test_header_settings_can_save_locale_specific_alignment_options(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $response = $this->actingAs($admin)->put(route('admin.header-settings.update'), [
            'header_logo_position_en' => 'left',
            'header_logo_position_ar' => 'right',
            'header_menu_position_en' => 'left',
            'header_menu_position_ar' => 'right',
            'header_logo_enabled' => '1',
            'header_is_sticky' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $fresh = Setting::query()->firstOrFail();

        $this->assertSame('left', $fresh->header_logo_position_en);
        $this->assertSame('right', $fresh->header_logo_position_ar);
        $this->assertSame('left', $fresh->header_menu_position_en);
        $this->assertSame('right', $fresh->header_menu_position_ar);
    }

    public function test_header_uses_locale_specific_alignment_classes_on_frontend(): void
    {
        $this->seed(DatabaseSeeder::class);

        Setting::query()->firstOrFail()->update([
            'header_logo_position_en' => 'left',
            'header_logo_position_ar' => 'right',
            'header_menu_position_en' => 'left',
            'header_menu_position_ar' => 'right',
        ]);

        $this->withSession(['locale' => 'en'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('tw-navbar-shell--logo-left', false)
            ->assertSee('tw-navbar-collapse-shell--menu-left', false);

        $this->withSession(['locale' => 'ar'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('tw-navbar-shell--logo-right', false)
            ->assertSee('tw-navbar-collapse-shell--menu-right', false);
    }

    public function test_header_settings_can_update_aspect_ratio_and_sticky_flags_without_logo_upload(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $setting = Setting::query()->firstOrFail();
        $existingLogoPath = $setting->header_logo_path ?: $setting->logo_path;

        $response = $this->actingAs($admin)->put(route('admin.header-settings.update'), [
            'header_logo_keep_aspect_ratio' => '1',
            'header_logo_enabled' => '1',
            'header_is_sticky' => '0',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $fresh = $setting->fresh();
        $this->assertSame($existingLogoPath, $fresh->header_logo_path ?: $fresh->logo_path);
        $this->assertTrue((bool) $fresh->header_logo_keep_aspect_ratio);
        $this->assertFalse((bool) $fresh->header_is_sticky);
    }

    public function test_header_settings_can_save_unchecked_boolean_toggles_as_false(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $setting = Setting::query()->firstOrFail();
        $setting->update([
            'header_logo_keep_aspect_ratio' => true,
            'header_logo_enabled' => true,
            'header_is_sticky' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.header-settings.update'), [
            'header_logo_keep_aspect_ratio' => '0',
            'header_logo_enabled' => '0',
            'header_is_sticky' => '0',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $fresh = $setting->fresh();
        $this->assertFalse((bool) $fresh->header_logo_keep_aspect_ratio);
        $this->assertFalse((bool) $fresh->header_logo_enabled);
        $this->assertFalse((bool) $fresh->header_is_sticky);
    }

    public function test_brand_settings_can_update_favicon_only_without_optional_dimensions(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $setting = Setting::query()->firstOrFail();
        $existingLogoPath = $setting->logo_path;
        $existingFooterLogoPath = $setting->footer_logo_path;
        $favicon = UploadedFile::fake()->create('favicon.png', 16, 'image/png');

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'favicon' => $favicon,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $fresh = $setting->fresh();
        $this->assertSame($existingLogoPath, $fresh->logo_path);
        $this->assertSame($existingFooterLogoPath, $fresh->footer_logo_path);
        $this->assertNotNull($fresh->favicon_path);
        Storage::disk('public')->assertExists($fresh->favicon_path);
    }

    public function test_header_settings_can_update_logo_only_without_optional_dimensions(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $setting = Setting::query()->firstOrFail();
        $logo = UploadedFile::fake()->create('logo.png', 24, 'image/png');

        $response = $this->actingAs($admin)->put(route('admin.header-settings.update'), [
            'header_logo' => $logo,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $fresh = $setting->fresh();
        $this->assertNotNull($fresh->header_logo_path);
        Storage::disk('public')->assertExists($fresh->header_logo_path);
        $this->assertNotNull($fresh->header_logo_width);
    }

    public function test_footer_settings_can_update_footer_logo_only(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $setting = Setting::query()->firstOrFail();
        $footerLogo = UploadedFile::fake()->create('footer-logo.png', 24, 'image/png');

        $response = $this->actingAs($admin)->put(route('admin.footer-settings.update'), [
            'footer_logo' => $footerLogo,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $fresh = $setting->fresh();
        $this->assertNotNull($fresh->footer_logo_path);
        Storage::disk('public')->assertExists($fresh->footer_logo_path);
        $this->assertNotNull($fresh->footer_vertical_padding);
    }

    public function test_header_logo_size_settings_are_saved_safely_and_applied_on_frontend(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        Storage::disk('public')->put('settings/header-logo.png', 'header-logo-binary');

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $setting = Setting::query()->firstOrFail();
        $setting->update([
            'header_logo_path' => 'settings/header-logo.png',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.header-settings.update'), [
            'header_logo_display_mode' => 'custom',
            'header_logo_width' => 260,
            'header_logo_height' => 88,
            'header_mobile_logo_width' => 180,
            'header_logo_keep_aspect_ratio' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $fresh = $setting->fresh();
        $this->assertSame(260, $fresh->header_logo_width);
        $this->assertSame(88, $fresh->header_logo_height);
        $this->assertSame(180, $fresh->header_mobile_logo_width);
        $this->assertTrue((bool) $fresh->header_logo_keep_aspect_ratio);
        $this->assertSame('custom', $fresh->header_logo_display_mode);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('width:min(100%, 260px);height:88px;object-fit:contain;', false);
    }

    public function test_footer_logo_size_settings_are_saved_safely_and_applied_on_frontend(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        Storage::disk('public')->put('settings/footer-logo.png', 'footer-logo-binary');

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $setting = Setting::query()->firstOrFail();
        $setting->update([
            'footer_logo_path' => 'settings/footer-logo.png',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.footer-settings.update'), [
            'footer_logo_display_mode' => 'custom',
            'footer_logo_width' => 230,
            'footer_logo_height' => 76,
            'footer_logo_keep_aspect_ratio' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $fresh = $setting->fresh();
        $this->assertSame(230, $fresh->footer_logo_width);
        $this->assertSame(76, $fresh->footer_logo_height);
        $this->assertTrue((bool) $fresh->footer_logo_keep_aspect_ratio);
        $this->assertSame('custom', $fresh->footer_logo_display_mode);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('width:min(100%, 230px);height:76px;object-fit:contain;', false);
    }

    public function test_header_logo_original_mode_preserves_natural_rendering_without_forced_square_style(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        Storage::disk('public')->put('settings/original-header-logo.png', 'original-header-logo');

        Setting::query()->firstOrFail()->update([
            'header_logo_path' => 'settings/original-header-logo.png',
            'header_logo_display_mode' => 'original',
            'header_logo_width' => 320,
            'header_logo_height' => 120,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('width:auto;height:auto;max-height:none;object-fit:initial;', false)
            ->assertDontSee('width:min(100%, 320px);height:120px;', false);
    }

    public function test_header_logo_display_mode_persists_after_save_and_reload(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.header-settings.update'), [
            'header_logo_display_mode' => 'cover',
            'header_logo_enabled' => '1',
            'header_is_sticky' => '1',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $setting = Setting::query()->firstOrFail();
        $this->assertSame('cover', $setting->header_logo_display_mode);
        $this->assertSame('cover', $setting->logoDisplayModeFor('header'));

        $this->actingAs($admin)
            ->get(route('admin.header-settings.edit'))
            ->assertOk()
            ->assertSee('<option value="cover" selected>Cover</option>', false);
    }

    public function test_header_and_footer_logo_settings_are_fully_independent(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $setting = Setting::query()->firstOrFail();

        $setting->update([
            'header_logo_width' => 210,
            'header_logo_height' => 70,
            'header_logo_keep_aspect_ratio' => true,
            'footer_logo_width' => 190,
            'footer_logo_height' => 62,
            'footer_logo_keep_aspect_ratio' => true,
        ]);

        $this->actingAs($admin)->put(route('admin.header-settings.update'), [
            'header_logo_width' => 280,
            'header_logo_height' => 90,
            'header_logo_keep_aspect_ratio' => '0',
            'header_mobile_logo_width' => 175,
            'header_logo_enabled' => '1',
            'header_is_sticky' => '1',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $fresh = $setting->fresh();
        $this->assertSame(280, $fresh->header_logo_width);
        $this->assertSame(90, $fresh->header_logo_height);
        $this->assertFalse((bool) $fresh->header_logo_keep_aspect_ratio);
        $this->assertSame(190, $fresh->footer_logo_width);
        $this->assertSame(62, $fresh->footer_logo_height);
        $this->assertTrue((bool) $fresh->footer_logo_keep_aspect_ratio);

        $this->actingAs($admin)->put(route('admin.footer-settings.update'), [
            'footer_logo_width' => 240,
            'footer_logo_height' => 78,
            'footer_logo_keep_aspect_ratio' => '0',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $fresh = $setting->fresh();
        $this->assertSame(280, $fresh->header_logo_width);
        $this->assertSame(90, $fresh->header_logo_height);
        $this->assertFalse((bool) $fresh->header_logo_keep_aspect_ratio);
        $this->assertSame(240, $fresh->footer_logo_width);
        $this->assertSame(78, $fresh->footer_logo_height);
        $this->assertFalse((bool) $fresh->footer_logo_keep_aspect_ratio);
    }

    public function test_header_settings_edit_page_reloads_saved_values_instead_of_old_ones(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.header-settings.update'), [
            'header_logo_width' => 314,
            'header_logo_height' => 92,
            'header_mobile_logo_width' => 181,
            'header_logo_keep_aspect_ratio' => '0',
            'header_logo_enabled' => '1',
            'header_is_sticky' => '0',
            'header_vertical_padding' => 14,
            'header_button_text_color' => '#123456',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->actingAs($admin)
            ->get(route('admin.header-settings.edit'))
            ->assertOk()
            ->assertSee('name="header_logo_width" value="314"', false)
            ->assertSee('name="header_logo_height" value="92"', false)
            ->assertSee('name="header_mobile_logo_width" value="181"', false)
            ->assertSee('name="header_vertical_padding" value="14"', false)
            ->assertSee('name="header_button_text_color" value="#123456"', false)
            ->assertDontSee('name="header_logo_keep_aspect_ratio" value="1" id="header_logo_keep_aspect_ratio" checked', false)
            ->assertDontSee('name="header_is_sticky" value="1" id="header_is_sticky" checked', false);
    }

    public function test_brand_settings_can_update_text_fields_without_image_changes(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $setting = Setting::query()->firstOrFail();
        $existingFaviconPath = $setting->favicon_path;

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'site_name_en' => 'Travel Wave Updated',
            'site_name_ar' => 'ترافل ويف الجديدة',
            'primary_color' => '#101820',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $fresh = $setting->fresh();
        $this->assertSame('Travel Wave Updated', $fresh->site_name_en);
        $this->assertSame('ترافل ويف الجديدة', $fresh->site_name_ar);
        $this->assertSame('#101820', $fresh->primary_color);
        $this->assertSame($existingFaviconPath, $fresh->favicon_path);
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
            'header_logo_width' => 240,
            'header_mobile_logo_width' => 160,
            'header_vertical_padding' => 12,
            'header_logo_enabled' => '1',
            'header_is_sticky' => '1',
            'header_logo' => $logo,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $newPath = $setting->fresh()->header_logo_path;

        $this->assertNotNull($newPath);
        $this->assertNotSame('settings/travel-wave-logo.svg', $newPath);
        Storage::disk('public')->assertExists($newPath);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('/storage/' . $newPath, false);
    }

    public function test_existing_media_library_png_can_be_selected_as_header_logo_and_rendered(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        Storage::disk('public')->put('media-library/header-selected-logo.png', 'header-selected-logo');
        MediaAsset::query()->create([
            'title' => 'Header Selected Logo',
            'disk' => 'public',
            'directory' => 'media-library',
            'file_name' => 'header-selected-logo.png',
            'path' => 'media-library/header-selected-logo.png',
            'mime_type' => 'image/png',
            'extension' => 'png',
        ]);

        $this->actingAs($admin)->put(route('admin.header-settings.update'), [
            'header_logo_existing_path' => 'media-library/header-selected-logo.png',
            'header_logo_display_mode' => 'original',
            'header_logo_enabled' => '1',
            'header_is_sticky' => '1',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $setting = Setting::query()->firstOrFail();
        $this->assertSame('media-library/header-selected-logo.png', $setting->header_logo_path);
        $this->assertSame('media-library/header-selected-logo.png', $setting->logo_path);

        $this->actingAs($admin)
            ->get(route('admin.header-settings.edit'))
            ->assertOk()
            ->assertSee('/storage/media-library/header-selected-logo.png', false);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('/storage/media-library/header-selected-logo.png', false);
    }

    public function test_existing_media_library_png_can_be_selected_as_footer_logo_independently(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        Storage::disk('public')->put('media-library/footer-selected-logo.png', 'footer-selected-logo');
        MediaAsset::query()->create([
            'title' => 'Footer Selected Logo',
            'disk' => 'public',
            'directory' => 'media-library',
            'file_name' => 'footer-selected-logo.png',
            'path' => 'media-library/footer-selected-logo.png',
            'mime_type' => 'image/png',
            'extension' => 'png',
        ]);

        $this->actingAs($admin)->put(route('admin.footer-settings.update'), [
            'footer_logo_existing_path' => 'media-library/footer-selected-logo.png',
            'footer_logo_display_mode' => 'original',
            'footer_logo_keep_aspect_ratio' => '1',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $setting = Setting::query()->firstOrFail();
        $this->assertSame('media-library/footer-selected-logo.png', $setting->footer_logo_path);
        $this->assertNotSame($setting->header_logo_path, $setting->footer_logo_path);

        $this->actingAs($admin)
            ->get(route('admin.footer-settings.edit'))
            ->assertOk()
            ->assertSee('/storage/media-library/footer-selected-logo.png', false);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('/storage/media-library/footer-selected-logo.png', false);
    }

    public function test_header_uses_main_logo_path_instead_of_footer_logo_path(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        Storage::disk('public')->put('settings/header-logo.png', 'header');
        Storage::disk('public')->put('settings/footer-logo.png', 'footer');

        Setting::query()->firstOrFail()->update([
            'header_logo_path' => 'settings/header-logo.png',
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
            'header_logo_path' => 'storage/settings/prefixed-logo.png',
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
            'header_logo_path' => 'settings/missing-logo.svg',
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

    public function test_default_roles_and_permissions_are_seeded_for_admin_access(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->assertTrue(Role::query()->where('slug', 'super-admin')->exists());
        $this->assertTrue(Permission::query()->where('slug', 'dashboard.access')->exists());
        $this->assertTrue($admin->roles()->where('slug', 'super-admin')->exists());
        $this->assertTrue($admin->canAccessDashboard());
        $this->assertTrue($admin->hasPermission('users.view'));
        $this->assertTrue($admin->hasPermission('seo.manage'));
    }

    public function test_admin_users_roles_and_permissions_pages_render_for_super_admin(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Users Management');

        $this->actingAs($admin)->get(route('admin.roles.index'))
            ->assertOk()
            ->assertSee('Roles Management');

        $this->actingAs($admin)->get(route('admin.permissions.index'))
            ->assertOk()
            ->assertSee('Permissions Management');
    }

    public function test_user_without_users_permission_cannot_access_users_management(): void
    {
        $this->seed(DatabaseSeeder::class);

        $viewerRole = Role::query()->where('slug', 'viewer-analyst')->firstOrFail();

        $user = User::query()->create([
            'name' => 'Viewer User',
            'email' => 'viewer@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'preferred_language' => 'en',
        ]);
        $user->roles()->sync([$viewerRole->id]);

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admin_media_library_page_renders(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.media-library.index'))
            ->assertOk()
            ->assertSee('Media Library');
    }

    public function test_uploaded_dashboard_image_is_registered_in_media_library(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $image = UploadedFile::fake()->create('testimonial-cover.png', 120, 'image/png');

        $response = $this->actingAs($admin)->post(route('admin.testimonials.store'), [
            'client_name' => 'Media Test',
            'client_role_en' => 'Traveler',
            'client_role_ar' => 'مسافر',
            'testimonial_en' => 'Helpful team.',
            'testimonial_ar' => 'فريق متعاون.',
            'rating' => 5,
            'sort_order' => 20,
            'is_active' => 1,
            'image' => $image,
        ]);

        $response->assertRedirect(route('admin.testimonials.index'));
        $response->assertSessionHasNoErrors();

        $testimonial = \App\Models\Testimonial::query()->where('client_name', 'Media Test')->firstOrFail();

        $this->assertNotNull($testimonial->image);
        $this->assertDatabaseHas('media_assets', [
            'path' => $testimonial->image,
            'disk' => 'public',
        ]);

        $asset = MediaAsset::query()->where('path', $testimonial->image)->firstOrFail();
        $this->assertSame('png', $asset->extension);
    }

    public function test_media_library_direct_upload_creates_database_record_and_lists_item(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $image = UploadedFile::fake()->create('library-upload.png', 80, 'image/png');

        $this->actingAs($admin)
            ->post(route('admin.media-library.store'), [
                'files' => [$image],
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $asset = MediaAsset::query()->where('title', 'library-upload')->firstOrFail();

        Storage::disk('public')->assertExists($asset->path);

        $this->actingAs($admin)
            ->get(route('admin.media-library.index'))
            ->assertOk()
            ->assertSee('library-upload');
    }

    public function test_media_library_recovers_old_storage_prefixed_paths_from_settings(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        Storage::disk('public')->put('settings/legacy-logo.png', 'legacy-logo-binary');

        Setting::query()->firstOrFail()->update([
            'logo_path' => 'storage/settings/legacy-logo.png',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.media-library.index'))
            ->assertOk()
            ->assertSee('legacy-logo');

        $asset = MediaAsset::query()->where('path', 'settings/legacy-logo.png')->firstOrFail();
        $this->assertTrue($asset->file_exists);
    }

    public function test_media_library_preview_route_handles_normalized_and_missing_paths(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        Storage::disk('public')->put('media-library/sample-preview.png', 'preview-binary');

        $asset = MediaAsset::query()->create([
            'title' => 'Sample Preview',
            'disk' => 'public',
            'directory' => 'media-library',
            'file_name' => 'sample-preview.png',
            'path' => 'storage/media-library/sample-preview.png',
            'extension' => 'png',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.media-library.preview', $asset))
            ->assertOk();

        $missing = MediaAsset::query()->create([
            'title' => 'Missing Preview',
            'disk' => 'public',
            'directory' => 'media-library',
            'file_name' => 'missing-preview.png',
            'path' => 'media-library/missing-preview.png',
            'extension' => 'png',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.media-library.preview', $missing))
            ->assertOk()
            ->assertHeader('content-type', 'image/svg+xml; charset=UTF-8');
    }

    public function test_admin_chatbot_settings_page_renders_and_can_be_updated(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.chatbot-settings.edit'))
            ->assertOk()
            ->assertSee('AI Chatbot')
            ->assertSee('Rebuild Knowledge');

        $this->actingAs($admin)
            ->put(route('admin.chatbot-settings.update'), [
                'chatbot_enabled' => '1',
                'chatbot_bot_name_en' => 'Travel Wave Copilot',
                'chatbot_bot_name_ar' => 'مساعد ترافل ويف',
                'chatbot_primary_language' => 'ar',
                'chatbot_welcome_message_ar' => 'أهلًا بك في Travel Wave.',
                'chatbot_fallback_message_ar' => 'لا أملك إجابة مؤكدة الآن.',
                'chatbot_suggested_questions_ar' => "ما خطوات تأشيرة فرنسا؟\nكيف أتواصل معكم؟",
                'chatbot_show_whatsapp_handoff' => '1',
                'chatbot_show_contact_handoff' => '1',
                'chatbot_content_sources' => ['pages', 'visa_countries', 'contact_details'],
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('settings', [
            'chatbot_enabled' => 1,
            'chatbot_bot_name_en' => 'Travel Wave Copilot',
            'chatbot_primary_language' => 'ar',
        ]);
    }

    public function test_chatbot_knowledge_can_be_rebuilt_from_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.chatbot-settings.rebuild'))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertGreaterThan(0, ChatbotKnowledgeItem::query()->count());
        $this->assertDatabaseHas('chatbot_knowledge_items', [
            'source_type' => 'contact_details',
            'locale' => 'ar',
        ]);
    }

    public function test_chatbot_ask_endpoint_returns_grounded_contact_answer(): void
    {
        $this->seed(DatabaseSeeder::class);

        Setting::query()->firstOrFail()->update([
            'chatbot_enabled' => true,
            'phone' => '+20 106 050 0236',
            'whatsapp_number' => '201060500236',
            'contact_email' => 'hello@travelwave.test',
        ]);

        ChatbotKnowledgeItem::query()->create([
            'source_type' => 'contact_details',
            'source_id' => null,
            'source_key' => 'site-contact',
            'locale' => 'ar',
            'title' => 'بيانات التواصل',
            'summary' => 'الهاتف وواتساب والبريد الإلكتروني.',
            'content' => 'الهاتف +20 106 050 0236 واتساب 201060500236 البريد hello@travelwave.test',
            'url' => route('contact'),
            'sort_order' => 1,
        ]);

        $this->postJson(route('chatbot.ask'), [
            'question' => 'ما رقم الواتساب؟',
            'locale' => 'ar',
        ])
            ->assertOk()
            ->assertJsonPath('was_answered', true)
            ->assertJsonPath('handoff.contact_url', route('contact'))
            ->assertJsonPath('interaction_id', 1);

        $this->assertDatabaseHas('chatbot_interactions', [
            'question' => 'ما رقم الواتساب؟',
            'was_answered' => 1,
        ]);
    }

    public function test_admin_can_create_chatbot_knowledge_entry_from_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.chatbot-knowledge.store'), [
                'title_en' => 'Visa application steps',
                'title_ar' => 'خطوات التقديم على التأشيرة',
                'question_en' => 'How can I apply for a visa?',
                'question_ar' => 'كيف أقدم على التأشيرة؟',
                'answer_en' => 'Send us your passport copy and we will guide you through the required documents and appointment steps.',
                'answer_ar' => 'أرسل لنا صورة جواز السفر وسنرشدك إلى الأوراق المطلوبة وخطوات الحجز والتقديم.',
                'keywords_en' => 'visa, apply, documents',
                'keywords_ar' => 'تأشيرة، تقديم، أوراق',
                'category_en' => 'Visas',
                'category_ar' => 'التأشيرات',
                'priority' => 1,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.chatbot-knowledge.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('chatbot_knowledge_entries', [
            'title_en' => 'Visa application steps',
            'title_ar' => 'خطوات التقديم على التأشيرة',
            'is_active' => 1,
        ]);
    }

    public function test_chatbot_prefers_manual_knowledge_answer_in_english(): void
    {
        $this->seed(DatabaseSeeder::class);

        Setting::query()->firstOrFail()->update([
            'chatbot_enabled' => true,
            'chatbot_primary_language' => 'en',
        ]);

        ChatbotKnowledgeEntry::query()->create([
            'title_en' => 'Visa application support',
            'title_ar' => 'مساعدة التقديم على التأشيرة',
            'question_en' => 'How can I apply for a visa?',
            'question_ar' => 'كيف أقدم على التأشيرة؟',
            'answer_en' => 'To apply for a visa, send us your passport copy and travel dates, and our team will help you with the checklist and appointment steps.',
            'answer_ar' => 'للتقديم على التأشيرة أرسل لنا صورة جواز السفر وتاريخ السفر وسيساعدك فريقنا في قائمة الأوراق وخطوات الموعد.',
            'keywords_en' => 'visa apply application documents',
            'keywords_ar' => 'تأشيرة تقديم طلب أوراق',
            'category_en' => 'Visas',
            'category_ar' => 'التأشيرات',
            'priority' => 0,
            'is_active' => true,
        ]);

        ChatbotKnowledgeItem::query()->create([
            'source_type' => 'pages',
            'source_id' => 1,
            'source_key' => 'visas',
            'locale' => 'en',
            'title' => 'Visa Services',
            'summary' => 'Visa services page',
            'content' => 'Visit our visas page for more details.',
            'url' => route('visas.index'),
            'sort_order' => 1,
        ]);

        $this->postJson(route('chatbot.ask'), [
            'question' => 'How can I apply for a visa?',
            'locale' => 'en',
        ])
            ->assertOk()
            ->assertJsonPath('was_answered', true)
            ->assertJsonPath('answer', 'To apply for a visa, send us your passport copy and travel dates, and our team will help you with the checklist and appointment steps.')
            ->assertJsonCount(0, 'sources');
    }

    public function test_chatbot_prefers_manual_knowledge_answer_in_arabic(): void
    {
        $this->seed(DatabaseSeeder::class);

        Setting::query()->firstOrFail()->update([
            'chatbot_enabled' => true,
            'chatbot_primary_language' => 'ar',
        ]);

        ChatbotKnowledgeEntry::query()->create([
            'title_en' => 'Booking steps',
            'title_ar' => 'خطوات الحجز',
            'question_en' => 'What are the booking steps?',
            'question_ar' => 'ما خطوات الحجز؟',
            'answer_en' => 'Send your travel dates and destination, then our team will confirm availability and complete the booking with you.',
            'answer_ar' => 'أرسل تاريخ السفر والوجهة المطلوبة ثم يؤكد فريقنا التوفر ويكمل معك إجراءات الحجز خطوة بخطوة.',
            'keywords_en' => 'booking reservation travel steps',
            'keywords_ar' => 'حجز حجزات سفر خطوات',
            'category_en' => 'Booking',
            'category_ar' => 'الحجوزات',
            'priority' => 0,
            'is_active' => true,
        ]);

        $this->postJson(route('chatbot.ask'), [
            'question' => 'ما خطوات الحجز؟',
            'locale' => 'ar',
        ])
            ->assertOk()
            ->assertJsonPath('was_answered', true)
            ->assertJsonPath('answer', 'أرسل تاريخ السفر والوجهة المطلوبة ثم يؤكد فريقنا التوفر ويكمل معك إجراءات الحجز خطوة بخطوة.')
            ->assertJsonCount(0, 'sources');
    }

    public function test_frontend_chatbot_widget_renders_when_enabled(): void
    {
        $this->seed(DatabaseSeeder::class);

        Setting::query()->firstOrFail()->update([
            'chatbot_enabled' => true,
            'chatbot_bot_name_en' => 'Travel Wave Assistant',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-chatbot', false)
            ->assertSee('Travel Wave Assistant');
    }

    public function test_crm_dashboard_pages_render_for_admin(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.crm.dashboard'))
            ->assertOk()
            ->assertSee('CRM');

        $this->actingAs($admin)
            ->get(route('admin.crm.leads.index'))
            ->assertOk()
            ->assertSee('Leads');

        $this->actingAs($admin)
            ->get(route('admin.crm.service-types'))
            ->assertOk()
            ->assertSee('Service Types');
    }

    public function test_frontend_inquiry_is_created_with_default_crm_fields(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->post(route('inquiries.store') . '?utm_source=meta&utm_campaign=france_visa_2026', [
            'type' => 'visa',
            'full_name' => 'CRM Lead',
            'phone' => '01060500236',
            'whatsapp_number' => '201060500236',
            'nationality' => 'Egypt',
            'destination' => 'France',
            'service_type' => 'Schengen',
        ])->assertRedirect();

        $lead = Inquiry::query()->where('full_name', 'CRM Lead')->firstOrFail();

        $this->assertSame('201060500236', $lead->whatsapp_number);
        $this->assertSame('Egypt', $lead->country);
        $this->assertSame('meta', $lead->lead_source);
        $this->assertSame('france_visa_2026', $lead->utm_campaign);
        $this->assertNotNull($lead->crm_status_id);
        $this->assertNotNull($lead->crm_status_updated_at);
        $this->assertSame('new-lead', $lead->crmStatus?->slug);
    }

    public function test_crm_lead_can_be_updated_with_one_main_status_and_logged_in_history(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $status = \App\Models\CrmStatus::query()->where('slug', 'documents-complete')->firstOrFail();
        $source = \App\Models\CrmLeadSource::query()->where('slug', 'facebook-message')->firstOrFail();
        $defaultStatusId = \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id');

        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'CRM Update Lead',
            'phone' => '01000000000',
            'status' => 'new',
            'crm_status_id' => $defaultStatusId,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.crm.leads.update', $lead), [
                'full_name' => 'CRM Update Lead',
                'phone' => '01000000000',
                'crm_status_id' => $status->id,
                'crm_source_id' => $source->id,
                'assigned_user_id' => $admin->id,
                'country' => 'Egypt',
                'destination' => 'France',
                'service_type' => 'Tourism Visa',
                'travelers_count' => 3,
                'campaign_name' => 'Campaign A',
                'total_price' => 10000,
                'expenses' => 2500,
                'priority' => 'high',
                'follow_up_result' => 'Reached on WhatsApp',
                'status_change_note' => 'Customer completed documents.',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $lead->refresh();

        $this->assertSame($status->id, $lead->crm_status_id);
        $this->assertSame($source->id, $lead->crm_source_id);
        $this->assertSame($admin->id, $lead->assigned_user_id);
        $this->assertSame('Egypt', $lead->country);
        $this->assertSame('France', $lead->destination);
        $this->assertSame('Tourism Visa', $lead->service_type);
        $this->assertSame(3, $lead->travelers_count);
        $this->assertSame('7500.00', $lead->net_price);
        $this->assertSame($admin->id, $lead->crm_status_updated_by);
        $this->assertDatabaseHas('crm_status_updates', [
            'inquiry_id' => $lead->id,
            'status_level' => 'main',
            'new_status_id' => $status->id,
            'changed_by' => $admin->id,
            'note' => 'Customer completed documents.',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.crm.leads.notes.store', $lead), [
                'body' => 'Customer asked for follow-up tomorrow.',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('crm_lead_notes', [
            'inquiry_id' => $lead->id,
            'body' => 'Customer asked for follow-up tomorrow.',
        ]);
    }

    public function test_crm_admin_can_trash_restore_and_force_delete_leads(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Trash CRM Lead',
            'phone' => '01011111111',
            'status' => 'new',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.crm.leads.destroy', $lead))
            ->assertRedirect(route('admin.crm.leads.index'));

        $this->assertSoftDeleted('inquiries', ['id' => $lead->id]);

        $this->actingAs($admin)
            ->get(route('admin.crm.leads.trash'))
            ->assertOk()
            ->assertSee('Trash CRM Lead');

        $this->actingAs($admin)
            ->post(route('admin.crm.leads.restore', $lead->id))
            ->assertRedirect(route('admin.crm.leads.trash'));

        $this->assertDatabaseHas('inquiries', [
            'id' => $lead->id,
            'deleted_at' => null,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.crm.leads.destroy', $lead))
            ->assertRedirect(route('admin.crm.leads.index'));

        $this->actingAs($admin)
            ->delete(route('admin.crm.leads.force-destroy', $lead->id))
            ->assertRedirect(route('admin.crm.leads.trash'));

        $this->assertDatabaseMissing('inquiries', ['id' => $lead->id]);
    }

    public function test_sales_role_cannot_delete_crm_leads(): void
    {
        $this->seed(DatabaseSeeder::class);

        $role = Role::query()->where('slug', 'sales-leads-manager')->firstOrFail();
        $user = User::query()->create([
            'name' => 'Sales User',
            'email' => 'sales@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'preferred_language' => 'ar',
        ]);
        $user->roles()->sync([$role->id]);

        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Protected CRM Lead',
            'phone' => '01011111111',
            'status' => 'new',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
        ]);

        $this->actingAs($user)
            ->delete(route('admin.crm.leads.destroy', $lead))
            ->assertForbidden();
    }

    public function test_seller_can_only_view_assigned_crm_leads(): void
    {
        $this->seed(DatabaseSeeder::class);

        $role = Role::query()->where('slug', 'sales-leads-manager')->firstOrFail();

        $sellerA = User::query()->create([
            'name' => 'Seller A',
            'email' => 'seller-a@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'preferred_language' => 'ar',
        ]);
        $sellerA->roles()->sync([$role->id]);

        $sellerB = User::query()->create([
            'name' => 'Seller B',
            'email' => 'seller-b@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'preferred_language' => 'ar',
        ]);
        $sellerB->roles()->sync([$role->id]);

        $assignedToA = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Lead For A',
            'phone' => '01010000001',
            'status' => 'new',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
            'assigned_user_id' => $sellerA->id,
        ]);

        $assignedToB = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Lead For B',
            'phone' => '01010000002',
            'status' => 'new',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
            'assigned_user_id' => $sellerB->id,
        ]);

        $unassigned = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Unassigned Lead',
            'phone' => '01010000003',
            'status' => 'new',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
        ]);

        $this->actingAs($sellerA)
            ->get(route('admin.crm.leads.index'))
            ->assertOk()
            ->assertSee('Lead For A')
            ->assertDontSee('Lead For B')
            ->assertDontSee('Unassigned Lead');

        $this->actingAs($sellerA)
            ->get(route('admin.crm.leads.show', $assignedToA))
            ->assertOk()
            ->assertSee('Lead For A');

        $this->actingAs($sellerA)
            ->get(route('admin.crm.leads.show', $assignedToB))
            ->assertForbidden();

        $this->actingAs($sellerA)
            ->get(route('admin.crm.leads.show', $unassigned))
            ->assertForbidden();
    }

    public function test_admin_can_bulk_assign_change_status_and_trash_crm_leads(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $sellerRole = Role::query()->where('slug', 'sales-leads-manager')->firstOrFail();
        $seller = User::query()->create([
            'name' => 'Bulk Seller',
            'email' => 'bulk-seller@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'preferred_language' => 'ar',
        ]);
        $seller->roles()->sync([$sellerRole->id]);

        $leadOne = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Bulk Lead One',
            'phone' => '01020000001',
            'status' => 'new',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
        ]);
        $leadTwo = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Bulk Lead Two',
            'phone' => '01020000002',
            'status' => 'new',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
        ]);

        $qualified = \App\Models\CrmStatus::query()->where('slug', 'documents-complete')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.crm.leads.bulk-update'), [
                'lead_ids' => [$leadOne->id, $leadTwo->id],
                'action' => 'assign',
                'bulk_assigned_user_id' => $seller->id,
                'bulk_note' => 'Assigned from admin queue.',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame($seller->id, $leadOne->fresh()->assigned_user_id);
        $this->assertSame($seller->id, $leadTwo->fresh()->assigned_user_id);
        $this->assertDatabaseHas('crm_lead_assignments', [
            'inquiry_id' => $leadOne->id,
            'new_assigned_user_id' => $seller->id,
            'changed_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.crm.leads.bulk-update'), [
                'lead_ids' => [$leadOne->id, $leadTwo->id],
                'action' => 'status',
                'bulk_status_id' => $qualified->id,
                'bulk_note' => 'Bulk qualified.',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame($qualified->id, $leadOne->fresh()->crm_status_id);
        $this->assertSame($qualified->id, $leadTwo->fresh()->crm_status_id);

        $this->actingAs($admin)
            ->post(route('admin.crm.leads.bulk-update'), [
                'lead_ids' => [$leadOne->id, $leadTwo->id],
                'action' => 'trash',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSoftDeleted('inquiries', ['id' => $leadOne->id]);
        $this->assertSoftDeleted('inquiries', ['id' => $leadTwo->id]);
    }

    public function test_admin_can_bulk_update_seller_and_status_together(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $sellerRole = Role::query()->where('slug', 'sales-leads-manager')->firstOrFail();
        $seller = User::query()->create([
            'name' => 'Combined Seller',
            'email' => 'combined-seller@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'preferred_language' => 'ar',
        ]);
        $seller->roles()->sync([$sellerRole->id]);

        $targetStatus = \App\Models\CrmStatus::query()->where('slug', 'documents-complete')->firstOrFail();

        $leadOne = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Combo Lead One',
            'phone' => '01030000001',
            'status' => 'new',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
        ]);

        $leadTwo = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Combo Lead Two',
            'phone' => '01030000002',
            'status' => 'new',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.crm.leads.bulk-update'), [
                'lead_ids' => [$leadOne->id, $leadTwo->id],
                'bulk_assigned_user_id' => $seller->id,
                'bulk_status_id' => $targetStatus->id,
                'bulk_note' => 'Combined bulk update.',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame($seller->id, $leadOne->fresh()->assigned_user_id);
        $this->assertSame($targetStatus->id, $leadOne->fresh()->crm_status_id);
        $this->assertSame($seller->id, $leadTwo->fresh()->assigned_user_id);
        $this->assertSame($targetStatus->id, $leadTwo->fresh()->crm_status_id);

        $this->assertDatabaseHas('crm_lead_assignments', [
            'inquiry_id' => $leadOne->id,
            'new_assigned_user_id' => $seller->id,
            'changed_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('crm_status_updates', [
            'inquiry_id' => $leadOne->id,
            'new_status_id' => $targetStatus->id,
            'changed_by' => $admin->id,
            'note' => 'Combined bulk update.',
        ]);
    }

    public function test_call_later_status_requires_scheduling_and_creates_follow_up_record(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $callLater = \App\Models\CrmStatus::query()->where('slug', 'call-later')->firstOrFail();
        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Call Later Lead',
            'phone' => '01012345678',
            'status' => 'new',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
        ]);

        $this->actingAs($admin)
            ->put(route('admin.crm.leads.update', $lead), [
                'full_name' => 'Call Later Lead',
                'phone' => '01012345678',
                'crm_status_id' => $callLater->id,
                'assigned_user_id' => $admin->id,
                'scheduled_follow_up_date' => now()->addDay()->format('Y-m-d'),
                'scheduled_follow_up_time' => '14:30',
                'follow_up_reminder_offset' => '30',
                'follow_up_schedule_note' => 'Customer asked to be called tomorrow afternoon.',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $followUp = CrmFollowUp::query()->where('inquiry_id', $lead->id)->firstOrFail();
        $this->assertSame(CrmFollowUp::STATUS_PENDING, $followUp->status);
        $this->assertSame($admin->id, $followUp->assigned_user_id);
        $this->assertSame(30, $followUp->reminder_offset_minutes);
        $this->assertSame('Customer asked to be called tomorrow afternoon.', $followUp->note);
        $this->assertSame('call-later', $lead->fresh()->crmStatus?->slug);
    }

    public function test_call_later_follow_up_dispatches_internal_notifications_to_seller_and_admin(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $salesRole = Role::query()->where('slug', 'sales-leads-manager')->firstOrFail();
        $seller = User::query()->create([
            'name' => 'Seller User',
            'email' => 'seller@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'preferred_language' => 'ar',
        ]);
        $seller->roles()->sync([$salesRole->id]);

        $callLater = \App\Models\CrmStatus::query()->where('slug', 'call-later')->firstOrFail();
        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Reminder Lead',
            'phone' => '01099999999',
            'status' => 'new',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
        ]);

        $scheduledAt = now()->addHour();

        $this->actingAs($admin)
            ->put(route('admin.crm.leads.update', $lead), [
                'full_name' => 'Reminder Lead',
                'phone' => '01099999999',
                'crm_status_id' => $callLater->id,
                'assigned_user_id' => $seller->id,
                'scheduled_follow_up_date' => $scheduledAt->format('Y-m-d'),
                'scheduled_follow_up_time' => $scheduledAt->format('H:i'),
                'follow_up_reminder_offset' => '30',
            ])
            ->assertRedirect();

        $followUp = CrmFollowUp::query()->where('inquiry_id', $lead->id)->firstOrFail();

        $this->travelTo($followUp->remind_at->copy()->addMinute());

        $this->actingAs($admin)
            ->get(route('admin.crm.dashboard'))
            ->assertOk();

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $admin->id,
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $seller->id,
        ]);

        $this->assertNotNull($followUp->fresh()->reminder_sent_at);
    }

    public function test_call_later_reminder_renders_dashboard_popup_and_can_be_dismissed(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $salesRole = Role::query()->where('slug', 'sales-leads-manager')->firstOrFail();
        $seller = User::query()->create([
            'name' => 'Popup Seller',
            'email' => 'popup-seller@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'preferred_language' => 'ar',
        ]);
        $seller->roles()->sync([$salesRole->id]);

        $callLater = \App\Models\CrmStatus::query()->where('slug', 'call-later')->firstOrFail();
        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Popup Lead',
            'phone' => '01055555555',
            'status' => 'new',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
        ]);

        $scheduledAt = now()->addHour();

        $this->actingAs($admin)
            ->put(route('admin.crm.leads.update', $lead), [
                'full_name' => 'Popup Lead',
                'phone' => '01055555555',
                'crm_status_id' => $callLater->id,
                'assigned_user_id' => $seller->id,
                'scheduled_follow_up_date' => $scheduledAt->format('Y-m-d'),
                'scheduled_follow_up_time' => $scheduledAt->format('H:i'),
                'follow_up_reminder_offset' => '30',
                'follow_up_schedule_note' => 'Popup reminder note.',
            ])
            ->assertRedirect();

        $followUp = CrmFollowUp::query()->where('inquiry_id', $lead->id)->firstOrFail();

        $this->travelTo($followUp->remind_at->copy()->addMinute());

        $this->actingAs($seller)
            ->get(route('admin.crm.dashboard'))
            ->assertOk()
            ->assertSee('data-admin-followup-popup', false)
            ->assertSee('Popup Lead');

        $notification = $seller->unreadNotifications()->where('type', \App\Notifications\CrmFollowUpReminderNotification::class)->latest()->firstOrFail();

        $this->actingAs($seller)
            ->post(route('admin.notifications.read', $notification->id), [], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJson(['status' => 'ok']);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_call_later_follow_up_can_be_snoozed_from_popup_workflow(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $callLater = \App\Models\CrmStatus::query()->where('slug', 'call-later')->firstOrFail();
        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Snooze Lead',
            'phone' => '01066666666',
            'status' => 'new',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
        ]);

        $this->actingAs($admin)
            ->put(route('admin.crm.leads.update', $lead), [
                'full_name' => 'Snooze Lead',
                'phone' => '01066666666',
                'crm_status_id' => $callLater->id,
                'assigned_user_id' => $admin->id,
                'scheduled_follow_up_date' => now()->addDay()->format('Y-m-d'),
                'scheduled_follow_up_time' => '16:00',
                'follow_up_reminder_offset' => '30',
            ])
            ->assertRedirect();

        $followUp = CrmFollowUp::query()->where('inquiry_id', $lead->id)->firstOrFail();
        $previousRemindAt = $followUp->remind_at;

        $this->actingAs($admin)
            ->putJson(route('admin.crm.follow-ups.update', $followUp), [
                'action' => 'snooze',
                'snooze_minutes' => 15,
            ])
            ->assertOk()
            ->assertJson(['status' => 'ok']);

        $followUp->refresh();

        $this->assertNull($followUp->reminder_sent_at);
        $this->assertTrue($followUp->remind_at->greaterThan(now()->addMinutes(14)));
        $this->assertTrue($followUp->remind_at->notEqualTo($previousRemindAt));
    }

    public function test_crm_lead_supports_dynamic_service_type_subtype_and_destination_fields(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Dynamic Type Lead',
            'phone' => '01012312312',
            'status' => 'new',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
        ]);

        $serviceType = \App\Models\CrmServiceType::query()->where('slug', 'external-visas')->firstOrFail();
        $subtype = \App\Models\CrmServiceSubtype::query()->where('slug', 'european-union')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.crm.leads.update', $lead), [
                'full_name' => 'Dynamic Type Lead',
                'phone' => '01012312312',
                'crm_status_id' => $lead->crm_status_id,
                'crm_service_type_id' => $serviceType->id,
                'crm_service_subtype_id' => $subtype->id,
                'service_country_name' => 'France',
                'country' => 'Egypt',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $lead->refresh();

        $this->assertSame($serviceType->id, $lead->crm_service_type_id);
        $this->assertSame($subtype->id, $lead->crm_service_subtype_id);
        $this->assertSame('France', $lead->service_country_name);
        $this->assertSame('France', $lead->destination);
        $this->assertSame('France', $lead->country);

        $this->actingAs($admin)
            ->get(route('admin.crm.leads.show', $lead))
            ->assertOk()
            ->assertSee('France')
            ->assertSee($serviceType->localizedName())
            ->assertSee($subtype->localizedName());
    }

    public function test_call_later_status_is_active_and_visible_in_crm_status_dropdown(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Dropdown Lead',
            'phone' => '01077777777',
            'status' => 'new',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
        ]);

        $status = \App\Models\CrmStatus::query()->where('slug', 'call-later')->firstOrFail();

        $this->assertTrue((bool) $status->is_active);

        $this->actingAs($admin)
            ->get(route('admin.crm.leads.show', $lead))
            ->assertOk()
            ->assertSee($status->localizedName());
    }

    public function test_crm_whatsapp_links_include_logged_in_seller_name_message(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'WhatsApp Lead',
            'phone' => '01011111111',
            'whatsapp_number' => '+20 101 234 5678',
            'status' => 'new',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
        ]);

        $expectedMessage = rawurlencode('اهلا وسهلا بحضرتك معاك ' . $admin->name . ' من Travel Wave');

        $this->actingAs($admin)
            ->get(route('admin.crm.leads.index'))
            ->assertOk()
            ->assertSee('https://wa.me/201012345678?text=' . $expectedMessage, false);

        $this->actingAs($admin)
            ->get(route('admin.crm.leads.show', $lead))
            ->assertOk()
            ->assertSee('https://wa.me/201012345678?text=' . $expectedMessage, false);
    }

    public function test_admin_can_mark_individual_and_all_notifications_as_read(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $callLater = \App\Models\CrmStatus::query()->where('slug', 'call-later')->firstOrFail();
        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Notification Lead',
            'phone' => '01022222222',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
            'status' => 'new',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.crm.leads.update', $lead), [
                'full_name' => 'Notification Lead',
                'phone' => '01022222222',
                'crm_status_id' => $callLater->id,
                'assigned_user_id' => $admin->id,
                'scheduled_follow_up_date' => now()->addDay()->format('Y-m-d'),
                'scheduled_follow_up_time' => '11:30',
                'follow_up_reminder_offset' => '30',
            ])
            ->assertRedirect();

        $followUp = CrmFollowUp::query()->where('inquiry_id', $lead->id)->firstOrFail();
        $this->travelTo($followUp->remind_at->copy()->addMinute());

        $this->actingAs($admin)->get(route('admin.crm.dashboard'))->assertOk();

        $notification = $admin->unreadNotifications()->latest()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.notifications.read', $notification->id), [], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJson(['status' => 'ok']);

        $this->assertNotNull($notification->fresh()->read_at);

        $admin->notify(new \App\Notifications\CrmFollowUpReminderNotification($followUp));
        $admin->notify(new \App\Notifications\CrmFollowUpReminderNotification($followUp));

        $this->assertGreaterThan(0, $admin->fresh()->unreadNotifications()->count());

        $this->actingAs($admin)
            ->post(route('admin.notifications.read-all'), [], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'unread_count' => 0,
            ]);

        $this->assertSame(0, $admin->fresh()->unreadNotifications()->count());
    }

    public function test_admin_can_preview_and_import_crm_leads_from_csv(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $csv = implode("\n", [
            'اسم العميل,رقم الموبايل,رقم الواتساب,الإيميل,الحالة,النوع,تصنيف النوع,الدولة,العدد,ملاحظات,ملاحظات أخرى,السعر الإجمالي,المصروفات,السعر الصافي,مصدر الليد,البائع / المسؤول',
            'عميل مستورد,01088888888,01088888888,imported@example.com,ليد جديد,تأشيرات خارجية,الاتحاد الأوروبي,إسبانيا,2,ملاحظة أولى,ملاحظة ثانية,10000,1500,8500,Facebook (lead Generation),Admin',
        ]);

        $file = UploadedFile::fake()->createWithContent('crm-import.csv', $csv);

        $this->actingAs($admin)
            ->post(route('admin.crm.leads.import.preview'), [
                'duplicate_mode' => 'skip',
                'duplicate_detector' => 'phone',
                'import_file' => $file,
            ])
            ->assertRedirect(route('admin.crm.leads.transfer'));

        $this->actingAs($admin)
            ->post(route('admin.crm.leads.import'))
            ->assertRedirect(route('admin.crm.leads.transfer'));

        $this->assertDatabaseHas('inquiries', [
            'full_name' => 'عميل مستورد',
            'phone' => '01088888888',
            'whatsapp_number' => '01088888888',
            'email' => 'imported@example.com',
        ]);
    }

    public function test_admin_can_open_manual_crm_lead_create_page_and_store_a_manual_lead(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.crm.leads.index'))
            ->assertOk()
            ->assertSee('Add Lead');

        $this->actingAs($admin)
            ->get(route('admin.crm.leads.create'))
            ->assertOk()
            ->assertSee('Save Lead');

        $this->actingAs($admin)
            ->post(route('admin.crm.leads.store'), [
                'full_name' => 'Manual Lead',
                'phone' => '01012121212',
                'whatsapp_number' => '01012121212',
                'email' => 'manual@example.com',
                'country' => 'Egypt',
                'destination' => 'Cairo',
                'admin_notes' => 'Created manually from dashboard.',
                'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
            ])
            ->assertRedirect(route('admin.crm.leads.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('inquiries', [
            'full_name' => 'Manual Lead',
            'phone' => '01012121212',
            'whatsapp_number' => '01012121212',
            'email' => 'manual@example.com',
            'lead_source' => 'manual',
            'source_page' => 'admin-manual',
        ]);
    }

    public function test_manual_crm_lead_creation_blocks_duplicate_phone_numbers(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Existing Manual Duplicate',
            'phone' => '01056565656',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
            'status' => 'new',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.crm.leads.create'))
            ->post(route('admin.crm.leads.store'), [
                'full_name' => 'Blocked Duplicate',
                'phone' => '01056565656',
            ])
            ->assertRedirect(route('admin.crm.leads.create'))
            ->assertSessionHasErrors(['phone']);
    }

    public function test_admin_can_view_delayed_leads_with_red_delay_reason_labels(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $statusId = \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id');

        $overdueLead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Overdue Follow Up Lead',
            'phone' => '01030101010',
            'crm_status_id' => $statusId,
            'status' => 'new',
        ]);
        $overdueLead->forceFill([
            'updated_at' => now()->subDays(3),
            'created_at' => now()->subDays(7),
        ])->save();

        CrmFollowUp::query()->create([
            'inquiry_id' => $overdueLead->id,
            'crm_status_id' => $statusId,
            'assigned_user_id' => $admin->id,
            'created_by' => $admin->id,
            'status' => CrmFollowUp::STATUS_PENDING,
            'scheduled_at' => now()->subDays(2),
            'reminder_offset_minutes' => 30,
            'remind_at' => now()->subDays(2)->subMinutes(30),
        ]);

        $inactiveLead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Inactive Lead',
            'phone' => '01030202020',
            'crm_status_id' => $statusId,
            'status' => 'new',
        ]);
        $inactiveLead->forceFill([
            'updated_at' => now()->subDays(6),
            'created_at' => now()->subDays(9),
        ])->save();

        $activeLead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Recently Active Lead',
            'phone' => '01030303030',
            'crm_status_id' => $statusId,
            'status' => 'new',
        ]);
        $activeLead->forceFill([
            'updated_at' => now()->subDays(10),
            'created_at' => now()->subDays(12),
        ])->save();

        $activeLead->crmNotes()->create([
            'user_id' => $admin->id,
            'body' => 'Recent note keeps this lead active.',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.crm.leads.delayed'))
            ->assertOk()
            ->assertSee('Overdue Follow Up Lead')
            ->assertSee('Inactive Lead')
            ->assertDontSee('Recently Active Lead')
            ->assertSee('text-bg-danger', false)
            ->assertSee('تمت الجدولة ولم يتم تغيير الحالة')
            ->assertSee('لم يتم اتخاذ أي إجراء منذ 5 أيام');
    }

    public function test_admin_can_view_enhanced_crm_reports_with_filters_and_activity_log(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $seller = User::query()->create([
            'name' => 'Reports Seller',
            'email' => 'reports-seller@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'preferred_language' => 'ar',
        ]);

        $statusNew = \App\Models\CrmStatus::query()->where('slug', 'new-lead')->firstOrFail();
        $statusNoAnswer = \App\Models\CrmStatus::query()->where('slug', 'no-answer')->firstOrFail();
        $source = \App\Models\CrmLeadSource::query()->first();

        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Report Lead',
            'phone' => '01010101010',
            'assigned_user_id' => $seller->id,
            'crm_status_id' => $statusNew->id,
            'status' => 'new',
            'crm_source_id' => $source?->id,
            'lead_source' => $source?->name_en ?? 'manual',
        ]);

        \App\Models\CrmLeadNote::query()->create([
            'inquiry_id' => $lead->id,
            'user_id' => $seller->id,
            'body' => 'Daily follow-up note',
            'created_at' => now()->subHour(),
            'updated_at' => now()->subHour(),
        ]);

        \App\Models\CrmStatusUpdate::query()->create([
            'inquiry_id' => $lead->id,
            'status_level' => 'primary',
            'old_status_id' => $statusNew->id,
            'new_status_id' => $statusNoAnswer->id,
            'changed_by' => $seller->id,
            'changed_at' => now()->subMinutes(30),
            'note' => 'No answer today',
        ]);

        \App\Models\CrmFollowUp::query()->create([
            'inquiry_id' => $lead->id,
            'crm_status_id' => $statusNoAnswer->id,
            'assigned_user_id' => $seller->id,
            'created_by' => $seller->id,
            'status' => \App\Models\CrmFollowUp::STATUS_PENDING,
            'scheduled_at' => now()->addHour(),
            'reminder_offset_minutes' => 30,
            'remind_at' => now()->addMinutes(30),
            'note' => 'Call back later',
            'created_at' => now()->subMinutes(20),
            'updated_at' => now()->subMinutes(20),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.crm.reports', [
                'employee_id' => $seller->id,
                'day' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('فلاتر التقارير')
            ->assertSee('تفاصيل النشاط')
            ->assertSee('Report Lead')
            ->assertSee('Daily follow-up note');
    }

    public function test_admin_can_view_crm_reports2_grouped_by_status_for_selected_seller(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $seller = User::query()->create([
            'name' => 'Reports 2 Seller',
            'email' => 'reports2-seller@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'preferred_language' => 'ar',
        ]);

        $salesRole = Role::query()->where('slug', 'sales-leads-manager')->firstOrFail();
        $seller->roles()->sync([$salesRole->id]);

        $statusNew = \App\Models\CrmStatus::query()->where('slug', 'new-lead')->firstOrFail();
        $statusNoAnswer = \App\Models\CrmStatus::query()->where('slug', 'no-answer')->firstOrFail();

        Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Reports2 Lead A',
            'phone' => '01021021021',
            'assigned_user_id' => $seller->id,
            'crm_status_id' => $statusNew->id,
            'status' => 'new',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Reports2 Lead B',
            'phone' => '01021021022',
            'assigned_user_id' => $seller->id,
            'crm_status_id' => $statusNoAnswer->id,
            'status' => 'new',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.crm.reports2', [
                'seller_id' => $seller->id,
                'from' => now()->subDays(2)->toDateString(),
                'to' => today()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Reports 2', false)
            ->assertSee($seller->name)
            ->assertSee($statusNew->localizedName())
            ->assertSee($statusNoAnswer->localizedName());
    }

    public function test_admin_can_create_and_review_general_crm_task_module_pages(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $seller = User::query()->create([
            'name' => 'Task Seller',
            'email' => 'task-seller@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'preferred_language' => 'ar',
        ]);

        $salesRole = Role::query()->where('slug', 'sales-leads-manager')->firstOrFail();
        $seller->roles()->sync([$salesRole->id]);

        $this->actingAs($admin)
            ->post(route('admin.crm.tasks.store'), [
                'title' => 'Internal Operations Task',
                'description' => 'Review pricing sheet',
                'task_type' => \App\Models\CrmTask::TYPE_GENERAL,
                'assigned_user_id' => $seller->id,
                'priority' => \App\Models\CrmTask::PRIORITY_HIGH,
                'status' => \App\Models\CrmTask::STATUS_NEW,
                'due_at' => now()->addDay()->format('Y-m-d H:i:s'),
            ])
            ->assertRedirect();

        $task = \App\Models\CrmTask::query()->where('title', 'Internal Operations Task')->firstOrFail();

        $this->assertNull($task->inquiry_id);
        $this->assertDatabaseHas('crm_task_activities', [
            'crm_task_id' => $task->id,
            'action_type' => 'created',
        ]);

        $this->actingAs($admin)->get(route('admin.crm.tasks.index'))->assertOk()->assertSee('Internal Operations Task');
        $this->actingAs($admin)->get(route('admin.crm.tasks.board'))->assertOk()->assertSee('Internal Operations Task');
        $this->actingAs($admin)->get(route('admin.crm.tasks.reports'))->assertOk()->assertSee('Task Reports')->assertSee($seller->name);
    }

    public function test_lead_linked_task_can_be_created_from_lead_page_and_is_visible(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $status = \App\Models\CrmStatus::query()->where('slug', 'new-lead')->firstOrFail();

        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Lead Task Customer',
            'phone' => '01045454545',
            'crm_status_id' => $status->id,
            'status' => 'new',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.crm.leads.tasks.store', $lead), [
                'title' => 'Call customer about documents',
                'description' => 'Need missing embassy file',
                'assigned_user_id' => $admin->id,
                'task_type' => \App\Models\CrmTask::TYPE_LEAD,
                'priority' => \App\Models\CrmTask::PRIORITY_URGENT,
                'status' => \App\Models\CrmTask::STATUS_IN_PROGRESS,
                'due_at' => now()->addHours(5)->format('Y-m-d H:i:s'),
            ])
            ->assertRedirect(route('admin.crm.leads.show', $lead));

        $task = \App\Models\CrmTask::query()->where('title', 'Call customer about documents')->firstOrFail();

        $this->assertSame($lead->id, $task->inquiry_id);

        $this->actingAs($admin)
            ->get(route('admin.crm.leads.show', $lead))
            ->assertOk()
            ->assertSee('Call customer about documents')
            ->assertSee('In Progress');
    }

    public function test_admin_can_sync_accounting_account_from_crm_lead_update(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $status = \App\Models\CrmStatus::query()->where('slug', 'new-lead')->firstOrFail();

        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Finance Lead',
            'phone' => '01077777777',
            'crm_status_id' => $status->id,
            'status' => 'new',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.crm.leads.update', $lead), [
                'full_name' => 'Finance Lead',
                'phone' => '01077777777',
                'crm_status_id' => $status->id,
                'total_amount' => 1000,
                'paid_amount' => 400,
            ])
            ->assertRedirect(route('admin.crm.leads.show', $lead));

        $this->assertDatabaseHas('accounting_customer_accounts', [
            'inquiry_id' => $lead->id,
            'total_amount' => 1000,
            'paid_amount' => 400,
            'remaining_amount' => 600,
            'payment_status' => 'partially_paid',
        ]);

        $this->assertDatabaseHas('accounting_customer_payments', [
            'amount' => 400,
            'payment_type' => 'payment',
        ]);
    }

    public function test_admin_can_open_accounting_dashboard_and_reports(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.accounting.dashboard'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.accounting.reports'))
            ->assertOk();
    }

    public function test_accounting_profit_is_calculated_from_paid_amount_only(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $status = \App\Models\CrmStatus::query()->where('slug', 'new-lead')->firstOrFail();

        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Paid Amount Profit Lead',
            'phone' => '01088888888',
            'crm_status_id' => $status->id,
            'status' => 'new',
            'total_amount' => 5000,
            'paid_amount' => 2500,
            'remaining_amount' => 2500,
            'payment_status' => 'partially_paid',
        ]);

        $calculator = app(\App\Support\AccountingCalculatorService::class);
        $account = $calculator->syncLeadAccount($lead, $admin->id);

        $category = \App\Models\AccountingExpenseCategory::query()->firstOrFail();
        $account->expenses()->create([
            'accounting_expense_category_id' => $category->id,
            'amount' => 2000,
            'expense_date' => now()->toDateString(),
            'created_by' => $admin->id,
        ]);

        $account = $calculator->syncLeadAccount($lead->fresh(), $admin->id)->fresh();

        $this->assertSame(2500.0, (float) $account->paid_amount);
        $this->assertSame(2500.0, (float) $account->remaining_amount);
        $this->assertSame(2000.0, (float) $account->total_customer_expenses);
        $this->assertSame(500.0, (float) $account->company_profit_before_seller);
        $this->assertSame(50.0, (float) $account->seller_profit);
        $this->assertSame(450.0, (float) $account->final_company_profit);

        $this->actingAs($admin)
            ->get(route('admin.accounting.customers.show', $account))
            ->assertOk()
            ->assertSee('500.00')
            ->assertSee('50.00')
            ->assertSee('450.00');
    }

    public function test_lead_page_hides_accounting_fields_but_accounting_page_keeps_them(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $status = \App\Models\CrmStatus::query()->where('slug', 'new-lead')->firstOrFail();

        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Lead Without Finance UI',
            'phone' => '01012121212',
            'crm_status_id' => $status->id,
            'status' => 'new',
            'total_amount' => 3000,
            'paid_amount' => 1500,
            'remaining_amount' => 1500,
            'payment_status' => 'partially_paid',
        ]);

        $account = app(\App\Support\AccountingCalculatorService::class)->syncLeadAccount($lead, $admin->id);

        $this->actingAs($admin)
            ->get(route('admin.crm.leads.show', $lead))
            ->assertOk()
            ->assertDontSee('name="total_amount"', false)
            ->assertDontSee('name="paid_amount"', false)
            ->assertDontSee('name="expenses"', false)
            ->assertDontSee('name="net_price"', false)
            ->assertDontSee(__('admin.accounting_total_amount'))
            ->assertDontSee(__('admin.accounting_total_customer_expenses'))
            ->assertDontSee(__('admin.accounting_final_company_profit'));

        $this->actingAs($admin)
            ->get(route('admin.accounting.customers.show', $account))
            ->assertOk()
            ->assertSee(__('admin.accounting_total_amount'))
            ->assertSee(__('admin.accounting_total_customer_expenses'))
            ->assertSee(__('admin.accounting_final_company_profit'));
    }

    public function test_converted_customer_is_visible_from_lead_page(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $status = \App\Models\CrmStatus::query()->where('slug', 'new-lead')->firstOrFail();

        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Converted Lead',
            'phone' => '01043434343',
            'crm_status_id' => $status->id,
            'status' => 'new',
            'assigned_user_id' => $admin->id,
        ]);

        $customer = app(\App\Support\CustomerConversionService::class)
            ->convertFromLead($lead, $admin, ['stage' => \App\Models\CrmCustomer::STAGE_UNDER_PROCESSING]);

        $this->actingAs($admin)
            ->get(route('admin.crm.leads.show', $lead))
            ->assertOk()
            ->assertSee($customer->full_name)
            ->assertSee($customer->customer_code)
            ->assertSee(route('admin.crm.customers.show', $customer), false);
    }

    public function test_admin_can_create_crm_information_and_track_recipients(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $seller = User::query()->create([
            'name' => 'Info Seller',
            'email' => 'info-seller@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'preferred_language' => 'ar',
        ]);

        $salesRole = Role::query()->where('slug', 'sales-leads-manager')->firstOrFail();
        $seller->roles()->sync([$salesRole->id]);

        $this->actingAs($admin)
            ->post(route('admin.crm.information.store'), [
                'title' => 'Embassy Update',
                'content' => 'New embassy process for this week.',
                'category' => 'embassy_decision',
                'priority' => 'important',
                'audience_type' => 'selected_users',
                'selected_users' => [$seller->id],
                'event_date' => now()->toDateString(),
            ])
            ->assertRedirect();

        $information = \App\Models\CrmInformation::query()->where('title', 'Embassy Update')->firstOrFail();

        $this->assertDatabaseHas('crm_information_recipients', [
            'crm_information_id' => $information->id,
            'user_id' => $seller->id,
        ]);
    }

    public function test_admin_can_open_crm_information_create_page(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.crm.information.create'))
            ->assertOk()
            ->assertSee('name="title"', false);
    }

    public function test_targeted_user_can_acknowledge_crm_information_but_other_user_cannot_open_it(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $targetSeller = User::query()->create([
            'name' => 'Target Seller',
            'email' => 'target-seller@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'preferred_language' => 'ar',
        ]);
        $otherViewer = User::query()->create([
            'name' => 'Other Viewer',
            'email' => 'other-viewer@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'preferred_language' => 'ar',
        ]);

        $salesRole = Role::query()->where('slug', 'sales-leads-manager')->firstOrFail();
        $viewerRole = Role::query()->where('slug', 'viewer-analyst')->firstOrFail();
        $targetSeller->roles()->sync([$salesRole->id]);
        $otherViewer->roles()->sync([$viewerRole->id]);

        $information = \App\Models\CrmInformation::query()->create([
            'title' => 'Holiday Notice',
            'content' => 'Office will be closed on Friday.',
            'audience_type' => 'selected_users',
            'priority' => 'urgent',
            'created_by' => $admin->id,
            'is_active' => true,
        ]);

        $information->recipients()->create([
            'user_id' => $targetSeller->id,
            'delivered_at' => now(),
        ]);

        $this->actingAs($targetSeller)
            ->get(route('admin.crm.information.show', $information))
            ->assertOk()
            ->assertSee('Holiday Notice');

        $this->actingAs($targetSeller)
            ->post(route('admin.crm.information.acknowledge', $information))
            ->assertRedirect(route('admin.crm.information.show', $information));

        $this->assertDatabaseHas('crm_information_recipients', [
            'crm_information_id' => $information->id,
            'user_id' => $targetSeller->id,
        ]);

        $this->assertNotNull(
            \App\Models\CrmInformationRecipient::query()
                ->where('crm_information_id', $information->id)
                ->where('user_id', $targetSeller->id)
                ->value('acknowledged_at')
        );

        $this->actingAs($otherViewer)
            ->get(route('admin.crm.information.show', $information))
            ->assertNotFound();
    }

    public function test_admin_can_detect_duplicate_leads_by_phone_and_download_duplicate_report(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Existing Lead',
            'phone' => '01044444444',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
            'status' => 'new',
        ]);

        $csv = implode("\n", [
            'اسم العميل,رقم الموبايل,رقم الواتساب,الإيميل,الحالة',
            'عميل صالح 1,01055555555,01055555555,valid1@example.com,ليد جديد',
            'عميل مكرر قاعدة البيانات,01044444444,01044444444,duplicate-db@example.com,ليد جديد',
            'عميل صالح 2,01066666666,01066666666,valid2@example.com,ليد جديد',
            'عميل مكرر داخل الملف 1,01077777777,01077777777,file-1@example.com,ليد جديد',
            'عميل مكرر داخل الملف 2,01077777777,01088888881,file-2@example.com,ليد جديد',
        ]);

        $file = UploadedFile::fake()->createWithContent('crm-duplicate-import.csv', $csv);

        $this->actingAs($admin)
            ->post(route('admin.crm.leads.import.preview'), [
                'duplicate_mode' => 'skip',
                'duplicate_detector' => 'phone',
                'import_file' => $file,
            ])
            ->assertRedirect(route('admin.crm.leads.transfer'));

        $this->actingAs($admin)
            ->post(route('admin.crm.leads.import'))
            ->assertRedirect(route('admin.crm.leads.transfer'));

        $this->assertDatabaseHas('inquiries', [
            'full_name' => 'عميل صالح 1',
            'phone' => '01055555555',
        ]);

        $this->assertDatabaseHas('inquiries', [
            'full_name' => 'عميل صالح 2',
            'phone' => '01066666666',
        ]);

        $this->assertSame(1, Inquiry::query()->where('phone', '01044444444')->count());
        $this->assertSame(1, Inquiry::query()->where('phone', '01077777777')->count());

        $this->actingAs($admin)
            ->get(route('admin.crm.leads.import.report', ['report' => 'duplicates', 'format' => 'xlsx']))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_admin_can_merge_duplicate_leads_during_import_and_apply_merged_status(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $existingLead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Lead To Merge',
            'phone' => '01099911122',
            'whatsapp_number' => '01099911122',
            'email' => 'old@example.com',
            'admin_notes' => 'Old note',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
            'status' => 'new',
        ]);

        $csv = implode("\n", [
            'اسم العميل,رقم الموبايل,رقم الواتساب,الإيميل,الحالة,ملاحظات',
            'Lead To Merge Updated,01099911122,01099911122,new@example.com,ليد جديد,Fresh note',
        ]);

        $file = UploadedFile::fake()->createWithContent('crm-merge-import.csv', $csv);

        $this->actingAs($admin)
            ->post(route('admin.crm.leads.import.preview'), [
                'duplicate_mode' => 'merge_existing',
                'duplicate_detector' => 'phone',
                'import_file' => $file,
            ])
            ->assertRedirect(route('admin.crm.leads.transfer'));

        $this->actingAs($admin)
            ->post(route('admin.crm.leads.import'))
            ->assertRedirect(route('admin.crm.leads.transfer'));

        $existingLead->refresh();
        $mergedStatusId = \App\Models\CrmStatus::query()->where('slug', 'merged')->value('id');

        $this->assertSame('Lead To Merge Updated', $existingLead->full_name);
        $this->assertSame('new@example.com', $existingLead->email);
        $this->assertSame('Fresh note', $existingLead->admin_notes);
        $this->assertSame($mergedStatusId, $existingLead->crm_status_id);
        $this->assertSame(1, Inquiry::query()->where('phone', '01099911122')->count());

        $this->actingAs($admin)
            ->get(route('admin.crm.leads.import.report', ['report' => 'merged', 'format' => 'xlsx']))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_only_admin_scope_can_export_crm_leads(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $salesRole = Role::query()->where('slug', 'sales-leads-manager')->firstOrFail();
        $seller = User::query()->create([
            'name' => 'Export Seller',
            'email' => 'export-seller@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'preferred_language' => 'ar',
        ]);
        $seller->roles()->sync([$salesRole->id]);

        Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Export Lead',
            'phone' => '01033333333',
            'crm_status_id' => \App\Models\CrmStatus::query()->where('slug', 'new-lead')->value('id'),
            'status' => 'new',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.crm.leads.export'), [
                'format' => 'csv',
                'fields' => ['full_name', 'phone', 'crm_status'],
            ])
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $this->actingAs($seller)
            ->post(route('admin.crm.leads.export'), [
                'format' => 'csv',
                'fields' => ['full_name', 'phone'],
            ])
            ->assertForbidden();
    }

    public function test_admin_can_create_utm_campaign_and_view_module_pages(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.utm.dashboard'))
            ->assertOk()
            ->assertSee('UTM', false);

        $this->actingAs($admin)
            ->get(route('admin.utm.create'))
            ->assertOk()
            ->assertSee('name="display_name"', false)
            ->assertSee('name="base_url"', false);

        $this->actingAs($admin)
            ->post(route('admin.utm.store'), [
                'display_name' => 'Meta Visa Campaign',
                'base_url' => 'https://travelwave.test/visas',
                'utm_source' => 'facebook',
                'utm_medium' => 'paid-social',
                'utm_campaign' => 'march-visa',
                'utm_content' => 'video-a',
                'platform' => 'Meta Ads',
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.utm.index'));

        $campaign = UtmCampaign::query()->where('display_name', 'Meta Visa Campaign')->firstOrFail();

        $this->assertStringContainsString('utm_source=facebook', $campaign->generated_url);
        $this->assertStringContainsString('utm_campaign=march-visa', $campaign->generated_url);

        $this->actingAs($admin)
            ->get(route('admin.utm.index'))
            ->assertOk()
            ->assertSee('Meta Visa Campaign')
            ->assertSee('utm_source=facebook')
            ->assertSee('utm_campaign=march-visa');
    }

    public function test_inquiry_captures_utm_attribution_and_links_saved_campaign(): void
    {
        $this->seed(DatabaseSeeder::class);

        $campaign = UtmCampaign::query()->create([
            'display_name' => 'Search Germany',
            'base_url' => 'https://travelwave.test/visa-country/france',
            'generated_url' => 'https://travelwave.test/visa-country/france?utm_source=google&utm_medium=cpc&utm_campaign=spring-search',
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'spring-search',
            'status' => 'active',
        ]);

        $this->get('/?utm_source=google&utm_medium=cpc&utm_campaign=spring-search')
            ->assertOk();

        $this->post(route('inquiries.store'), [
            'type' => 'visa',
            'full_name' => 'UTM Lead',
            'phone' => '01090909090',
            'destination' => 'France',
            'service_type' => 'visa',
        ])->assertSessionHasNoErrors();

        $lead = Inquiry::query()->where('full_name', 'UTM Lead')->firstOrFail();

        $this->assertSame($campaign->id, $lead->utm_campaign_id);
        $this->assertSame('google', $lead->utm_source);
        $this->assertSame('cpc', $lead->utm_medium);
        $this->assertSame('spring-search', $lead->utm_campaign);
        $this->assertSame('google', $lead->first_utm_source);
        $this->assertNotNull($lead->first_touch_at);
        $this->assertNotNull($lead->last_touch_at);

        $this->assertDatabaseHas('utm_visits', [
            'utm_campaign_id' => $campaign->id,
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'spring-search',
        ]);
    }

    public function test_crm_task_board_status_endpoint_updates_completed_at_correctly(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $seller = User::query()->create([
            'name' => 'Board Seller',
            'email' => 'board-seller@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'preferred_language' => 'ar',
        ]);

        $salesRole = Role::query()->where('slug', 'sales-leads-manager')->firstOrFail();
        $seller->roles()->sync([$salesRole->id]);

        $task = \App\Models\CrmTask::query()->create([
            'title' => 'Board Move Task',
            'task_type' => \App\Models\CrmTask::TYPE_GENERAL,
            'priority' => \App\Models\CrmTask::PRIORITY_HIGH,
            'status' => \App\Models\CrmTask::STATUS_NEW,
            'assigned_user_id' => $seller->id,
            'created_by' => $admin->id,
        ]);

        $this->actingAs($seller)
            ->get(route('admin.crm.tasks.index'))
            ->assertOk()
            ->assertSee('Sortable.min.js', false)
            ->assertSee('Board Move Task');

        $this->actingAs($seller)
            ->patchJson(route('admin.crm.tasks.status', $task), [
                'status' => \App\Models\CrmTask::STATUS_COMPLETED,
            ])
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'task' => [
                    'status' => \App\Models\CrmTask::STATUS_COMPLETED,
                ],
            ]);

        $task->refresh();
        $this->assertNotNull($task->completed_at);

        $this->actingAs($seller)
            ->patchJson(route('admin.crm.tasks.status', $task), [
                'status' => \App\Models\CrmTask::STATUS_IN_PROGRESS,
            ])
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'task' => [
                    'status' => \App\Models\CrmTask::STATUS_IN_PROGRESS,
                ],
            ]);

        $task->refresh();
        $this->assertNull($task->completed_at);
    }

    public function test_notifications_center_page_renders_database_notifications(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $admin->notify(new AdminDatabaseNotification([
            'type' => 'system_alert',
            'module' => 'system',
            'severity' => 'warning',
            'title_ar' => 'تنبيه اختباري',
            'title_en' => 'Test Alert',
            'message_ar' => 'رسالة إشعار تجريبية',
            'message_en' => 'Test notification message',
            'url' => route('admin.dashboard'),
        ]));

        $this->actingAs($admin)
            ->get(route('admin.notifications.index'))
            ->assertOk()
            ->assertSee('Test Alert')
            ->assertSee('Test notification message');
    }

    public function test_creating_task_sends_assignment_notification_to_assigned_user(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $seller = User::query()->create([
            'name' => 'Task Seller',
            'email' => 'task-seller@travelwave.test',
            'password' => bcrypt('password123'),
            'is_admin' => true,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.crm.tasks.store'), [
                'title' => 'Call delayed lead',
                'task_type' => \App\Models\CrmTask::TYPE_GENERAL,
                'category' => \App\Models\CrmTask::CATEGORY_INTERNAL,
                'assigned_user_id' => $seller->id,
                'priority' => \App\Models\CrmTask::PRIORITY_MEDIUM,
                'status' => \App\Models\CrmTask::STATUS_NEW,
                'due_at' => now()->addDay()->toDateTimeString(),
            ])
            ->assertRedirect();

        $notification = $seller->notifications()->latest()->first();

        $this->assertNotNull($notification);
        $this->assertSame('task_assigned', $notification->data['type'] ?? null);
    }

    public function test_publishing_information_creates_notifications_for_selected_users(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $target = User::query()->create([
            'name' => 'Info Seller',
            'email' => 'info-seller@travelwave.test',
            'password' => bcrypt('password123'),
            'is_admin' => true,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.crm.information.store'), [
                'title' => 'Urgent office notice',
                'content' => 'Please review the updated process.',
                'priority' => 'urgent',
                'audience_type' => CrmInformation::AUDIENCE_SELECTED_USERS,
                'selected_users' => [$target->id],
                'is_active' => 1,
            ])
            ->assertRedirect();

        $notification = $target->notifications()->latest()->first();

        $this->assertNotNull($notification);
        $this->assertSame('information_new', $notification->data['type'] ?? null);
    }

    public function test_recording_accounting_payment_creates_notification_for_assigned_user(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $seller = User::query()->create([
            'name' => 'Finance Seller',
            'email' => 'finance-seller@travelwave.test',
            'password' => bcrypt('password123'),
            'is_admin' => true,
            'is_active' => true,
        ]);

        $lead = Inquiry::query()->create([
            'type' => 'general',
            'full_name' => 'Accounting Lead',
            'phone' => '+201000000000',
            'assigned_user_id' => $seller->id,
            'status' => 'new',
            'total_amount' => 5000,
            'paid_amount' => 0,
            'remaining_amount' => 5000,
            'payment_status' => 'unpaid',
        ]);

        $account = AccountingCustomerAccount::query()->create([
            'inquiry_id' => $lead->id,
            'assigned_user_id' => $seller->id,
            'created_by' => $admin->id,
            'customer_name' => $lead->full_name,
            'phone' => $lead->phone,
            'total_amount' => 5000,
            'paid_amount' => 0,
            'remaining_amount' => 5000,
            'payment_status' => 'unpaid',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.accounting.customers.payments.store', $account), [
                'amount' => 1000,
                'payment_date' => now()->toDateString(),
                'note' => 'Initial payment',
            ])
            ->assertRedirect();

        $notification = $seller->notifications()->latest()->first();

        $this->assertNotNull($notification);
        $this->assertSame('accounting_payment', $notification->data['type'] ?? null);
    }
}
