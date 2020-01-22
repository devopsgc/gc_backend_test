<?php

namespace App\Http\Controllers;

use Auth;
use App\Helpers\Metrics;
use App\Models\Country;
use Illuminate\Http\Request;

class MetricsController extends Controller
{
    public function index(Request $request)
    {
        $country = $request->country_id ? Country::findOrFail($request->country_id) : null;

        $this->data['metrics'] = Metrics::generateMetrics($country);
        $this->data['country'] = $country;

        $countries = Auth::user()->countries->pluck('iso_3166_2')->toArray();
        $this->data['countriesFilter'] = Country::getAllEnabledCountries($countries)->pluck('name', 'id')->toArray();
        $this->data['countriesFilter'] = ['' => 'All Countries'] + $this->data['countriesFilter'];

        return view('metrics', $this->data);
    }
}
