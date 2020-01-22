<?php

namespace App\Helpers;

use App\Models\Record;
use Carbon\Carbon;

class RecordQueryHelper
{
    public static function generateQuery($request, $countries)
    {
        $query = Record::query();

        $query->orderBy($request->order ? $request->order : 'created_at', 'desc');
        if ($request->order === 'instagram_followers') $query->whereNotNull('instagram_followers');
        if ($request->order === 'facebook_followers') $query->whereNotNull('facebook_followers');
        if ($request->order === 'youtube_subscribers') $query->whereNotNull('youtube_subscribers');
        if ($request->order === 'twitter_followers') $query->whereNotNull('twitter_followers');
        if ($request->order === 'tiktok_followers') $query->whereNotNull('tiktok_followers');
        if ($request->order === 'weibo_followers') $query->whereNotNull('weibo_followers');
        if ($request->order === 'xiaohongshu_followers') $query->whereNotNull('xiaohongshu_followers');
        if ($request->q) $query->where(function ($query) use ($request) {
            $query->where('name', 'LIKE', '%'.$request->q.'%');
            $query->orWhere('second_name', 'LIKE', '%'.$request->q.'%');
            $query->orWhere('facebook_id', 'LIKE', '%'.$request->q.'%');
            $query->orWhere('instagram_id', 'LIKE', '%'.$request->q.'%');
            $query->orWhere('twitter_id', 'LIKE', '%'.$request->q.'%');
            $query->orWhere('tiktok_id', 'LIKE', '%'.$request->q.'%'); });
        if ($request->country) $query->where(function ($query) use ($request, $countries) {
            foreach ($request->country as $country) if ( ! $countries || in_array($country, $countries))
                $query->orWhere('country_code', '=', $country); });
        else $query->where(function ($query) use ($countries) {
            foreach ($countries as $country) $query->orWhere('country_code', '=', $country); });
        if ($request->gender) $query->where(function ($query) use ($request) {
            foreach ($request->gender as $gender) $query->orWhere('gender', '=', $gender); });
        if ($request->platform && $request->disabled) $query->where(function ($query) use ($request) {
            foreach ($request->platform as $platform)
                $query->orWhere(function ($query) use ($request, $platform) {
                    $query->whereNotNull($platform.'_id');
                    $query->whereNotNull($platform.'_update_disabled_at');
                    if ($platform == 'facebook') $query->whereNull($platform.'_user_page');
                });
            });
        elseif ($request->platform) $query->where(function ($query) use ($request) {
            foreach ($request->platform as $platform) $query->orWhereNotNull($platform.($platform == 'blog' ? '_url' : '_id')); });
        if ($request->campaigns) $query->where(function ($query) use ($request) {
            foreach ($request->campaigns as $campaign) $query->orWhere('campaigns', 'like', '%'.$campaign.'%'); });
        if ($request->affiliations) $query->where(function ($query) use ($request) {
            foreach ($request->affiliations as $affiliation) {
                $query->orWhereHas('affiliationTags', function($query2) use ($affiliation) {
                    $query2->where('name', $affiliation);
                });
            }
        });
        if ($request->professions) $query->where(function ($query) use ($request) {
            foreach ($request->professions as $profession) {
                $query->orWhereHas('professionsCore', function($query2) use ($profession) {
                    $query2->where('name', $profession);
                });
            }
        });
        if ($request->interests) $query->where(function ($query) use ($request) {
            foreach ($request->interests as $interest) {
                $query->orWhereHas('interestsCore', function($query2) use ($interest) {
                    $query2->where('name', $interest);
                });
            }
        });
        if ($request->age_min) $query->where(function ($query) use ($request) {
            $query->orWhere('date_of_birth', '<=', Carbon::now()->subYears($request->age_min)); });
        if ($request->age_max) $query->where(function ($query) use ($request) {
            $query->orWhere('date_of_birth', '>=', Carbon::now()->subYears($request->age_max+1)); });
        if ($request->facebook_min) $query->where(function ($query) use ($request) {
            $query->orWhere('facebook_followers', '>=', $request->facebook_min); });
        if ($request->facebook_max) $query->where(function ($query) use ($request) {
            $query->orWhere('facebook_followers', '<=', $request->facebook_max); });
        if ($request->instagram_min) $query->where(function ($query) use ($request) {
            $query->orWhere('instagram_followers', '>=', $request->instagram_min); });
        if ($request->instagram_max) $query->where(function ($query) use ($request) {
            $query->orWhere('instagram_followers', '<=', $request->instagram_max); });
        if ($request->youtube_min) $query->where(function ($query) use ($request) {
            $query->orWhere('youtube_subscribers', '>=', $request->youtube_min); });
        if ($request->youtube_max) $query->where(function ($query) use ($request) {
            $query->orWhere('youtube_subscribers', '<=', $request->youtube_max); });
        if ($request->weibo_min) $query->where(function ($query) use ($request) {
            $query->orWhere('weibo_followers', '>=', $request->weibo_min); });
        if ($request->weibo_max) $query->where(function ($query) use ($request) {
            $query->orWhere('weibo_followers', '<=', $request->weibo_max); });
        if ($request->xiaohongshu_min) $query->where(function ($query) use ($request) {
            $query->orWhere('xiaohongshu_followers', '>=', $request->xiaohongshu_min); });
        if ($request->xiaohongshu_max) $query->where(function ($query) use ($request) {
            $query->orWhere('xiaohongshu_followers', '<=', $request->xiaohongshu_max); });
        if ($request->miaopai_min) $query->where(function ($query) use ($request) {
            $query->orWhere('miaopai_followers', '>=', $request->miaopai_min); });
        if ($request->miaopai_max) $query->where(function ($query) use ($request) {
            $query->orWhere('miaopai_followers', '<=', $request->miaopai_max); });
        if ($request->yizhibo_min) $query->where(function ($query) use ($request) {
            $query->orWhere('yizhibo_followers', '>=', $request->yizhibo_min); });
        if ($request->yizhibo_max) $query->where(function ($query) use ($request) {
            $query->orWhere('yizhibo_followers', '<=', $request->yizhibo_max); });

        return $query;
    }
}
