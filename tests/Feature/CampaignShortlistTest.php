<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Record;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CampaignShortlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_remove_shortlist_from_campaign()
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

        $prePopulatedrecords = factory(Record::class, 3)->create();

        foreach ($prePopulatedrecords as $record) {
            $campaign->records()->attach($record);
        }

        $this->actingAs($admin)
            ->get('campaigns/'.$campaign->id.'/shortlist')
            ->assertRedirect('campaigns/shortlist');

        foreach ($prePopulatedrecords as $record) {
            $this->actingAs($admin)
                ->get('campaigns/shortlist')
                ->assertSeeText($record->name);
        }

        $toAddRecord = factory(Record::class)->create();

        $this->actingAs($admin)
            ->get('records')
            ->assertStatus(200);

        $this->actingAs($admin)
            ->post('campaigns/shortlist', [
                'record_id' => $toAddRecord->id
            ])
            ->assertStatus(200)
            ->assertJson([
                'message' => 'success',
                'badge_count' => 4,
            ]);

        $this->actingAs($admin)
            ->get('campaigns/shortlist')
            ->assertSeeText($toAddRecord->name);

        $toRemoveRecord = $prePopulatedrecords->first();

        $this->actingAs($admin)
            ->post('campaigns/shortlist/remove-selection', [
                'record_id' => $toRemoveRecord->id
            ])
            ->assertStatus(200)
            ->assertJson([
                'message' => 'success',
                'badge_count' => 3,
            ]);

        $this->actingAs($admin)
            ->get('campaigns/shortlist')
            ->assertDontSeeText($toRemoveRecord->name);
    }
}
