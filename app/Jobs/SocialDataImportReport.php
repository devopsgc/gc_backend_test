<?php

namespace App\Jobs;

use App\Models\Record;
use App\Helpers\SocialDataApi;
use App\Helpers\TagHelper;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Image;
use Storage;
use Log;

class SocialDataImportReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $export;

    public function __construct($export)
    {
        $this->export = $export;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SocialDataApi $api)
    {
        $importedCount = 0;
        if ($accountsData = $api->downloadReport($this->export->export_id)) {
            $inArrayAccountsData = array_filter(explode("\n", $accountsData));

            foreach ($inArrayAccountsData as $account) {
                $accountInObject = json_decode($account)->account->user_profile;

                if (!$this->alreadyExists($accountInObject)) {
                    $importedCount++;
                    $this->saveAsRecord($accountInObject);
                } else {
                    Log::info('SocialDataImportReport already exists: '. $accountInObject->user_id);
                }
            }
        }
        Log::info('SocialDataImportReport imported: '. $importedCount);
    }

    protected function alreadyExists($account)
    {
        return Record::withTrashed()->where('instagram_id', $account->username)
            ->orWhere('socapi_user_id', $account->user_id)
            ->count() > 0;
    }

    protected function saveAsRecord($account)
    {
        $record = new Record;
        $record->socapi_user_id = $account->user_id;
        $record->instagram_id = isset($account->username) ? $account->username : '';
        $record->name = isset($account->fullname) ? $account->fullname : '';
        if (isset($account->gender)) {
            if ($account->gender === 'FEMALE') {
                $record->gender = 'F';
            } elseif ($account->gender === 'MALE') {
                $record->gender = 'M';
            }
        }
        $record->instagram_name = isset($account->fullname) ? $account->fullname : '';
        $record->description = isset($account->description) ? $account->description : '';
        $record->instagram_followers = isset($account->followers) ? $account->followers : '';
        $record->country_code = isset($account->geo->country->code) ? $account->geo->country->code : '';
        $record->city = isset($account->geo->city->name) ? $account->geo->city->name : '';
        $record->save();

        $record->instagram_photo = isset($account->picture) ? $this->storeImageToStorageAndGetLink($account, $record) : '';
        $record->save();

        if (isset($account->interests)) {
            TagHelper::createOrRestoreInterestTags($record, collect($account->interests)->pluck('name')->toArray());
        }

        Log::info('SocialDataImportReport imported record: '. $record->id);
    }

    protected function storeImageToStorageAndGetLink($account, $record)
    {
        try {
            $image = Image::make($account->picture)->stream()->__toString();
            Storage::put('images/instagram/' . sha1($record->id) . '.jpeg', $image);
            $image = Image::make($account->picture)
                    ->fit(400, 400, function ($constraint) { $constraint->upsize(); })
                    ->stream()->__toString();
            Storage::put('images/instagram/thumbnail/' . sha1($record->id) . '.jpeg', $image);
            return 'images/instagram/' . sha1($record->id) . '.jpeg';
        } catch (Exception $e) {
            Log::info('SocialDataImportReport image not stored for record: '. $record->id);
            Log::info($e->getMessage());
        }
    }
}
