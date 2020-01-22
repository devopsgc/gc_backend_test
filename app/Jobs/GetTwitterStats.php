<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use DG\Twitter\Twitter;
use App\Models\Record;
use App\Models\Stats;
use Carbon\Carbon;
use Storage;
use Image;

class GetTwitterStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $record;
    protected $twitter;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->record = Record::find($id);
        $this->twitter = new Twitter(
            config('services.twitter.consumer_key'),
            config('services.twitter.consumer_secret'),
            config('services.twitter.access_token'),
            config('services.twitter.access_token_secret')
        );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $twitterUser = $this->twitter->request('users/show', 'GET', ['screen_name' => $this->record->twitter_id]);
        $this->record->twitter_name = $twitterUser->name;
        $this->record->twitter_followers = $twitterUser->followers_count;
        Stats::create([
            'record_id' => $this->record->id,
            'key' => 'twitter_followers',
            'value' => $this->record->twitter_followers,
        ]);
        $this->record->twitter_tweets = $twitterUser->statuses_count;
        Stats::create([
            'record_id' => $this->record->id,
            'key' => 'twitter_tweets',
            'value' => $this->record->twitter_tweets,
        ]);

        if (isset($twitterUser->profile_image_url_https))
        {
            // try replace the image with a larger one because the one from api is too small
            $imagePath = str_replace("_normal.jpg", "_400x400.jpg", $twitterUser->profile_image_url_https);
            $image = Image::make($imagePath)->stream()->__toString();
            Storage::put('images/twitter/'.sha1($this->record->id).'.jpeg', $image);
            $image = Image::make($imagePath)
                ->fit(400, 400, function ($constraint) { $constraint->upsize(); })
                ->stream()->__toString();
            Storage::put('images/twitter/thumbnail/'.sha1($this->record->id).'.jpeg', $image);
            $this->record->twitter_photo = 'images/twitter/'.sha1($this->record->id).'.jpeg';
        }

        // Get tweet engagement rate based on last 10 tweets
        $tweets = $this->twitter->request('statuses/home_timeline', 'GET', [
            'screen_name' => $this->record->twitter_id,
            'count' => 10
        ]);
        $engagement_rate = 0;
        foreach ($tweets as $index => $tweet)
        {
            $engagement_rate += $tweet->retweet_count;
            $engagement_rate += $tweet->favorite_count;
        }
        if (sizeof($tweets) && $engagement_rate)
        {
            $this->record->twitter_engagement_rate = $engagement_rate / sizeof($tweets);
            Stats::create([
                'record_id' => $this->record->id,
                'key' => 'twitter_engagement_rate',
                'value' => $this->record->twitter_engagement_rate,
            ]);
        }
        $this->record->twitter_update_succeeded_at = Carbon::now();
        $this->record->twitter_update_disabled_at = null;
        $this->record->save();
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed($e)
    {
        // TODO: try-catch and handle the following exceptions so that they don't go to failed jobs table
        // RuntimeException: [50] User not found.
        // RuntimeException: [63] User has been suspended.
        $this->record->twitter_update_disabled_at = Carbon::now();
        $this->record->save();
    }
}
