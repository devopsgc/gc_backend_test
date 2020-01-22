<?php

namespace App\Models;

use App\Models\Model;

class Description extends Model
{
    protected $fillable = [
        'record_id', 'iso_639_1', 'iso_3166_2', 'description',
    ];

    public function record()
    {
        return $this->belongsTo('App\Models\Record');
    }
}
