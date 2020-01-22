<?php

namespace Tests\Feature;

use App\Jobs\SocialDataGenerateReport;
use App\Jobs\SocialDataImportReport;
use App\Models\Data\SocialDataReport;
use App\Models\Record;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Artisan;
use Queue;

class SocialDataReportHelperTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
        foreach (SocialDataReport::all() as $data) {
            $data->delete();
        }
    }

    public function test_command_social_data_generate_report_dispatch_a_job_queue_for_each_countries_enabled()
    {
        Queue::fake();

        Artisan::call('socialdata:generate-report');

        Queue::assertPushed(SocialDataGenerateReport::class, count(SocialDataReport::getCountriesEnabled()));
    }

    public function test_same_report_id_from_social_data_dont_get_stored_twice()
    {
        Artisan::call('socialdata:generate-report');
        Artisan::call('socialdata:generate-report');

        // because its using the same json test file so it should only store once
        $this->assertEquals(SocialDataReport::all()->count(), count(SocialDataReport::getCountriesEnabled()));
    }

    public function test_job_social_data_generate_report_save_report_successfully_for_each_countries_enabled()
    {
        Artisan::call('socialdata:generate-report');

        $this->assertEquals(SocialDataReport::all()->count(), count(SocialDataReport::getCountriesEnabled()));
    }

    public function test_after_generation_of_report_will_dispatch_a_import_command_to_import_immediately()
    {
        Artisan::call('socialdata:generate-report');

        $this->assertEquals(SocialDataReport::whereNotNull('imported_at')->count(), count(SocialDataReport::getCountriesEnabled()));
    }

    public function test_get_filters_for_countries()
    {
        $filters = SocialDataReport::getReportGenerationFilters(536780);

        $this->assertEquals($filters['filter']['geo'][0]['id'], '536780');
    }

    public function test_job_import_will_import_the_accounts()
    {
        Artisan::call('socialdata:generate-report');

        $this->assertTrue(Record::whereNotNull('socapi_user_id')->count() > 0);
        $this->assertTrue(SocialDataReport::whereNotNull('imported_at')->count() > 0);
    }

    public function test_will_not_import_if_instagram_id_already_exists()
    {
        Record::create(['instagram_id' => 'inthira16', 'country_code' => 'SG']);

        $this->assertEquals(Record::where('instagram_id', 'inthira16')->count(), 1);

        Artisan::call('socialdata:generate-report');

        $this->assertEquals(Record::where('instagram_id', 'inthira16')->count(), 1);
    }

    public function test_will_not_import_if_socapi_id_already_exists()
    {
        Record::create(['socapi_user_id' => 16022574280, 'country_code' => 'SG']);

        $this->assertEquals(Record::where('socapi_user_id', 16022574280)->count(), 1);

        Artisan::call('socialdata:generate-report');

        $this->assertEquals(Record::where('socapi_user_id', 16022574280)->count(), 1);
    }

    public function test_import_account_will_import_interests()
    {
        $this->seed('TagSeeder');

        Artisan::call('socialdata:generate-report');

        $this->assertTrue(Record::first()->interestsCore->count() > 0);
    }

    public function test_generate_report_dry_run_filters()
    {
        $this->assertEquals(SocialDataReport::getReportGenerationFiltersDryRun(SocialDataReport::SD_COUNTRY_SG)['dry_run'], true);
    }

    public function test_import_those_not_imported_job_should_be_run()
    {
        SocialDataReport::create(json_decode(file_get_contents(storage_path() . '/test/sample_' . SocialDataReport::SD_COUNTRY_SG . '_exports-new.json'), true));

        Artisan::call('socialdata:import-those-not-imported');

        $this->assertTrue(Record::count() > 0);
    }

    public function test_import_those_not_imported_job_should_be_run_for_a_single_export_id()
    {
        $sgReport = SocialDataReport::create(json_decode(file_get_contents(storage_path() . '/test/sample_' . SocialDataReport::SD_COUNTRY_SG . '_exports-new.json'), true));
        SocialDataReport::create(json_decode(file_get_contents(storage_path() . '/test/sample_' . SocialDataReport::SD_COUNTRY_MY . '_exports-new.json'), true));

        Artisan::call('socialdata:import-those-not-imported', ['export_id' => $sgReport->export_id]);

        $this->assertTrue(Record::count() > 0);
    }

    public function test_job_is_run_to_fix_already_imported_data()
    {
        SocialDataReport::create(json_decode(file_get_contents(storage_path() . '/test/sample_' . SocialDataReport::SD_COUNTRY_SG . '_exports-new.json'), true));

        Artisan::call('socialdata:generate-report');

        $recordToTestForUpdateGender = Record::where('gender', 'F')->first();
        $recordToTestForUpdateGender->gender = null;
        $recordToTestForUpdateGender->save();

        $this->assertEquals($recordToTestForUpdateGender->gender, null);

        Artisan::call('socialdata:update-imported-data');

        $this->assertEquals($recordToTestForUpdateGender->fresh()->gender, 'Female');
    }

    public function test_job_is_run_to_fix_already_imported_data_should_not_overwrite()
    {
        SocialDataReport::create(json_decode(file_get_contents(storage_path() . '/test/sample_' . SocialDataReport::SD_COUNTRY_SG . '_exports-new.json'), true));

        Artisan::call('socialdata:generate-report');

        $recordToTestForUpdateGender = Record::where('gender', 'F')->first();
        $recordToTestForUpdateGender->gender = 'M';
        $recordToTestForUpdateGender->save();

        $this->assertEquals($recordToTestForUpdateGender->gender, 'Male');

        Artisan::call('socialdata:update-imported-data');

        $this->assertEquals($recordToTestForUpdateGender->fresh()->gender, 'Male');
    }
}
