<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\User;
use App\Models\UtmCampaign;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingCampaignModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_view_marketing_campaign_module(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.marketing-campaigns.index'))
            ->assertOk()
            ->assertSee('Campaigns', false);

        $this->actingAs($admin)
            ->post(route('admin.marketing-campaigns.store'), [
                'display_name' => 'France Spring Push',
                'campaign_code' => 'CAMP-FR-2026-01',
                'platform' => 'Meta Ads',
                'utm_medium' => 'paid-social',
                'campaign_type' => 'Lead Generation',
                'objective' => 'Generate qualified France visa leads',
                'status' => UtmCampaign::STATUS_ACTIVE,
                'base_url' => 'https://travelwave.test/visas',
                'utm_source' => 'facebook',
                'utm_campaign' => 'france-spring-push',
                'budget' => 12000,
            ])
            ->assertRedirect(route('admin.marketing-campaigns.index'));

        $campaign = UtmCampaign::query()->where('display_name', 'France Spring Push')->firstOrFail();

        $this->assertSame('CAMP-FR-2026-01', $campaign->campaign_code);
        $this->assertStringContainsString('utm_campaign=france-spring-push', $campaign->generated_url);

        $this->actingAs($admin)
            ->get(route('admin.marketing-campaigns.show', $campaign))
            ->assertOk()
            ->assertSee('France Spring Push')
            ->assertSee('Meta Ads')
            ->assertSee('france-spring-push');
    }

    public function test_linked_campaign_is_visible_from_lead_details_page(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'admin@travelwave.test')->firstOrFail();
        $campaign = UtmCampaign::query()->create([
            'display_name' => 'Germany Search',
            'campaign_code' => 'DE-SEARCH-01',
            'base_url' => 'https://travelwave.test/visas',
            'generated_url' => 'https://travelwave.test/visas?utm_source=google&utm_medium=cpc&utm_campaign=germany-search',
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'germany-search',
            'platform' => 'Google',
            'status' => UtmCampaign::STATUS_ACTIVE,
        ]);

        $lead = Inquiry::query()->create([
            'type' => 'visa',
            'full_name' => 'Campaign Lead',
            'phone' => '01000000000',
            'crm_status_id' => \App\Models\CrmStatus::query()->first()->id,
            'status' => 'new',
            'utm_campaign_id' => $campaign->id,
            'campaign_name' => $campaign->display_name,
            'utm_source' => $campaign->utm_source,
            'utm_medium' => $campaign->utm_medium,
            'utm_campaign' => $campaign->utm_campaign,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.crm.leads.show', $lead))
            ->assertOk()
            ->assertSee('Germany Search')
            ->assertSee(route('admin.marketing-campaigns.show', $campaign), false);
    }
}
