<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Storage;

class SocapiController extends Controller
{
    public function index(Request $request)
    {
        /* See: https://docs.socapi.icu/#section/Error-codes
            invalid_api_token
            account_not_found
            empty_audience_data
            empty_audience
            retry_later
            no_tokens_remaining
            no_quota_remaining
            internal_server_error
            token_is_disabled
            subscription_expired
            not_authenticated
            not_found
        */
        if ($request->api_token != config('services.socapi.api_token'))
        {
            return response()->json([
                'success' => false,
                'error' => 'invalid_api_token',
                'error_message' => 'You need API Token.'
            ], 403);
        }
        if ( ! Storage::disk('local')->has('socapi/'.$request->url.'.json'))
        {
            return response()->json([
                'success' => false,
                'error' => 'account_not_found',
                'error_message' => 'The instagram account was not found.'
            ], 403);
        }
        return response()->json(json_decode(Storage::disk('local')->get('socapi/'.$request->url.'.json')));
    }
}
