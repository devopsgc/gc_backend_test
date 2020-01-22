<?php

namespace App\Models\Data;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ModelData extends Eloquent
{
    protected $connection = 'mongodb';
}
