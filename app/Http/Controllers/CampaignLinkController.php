<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Deliverable;
use App\Models\Link;
use Illuminate\Http\Request;
use Validator;

class CampaignLinkController extends Controller
{
    public function edit(Campaign $campaign)
    {
        if (!$campaign->canAccessLinks()) {
            abort(404);
        }

        $this->data['title'] = '[Campaign #' . $campaign->id . '] ' . $campaign->name;
        $this->data['campaign'] = $campaign;

        return view('campaign.report', $this->data);
    }

    public function update(Request $request, Campaign $campaign)
    {
        if (!$campaign->canAccessLinks()) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'url' => 'array',
            'url.*' => 'array',
            'url.*.*' => 'nullable|url',
            'added_url' => 'nullable|array',
            'added_url.*' => 'array',
            'added_url.*.*' => 'nullable|url',
        ], [
            'url.*.*' => 'Url format is invalid.',
            'added_url.*.*' => 'Url format is invalid.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        foreach ($request->url as $deliverable_id => $links) {
            if ($deliverable = Deliverable::find($deliverable_id)) {
                $deliverable->links()->forceDelete();
                foreach ($links as $link) {
                    if ($link) {
                        Link::create([
                            'deliverable_id' => $deliverable->id,
                            'url' => $link,
                        ]);
                    }
                }
            }
        }

        $campaign->valueAddedPosts()->forceDelete();

        if ($request->added_url) {
            foreach ($request->added_url as $record_id => $links) {
                for ($i = 0; $i < count($links); $i++) {
                    if ($links[$i]) {
                        $deliverable = Deliverable::create([
                            'campaign_id' => $campaign->id,
                            'record_id' => $record_id,
                            'platform' => ucwords(explode('_', $request->added_deliverables[$record_id][$i])[0]),
                            'type' => ucwords(explode('_', $request->added_deliverables[$record_id][$i])[1]),
                            'billing_type' => 'value_added',
                            'quantity' => 1,
                        ]);
                        Link::create([
                            'deliverable_id' => $deliverable->id,
                            'url' => $links[$i],
                        ]);
                    }
                }
            }
        }

        return redirect()->back()->with('message', 'The data has been saved.');
    }
}
