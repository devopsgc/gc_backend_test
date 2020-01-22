<?php

namespace App\Http\Controllers;

use App\Models\Campaign;

class CampaignLinkTrackingController extends Controller
{
    public function index(Campaign $campaign)
    {
        $this->data['campaign'] = $campaign;

        $this->authorize('view', $this->data['campaign']);

        $this->data['campaign']->load(['deliverables.links.deliverable']);

        return view('campaign.linkIndex', $this->data);
    }
}
