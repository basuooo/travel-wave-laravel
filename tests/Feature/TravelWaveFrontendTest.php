<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use App\Models\Setting;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('Discover Egypt and beyond with polished travel packages');
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

    public function test_arabic_locale_switch_renders_rtl_layout(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->withSession(['locale' => 'ar'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('dir="rtl"', false);
    }
}
