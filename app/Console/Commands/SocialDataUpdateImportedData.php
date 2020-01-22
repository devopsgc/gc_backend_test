<?php

namespace App\Console\Commands;

use App\Helpers\SocialDataApi;
use App\Models\Data\SocialDataReport;
use App\Models\Record;
use Illuminate\Console\Command;

class SocialDataUpdateImportedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'socialdata:update-imported-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the imported data from the social data. (eg. gender was not update properly, so re update again using this script)';

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
        foreach (SocialDataReport::all() as $export) {
            if ($accountsData = $api->downloadReport($export->export_id)) {
                $inArrayAccountsData = array_filter(explode("\n", $accountsData));

                foreach ($inArrayAccountsData as $accountData) {
                    $account = json_decode($accountData)->account->user_profile;
                    if ($record = Record::where('socapi_user_id', $account->user_id)->first()) {
                        if (! $record->gender && isset($account->gender)) {
                            if ($account->gender === 'FEMALE') {
                                $record->update(['gender' => 'F']);
                            } elseif ($account->gender === 'MALE') {
                                $record->update(['gender' => 'M']);
                            }
                        }
                    }
                }
            }
        }
    }
}
