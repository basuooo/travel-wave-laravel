<?php

namespace Tests\Feature;

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

    public function test_admin_login_page_is_accessible(): void
    {
        $this->get(route('admin.login'))->assertOk()->assertSee('Travel Wave Admin');
    }
}
