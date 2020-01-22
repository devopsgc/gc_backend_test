<?php

namespace App\Models\Data;

use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class InstagramSocialData extends ModelData
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $guarded = [];

    protected $table = 'instagram_social_datas';

    public function record()
    {
        return $this->belongsTo('App\Models\Record');
    }
}
