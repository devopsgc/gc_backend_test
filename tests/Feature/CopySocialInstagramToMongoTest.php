<?php

namespace Tests\Feature;

use App\Models\Data\InstagramSocialData;
use App\Models\Record;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Artisan;

class CopySocialInstagramToMongoTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
        foreach (InstagramSocialData::all() as $data) {
            $data->delete();
        }
    }

    public function test_can_copy_social_data_from_mysql_to_mongo()
    {
        $record = factory(Record::class)->create(['instagram_socapi' => $this->getSampleSocialData('A')]);

        Artisan::call('copy:socapi-to-mongo');

        $this->assertDatabaseHas('instagram_social_datas', ['record_id' => $record->id], 'mongodb');
    }

    public function test_can_will_not_copy_twice_for_the_same_report_number()
    {
        factory(Record::class)->create(['instagram_socapi' => $this->getSampleSocialData('A')]);

        Artisan::call('copy:socapi-to-mongo');
        Artisan::call('copy:socapi-to-mongo');

        $this->assertEquals(InstagramSocialData::all()->count(), 1);
    }

    public function test_can_update_one_report()
    {
        $willUpdateRecord = factory(Record::class)->create(['instagram_socapi' => $this->getSampleSocialData('A')]);
        factory(Record::class)->create(['instagram_socapi' => $this->getSampleSocialData('A')]);

        Artisan::call('copy:socapi-to-mongo', [
            'id' => $willUpdateRecord->id,
        ]);

        $this->assertEquals(InstagramSocialData::all()->count(), 1);
        $this->assertEquals(InstagramSocialData::first()->record_id, $willUpdateRecord->id);
    }
}
