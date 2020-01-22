<?php

namespace App\Console\Commands;

use App\Helpers\SocialDataApi;
use App\Models\Data\SocialDataReport;
use Illuminate\Console\Command;

class SocialDataGenerateDryRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'socapi:export-dry-run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dry run to see the export cost and value.';

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
    public function handle(SocialDataApi $api)
    {
        foreach (SocialDataReport::getCountriesEnabled() as $country_id) {
            $response = $api->generateReportDryRun($country_id);
            dump(json_decode($response));
        }
    }
}
