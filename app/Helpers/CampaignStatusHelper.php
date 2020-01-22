<?php

namespace App\Helpers;

use App\Models\Campaign;

class CampaignStatusHelper
{
    public static function getBtnStyle($status)
    {
        switch ($status) {
            case Campaign::STATUS_DRAFT:
                return 'warning';
            case Campaign::STATUS_ACCEPTED:
            case Campaign::STATUS_COMPLETED:
                return 'success';
            case Campaign::STATUS_REJECTED:
                return 'danger';
            case Campaign::STATUS_CANCELLED:
                return 'default';
            default:
                return 'default';
        }
    }
}
