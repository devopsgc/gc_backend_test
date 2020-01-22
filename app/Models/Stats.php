<?php

namespace App\Models;

use App\Models\Model;

class Stats extends Model
{
    protected $fillable = [
        'record_id', 'key', 'value',
    ];

    public function record()
    {
        return $this->belongsTo('App\Models\Record');
    }
}
