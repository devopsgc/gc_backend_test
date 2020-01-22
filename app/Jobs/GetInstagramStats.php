<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Cookie\CookieJar;
use App\Models\Instagram;
use App\Models\Record;
use App\Models\Stats;
use Carbon\Carbon;
use Storage;
use Image;

class GetInstagramStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $record;
    protected $video_count;
    protected $video_engagement;
    protected $post_count;
    protected $post_engagement;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->record = Record::find($id);
        $this->video_count = 0;
        $this->video_engagement = 0;
        $this->post_count = 0;
        $this->post_engagement = 0;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new \GuzzleHttp\Client();

        $instagram = Instagram::inRandomOrder()->first();
        $cookieJar = CookieJar::fromArray(['sessionid' => $instagram->session_id], 'instagram.com');
        $response = $client->get('https://www.instagram.com/'.$this->record->instagram_id.'?query_hash='.$instagram->query_hash, ['cookies' => $cookieJar])->getBody();

        // TODO: Add user agent? don't know if that would help or make a difference.
        // Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36

        preg_match_all('/window._sharedData = (.*?);<\/script>/s', (string)$response, $matches);
        //preg_match_all('/"rhx_gis":"(.*?)"/s', (string)$response, $matches_gis);
        $user = json_decode($matches[1][0])->entry_data->ProfilePage[0]->graphql->user;
        $user_id = $user->id;
        $edges = $user->edge_owner_to_timeline_media->edges;
        $this->record->instagram_followers = $user->edge_followed_by->count;
        $this->record->instagram_name = $user->full_name;
        $this->record->save();
        Stats::create([
            'record_id' => $this->record->id,
            'key' => 'instagram_followers',
            'value' => $this->record->instagram_followers,
        ]);

        // echo '================================='."\n";
        // echo 'user id: '.$user_id."\n";
        // echo 'followers: '.$this->record->instagram_followers."\n";
        // echo 'size of edges: '.sizeof($edges)."\n";
        // echo '================================='."\n";

        if (isset($user->profile_pic_url_hd) && $user->profile_pic_url_hd)
        {
            $image = Image::make($user->profile_pic_url_hd)->stream()->__toString();
            Storage::put('images/instagram/'.sha1($this->record->id).'.jpeg', $image);
            $image = Image::make($user->profile_pic_url_hd)
                ->fit(400, 400, function ($constraint) { $constraint->upsize(); })
                ->stream()->__toString();
            Storage::put('images/instagram/thumbnail/'.sha1($this->record->id).'.jpeg', $image);
            $this->record->instagram_photo = 'images/instagram/'.sha1($this->record->id).'.jpeg';
        }

        /*
        $user_agent = 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Mobile Safari/537.36';
        $rhx_gis = 'be4f6de8892212b0150e0c3d78fee29d';
        $variables = '{"id":"519717405","first":12,"after":"AQALiE3VQ_isr53yOjLWYOr1O5LjPKS3BOjV5Ij19d7C-2GYzXMXP4HuL01IzsVniEhN6URqqU5b6sRORKt1-D4OOW8A3a6yXPQHiIkzEVwJpQ"}';
        $query_hash = 'e7e2f4da4b02303f74f0841279e52d76';
        $x_instagram_gis = '11b3b14bd379fd501cda051d21384467';
        // No one has figure out how to break this and generate the x_instagram_gis key yet...
        //$x_instagram_gis = md5($rhx_gis.':'.$user_agent.':'.urlencode($variables));

        $response = $client->request('GET', 'https://www.instagram.com/graphql/query/?query_hash='.$query_hash.'&variables='.$variables, [
            'headers' => [
                'user-agent' => $user_agent,
                'x-instagram-gis' => $x_instagram_gis,
            ]
        ])->getBody();
        dd(json_decode($response));
        */

        while (sizeof($edges) < 150 && $this->getStats($this->record, $edges) && $user->edge_owner_to_timeline_media->page_info->has_next_page)
        {
            $response = $client->request('GET', 'https://www.instagram.com/graphql/query/?query_hash='.$instagram->query_hash.'&variables={"id":"'.$user_id.'","first":50,"after":"'.$user->edge_owner_to_timeline_media->page_info->end_cursor.'"}', ['cookies' => $cookieJar])->getBody();
            $user = json_decode($response)->data->user;
            $edges = array_merge($edges, $user->edge_owner_to_timeline_media->edges);
            // echo '================================='."\n";
            // echo 'size of edges: '.sizeof($edges)."\n";
            // echo '================================='."\n";
        }

        if ($this->video_count)
        {
            $this->record->instagram_engagement_rate_video = $this->video_engagement / $this->video_count;
            Stats::create([
                'record_id' => $this->record->id,
                'key' => 'instagram_engagement_rate_video',
                'value' => $this->record->instagram_engagement_rate_video,
            ]);
        }
        if ($this->post_count)
        {
            $this->record->instagram_engagement_rate_post = $this->post_engagement / $this->post_count;
            Stats::create([
                'record_id' => $this->record->id,
                'key' => 'instagram_engagement_rate_post',
                'value' => $this->record->instagram_engagement_rate_post,
            ]);
        }

        $this->record->instagram_update_succeeded_at = Carbon::now();
        $this->record->instagram_update_disabled_at = null;
        $this->record->save();
    }

    public function getStats($record, $edges)
    {
        foreach ($edges as $edge)
        {
            if ($edge->node->is_video)
            {
                if ($this->video_count < 10)
                {
                    $this->video_count++;
                    $this->video_engagement += $edge->node->edge_media_to_comment->count;
                    $this->video_engagement += $edge->node->edge_media_preview_like->count;
                    $this->video_engagement += $edge->node->video_view_count;
                    // echo 'video count: '.$this->video_count."\n";
                    // echo 'video engagement: '.$this->video_engagement."\n";
                    // echo '...............................'."\n";
                }
            }
            else
            {
                if ($this->post_count < 10)
                {
                    $this->post_count++;
                    $this->post_engagement += $edge->node->edge_media_to_comment->count;
                    $this->post_engagement += $edge->node->edge_media_preview_like->count;
                    // echo 'post count: '.$this->post_count."\n";
                    // echo 'post engagement: '.$this->post_engagement."\n";
                    // echo '...............................'."\n";
                }
            }
        }

        return $this->video_count == 10 && $this->post_count == 10 ? false : true;
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed($e)
    {
        // if instagram closes the connect,
        // we silently fail and attempt to crawl instagram again.
        if (
            strpos($e->getMessage(), 'cURL error 18') !== false ||
            strpos($e->getMessage(), 'cURL error 56') !== false)
        {
            $this->record->instagram_updated_at = null;
            $this->record->save();
        }
        else
        {
            $this->record->instagram_update_disabled_at = Carbon::now();
            $this->record->save();
        }
    }
}
