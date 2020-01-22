<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Record;
use Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $countries = Auth::user()->countries->pluck('iso_3166_2')->toArray();
        $this->data['countries'] = Country::getAllEnabledCountries($countries);

        foreach ($this->data['countries'] as &$country) {
            $country->influences = Record::where('country_code', '=', $country->iso_3166_2)->count();
            $country->followers = 0;
            $country->followers += Record::where('country_code', '=', $country->iso_3166_2)->sum('facebook_followers');
            $country->followers += Record::where('country_code', '=', $country->iso_3166_2)->sum('instagram_followers');
            $country->followers += Record::where('country_code', '=', $country->iso_3166_2)->sum('youtube_subscribers');
            $country->followers += Record::where('country_code', '=', $country->iso_3166_2)->sum('twitter_followers');
            $country->followers += Record::where('country_code', '=', $country->iso_3166_2)->sum('weibo_followers');
            $country->followers += Record::where('country_code', '=', $country->iso_3166_2)->sum('xiaohongshu_followers');
            $country->followers += Record::where('country_code', '=', $country->iso_3166_2)->sum('miaopai_followers');
            $country->followers += Record::where('country_code', '=', $country->iso_3166_2)->sum('yizhibo_followers');
        }

        $this->data['title'] = 'Dashboard';

        return view('dashboard', $this->data);
    }
}
