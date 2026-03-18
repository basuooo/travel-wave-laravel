<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
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
