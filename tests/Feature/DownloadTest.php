<?php

namespace Tests\Feature;

use App\Jobs\GenerateRecordsExcel;
use App\Jobs\UpdateReportToGenerated;
use App\Mail\ReportReady;
use App\Models\Campaign;
use App\Models\Record;
use App\Models\Report;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Queue;

class DownloadTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_can_download_campaign_ppt()
    {
        $admin = $this->getSuperAdminUser();
        $this->seed('CountriesSeeder');

        // given a campaign with record without deliverables
        $campaign = factory(Campaign::class)->create();
        factory(Record::class, 3)->create(['country_code' => 'SG']);
        $records = Record::all();

        Report::create([
            'user_id' => $admin->id,
            'records' => implode("\n", $records->pluck('id')->toArray()),
            'iso_639_1' => 'en',
            'campaign_id' => $campaign->id,
        ]);

        foreach ($records as $record) {
            $campaign->records()->attach($record, ['package_price' => 1]);
        }

        $this->actingAs($admin)
            ->get($campaign->getPath())
            ->assertStatus(200);

        $this->actingAs($admin)
            ->post($campaign->getDownloadPptPath())
            ->assertStatus(302)
            ->assertSessionHas('message', '<strong>Generating report.</strong> An email will be send to you once your report is ready to be downloaded.');

        Mail::assertSent(ReportReady::class, function ($mail) use ($admin) {
            return $mail->hasTo($admin->email);
        });
    }

    public function test_non_admin_cannot_see_download_excel_link()
    {
        $sales = $this->getSalesUser();

        $this->seed('CountriesSeeder');

        // given a campaign with record without deliverables
        $campaign = factory(Campaign::class)->create(['created_by_user_id' => $sales->id]);
        factory(Record::class, 3)->create(['country_code' => 'SG']);
        $records = Record::all();

        foreach ($records as $record) {
            $campaign->records()->attach($record, ['package_price' => 1]);
        }

        $this->actingAs($sales)
            ->get($campaign->getPath())
            ->assertSee('Download PowerPoint')
            ->assertDontSee('Download Excel');

        $this->actingAs($sales)
            ->post($campaign->getDownloadExcelPath())
            ->assertStatus(404);
    }

    public function test_can_download_campaign_xls()
    {
        Queue::fake();

        $admin = $this->getSuperAdminUser();
        $this->seed('CountriesSeeder');

        // given a campaign with record without deliverables
        $campaign = factory(Campaign::class)->create();
        factory(Record::class, 3)->create(['country_code' => 'SG']);
        $records = Record::all();

        Report::create([
            'user_id' => $admin->id,
            'records' => implode("\n", $records->pluck('id')->toArray()),
            'iso_639_1' => 'en',
            'campaign_id' => $campaign->id,
        ]);

        foreach ($records as $record) {
            $campaign->records()->attach($record, ['package_price' => 1]);
        }

        $this->actingAs($admin)
            ->get($campaign->getPath())
            ->assertStatus(200);

        $this->actingAs($admin)
            ->post($campaign->getDownloadExcelPath())
            ->assertStatus(302)
            ->assertSessionHas('message', '<strong>Generating excel.</strong> An email will be send to you once your excel is ready to be downloaded.');

        Queue::assertPushed(GenerateRecordsExcel::class, 1);
    }
}
