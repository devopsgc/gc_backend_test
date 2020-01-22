<?php

namespace App\Console\Commands;

use App\Jobs\GenerateRecordsExcel as Job;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateRecordsExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deck:xls {id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate records in excel (.xlsx) format';

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
            $report = Report::find($this->argument('id'));
            Job::dispatch($report);
            $this->info('Generate reports excel job dispatched.');
        } else {
            if ($reports = Report::whereNull('generated_at')
                ->where('created_at', '<', Carbon::now()->subHours(3)->format('Y-m-d H:i:s'))
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()) {
                foreach ($reports as $report) {
                    Job::dispatch($report);
                }
            }
        }
    }
}
