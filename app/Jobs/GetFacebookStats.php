<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Facebook;
use App\Models\Record;
use App\Models\Stats;
use Carbon\Carbon;
use Storage;
use Image;

class GetFacebookStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fb;
    protected $record;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->fb = new Facebook([
            'app_id' => config('services.facebook.app_id'),
            'app_secret' => config('services.facebook.app_secret'),
            'default_graph_version' => config('services.facebook.graph_version')
        ]);
        $this->fb->setDefaultAccessToken($this->fb->getApp()->getAccessToken());
        $this->record = Record::find($id);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fields = 'id,name,category,location,fan_count,picture.width(800)';

        // Try to get page data from Facebook API using the Facebook ID
        // $this->record->facebook_id = '1889177611166465';
        // $this->record->facebook_id = 'rxemedium';
        try
        {
            $response = $this->fb->get($this->record->facebook_id.'?fields='.$fields);
            $this->save($response);
        }
        catch(FacebookResponseException $e)
        {
            // If that fails because the facebook ID is invalid
            // retry with last numeric segment of the facebook ID
            // $this->record->facebook_id = 'Singapore-Codeigniter-User-Group-390282287737429';
            // $this->record->facebook_id = 'Monica-Ivena-370532923059542';
            if (strpos($e->getMessage(), 'Some of the aliases you requested do not exist') !== false)
            {
                $tokens = explode('-', $this->record->facebook_id);
                $this->record->facebook_id = end($tokens);
                if (is_numeric($this->record->facebook_id))
                {
                    $response = $this->fb->get($this->record->facebook_id.'?fields='.$fields);
                    $this->save($response);
                    return;
                }
                $this->record->facebook_update_disabled_at = Carbon::now();
                $this->record->save();
                return;
            }
            // If that fails and because it's a facebook user page
            // facebook does not allow us to get data from user pages
            // i.e. pages which you must "Add As Friend"
            elseif (strpos($e->getMessage(), 'Cannot query users by their username') !== false ||
                strpos($e->getMessage(), 'on node type (User)') !== false)
            {
                $this->record->facebook_user_page = Carbon::now();
                $this->record->facebook_update_disabled_at = Carbon::now();
                $this->record->save();
                return;
            }
            throw $e;
        }
    }

    private function save($response)
    {
        if ($response->getGraphPage())
        {
            // Store the facebook picture
            if ($response->getGraphPage()->getPicture())
            {
                $picture_url = $response->getGraphPage()->getPicture()->getUrl();
                $image = Image::make($picture_url)->stream()->__toString();
                Storage::put('images/facebook/'.sha1($this->record->id).'.jpeg', $image);
                $image = Image::make($picture_url)
                    ->fit(400, 400, function ($constraint) { $constraint->upsize(); })
                    ->stream()->__toString();
                Storage::put('images/facebook/thumbnail/'.sha1($this->record->id).'.jpeg', $image);
                $this->record->facebook_photo = 'images/facebook/'.sha1($this->record->id).'.jpeg';
            }

            // Store the facebook name and fan count
            $this->record->facebook_name = $response->getGraphPage()->getName();
            $this->record->facebook_followers = $response->getGraphPage()->getFanCount();
            $this->record->save();
            Stats::create([
                'record_id' => $this->record->id,
                'key' => 'facebook_followers',
                'value' => $this->record->facebook_followers,
            ]);

            $fields = 'id,reactions.limit(0).summary(total_count),comments.limit(0).summary(total_count)';

            // Get the last 10 post engagements
            $this->record->facebook_engagement_rate_post = null;
            $response = $this->fb->get($this->record->facebook_id.'/posts?limit=10&fields='.$fields);
            $response = $response->getDecodedBody();
            $response['engagement'] = 0;
            foreach ($response['data'] as $post)
            {
                if (isset($post['reactions']['summary']['total_count']))
                    $response['engagement'] += $post['reactions']['summary']['total_count'];
                if (isset($post['comments']['summary']['total_count)']))
                    $response['engagement'] += $post['comments']['summary']['total_count'];
            }
            if ($response['engagement'] && $this->record->facebook_followers)
            {
                $this->record->facebook_engagement_rate_post = $response['engagement'] / sizeof($response['data']);
                $this->record->save();
                Stats::create([
                    'record_id' => $this->record->id,
                    'key' => 'facebook_engagement_rate_post',
                    'value' => $this->record->facebook_engagement_rate_post,
                ]);
            }

            // Get the last 10 video engagements
            $this->record->facebook_engagement_rate_video = null;
            $response = $this->fb->get($this->record->facebook_id.'/videos?limit=10&fields='.$fields);
            $response = $response->getDecodedBody();
            $response['engagement'] = 0;
            foreach ($response['data'] as $video)
            {
                if (isset($video['reactions']['summary']['total_count']))
                    $response['engagement'] += $video['reactions']['summary']['total_count'];
                if (isset($video['comments']['summary']['total_count']))
                    $response['engagement'] += $video['comments']['summary']['total_count'];
            }
            if ($response['engagement'] && $this->record->facebook_followers)
            {
                $this->record->facebook_engagement_rate_video = $response['engagement'] / sizeof($response['data']);
                $this->record->save();
                Stats::create([
                    'record_id' => $this->record->id,
                    'key' => 'facebook_engagement_rate_video',
                    'value' => $this->record->facebook_engagement_rate_video,
                ]);
            }

            $this->record->facebook_update_succeeded_at = Carbon::now();
            $this->record->facebook_update_disabled_at = null;
            $this->record->facebook_user_page = null;
            $this->record->save();
        }
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed($e)
    {
        $this->record->facebook_update_disabled_at = Carbon::now();
        $this->record->save();
    }
}
