<?php

namespace App\Http\Controllers;

use App\Models\Campaign;

class CampaignUpdateNameController extends Controller
{
    public function store(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        if (!$campaign->canEdit()) {
            abort(404);
        }

        $validated = $this->validate(request(), [
            'name' => 'required|string',
        ]);

        $campaign->update($validated);

        return redirect('/campaigns/shortlist');
    }
}
