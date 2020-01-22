<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ImportRecords as Job;

class ImportRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:records {file_name} {country_code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import records from xls file into database';

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
        Job::dispatch($this->argument('file_name'), $this->argument('country_code'));
        $this->info('Import job dispatched: '.$this->argument('file_name'));
    }
}
