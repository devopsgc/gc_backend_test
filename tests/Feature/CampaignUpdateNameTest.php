<?php

namespace Tests\Feature;

use App\Models\Campaign;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CampaignUpdateNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_proposal_name()
    {
        $admin = $this->getSuperAdminUser();

        $newCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
            'currency_code' => 'SGD',
            'country_code' => 'SG',
            'name' => 'Proposal 1',
            'brand' => null,
            'description' => null,
            'start_at' => null,
            'end_at' => null,
        ]);

        $newName = 'new name';

        $this->actingAs($admin)
        ->post('/campaigns/'.$newCampaign->id.'/update-name', [
            'name' => $newName
        ])->assertRedirect('campaigns/shortlist');

        $this->assertDatabaseHas('campaigns', [
            'name' => $newName,
        ]);
    }

    public function test_only_draft_and_accepted_campaigns_can_be_updated()
    {
        $admin = $this->getSuperAdminUser();

        $canUpdateStatus = [
            Campaign::STATUS_DRAFT,
            Campaign::STATUS_ACCEPTED,
        ];

        foreach ($canUpdateStatus as $status) {
            $newCampaign = factory(Campaign::class)->create([
                'status' => $status,
                'currency_code' => 'SGD',
                'country_code' => 'SG',
                'name' => 'Proposal 1',
                'brand' => null,
                'description' => null,
                'start_at' => null,
                'end_at' => null,
            ]);

            $this->actingAs($admin)
            ->post('/campaigns/'.$newCampaign->id.'/update-name', [
                'name' => 'new'
            ])->assertRedirect('campaigns/shortlist');
        }

        $cannotUpdateStatus = [
            Campaign::STATUS_CANCELLED,
            Campaign::STATUS_REJECTED,
        ];

        foreach ($cannotUpdateStatus as $status) {
            $newCampaign = factory(Campaign::class)->create([
                'status' => $status,
                'currency_code' => 'SGD',
                'country_code' => 'SG',
                'name' => 'Proposal 1',
                'brand' => null,
                'description' => null,
                'start_at' => null,
                'end_at' => null,
            ]);

            $this->actingAs($admin)
            ->post('/campaigns/'.$newCampaign->id.'/update-name', [
                'name' => 'new'
            ])->assertStatus(404);
        }
    }
}
