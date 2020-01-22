<?php

namespace App\Models;

class Country extends Model
{
    public function countries()
    {
        return $this->belongsToMany('App\Models\User');
    }

    public static function getAllEnabledCountries($countries = null)
    {
        $recordCountries = Record::distinct()->get(['country_code']);
        return Country::whereIn('iso_3166_2', $recordCountries)->when($countries, function ($query) use ($countries) {
            return $query->whereIn('iso_3166_2', $countries);
        })->get();
    }
}
