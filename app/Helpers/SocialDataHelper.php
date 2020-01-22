<?php

namespace App\Helpers;

use Carbon\Carbon;

class SocialDataHelper
{
    public static function canUpdateForDownloadSlide($record)
    {
        return self::notUpdatedYet($record)
            || self::updateExpired($record)
            || self::instagramHandleChanged($record)
            || self::updatedButDontHaveDataPreviously($record);
    }

    public static function notUpdatedYet($record)
    {
        return !$record->instagram_socapi_updated_at;
    }

    public static function updateExpired($record)
    {
        return $record->instagram_socapi_updated_at->addDays(30)->lt(Carbon::now());
    }

    public static function instagramHandleChanged($record)
    {
        return ($record->instagramSocapiData && $record->instagram_id != json_decode(json_encode($record->instagramSocapiData))->user_profile->username);
    }

    public static function updatedButDontHaveDataPreviously($record)
    {
        return !$record->instagramSocapiData;
    }
}
