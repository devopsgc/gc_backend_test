<?php

namespace App\Http\Controllers;

use App\Helpers\GenerateDeckPptHelper;
use App\Helpers\PptConfigGenerator;
use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignPowerPointDownloadController extends Controller
{
    public function store(Request $request, Campaign $campaign)
    {
        $recordIds = $campaign->records->pluck('id')->toArray();

        if (!GenerateDeckPptHelper::validateRecordsHaveDescription($recordIds)) {
            return redirect()->back()->with('warning', '<strong>Download failed.</strong> All records must have a PowerPoint description in english before they can be exported.');
        }

        $config = [];
        if ($request->net_costing == 'yes') {
            $config = PptConfigGenerator::addNetCostingConfig($config);
        }

        GenerateDeckPptHelper::createCampaignDeckReport($recordIds, $campaign, $request->language, $config);

        return redirect()->back()->with('message', '<strong>Generating report.</strong> An email will be send to you once your report is ready to be downloaded.');
    }
}
