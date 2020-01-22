<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CountriesSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return  void
     */
    public function run()
    {
        // Empty the countries table
        DB::table(\Config::get('countries.table_name'))->delete();

        // Get all of the countries
        $countries = Countries::getList();
        foreach ($countries as $countryId => $country)
        {
            if (isset($country['name']))
            {
                if ($country['name'] == 'Korea, Republic of') $country['name'] = 'Korea';
                if ($country['name'] == 'Taiwan, Province of China') $country['name'] = 'Taiwan';
                if ($country['name'] == 'Viet Nam') $country['name'] = 'Vietnam';
            }

            DB::table(\Config::get('countries.table_name'))->insert(array(
                'id' => $countryId,
                'capital' => ((isset($country['capital'])) ? $country['capital'] : null),
                'citizenship' => ((isset($country['citizenship'])) ? $country['citizenship'] : null),
                'country_code' => $country['country-code'],
                'currency' => ((isset($country['currency'])) ? $country['currency'] : null),
                'currency_code' => ((isset($country['currency_code'])) ? $country['currency_code'] : null),
                'currency_sub_unit' => ((isset($country['currency_sub_unit'])) ? $country['currency_sub_unit'] : null),
                'currency_decimals' => ((isset($country['currency_decimals'])) ? $country['currency_decimals'] : null),
                'full_name' => ((isset($country['full_name'])) ? $country['full_name'] : null),
                'iso_3166_2' => $country['iso_3166_2'],
                'iso_3166_3' => $country['iso_3166_3'],
                'name' => $country['name'],
                'region_code' => $country['region-code'],
                'sub_region_code' => $country['sub-region-code'],
                'eea' => (bool)$country['eea'],
                'calling_code' => $country['calling_code'],
                'currency_symbol' => ((isset($country['currency_symbol'])) ? $country['currency_symbol'] : null),
                'flag' =>((isset($country['flag'])) ? $country['flag'] : null),
                'enabled_at' => in_array($country['iso_3166_2'], [
                    'SG', 'MY', 'TH', 'JP', 'PH', 'KR', 'US', 'AU', 'VN', 'HK', 'TW', 'ID'
                ]) ? Carbon::now() : null,
            ));
        }
    }
}
