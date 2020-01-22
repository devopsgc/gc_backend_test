<?php

namespace App\Console\Commands;

use App\Models\Data\SocialDataReport;
use App\Jobs\SocialDataImportReport as JobsSocialDataImportReport;
use Illuminate\Console\Command;

class SocialDataImportThoseNotImported extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'socialdata:import-those-not-imported {export_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'From exported documents in gush, re-import those influencers that was not imported previously.';

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
        if ($this->argument('export_id')) {
            if ($export = SocialDataReport::where('export_id', $this->argument('export_id'))->first()) {
                JobsSocialDataImportReport::dispatchNow($export);
            }
        } else {
            foreach (SocialDataReport::all() as $export) {
                JobsSocialDataImportReport::dispatchNow($export);
            }
        }
    }
}
