<?php

namespace Tests\Feature;

use App\Helpers\Metrics;
use App\Models\Campaign;
use App\Models\Country;
use App\Models\Record;
use App\Models\Report;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class MetricsUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_metrics_returns_correct_monthly_items_generated()
    {
        // test for this month
        factory(Record::class, 3)->create();

        $this->assertTrue(Metrics::generateMetrics()['recordsGeneratedChart']['y-axis'][5] === 3);

        // test for last month on edge days, start of month and end of month
        factory(Record::class, 2)->create(['created_at' => Carbon::now()->timezone('Asia/Singapore')->startOfMonth()->subMonth(1)]);
        factory(Record::class, 2)->create(['created_at' => Carbon::now()->timezone('Asia/Singapore')->endOfMonth()->subMonth(1)]);

        $this->assertTrue(Metrics::generateMetrics()['recordsGeneratedChart']['y-axis'][4] === 4);
    }

    public function test_metrics_count_of_campaigns_generated_by_country()
    {
        $this->seed('CountriesSeeder');

        $singapore = Country::where('iso_3166_2', 'SG')->first();
        $malaysia = Country::where('iso_3166_2', 'MY')->first();

        factory(Campaign::class, 3)->create(['country_code' => 'SG']);
        factory(Campaign::class, 2)->create(['country_code' => 'MY']);
        $this->assertTrue(Metrics::generateMetrics($singapore)['campaignsGeneratedChart']['y-axis'][5] === 3);
        $this->assertTrue(Metrics::generateMetrics($malaysia)['campaignsGeneratedChart']['y-axis'][5] === 2);
    }

    public function test_metrics_count_of_report_generated_by_country()
    {
        $this->seed('CountriesSeeder');

        $singapore = Country::where('iso_3166_2', 'SG')->first();
        $malaysia = Country::where('iso_3166_2', 'MY')->first();

        $campaign = factory(Campaign::class)->create(['country_code' => 'SG']);
        factory(Report::class, 2)->create(['campaign_id' => $campaign]);

        $this->assertTrue(Metrics::generateMetrics($singapore)['reportsGeneratedChart']['y-axis'][5] === 2);
        $this->assertTrue(Metrics::generateMetrics($malaysia)['reportsGeneratedChart']['y-axis'][5] === 0);
    }

    public function test_metrics_count_of_influencers_generated_by_country()
    {
        $this->seed('CountriesSeeder');

        $singapore = Country::where('iso_3166_2', 'SG')->first();
        $malaysia = Country::where('iso_3166_2', 'MY')->first();

        factory(Record::class, 2)->create(['country_code' => $singapore->iso_3166_2]);
        factory(Record::class, 1)->create(['country_code' => $malaysia->iso_3166_2]);

        $this->assertTrue(Metrics::generateMetrics($singapore)['recordsGeneratedChart']['y-axis'][5] === 2);
        $this->assertTrue(Metrics::generateMetrics($malaysia)['recordsGeneratedChart']['y-axis'][5] === 1);
    }
}
