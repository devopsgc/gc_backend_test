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

class GetWeiboStats implements ShouldQueue
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
        // CURL the weibo user page with the user-agent as "spider"
        $client = new \GuzzleHttp\Client();
        $response = $client->get('https://www.weibo.com/'.$this->record->weibo_id, ['headers' => ['User-Agent' => 'spider']]);

        // Check that the page doesnt have the word "pagenotfound"
        preg_match_all('/pagenotfound/s', $response->getBody(), $pagenotfound);
        if (isset($pagenotfound[0]) && sizeof($pagenotfound[0]))
        {
            $this->record->weibo_update_disabled_at = Carbon::now();
            $this->record->save();
            return;
        }

        // Get profile picture, its somewhere after 'photo_wrap' is expected to only have one occurence
        // we expect the image to be jpg format but we cannot be certain
        // if there are other formats this part will break
        preg_match_all('/photo_wrap(.*?)<img src="\/\/(.*?).jpg/s', $response->getBody(), $picture_matches);
        $picture_segments = explode('/', $picture_matches[2][0]);
        $picture_id = $picture_segments[2];

        // 1080.1080.1080 seems to be controlling the size and the croping of the image
        // we're trying to get a size which is big enough
        $picture = 'https://tva2.sinaimg.cn/crop.0.0.1080.1080.1080/'.$picture_id;
        $image = Image::make($picture)
            ->stream()->__toString();
        Storage::put('images/weibo/'.$picture_id.'.jpeg', $image);
        $image = Image::make($picture)
            ->fit(400, 400, function ($constraint) { $constraint->upsize(); })
            ->stream()->__toString();
        Storage::put('images/weibo/thumbnail/'.$picture_id.'.jpeg', $image);
        $this->record->weibo_photo = 'images/weibo/'.$picture_id.'.jpeg';

        // Get number of followers by looking between 'tb_counter' which should only appear once
        // and the closest '微博' word, the stats are wrapped in <strong> tags
        preg_match_all('/tb_counter(.*?)>微博</s', $response->getBody(), $followers_matches);
        preg_match_all('/<strong class="(.*?)">(.*?)<\/strong>/s', $followers_matches[1][0], $followers_matches2);
        $this->record->weibo_followers = $followers_matches2[2][1];
        Stats::create([
            'record_id' => $this->record->id,
            'key' => 'weibo_followers',
            'value' => $this->record->weibo_followers,
        ]);

        // Get post engagements by looking for '收藏' and ending with 'cmtarrow'
        // the stats are wrapped in <em> tags
        $engagements = 0;
        $count = 0;
        preg_match_all('/收藏(.*?)cmtarrow/s', $response->getBody(), $reactions_matches);
        foreach ($reactions_matches[1] as $reactions_match)
        {
            if ($count == 10) break; $count++;
            preg_match_all('/<em>(.*?)<\/em>/s', $reactions_match, $reactions_matches2);
            if (is_numeric($reactions_matches2[1][0])) $engagements += $reactions_matches2[1][0];
            if (is_numeric($reactions_matches2[1][1])) $engagements += $reactions_matches2[1][1];
            if (is_numeric($reactions_matches2[1][2])) $engagements += $reactions_matches2[1][2];
        }

        if ($engagements && $count)
        {
            $this->record->weibo_engagement_rate_post = $engagements / $count;
            Stats::create([
                'record_id' => $this->record->id,
                'key' => 'weibo_engagement_rate_post',
                'value' => $this->record->weibo_engagement_rate_post,
            ]);
        }

        $this->record->weibo_update_succeeded_at = Carbon::now();
        $this->record->weibo_update_disabled_at = null;
        $this->record->save();
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed($e)
    {
        $this->record->weibo_update_disabled_at = Carbon::now();
        $this->record->save();
    }
}
