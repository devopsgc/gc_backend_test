<?php

namespace App\Http\Controllers;

use Auth;
use App\Helpers\GenerateDeckPptHelper;
use App\Models\Campaign;
use App\Models\Record;

class CampaignExcelDownloadController extends Controller
{
    public function store(Campaign $campaign)
    {
        if (! Auth::user()->can('download_excel', Record::class)) abort(404);

        GenerateDeckPptHelper::createRecordsExcel($campaign->records->pluck('id')->toArray(), $campaign->id);

        return redirect()->back()->with('message', '<strong>Generating excel.</strong> An email will be send to you once your excel is ready to be downloaded.');
    }
}
