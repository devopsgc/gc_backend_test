<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Record;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignLinkTest extends TestCase
{
    use RefreshDatabase;

    private function generateSGCountry()
    {
        \DB::table('countries')->insert(['id' => 1, 'iso_3166_2' => 'SG', 'name' => 'Singapore']);
    }

    public function test_if_given_campaign_has_no_link_can_see_links_report()
    {
        if (! config('featureToggle.campaignReport')) {
            $this->assertTrue(true);
            return;
        }

        $user = $this->getSuperAdminUser();
        $campaign = factory(Campaign::class)->create();
        $this->generateSGCountry();
        $record = factory(Record::class)->create();
        $campaign->records()->attach($record->id, ['package_price' => 100]);

        $this->actingAs($user)->get('/campaigns/' . $campaign->id)->assertStatus(200);
        $this->actingAs($user)->get('/campaigns/' . $campaign->id . '/links-tracking')->assertStatus(200);
    }
}
