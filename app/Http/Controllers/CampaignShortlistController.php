<?php

namespace App\Http\Controllers;

use App\Helpers\GenerateDeckPptHelper;
use App\Models\Campaign;
use App\Traits\CampaignSessionHelper;

class CampaignShortlistController extends Controller
{
    use CampaignSessionHelper;

    public function index(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        if (!$campaign->canEdit()) {
            abort(404);
        }

        if ($campaign && session('campaign.campaign_id') != $campaign->id) {
            $this->loadCampaignToSession($campaign);
        }

        return redirect('campaigns/shortlist');
    }

    public function update(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        if ($this->sessionLoadedCampaignDifferentFrom($campaign)) {
            abort(403);
        }

        if ($failedValidator = Campaign::validateCampaignFails(request())) {
            return redirect('campaigns/shortlist')
                ->withInput()
                ->withErrors($failedValidator);
        }

        $this->updateSessionDeliverables(request());

        $this->updateCampaignFromSession($campaign);

        $redirectMessage = 'The data has been saved.';

        if (request('with_download') === '1') {
            GenerateDeckPptHelper::createCampaignDeckReport($campaign->records->pluck('id')->toArray(), $campaign);
            GenerateDeckPptHelper::createRecordsExcel($campaign->records->pluck('id')->toArray(), $campaign->id);
            $redirectMessage .= '<br />' .
            '<strong>Generating reports.</strong> An email will be send to you once your reports are ready to be downloaded.';
        }

        return redirect(url('/campaigns/shortlist'))
            ->with('message', $redirectMessage);
    }

    protected function sessionLoadedCampaignDifferentFrom($campaign)
    {
        return session('campaign.campaign_id') !== $campaign->id;
    }
}
