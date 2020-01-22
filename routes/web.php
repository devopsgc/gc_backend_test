<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register' => false]);
Route::get('connect', 'Auth\FacebookController@index');
Route::get('connect/facebook', 'Auth\FacebookController@connect');
Route::get('connect/facebook/callback', 'Auth\FacebookController@callback');
Route::get('privacy-policy', 'PageController@privacy');
Route::get('terms-and-conditions', 'PageController@terms');

Route::group(['middleware' => ['auth', 'status']], function () {
    Route::get('report/poc', 'ReportController@index');
    Route::get('reports/{id}', 'ReportController@show');
    Route::get('reports/{id}', 'ReportController@show');

    Route::post('users/{user}/suspend', 'UserController@suspend');
    Route::post('users/{user}/restore', 'UserController@restore');
    Route::resource('users', 'UserController');

    Route::get('campaigns/{record}/deliverables', 'CampaignDeliverableController@edit');
    Route::put('campaigns/{record}/deliverables', 'CampaignDeliverableController@update');
    if (config('featureToggle.campaignReport')) {
        Route::post('campaigns/{campaign}/links', 'CampaignLinkController@update');
        Route::get('campaigns/{campaign}/links', 'CampaignLinkController@edit');
        Route::get('campaigns/{campaign}/links-tracking', 'CampaignLinkTrackingController@index');
    }
    Route::post('campaigns/shortlist/exit', 'CampaignShortlistExitController@store');
    Route::post('campaigns/shortlist/remove-selection', 'CampaignShortlistRemoveSelectionController@store');
    Route::post('campaigns/shortlist', 'ShortlistController@store');
    Route::get('campaigns/shortlist', 'ShortlistController@index');
    Route::get('campaigns/{campaign}/shortlist', 'CampaignShortlistController@index');
    Route::put('campaigns/{campaign}/shortlist', 'CampaignShortlistController@update');
    Route::post('campaigns/{campaign}/ppt', 'CampaignPowerPointDownloadController@store');
    Route::post('campaigns/{campaign}/xls', 'CampaignExcelDownloadController@store');
    Route::post('campaigns/{campaign}/status', 'CampaignStatusController@store');
    Route::delete('campaigns/{campaign}/status', 'CampaignStatusController@destroy');
    Route::post('campaigns/{campaign}/update-name', 'CampaignUpdateNameController@store');
    Route::resource('campaigns', 'CampaignController')->except(['create', 'destroy']);

    /*
        TODO: temporary to patch the bug where initialy it was a GET request to recordController@index and
        query string was too long, converted to a post temporarily
    */
    Route::post('records-shortlist', 'RecordController@index');

    Route::get('records/{id}/socapi', 'RecordController@socapi');
    Route::resource('records', 'RecordController');

    Route::get('instagram', 'InstagramController@edit');
    Route::post('instagram', 'InstagramController@update');

    Route::get('metrics', 'MetricsController@index');
    Route::get('', 'DashboardController@index');

    Route::get('social-data/dictionaries', 'SocialDataDictionariesController@index');
});
