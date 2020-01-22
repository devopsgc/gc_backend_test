<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Language;
use App\Models\Record;
use App\Traits\CampaignSessionHelper;

class ShortlistController extends Controller
{
    use CampaignSessionHelper;

    public function index()
    {
        $selected = session('selected') ? session('selected') : [];

        $this->data['records'] = Record::whereIn('id', $selected)->get();
        $this->data['title'] = 'Shortlist';
        $this->data['countries'] = Country::getAllEnabledCountries()->sortBy('currency_code');
        $this->data['languages'] = Language::get();

        return view('campaign.create', $this->data);
    }

    public function store()
    {
        $this->addSelection(request('record_id'));

        return [
            'message' => 'success',
            'badge_count' => is_array(session('selected')) && sizeof(session('selected')) ? sizeof(session('selected')) : 0
        ];
    }
}
