<?php

namespace App\Models;

use App\Models\Model;

class Instagram extends Model
{
    protected $table = 'instagram';
    protected $fillable = [
        'session_id', 'query_hash',
    ];
}
