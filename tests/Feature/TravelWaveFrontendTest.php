<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use App\Models\Inquiry;
use App\Models\Setting;
use App\Models\User;
use App\Models\VisaCountry;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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
            ->assertSee('Office and Visa Support Location')
            ->assertSee('Talk to Travel Wave About Your France Visa');
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
