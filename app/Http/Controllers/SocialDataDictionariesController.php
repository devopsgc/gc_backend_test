<?php

namespace App\Http\Controllers;

class SocialDataDictionariesController extends Controller
{
    public function index()
    {
        $data = json_decode(file_get_contents(resource_path() . '/files/socialDataInterestsBrands.json'));

        $interests = collect($data->data->interests)->where('deprecated', false);
        $brands = collect($data->data->brands)->where('deprecated', false);

        $data = json_decode(file_get_contents(resource_path() . '/files/socialDataLanguage.json'));
        $languages = $data->data;

        $data = json_decode(file_get_contents(resource_path() . '/files/socialDataCountries.json'));
        $countries = $this->formatCountries($data->data);

        return view('socialData.dictionaries', compact(['interests', 'brands', 'languages', 'countries']));
    }

    protected function formatCountries($data)
    {
        $countries = collect($data)->filter(function ($item, $key) {
            if (in_array('country', $item->type)) {
                return ['id' => $item->id, 'name' => $item->name, 'title' => $item->title, 'code' => $item->country->code, 'cities' => []];
            }
        });

        $cities = collect($data)->filter(function ($item, $key) {
            if (in_array('city', $item->type)) {
                return ['id' => $item->id, 'name' => $item->name];
            }
        });

        foreach ($countries as $country) {
            $country->cities = $cities->where('country.id', $country->id);
        }

        return $countries;
    }
}
