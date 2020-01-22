<?php

namespace App\Models;

use App\Models\Model;
use Storage;

class Report extends Model
{
    protected $fillable = [
        'user_id', 'records', 'iso_639_1', 'iso_3166_2', 'campaign_id', 'file', 'config'
    ];

    protected $hidden = [
        'deleted_at',
    ];

    protected $dates = [
        'downloaded_at',
        'generated_at',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function campaign()
    {
        return $this->belongsTo('App\Models\Campaign');
    }

    public function getRelativeFileUrlAttribute()
    {
        if (!$this->campaign) {
            return 'reports/'.$this->file;
        } else {
            return 'reports/'.$this->id.'/'.$this->file;
        }
    }

    public function getFileUrlAttribute($value)
    {
        return Storage::temporaryUrl($this->relative_file_url, now()->addMinutes(5));
    }

    public function getLanguageAttribute()
    {
        return $this['iso_3166_2'] ? $this['iso_639_1'].'_'.$this['iso_3166_2'] : $this['iso_639_1'];
    }
}
