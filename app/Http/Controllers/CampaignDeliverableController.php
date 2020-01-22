<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;

class CampaignDeliverableController extends Controller
{
    public function update(Request $request, Record $record)
    {
        if (count($request->quantity) > 0) {
            $record_deliverables = [];
            for ($i = 0; $i < count($request->quantity); $i++) {
                $record_deliverable = [
                    'quantity' => $request->quantity[$i],
                    'platform' => ucwords(explode('_', $request->deliverable[$i])[0]),
                    'type' => ucwords(explode('_', $request->deliverable[$i])[1]),
                    'price' => $request->price[$i],
                ];
                array_push($record_deliverables, $record_deliverable);
            }
            session(['campaign.selected.' . $record->id . '.deliverables' => $record_deliverables]);
        }
        if (session()->has('campaign.campaign_id')) {
            return redirect('campaigns/' . session('campaign.campaign_id') . '/shortlist');
        }
        return redirect('campaigns/shortlist');
    }

    public function edit(Record $record)
    {
        $this->data['record'] = $record;
        $this->data['deliverables'] = session('campaign.selected.' . $record->id . '.deliverables', []);

        return view('deliverableEdit', $this->data);
    }
}
