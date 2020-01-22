<?php

namespace Tests\Feature;

use App\Jobs\GenerateCampaignDeckPpt;
use App\Jobs\GenerateRecordsExcel;
use App\Models\Campaign;
use App\Models\Record;
use App\Models\Tag;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
        Queue::fake();
    }

    public function createRecordWithTagsAndAddInSession($property, $num)
    {
        $this->seed('CountriesSeeder');

        factory(Record::class, $num)->create($property);

        $records = Record::all();

        $this->withSession([
            'selected' => $records->pluck('id'),
        ]);

        $interestTag = factory(Tag::class)->create(['name' => 'Music', 'type' => 'interest_core']);
        $professionTag = factory(Tag::class)->create(['name' => 'Actor/Actress', 'type' => 'profession_core']);
        foreach ($records as $record) {
            $record->tags()->attach($interestTag->id, ['type' => $interestTag->type]);
            $record->tags()->attach($professionTag->id, ['type' => $professionTag->type]);
        }

        return $records;
    }

    public function test_can_create_proposal_from_short_list()
    {
        $admin = $this->getSuperAdminUser();

        $this->createRecordWithTagsAndAddInSession([], 2);

        $this->actingAs($admin)
            ->post('/campaigns', [
                'currency_code' => 'SGD'
            ])
            ->assertRedirect('/campaigns')
            ->assertSessionHas('message');

        $this->assertEquals(Campaign::all()->count(), 1);

        $newCampaignCreated = Campaign::first();

        $this->assertDatabaseHas('campaigns', [
            'name' => 'Proposal ' . $newCampaignCreated->id,
            'currency_code' => 'SGD',
            'status' => Campaign::STATUS_DRAFT,
            'country_code' => 'SG',
            'created_by_user_id' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get('/campaigns/shortlist')
            ->assertSeeText('Proposal ' . $newCampaignCreated->id);
    }

    public function test_cannot_create_campaign_from_short_list_with_negative_price_list()
    {
        $admin = $this->getSuperAdminUser();

        $this->createRecordWithTagsAndAddInSession([], 2);

        $this->actingAs($admin)
            ->post('/campaigns', [
                'currency_code' => 'SGD',
                'total_price' => '-1',
                'package_price' => ['0', '0'],
            ])
            ->assertSessionHasErrors('total_price')
            ->assertStatus(302);
    }

    public function test_cannot_create_campaign_from_short_list_with_total_price_more_than_package_price()
    {
        $admin = $this->getSuperAdminUser();

        $this->createRecordWithTagsAndAddInSession([], 2);

        $this->actingAs($admin)
            ->post('/campaigns', [
                'currency_code' => 'SGD',
                'total_price' => '10',
                'package_price' => ['1', '2'],
            ])
            ->assertSessionHasErrors('total_price')
            ->assertStatus(302);
    }

    public function test_cannot_create_campaign_from_short_list_with_no_currency_code()
    {
        $admin = $this->getSuperAdminUser();

        $this->createRecordWithTagsAndAddInSession([], 2);

        $this->actingAs($admin)
            ->post('/campaigns', [
                'total_price' => '1',
                'package_price' => ['1', '2'],
            ])
            ->assertSessionHasErrors('currency_code')
            ->assertStatus(302);
    }

    public function test_can_update_campaign_status()
    {
        $admin = $this->getSuperAdminUser();

        $campaign = factory(Campaign::class)->create(['status' => Campaign::STATUS_DRAFT]);

        $this->actingAs($admin)
            ->post($campaign->getPath() . '/status', [
                'status' => Campaign::STATUS_ACCEPTED
            ])
            ->assertStatus(200);

        $this->assertEquals($campaign->refresh()->status, Campaign::STATUS_ACCEPTED);
    }

    public function test_can_update_campaign_detail()
    {
        $admin = $this->getSuperAdminUser();

        $campaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
            'country_code' => 'SG',
            'name' => 'Proposal 1',
            'brand' => null,
            'description' => null,
            'start_at' => null,
            'end_at' => null,
        ]);

        $startAt = Carbon::now()->subDays(30);
        $endAt = Carbon::now()->addDays(30);

        $attributes = [
            'country_code' => 'PH',
            'currency_code' => 'SGD',
            'name' => 'new name',
            'brand' => 'new brand',
            'budget' => '1000',
            'description' => 'new description',
        ];

        $this->actingAs($admin)
            ->put($campaign->getPath(), array_merge($attributes, [
                'interests' => 'Beauty & Cosmetics|Clothes, Shoes, Handbags & Accessories',
                'start_at' => $startAt->format('d F Y'),
                'end_at' => $endAt->format('d F Y'),
            ]))
            ->assertStatus(302)
            ->assertSessionHas('message', 'The data has been saved.');

        $this->assertDatabaseHas('campaigns', array_merge($attributes, [
            'categories' => 'Beauty & Cosmetics|Clothes, Shoes, Handbags & Accessories',
            'start_at' => $startAt->startOfDay()->format('Y-m-d H:i:s'),
            'end_at' => $endAt->startOfDay()->format('Y-m-d H:i:s'),
        ]));
    }

    public function test_can_see_proposal_in_campaigns_listing_page_default_tab()
    {
        $admin = $this->getSuperAdminUser();

        factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
            'country_code' => 'SG',
            'name' => 'Proposal 1',
            'brand' => null,
            'description' => null,
            'start_at' => null,
            'end_at' => null,
        ]);

        $this->actingAs($admin)
            ->get('/campaigns')
            ->assertSeeText('Proposal 1');
    }

    public function test_can_see_accepted_rejected_cancelled_in_campaigns_listing_page_campaigns_tab()
    {
        $admin = $this->getSuperAdminUser();

        $statusToCreate = [
            Campaign::STATUS_ACCEPTED,
            Campaign::STATUS_REJECTED,
            Campaign::STATUS_CANCELLED,
        ];

        foreach ($statusToCreate as $key => $status) {
            factory(Campaign::class)->create([
                'status' => $status,
                'country_code' => 'SG',
                'name' => 'Proposal ' . $key,
                'brand' => null,
                'description' => null,
                'start_at' => null,
                'end_at' => null,
            ]);

            $this->actingAs($admin)
                ->get('/campaigns?filter_tab=campaigns')
                ->assertSeeText('Proposal ' . $key);
        }
    }

    public function test_can_load_campaign_into_shortlist()
    {
        $admin = $this->getSuperAdminUser();

        $this->createRecordWithTagsAndAddInSession([], 2);

        $this->actingAs($admin)
            ->post('/campaigns', [
                'currency_code' => 'SGD'
            ]);

        $this->assertEquals(Campaign::all()->count(), 1);

        $newCampaignCreated = Campaign::first();

        $this->actingAs($admin)
            ->get('/campaigns/' . $newCampaignCreated->id . '/shortlist')
            ->assertRedirect('/campaigns/shortlist');

        $allRecordsCreated = Record::all();

        foreach ($allRecordsCreated as $recordCreated) {
            $this->actingAs($admin)
                ->get('/campaigns/shortlist')
                ->assertSeeText($recordCreated->name);
        }
    }

    public function test_when_creating_new_proposal_from_shortlist_will_generate_ppt_for_download()
    {
        $admin = $this->getSuperAdminUser();

        $this->createRecordWithTagsAndAddInSession([], 2);

        $this->actingAs($admin)
            ->post('/campaigns', [
                'currency_code' => 'SGD'
            ]);

        Queue::assertPushed(GenerateCampaignDeckPpt::class, 1);
        Queue::assertPushed(GenerateRecordsExcel::class, 1);
    }

    public function test_when_editing_current_proposal_from_shortlist_will_generate_ppt_and_xls_for_download()
    {
        $admin = $this->getSuperAdminUser();

        $this->createRecordWithTagsAndAddInSession([], 2);

        $this->actingAs($admin)
            ->post('/campaigns', [
                'currency_code' => 'SGD'
            ]);

        //queue fake has to be done after creating a campaign, cause that also generate the jobs
        Queue::fake();

        $newRecordsUpdated = $this->createRecordWithTagsAndAddInSession([], 3);

        $campaignCreated = Campaign::first();

        $this->actingAs($admin)
            ->get('/campaigns/' . $campaignCreated->id . '/shortlist');

        $this->actingAs($admin)
            ->put('/campaigns/' . $campaignCreated->id . '/shortlist', [
                'currency_code' => 'SGD',
                'with_download' => '1'
            ])
            ->assertRedirect('/campaigns/shortlist');

        $this->actingAs($admin)
            ->get('/campaigns/shortlist')
            ->assertSeeText($newRecordsUpdated->first()->name);

        Queue::assertPushed(GenerateCampaignDeckPpt::class, 1);
        Queue::assertPushed(GenerateRecordsExcel::class, 1);
    }

    public function test_if_a_proposal_is_already_loaded_in_session_another_proposal_cannot_update()
    {
        $admin = $this->getSuperAdminUser();

        $this->createRecordWithTagsAndAddInSession([], 2);

        $this->actingAs($admin)
            ->post('/campaigns', [
                'currency_code' => 'SGD'
            ]);

        session(['campaign' => null]);

        $firstCampaign = Campaign::first();

        $this->createRecordWithTagsAndAddInSession([], 1);

        $this->actingAs($admin)
            ->post('/campaigns', [
                'currency_code' => 'SGD'
            ]);

        $this->actingAs($admin)
            ->put('/campaigns/' . $firstCampaign->id . '/shortlist', [
                'currency_code' => 'SGD'
            ])
            ->assertStatus(403);
    }

    public function test_campaign_in_draft_cannot_be_edited()
    {
        $admin = $this->getSuperAdminUser();

        $newCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_DRAFT,
            'country_code' => 'SG',
            'name' => 'Proposal 1',
            'brand' => null,
            'description' => null,
            'start_at' => null,
            'end_at' => null,
        ]);

        $this->actingAs($admin)
            ->get('/campaigns/'.$newCampaign->id)
            ->assertStatus(404);
    }

    public function test_update_campaign_can_update_status_to_accepted()
    {
        $this->seed('CountriesSeeder');

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

        $this->actingAs($admin)
            ->get('/campaigns/'.$newCampaign->id.'/edit')
            ->assertSeeText('Create Campaign') // see as create campaign rather than update
            ->assertSee('input type="hidden" name="status" value="Accepted"'); // hidden field to update status to accepted

        $this->actingAs($admin)
            ->put('/campaigns/'.$newCampaign->id, [
                'name' => 'new campaign',
                'currency_code' => 'SGD',
                'status' => Campaign::STATUS_ACCEPTED,
                'country_code' => 'AU',
            ])
            ->assertRedirect($newCampaign->getPath());

        $this->assertDatabaseHas('campaigns', [
            'id' => $newCampaign->id,
            'status' => Campaign::STATUS_ACCEPTED,
        ]);
    }

    public function test_exit_campaign_redirect_to_campaign_listing_filtered_campaign()
    {
        $admin = $this->getSuperAdminUser();

        $newCampaign = factory(Campaign::class)->create([
            'status' => Campaign::STATUS_ACCEPTED,
            'currency_code' => 'SGD',
            'country_code' => 'SG',
            'name' => 'Proposal 1',
            'brand' => null,
            'description' => null,
            'start_at' => null,
            'end_at' => null,
        ]);

        $this->actingAs($admin)
            ->get('/campaigns/' . $newCampaign->id . '/shortlist')
            ->assertRedirect('/campaigns/shortlist');

        $this->actingAs($admin)
            ->post('campaigns/shortlist/exit')
            ->assertRedirect('/campaigns?filter_tab=campaigns');
    }

    public function test_exit_propsal_campaign_redirect_to_campaign_listing_filtered_proposal()
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

        $this->actingAs($admin)
            ->get('/campaigns/' . $newCampaign->id . '/shortlist')
            ->assertRedirect('/campaigns/shortlist');

        $this->actingAs($admin)
            ->post('campaigns/shortlist/exit')
            ->assertRedirect('/campaigns?filter_tab=drafts');
    }
}
