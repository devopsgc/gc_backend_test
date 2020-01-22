<?php

namespace App\Console\Commands;

use App\Jobs\SocialDataGenerateReport as JobsSocialDataGenerateReport;
use App\Models\Data\SocialDataReport;
use Illuminate\Console\Command;
use Log;

class SocialDataGenerateReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'socialdata:generate-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To generate report from social data platform to add new influencers.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('SocialDataGenerateReport started');
        foreach (SocialDataReport::getCountriesEnabled() as $country_id) {
            JobsSocialDataGenerateReport::dispatch($country_id);
            Log::info('SocialDataGenerateReport dispatched country: ' . SocialDataReport::getCountryName($country_id));
        }
    }
}
