<?php

namespace Tests\Feature;

use App\Helpers\SocialDataHelper;
use App\Models\Record;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class SocapiTest extends TestCase
{
    use RefreshDatabase;

    protected function createRecordWithNoNeedOfUpdate()
    {
        $record =  factory(Record::class)->create([
            'instagram_id' => 'testhandle',
            'instagram_socapi_updated_at' => Carbon::now(),
        ]);

        $record->instagramSocapiData()->create(json_decode('{"user_profile":{"username":"testhandle"}}', true));

        return $record;
    }

    public function test_given_just_updated_and_test_handle_correct_instagram_id_is_same_and_has_data_it_will_not_update()
    {
        $updatedRecord = $this->createRecordWithNoNeedOfUpdate();

        $this->assertEquals(SocialDataHelper::canUpdateForDownloadSlide($updatedRecord->refresh()), false);
    }

    public function test_given_not_updated_yet_can_update()
    {
        $updatedRecord = $this->createRecordWithNoNeedOfUpdate();
        $updatedRecord->instagram_socapi_updated_at = null;
        $updatedRecord->save();

        $this->assertEquals(SocialDataHelper::canUpdateForDownloadSlide($updatedRecord->refresh()), true);
    }

    public function test_given_instargram_id_is_changed_can_update()
    {
        $updatedRecord = $this->createRecordWithNoNeedOfUpdate();
        $updatedRecord->instagram_id = 'anotherhandle';
        $updatedRecord->save();

        $this->assertEquals(SocialDataHelper::canUpdateForDownloadSlide($updatedRecord->refresh()), true);
    }

    public function test_given_updated_more_than_thirty_days_ago_can_update()
    {
        $updatedRecord = $this->createRecordWithNoNeedOfUpdate();
        $updatedRecord->instagram_socapi_updated_at = Carbon::now()->subDays(31);
        $updatedRecord->save();

        $this->assertEquals(SocialDataHelper::canUpdateForDownloadSlide($updatedRecord->refresh()), true);
    }

    public function test_given_updated_before_but_without_data_can_update()
    {
        $updatedRecord = $this->createRecordWithNoNeedOfUpdate();
        $updatedRecord->instagramSocapiData->delete();
        $updatedRecord->save();

        $this->assertEquals(SocialDataHelper::canUpdateForDownloadSlide($updatedRecord->refresh()), true);
    }

    public function test_record_can_update_instagram_social_data()
    {
        $mock = \Mockery::mock('App\Helpers\SocialDataApi')->makePartial();
        $mock->shouldReceive('getJsonFromApi')->andReturn($this->getSampleSocialData('A'));

        $this->withoutExceptionHandling();
        $record = factory(Record::class)->create(['instagram_id' => 'test']);

        $mock->update($record);

        $this->assertEquals($record->refresh()->instagramSocapiData->report_info['report_id'], '5d8ac9097e691cf9e6049d0d', 'Instagram Data is not updated');
    }

    public function test_record_can_update_and_instagram_social_data_with_soft_delete()
    {
        $mock = \Mockery::mock('App\Helpers\SocialDataApi')->makePartial();
        $mock->shouldReceive('getJsonFromApi')->andReturn($this->getSampleSocialData('A'));

        $this->withoutExceptionHandling();
        $record = factory(Record::class)->create(['instagram_id' => 'test']);
        $record->instagramSocapiData()->create(json_decode($this->getSampleSocialData('B'), true));

        $this->assertDatabaseHas('instagram_social_datas', [
            'record_id' => $record->id,
            'report_info' => [
                'report_id' => '5d8c26b20c87fa86e1e95823',
                'created' => '2019-09-26T02:47:14.267+0000'
            ]
        ], 'mongodb');

        $mock->update($record);

        $this->assertEquals($record->refresh()->instagramSocapiData->report_info['report_id'], '5d8ac9097e691cf9e6049d0d', 'Instagram Data is not updated');

        $this->assertSoftDeleted('instagram_social_datas', [
            'record_id' => $record->id,
            'report_info' => [
                'report_id' => '5d8c26b20c87fa86e1e95823',
                'created' => '2019-09-26T02:47:14.267+0000'
            ]
        ], 'mongodb');
    }

    public function test_record_with_social_instagram_data_will_retain_old_data_if_api_fail()
    {
        $mock = \Mockery::mock('App\Helpers\SocialDataApi')->makePartial();
        $mock->shouldReceive('getJsonFromApi')->andReturn('');

        $this->withoutExceptionHandling();
        $record = factory(Record::class)->create(['instagram_id' => 'test']);
        $record->instagramSocapiData()->create(json_decode($this->getSampleSocialData('B'), true));

        $this->assertDatabaseHas('instagram_social_datas', [
            'record_id' => $record->id,
            'report_info' => [
                'report_id' => '5d8c26b20c87fa86e1e95823',
                'created' => '2019-09-26T02:47:14.267+0000'
            ]
        ], 'mongodb');

        $mock->update($record);

        $this->assertEquals($record->refresh()->instagramSocapiData->report_info['report_id'], '5d8c26b20c87fa86e1e95823', 'Instagram Data is not retained');
    }

}
