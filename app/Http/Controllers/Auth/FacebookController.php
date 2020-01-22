<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Record;
use Auth;
use Carbon\Carbon;
use Facebook;

class FacebookController extends Controller
{
    public function index()
    {
        return view('connectAccount');
    }

    public function connect()
    {
        return redirect(Facebook::getLoginUrl(['email', 'read_insights', 'manage_pages', 'instagram_basic', 'instagram_manage_insights']));
    }

    public function callback()
    {
        try {
            $token = Facebook::getAccessTokenFromRedirect();
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            abort(400, $e->getMessage());
        }

        if (!$token) {
            $helper = Facebook::getRedirectLoginHelper();
            if (!$helper->getError()) {
                abort(403, 'Unauthorized action.');
            }
            dd(
                $helper->getError(),
                $helper->getErrorCode(),
                $helper->getErrorReason(),
                $helper->getErrorDescription()
            );
        }

        Facebook::setDefaultAccessToken($token);

        try {

            $pageQuery = Facebook::get('/me/accounts?fields=username,access_token');
            $pages = $pageQuery->getGraphEdge();
            $hasAssociatedRecord = false;
            $connectedPage = '';

            foreach ($pages as $page) {
                $facebook_page_username = $page->getField('username');
                if ($facebook_page_username) {
                    $records = Record::where('facebook_id', $facebook_page_username)->get();
                    foreach ($records as $record) {
                        $record->facebook_page_access_token = $page->getField('access_token');
                        $record->facebook_page_id = $page->getField('id');
                        $record->facebook_page_access_token_updated_at = Carbon::now();
                        $record->save();
                        $connectedPage = $facebook_page_username;
                        $hasAssociatedRecord = true;
                    }
                }
            }

            if ($hasAssociatedRecord) {
                return redirect('connect')->with('message', 'Successfully connected '.$connectedPage.' Facebook page!');
            }

            return redirect('connect')->with('warning', 'We cant find your account.');
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            abort(400, $e->getMessage());
        }
    }
}
