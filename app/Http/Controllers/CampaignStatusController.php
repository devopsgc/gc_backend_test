<?php

namespace App\Http\Controllers;

use App\Models\Campaign;

class CampaignStatusController extends Controller
{
    public function store(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $this->validate(request(), ['status' => [
            'required',
            'in:' . implode(',', $campaign->getValidNextStatuses()),
        ]]);

        $campaign->status = request('status');
        $campaign->save();

        return 1;
    }

    public function destroy(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        if (in_array(Campaign::STATUS_DELETED, $campaign->getValidNextStatuses())) {
            $campaign->delete();
        }

        return redirect(request('redirect_url') ?: 'campaigns?filter_tab=campaigns');
    }
}
