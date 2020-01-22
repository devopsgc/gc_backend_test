<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class API
{
    public static function callSocialApi($instagram_id)
    {
        $client = new Client;
        try
        {
            $response = $client->post(config('services.social_api.url').'reports/new', [
                'form_params' => [
                    'api_token' => config('services.social_api.token'),
                    'url' => $instagram_id,
                ],
            ]);
            return json_decode((string) $response->getBody(), true);
        }
        catch (\Exception $e)
        {
            return $e->getResponse()->getBody(true);
        }
    }
}