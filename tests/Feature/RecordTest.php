<?php

namespace Tests\Feature;

use App\Helpers\TagHelper;
use App\Models\Campaign;
use App\Models\Record;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_record_for_unique_instagram_id_even_if_there_already_exists_a_deleted_one()
    {
        $record = factory(Record::class)->create(['instagram_id' => 'abc']);
        $record->delete();

        factory(Tag::class)->create(['name' => 'Music', 'type' => 'interest_core']);
        factory(Tag::class)->create(['name' => 'Actor/Actress', 'type' => 'profession_core']);

        $this->actingAs($this->getSuperAdminUser())
            ->post('/records', [
                'name' => 'hahaha',
                'country_code' => 'SG',
                'gender' => 'M',
                'interests' => 'Music',
                'professions' => 'Actor/Actress',
                'instagram_id' => 'abc',
            ])->assertSessionMissing(['errors']);
    }

    public function test_can_add_affliations_to_a_record()
    {
        $record = factory(Record::class)->create();

        factory(Tag::class)->create(['name' => 'Music', 'type' => 'interest_core']);
        factory(Tag::class)->create(['name' => 'Actor/Actress', 'type' => 'profession_core']);
        factory(Tag::class)->create(['name' => 'GTA', 'type' => 'affiliation']);

        $this->actingAs($this->getSuperAdminUser())
            ->put('/records/' . $record->id, [
                'name' => 'test name',
                'country_code' => 'SG',
                'gender' => 'M',
                'interests' => 'Music',
                'professions' => 'Actor/Actress',
                'affiliations' => 'GTA',
                'instagram_id' => 'abc',
                'language' => 'en',
                'description_ppt' => 'test description',
            ]);

        $this->assertEquals($record->refresh()->affiliationTags->first()->name, 'GTA', 'Tag is not created correctly in database');
    }

    public function test_can_remove_the_last_affliations_of_a_record()
    {
        $record = factory(Record::class)->create();

        factory(Tag::class)->create(['name' => 'Music', 'type' => 'interest_core']);
        factory(Tag::class)->create(['name' => 'Actor/Actress', 'type' => 'profession_core']);
        factory(Tag::class)->create(['name' => 'GTA', 'type' => 'affiliation']);

        TagHelper::createOrRestoreAffiliationTags($record, ['GTA']);

        $this->actingAs($this->getSuperAdminUser())
            ->put('/records/' . $record->id, [
                'name' => 'test name',
                'country_code' => 'SG',
                'gender' => 'M',
                'interests' => 'Music',
                'professions' => 'Actor/Actress',
                'instagram_id' => 'abc',
                'language' => 'en',
                'description_ppt' => 'test description',
            ]);

        $this->assertEquals($record->refresh()->affiliationTags->first(), null, 'Tag is not deleted correctly in database');
    }

    public function test_can_see_records_belong_to_which_campaigns()
    {
        $record = factory(Record::class)->create();
        factory(Campaign::class, 3)->create();
        foreach (Campaign::all() as $campaign) {
            $campaign->records()->attach($record->id, ['package_price' => 10]);
        }

        $response = $this->actingAs($this->getSuperAdminUser())
            ->get('/records/' . $record->id . '/edit');

        foreach (Campaign::all() as $campaign) {
            $response->assertSeeText($campaign->name);
        }
    }

    public function test_user_can_only_see_records_belong_to_which_campaigns_that_they_have_access_to()
    {
        $record = factory(Record::class)->create();
        $sales = $this->getSalesUser();
        $campaignCreatedBySales = factory(Campaign::class)->create(['created_by_user_id' => $sales->id]);
        $campaignCreatedByOthers = factory(Campaign::class)->create();
        $campaignCreatedBySales->records()->attach($record->id, ['package_price' => 10]);
        $campaignCreatedByOthers->records()->attach($record->id, ['package_price' => 10]);

        $this->actingAs($sales)
            ->get('/records/' . $record->id . '/edit')
            ->assertSeeText($campaignCreatedBySales->name)
            ->assertDontSeeText($campaignCreatedByOthers->name);
    }

    public function test_user_can_only_see_records_belong_to_accepted_and_completed_campaigns()
    {
        $validStatus = array_diff(Campaign::getAllStatuses(), [Campaign::STATUS_DELETED]);
        $record = factory(Record::class)->create();
        foreach ($validStatus as $status) {
            $campaign = factory(Campaign::class)->create(['status' => $status]);
            $campaign->records()->attach($record->id, ['package_price' => 10]);
        }

        $response = $this->actingAs($this->getSuperAdminUser())
            ->get('/records/' . $record->id . '/edit');

        foreach (Campaign::all() as $campaign) {
            if (in_array($campaign->status, [Campaign::STATUS_ACCEPTED, Campaign::STATUS_COMPLETED])) {
                $response->assertSeeText($campaign->name);
            } else {
                $response->assertDontSeeText($campaign->name);
            }
        }
    }

    public function test_user_can_see_record_listing()
    {
        $this->withoutExceptionHandling();
        $user = $this->getSuperAdminUser();

        factory(Record::class, 3)->create();

        $records = Record::all();

        $response = $this->actingAs($user)
            ->get('/records')
            ->assertStatus(200);

        foreach ($records as $record) {
            $response->assertSeeText($record->name);
        }
    }

    public function test_user_can_see_record_listing_with_search_filter()
    {
        $user = $this->getSuperAdminUser();

        factory(Record::class)->create(['name' => 'cannotsee']);
        factory(Record::class)->create(['name' => 'shouldsee']);

        $this->actingAs($user)
            ->get('/records?q=should')
            ->assertStatus(200)
            ->assertSeeText('shouldsee');
    }

    public function test_user_can_download_record_listing_excel()
    {
        $user = $this->getSuperAdminUser();

        factory(Record::class, 3)->create();

        $response = $this->actingAs($user)
            ->get('/records?submit=xls')
            ->assertStatus(200);

        $this->assertEquals(get_class($response->baseResponse), 'Symfony\Component\HttpFoundation\BinaryFileResponse');
    }
}
