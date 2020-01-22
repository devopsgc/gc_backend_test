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

class GetXiaoHongShuStats implements ShouldQueue
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
        // CURL the xiaohongshu user page with the user-agent as "spider"
        $client = new \GuzzleHttp\Client();
        $response = $client->get('https://www.xiaohongshu.com/user/profile/'.$this->record->xiaohongshu_id);

        if ($response->getStatusCode() == 404)
        {
            $this->record->xiaohongshu_update_disabled_at = Carbon::now();
            $this->record->save();
            return;
        }

        // Get profile picture
        preg_match_all('/xiaohongshu.com\/avatar\/(.*?)\.jpg/s', $response->getBody(), $picture_matches);
        $picture_id = $picture_matches[1][0];
        $picture = 'https://img.xiaohongshu.com/avatar/'.$picture_id.'.jpg';
        $image = Image::make($picture)
            ->stream()->__toString();
        Storage::put('images/xiaohongshu/'.$picture_id.'.jpeg', $image);
        $image = Image::make($picture)
            ->fit(400, 400, function ($constraint) { $constraint->upsize(); })
            ->stream()->__toString();
        Storage::put('images/xiaohongshu/thumbnail/'.$picture_id.'.jpeg', $image);
        $this->record->xiaohongshu_photo = 'images/xiaohongshu/'.$picture_id.'.jpeg';

        // Get number of followers
        preg_match_all('/<span class="info-number"(.*?)>(.*?)<\/span>/s', $response->getBody(), $numbers);
        if (sizeof($numbers[2]) === 3)
            foreach ($numbers[2] as &$number)
                $number = $this->cleanNumber($number);

        $this->record->xiaohongshu_followers = $numbers[2][1];
        $this->record->xiaohongshu_engagements = $numbers[2][2];
        Stats::create([
            'record_id' => $this->record->id,
            'key' => 'xiaohongshu_followers',
            'value' => $this->record->xiaohongshu_followers,
        ]);
        Stats::create([
            'record_id' => $this->record->id,
            'key' => 'xiaohongshu_engagements',
            'value' => $this->record->xiaohongshu_engagements,
        ]);

        // Get post engagements
        preg_match_all('/<span class="likes"(.*?)>(.*?)<\/span>/s', $response->getBody(), $likes_matches);
        $likes_matches = array_splice($likes_matches[2], 0, 10);
        if (sizeof($likes_matches))
        {
            $this->record->xiaohongshu_engagement_rate = array_sum($likes_matches) / sizeof($likes_matches);
            Stats::create([
                'record_id' => $this->record->id,
                'key' => 'xiaohongshu_engagement_rate',
                'value' => $this->record->xiaohongshu_engagement_rate,
            ]);
        }

        $this->record->xiaohongshu_update_succeeded_at = Carbon::now();
        $this->record->xiaohongshu_update_disabled_at = null;
        $this->record->save();
    }

    public function cleanNumber(&$number)
    {
        if (strpos($number, '万') !== false)
        {
            $number = str_replace('万', '', $number);
            $number = $number * 10000;
        }
        return $number;
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed($e)
    {
        $this->record->xiaohongshu_update_disabled_at = Carbon::now();
        $this->record->save();
    }
}
