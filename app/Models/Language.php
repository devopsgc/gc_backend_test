<?php

namespace App\Models;

use App\Models\Model;

class Language extends Model
{
    protected $fillable = [
        'iso_639_1', 'iso_639_2', 'name', 'native',
    ];

    public function record()
    {
        return $this->belongsTo('App\Models\Record');
    }
}
