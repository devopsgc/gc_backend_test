<?php

namespace App\Traits;

use App\Models\Campaign;
use App\Models\Deliverable;
use App\Models\Record;

trait CampaignSessionHelper
{
    protected function updateSessionDeliverables($request)
    {
        $package_prices = $request->package_price;
        $currency_code = $request->currency_code;
        $total_price = $request->total_price;
        $selected = session('selected');
        $deliverables = session('campaign.selected') ?: [];
        foreach($selected as $recordId)
        {
            $package_price = $package_prices[$recordId];
            $deliverables[$recordId]['package_price'] = $package_price;
        }
        session(['campaign.selected' => $deliverables]);
        session(['campaign.currency_code' => $currency_code]);
        session(['campaign.total_price' => $total_price]);
    }

    protected function updateCampaignFromSession(Campaign $campaign)
    {
        $selected = session('selected');
        Record::whereIn('id', $selected)->get();
        $deliverables = session('campaign.selected');
        $currency_code = session('campaign.currency_code', 'SGD');
        $total_price = session('campaign.total_price', 0);

        $this->saveCampaignDeliverables($campaign, $deliverables, $currency_code, $total_price);
    }

    protected function saveCampaignDeliverables(Campaign $campaign, $deliverables, $currency_code, $total_price)
    {
        $campaign->currency_code = $currency_code;
        $campaign->total_price = $total_price;
        $campaign->save();
        $campaign->records()->detach();
        $campaign->deliverables()->forceDelete();

        foreach ($deliverables as $recordId => $recordDeliverables) {
            $campaign->records()->attach($recordId, ['package_price' => $recordDeliverables['package_price']]);
            if (isset($recordDeliverables['deliverables'])) {
                foreach ($recordDeliverables['deliverables'] as $deliverable) {
                    Deliverable::create([
                        'campaign_id' => $campaign->id,
                        'record_id' => $recordId,
                        'platform' => $deliverable['platform'],
                        'type' => $deliverable['type'],
                        'billing_type' => 'deliverable',
                        'quantity' => $deliverable['quantity'],
                        'price' => $deliverable['price'],
                    ]);
                }
            }
        }
    }

    protected function addSelection($record_id)
    {
        $selected = session('selected');
        if ( ! is_array($selected)) $selected = [];
        $key = array_search($record_id, $selected);
        if ($key === false) array_push($selected, $record_id);
        session(['selected' => $selected]);
    }

    protected function removeSelection($record_id)
    {
        $selected = session('selected');
        if ( ! is_array($selected)) $selected = [];
        $key = array_search($record_id, $selected);
        if ($key !== false) {
            $deliverables = session('campaign.selected');
            if ($deliverables && array_key_exists($selected[$key], $deliverables)) {
                unset($deliverables[$selected[$key]]);
                session(['campaign.selected' => $deliverables]);
            }
            unset($selected[$key]);
        };
        session(['selected' => array_values($selected)]);
    }

    protected function removeAllSelections()
    {
        session(['campaign.selected' => []]);
        session(['selected' => []]);
        return redirect()->back();
    }

    protected function exitCampaign($request)
    {
        $this->clearCampaignInSession();
        $this->removeAllSelections();
    }

    protected function clearCampaignInSession()
    {
        session(['campaign.campaign_id' => null]);
        session(['campaign.currency_code' => 'SGD']);
        session(['campaign.total_price' => null]);
    }

    private function loadCampaignToSession(Campaign $campaign)
    {
        $deliverables = [];
        foreach ($campaign->records as $record) {
            $record_deliverables = [];

            foreach ($campaign->deliverables as $deliverable) {
                if ($deliverable->record_id == $record->id) {
                    $record_deliverable = [
                        'quantity' => $deliverable->quantity,
                        'platform' => $deliverable->platform,
                        'type' => $deliverable->type,
                        'price' => $deliverable->price,
                    ];
                    array_push($record_deliverables, $record_deliverable);
                }
            }
            $deliverables[$record->id]['deliverables'] = $record_deliverables;
            $deliverables[$record->id]['package_price'] = $record->pivot->package_price;
        }
        session(['selected' => $campaign->records->pluck('id')->toArray()]);
        session(['campaign.selected' => $deliverables]);
        session(['campaign.currency_code' => $campaign->currency_code]);
        session(['campaign.total_price' => $campaign->total_price]);
        session(['campaign.campaign_id' => $campaign->id]);
    }
}