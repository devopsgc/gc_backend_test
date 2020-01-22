<?php

namespace App\Helpers;

use App\Models\Campaign;
use App\Models\Record;
use App\Models\Report;
use Artisan;
use Auth;
use Carbon\Carbon;

class GenerateDeckPptHelper
{

    public static function createDeckReport(array $recordIds, $language = 'en', $filename = null)
    {
        $token = explode('_', $language);
        $iso_639_1 = isset($token[0]) ? $token[0] : 'en';
        $iso_3166_2 = isset($token[1]) ? $token[1] : null;
        $report = Report::create([
            'user_id' => Auth::user()->id,
            'records' => implode("\n", $recordIds),
            'iso_639_1' => $iso_639_1,
            'iso_3166_2' => $iso_3166_2,
        ]);
        $report->file = $filename ?: 'profile_report_'.$report->id.'_'.Carbon::now()->format('Ymd_His').'.pptx';
        $report->save();

        Artisan::call('deck:ppt', ['id' => $report->id]);
    }

    public static function validateRecordsHaveDescription(array $recordIds)
    {
        foreach ($recordIds as $recordId) {
            $record = Record::find($recordId);
            if (!$record->getDescriptionPpt()) {
                return false;
            }
        }
        return true;
    }

    public static function createCampaignDeckReport(array $recordIds, Campaign $campaign, $language = 'en', $config = [], $filename = null)
    {
        $token = explode('_', $language);
        $iso_639_1 = isset($token[0]) ? $token[0] : 'en';
        $iso_3166_2 = isset($token[1]) ? $token[1] : null;
        $report = Report::create([
            'user_id' => Auth::user()->id,
            'records' => implode("\n", $recordIds),
            'iso_639_1' => $iso_639_1,
            'iso_3166_2' => $iso_3166_2,
            'campaign_id' => $campaign->id,
            'config' => $config ?: null,
        ]);

        $report->file = $filename ?: 'gc'.strtolower($campaign->country_code).'_'.GenerateDeckPptHelper::convertToSnakeCase($campaign->name).'_'.Carbon::now()->format('Ymd_His').'.pptx';
        $report->save();

        Artisan::call('campaign:ppt', ['id' => $report->id]);
    }

    public static function createRecordsExcel(array $recordIds, $campaign_id = null)
    {
        $report = Report::create([
            'user_id' => Auth::user()->id,
            'records' => implode("\n", $recordIds),
        ]);

        if ($campaign_id) {
            $campaign = Campaign::find($campaign_id);
            $report->file = GenerateDeckPptHelper::convertToSnakeCase($campaign->name).'_'.$report->id.'_'.Carbon::now()->format('YmdHis').'.csv';
        } else {
            $report->file = 'profile_excel_'.$report->id.'_'.Carbon::now()->format('YmdHis').'.csv';
        }
        $report->save();

        Artisan::call('deck:xls', ['id' => $report->id]);
    }

    private static function convertToSnakeCase($str)
    {
        return strtolower(str_replace(' ', '_', $str));
    }
}
