<?php

namespace App\Http\Controllers;

use App\Traits\CampaignSessionHelper;

class CampaignShortlistRemoveSelectionController extends Controller
{
    use CampaignSessionHelper;

    public function store()
    {
        if (request('all')) {
            return $this->removeAllSelections();
        } elseif (request('record_id')) {
            $this->removeSelection(request('record_id'));

            return [
                'message' => 'success',
                'badge_count' => is_array(session('selected')) && sizeof(session('selected')) ? sizeof(session('selected')) : 0
            ];
        }
    }
}
