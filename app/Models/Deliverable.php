<?php

namespace App\Models;

use App\Models\Model;

class Deliverable extends Model
{
    const PLATFORMS = ['Facebook', 'Instagram', 'YouTube', 'Twitter'];
    const TYPES = ['Post', 'Video', 'Story'];

    protected $fillable = [
        'campaign_id',
        'record_id',
        'platform',
        'type',
        'billing_type',
        'quantity',
        'price',
        'url',
    ];

    public function campaign()
    {
        return $this->belongsTo('App\Models\Campaign');
    }

    public function record()
    {
        return $this->belongsTo('App\Models\Record');
    }

    public function links()
    {
        return $this->hasMany('App\Models\Link');
    }
}
