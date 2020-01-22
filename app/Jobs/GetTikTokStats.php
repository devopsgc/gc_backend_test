<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Record;
use App\Models\Stats;
use Carbon\Carbon;
use Storage;
use Image;

class GetTikTokStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $record;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->record = Record::find($id);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // CURL the tiktok user page with the user-agent as "spider"
        $client = new \GuzzleHttp\Client();
        $response = $client->get('https://www.tiktok.com/@'.$this->record->tiktok_id, ['headers' => ['User-Agent' => 'spider']]);

        // Check that the page doesnt have the word "Couldn't find this account"
        preg_match_all('/_error_page_text">Couldn&#x27;t find this account</s', $response->getBody(), $pagenotfound);
        if (isset($pagenotfound[0]) && sizeof($pagenotfound[0]))
        {
            $this->record->tiktok_update_disabled_at = Carbon::now();
            $this->record->save();
            return;
        }

        // Get name
        preg_match_all('/_user_header_nickName">(.*?)</s', $response->getBody(), $name_matches);
        $name = $name_matches[1][0];
        $this->record->tiktok_name = $name;

        // Get profile picture
        preg_match_all('/_user_header_cover" style="background-image:url\((.*?)\)/s', $response->getBody(), $picture_matches);
        $picture = $picture_matches[1][0];
        $image = Image::make($picture)
            ->stream()->__toString();
        Storage::put('images/tiktok/'.sha1($this->record->id).'.jpeg', $image);
        $image = Image::make($picture)
            ->fit(400, 400, function ($constraint) { $constraint->upsize(); })
            ->stream()->__toString();
        Storage::put('images/tiktok/thumbnail/'.sha1($this->record->id).'.jpeg', $image);
        $this->record->tiktok_photo = 'images/tiktok/'.sha1($this->record->id).'.jpeg';

        // Get number of following, fans and hearts
        preg_match_all('/"fans":(.*?),"heart":(.*?),/s', $response->getBody(), $number_matches);
        $this->record->tiktok_followers = $number_matches[1][0];
        Stats::create([
            'record_id' => $this->record->id,
            'key' => 'tiktok_followers',
            'value' => $this->record->tiktok_followers,
        ]);
        $this->record->tiktok_engagements = $number_matches[2][0];
        Stats::create([
            'record_id' => $this->record->id,
            'key' => 'tiktok_engagements',
            'value' => $this->record->tiktok_engagements,
        ]);

        $engagements = 0;
        $count = 0;
        preg_match_all('/"commentCount":"(.*?)","interactionCount":"(.*?)",/s', $response->getBody(), $reactions_matches);
        foreach ($reactions_matches[0] as $index => $value)
        {
            if ($count == 10) break; $count++;
            $engagements += (int) $reactions_matches[1][$index] + (int) $reactions_matches[2][$index];
        }
        if ($engagements && $count)
        {
            $this->record->tiktok_engagement_rate_post = $engagements / $count;
            Stats::create([
                'record_id' => $this->record->id,
                'key' => 'tiktok_engagement_rate_post',
                'value' => $this->record->tiktok_engagement_rate_post,
            ]);
        }

        $this->record->tiktok_update_succeeded_at = Carbon::now();
        $this->record->tiktok_update_disabled_at = null;
        $this->record->save();
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed($e)
    {
        $this->record->tiktok_update_disabled_at = Carbon::now();
        $this->record->save();
    }
}
