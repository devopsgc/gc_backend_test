<?php

namespace Tests\Feature;

use App\Models\Campaign;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CampaignStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_delete_campaign_from_campaign_detail_page()
    {
        $admin = $this->getSuperAdminUser();

        $campaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
            'country_code' => 'SG',
            'name' => 'new campaign',
            'brand' => null,
            'description' => null,
            'start_at' => null,
            'end_at' => null,
        ]);

        $this->actingAs($admin)
            ->delete($campaign->getPath() . '/status')
            ->assertRedirect('campaigns?filter_tab=campaigns');
    }
}
