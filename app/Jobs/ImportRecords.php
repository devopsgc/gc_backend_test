<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Imports\RecordsImport;
use App\Models\Country;
use Excel;

class ImportRecords implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $file_name;
    protected $country_code;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file_name, $country_code)
    {
        $this->file_name = $file_name;
        $this->country_code = $country_code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($country = Country::where('iso_3166_2', '=', $this->country_code)->first())
        {
            $import = new RecordsImport;
            $import->country_code = $country->iso_3166_2;
            Excel::import($import, 'import/'.$this->file_name);
        }
    }
}
