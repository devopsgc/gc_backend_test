<?php

namespace App\Models;

use App\Models\Model;

class Tag extends Model
{
    protected $fillable = ['name', 'description', 'type'];

    public function scopeAffiliationTags($query)
    {
        return $query->where('type', 'affiliation');
    }

    public function scopeInterestTags($query)
    {
        return $query->where('type', 'interest_core');
    }

    public function scopeProfessionsTags($query)
    {
        return $query->where('type', 'profession_core');
    }

    public static function getAllInterests()
    {
        $interests = [];
        foreach (Tag::InterestTags()->get() as $interest) {
            $interests[] = [
                "value" => $interest->name,
                "text" => $interest->name
            ];
        }
        return $interests;
    }

    public static function getAllProfessions()
    {
        $professions = [];
        foreach (Tag::ProfessionsTags()->get() as $profession) {
            $professions[] = ["value" => $profession->name, "text" => $profession->name];
        }
        return $professions;
    }

    public static function getAllAffiliations()
    {
        $affiliations = [];
        foreach (Tag::AffiliationTags()->get() as $affiliation) {
            $affiliations[] = ["value" => $affiliation->name, "text" => $affiliation->name];
        }
        return $affiliations;
    }
}
