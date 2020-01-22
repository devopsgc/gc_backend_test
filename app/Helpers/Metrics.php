<?php

namespace App\Helpers;

use App\Models\Campaign;
use App\Models\Country;
use App\Models\Record;
use App\Models\Report;
use Carbon\Carbon;

class Metrics
{
    public static function generateMetrics(Country $country = null)
    {
        $metrics = [];

        for ($month = 5; $month >= 0; $month--) {
            $dateToQueryStart = Carbon::now()->timezone('Asia/Singapore')->startOfMonth()->subMonths($month);
            $dateToQueryEnd = (clone $dateToQueryStart)->endOfMonth();

            $reportsCountForTheMonth = Report::where('created_at', '>=', $dateToQueryStart)
                ->where('created_at', '<=', $dateToQueryEnd)
                ->when($country, function($query) use ($country) {
                    return $query->whereHas('campaign', function ($campaignQ) use ($country) {
                        $campaignQ->where('country_code', '=', $country->iso_3166_2);
                    });
                })
                ->count();

            $metrics['reportsGeneratedChart']['x-axis'][] = $dateToQueryStart->format('M');
            $metrics['reportsGeneratedChart']['y-axis'][] = $reportsCountForTheMonth;

            $campaignsCountForTheMonth = Campaign::where('created_at', '>=', $dateToQueryStart)
                ->where('created_at', '<=', $dateToQueryEnd)
                ->when($country, function($query) use ($country) {
                    $query->where('country_code', '=', $country->iso_3166_2);
                })
                ->count();

            $metrics['campaignsGeneratedChart']['x-axis'][] = $dateToQueryStart->format('M');
            $metrics['campaignsGeneratedChart']['y-axis'][] = $campaignsCountForTheMonth;

            $recordsCountForTheMonth = Record::where('created_at', '>=', $dateToQueryStart)
                ->where('created_at', '<=', $dateToQueryEnd)
                ->when($country, function($query) use ($country) {
                    $query->where('country_code', '=', $country->iso_3166_2);
                })
                ->count();

            $metrics['recordsGeneratedChart']['x-axis'][] = $dateToQueryStart->format('M');
            $metrics['recordsGeneratedChart']['y-axis'][] = $recordsCountForTheMonth;
        }

        $metrics['reportsGeneratedChart']['title'] = 'Reports Generated';
        $metrics['campaignsGeneratedChart']['title'] = 'Campaigns Created';
        $metrics['recordsGeneratedChart']['title'] = 'Influencers Added';

        return $metrics;
    }
}
