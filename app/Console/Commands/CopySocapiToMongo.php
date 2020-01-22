<?php

namespace App\Console\Commands;

use App\Models\Data\InstagramSocialData;
use App\Models\Record;
use Illuminate\Console\Command;

class CopySocapiToMongo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy:socapi-to-mongo {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy instagram_socapi data from mysql to mongo.';

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
        if ($this->argument('id')) {
            if ($record = Record::find($this->argument('id'))) {
                $report = json_decode($record->instagram_socapi, true);

                if (!$this->alreadyExists($report)) {
                    $this->createReport($record, $report);
                }
            }
        } else {
            Record::whereNotNull('instagram_socapi')->doesntHave('instagramSocapiData')
                ->chunk(20, function ($records) {
                foreach ($records as $record) {
                    $report = json_decode($record->instagram_socapi, true);
                    if ($this->alreadyExists($report)) {
                        continue;
                    }
                    $this->createReport($record, $report);
                }
            });
        }
    }

    protected function alreadyExists($report)
    {
        return !!InstagramSocialData::where('report_info.report_id', $report['report_info']['report_id'])->first();
    }

    protected function createReport($record, $report)
    {
        $report['record_id'] = $record->id;
        InstagramSocialData::create($report);
    }
}
