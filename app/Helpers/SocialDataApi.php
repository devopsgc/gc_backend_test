<?php

namespace App\Helpers;

use App\Models\Data\SocialDataReport;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Log;
use Exception;

class SocialDataApi
{
    public function generateReportDryRun($country_id)
    {
        try {
            $guzzle = new Client;

            $body = SocialDataReport::getReportGenerationFiltersDryRun($country_id);

            $response = $guzzle->request('POST', config('services.socapi.api_url') . 'exports/new?api_token=' . config('services.socapi.api_token'), [
                'json' => $body,
            ]);

            $data = (string) $response->getBody();

            return $data;
        } catch (Exception $e) {
            Log::error('SocialDataHelper generateReport');
            Log::error($e->getMessage());
        }

        return null;
    }

    public function generateReport($country_id)
    {
        if (env('APP_ENV') === 'testing') {
            return file_get_contents(storage_path() . '/test/sample_' . $country_id . '_exports-new.json');
        }

        try {
            $guzzle = new Client;

            $body = SocialDataReport::getReportGenerationFilters($country_id);

            $response = $guzzle->request('POST', config('services.socapi.api_url') . 'exports/new?api_token=' . config('services.socapi.api_token'), [
                'json' => $body,
            ]);

            $data = (string) $response->getBody();

            return $data;
        } catch (Exception $e) {
            Log::warning('SocialDataHelper generateReport fail for country: ' . SocialDataReport::getCountryName($country_id));
            Log::warning($e->getMessage());
        }

        return null;
    }

    public function downloadReport($report_id)
    {
        if (env('APP_ENV') === 'testing') {
            return gzdecode(file_get_contents(storage_path() . '/test/sample_exports-' . SocialDataReport::SD_COUNTRY_ID . '-files-json.gz'));
        }

        try {
            $guzzle = new Client;
            $response = $guzzle->get(config('services.socapi.api_url') . 'exports/' . $report_id . '/files/json?api_token=' . config('services.socapi.api_token'));
            $data = $response->getBody();
            return gzdecode($data);
        } catch (Exception $e) {
            Log::error('SocialDataHelper downloadReport');
            Log::error($e->getMessage());
        }

        return null;
    }

    public function getJsonFromApi($record)
    {
        $client = new Client;

        try {
            $response = $client->post(config('services.socapi.api_url') . 'reports/new', [
                'form_params' => [
                    'api_token' => config('services.socapi.api_token'),
                    'url' => $record->instagram_id,
                ],
            ]);

            return (string) $response->getBody();
        } catch (Exception $e) {
            Log::error('SocialDataHelper Record ID: ' . $record->id);
            Log::error($e->getMessage());

            return '';
        }
    }

    public function update($record)
    {
        if (!$record || !$record->instagram_id) {
            return;
        }

        Log::info('SocialDataHelper Updating Record: ' . $record->id);

        $socapiJsonString = $this->getJsonFromApi($record);

        if ($socapiJsonString) {
            Log::info('SocialDataHelper Data found for Record : ' . $record->id);
            if ($record->instagramSocapiData) {
                $record->instagramSocapiData->delete();
            }
            $record->instagramSocapiData()->create(json_decode($socapiJsonString, true));
            $record->instagram_socapi_updated_at = Carbon::now();
            $record->save();
        }

        Log::info('SocialDataHelper Finished Record: ' . $record->id);
    }
}
