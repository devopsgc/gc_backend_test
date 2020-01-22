<?php

namespace App\Jobs;

use App\Helpers\SocialDataApi;
use App\Models\Data\SocialDataReport;
use App\Jobs\SocialDataImportReport as JobsSocialDataImportReport;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SocialDataGenerateReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $country_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($country_id)
    {
        $this->country_id = $country_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SocialDataApi $api)
    {
        if ($response = $api->generateReport($this->country_id)) {
            $exportInfo = json_decode($response, true);
            $reportDoesNotExists = !SocialDataReport::where('export_id', $exportInfo['export_id'])->first();

            if ($exportInfo['export_id'] && $reportDoesNotExists) {
                $export = SocialDataReport::create($exportInfo);
                JobsSocialDataImportReport::dispatch($export)->delay(now()->addMinutes(1));;
                $export->update(['imported_at' => Carbon::now()]);
            }
        };
    }
}
