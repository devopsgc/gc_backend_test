<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Traits\CampaignSessionHelper;

class CampaignShortlistExitController extends Controller
{
    use CampaignSessionHelper;

    public function store()
    {
        $campaign = Campaign::find(session('campaign.campaign_id'));

        $this->exitCampaign(request());

        if ($campaign->isDraft()) {
            return redirect('/campaigns?filter_tab=drafts');
        }

        return redirect('/campaigns?filter_tab=campaigns');
    }
}
