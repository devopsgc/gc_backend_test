<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Google_Service_YouTube;
use Google_Client;
use App\Models\Record;
use App\Models\Stats;
use Carbon\Carbon;
use Storage;
use Image;

class GetYoutubeStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $record;
    protected $youtube;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->record = Record::find($id);
        $service = new Google_Client();
        $service->setApplicationName(config('app.name'));
        $service->setDeveloperKey(config('services.google.developer_key'));
        $this->youtube = new Google_Service_Youtube($service);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Find Youtube info by channel ID, fallback to Username
        $response = $this->youtube->channels->listChannels('id,snippet,statistics', [
            'id' => $this->record->youtube_id]);
        if ( ! $response->items)
            $response = $this->youtube->channels->listChannels('id,snippet,statistics', [
                'forUsername' => $this->record->youtube_id]);

        if ( ! $response->items[0])
        {
            $this->record->facebook_update_disabled_at = Carbon::now();
            $this->record->save();
            return;
        }

        if (isset($response->items[0]->snippet->thumbnails->high->url))
        {
            $imageUrl = $response->items[0]->snippet->thumbnails->high->url;
            $image = Image::make($imageUrl)->stream()->__toString();
            Storage::put('images/youtube/'.sha1($this->record->id).'.jpeg', $image);
            $image = Image::make($imageUrl)
                ->fit(400, 400, function ($constraint) { $constraint->upsize(); })
                ->stream()->__toString();
            Storage::put('images/youtube/thumbnail/'.sha1($this->record->id).'.jpeg', $image);
            $this->record->youtube_photo = 'images/youtube/'.sha1($this->record->id).'.jpeg';
        }

        $this->record->youtube_id = $response->items[0]->id;
        $this->record->youtube_subscribers = $response->items[0]->statistics->subscriberCount;
        $this->record->youtube_views = $response->items[0]->statistics->viewCount;
        $this->record->youtube_name = $response->items[0]->snippet->title;
        Stats::create([
            'record_id' => $this->record->id,
            'key' => 'youtube_subscribers',
            'value' => $this->record->youtube_subscribers,
        ]);
        Stats::create([
            'record_id' => $this->record->id,
            'key' => 'youtube_views',
            'value' => $this->record->youtube_views,
        ]);

        $response = $this->youtube->search->listSearch('id', ['channelId' => $this->record->youtube_id, 'type' => 'video', 'maxResults' => 10, 'order' => 'date']);

        $videos = [];
        foreach ($response->items as $item)
            if (isset($item->id->videoId))
                array_push($videos, $item->id->videoId);

        if (sizeof($videos))
        {
            $response = $this->youtube->videos->listVideos('statistics', ['id' => implode(',', $videos)]);
            if ($response->items)
            {
                $count = 0;
                $this->record->youtube_view_rate = 0;
                foreach ($response->items as $item)
                {
                    if (isset($item->statistics->viewCount))
                    {
                        $this->record->youtube_view_rate += $item->statistics->viewCount;
                        $count++;
                    }
                }
                if ($count)
                {
                    $this->record->youtube_view_rate = $this->record->youtube_view_rate / $count;
                    Stats::create([
                        'record_id' => $this->record->id,
                        'key' => 'youtube_view_rate',
                        'value' => $this->record->youtube_view_rate,
                    ]);
                }
            }
        }

        $this->record->youtube_update_succeeded_at = Carbon::now();
        $this->record->youtube_update_disabled_at = null;
        $this->record->save();
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed($e)
    {
        $this->record->youtube_update_disabled_at = Carbon::now();
        $this->record->save();
    }
}
