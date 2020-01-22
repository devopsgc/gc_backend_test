<?php

namespace App\Models;

use GuzzleHttp\Client;
use Log;

class Link extends Model
{
    const EMBEDDED_WIDTH = 350;

    protected $fillable = [
        'deliverable_id',
        'url',
    ];

    public function deliverable()
    {
        return $this->belongsTo('App\Models\Deliverable');
    }

    public function getInstagramEmbedHtmlCode()
    {
        if ($this->deliverable->platform != 'Instagram') {
            return '';
        }

        if ($this->deliverable->type == 'Story') {
            return '<a href="'.$this->url.'">'.$this->url.'</a>';
        }

        $client = new Client;
        try {
            $response = $client->get('https://api.instagram.com/oembed?url='.$this->url.'&maxwidth='.self::EMBEDDED_WIDTH.'&omitscript=true');
            return json_decode((string) $response->getBody())->html;
        } catch (\Exception $e) {
            $e = json_decode((string) $e->getResponse()->getBody(true));
        }
        return '';
    }

    public function getTwitterEmbedHtmlCode()
    {
        if ($this->deliverable->platform != 'Twitter') {
            return '';
        }

        $client = new Client;
        try {
            $response = $client->get('https://publish.twitter.com/oembed?url='.$this->url.'&omit_script=true&hide_thread=true&maxwidth='.self::EMBEDDED_WIDTH.'');
            return json_decode((string) $response->getBody())->html;
        } catch (\Exception $e) {
            $e = json_decode((string) $e->getResponse()->getBody(true));
        }

        return '';
    }

    public function getYoutubeEmbedUrl()
    {
        if (strpos($this->url, 'https://www.youtube.com/watch?') === false) {
            return '';
        }

        $trailingUrl = substr($this->url, strpos($this->url, 'v='));
        $videoUrl = strpos($trailingUrl, '&') === false ? $trailingUrl : substr($trailingUrl, 0, strpos($trailingUrl, '&'));
        $videoId = substr($videoUrl, strpos($videoUrl, '=') + 1);

        return $videoId ? 'https://www.youtube.com/embed/'.$videoId : '';
    }

    public function isFacebookPost()
    {
        if (strpos($this->url, 'https://www.facebook.com/') !== false && strpos($this->url, '/posts/') !== false) {
            return true;
        }

        return false;
    }

    public function isFacebookPhoto()
    {
        if (strpos($this->url, 'https://www.facebook.com/') !== false && strpos($this->url, '/photos/') !== false) {
            return true;
        }

        return false;
    }

    public function isFacebookVideo()
    {
        if (strpos($this->url, 'https://www.facebook.com/') !== false && strpos($this->url, '/videos/') !== false) {
            return true;
        }

        return false;
    }

    public function isInstagramStory()
    {
        if (strpos($this->url, 'https://www.instagram.com/') !== false && strpos($this->url, '/stories/') !== false) {
            return true;
        }

        return false;
    }

    public function getPostId()
    {
        if ($this->isFacebookPost()) {
            $exploded = explode('/posts/', strtok($this->url, '?'));
            $postId = $exploded[1] ?? '';
            return rtrim($postId, '/');
        }

        if ($this->isFacebookPhoto()) {
            $exploded = explode('/', strtok($this->url, '?'));
            $postId = $exploded[6] ?? '';
            return rtrim($postId, '/');
        }

        if ($this->isFacebookVideo()) {
            $exploded = explode('/videos/', strtok($this->url, '?'));
            $postId = $exploded[1] ?? '';
            return rtrim($postId, '/');
        }

        if ($this->isInstagramStory()) {
            $exploded = explode('/', strtok($this->url, '?'));
            $postId = $exploded[5] ?? '';
            return rtrim($postId, '/');
        }

        return '';
    }

    public function getObjectId()
    {
        return $this->deliverable->record->facebook_page_id.'_'.$this->getPostId();
    }

    public function getInstagramPostProperties()
    {
        try {
            $media_query = \Facebook::get('/'.$this->facebook_media_id.'?fields=timestamp,comments_count,like_count,permalink', $this->deliverable->record->facebook_page_access_token);
            return $media_query->getDecodedBody();
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            Log::error('App\Link getInstagramPostProperties: '.$e->getMessage());
            return [];
        } catch (\Exception $e) {
            Log::error('App\Link getInstagramPostProperties: '.$e->getMessage());
            return [];
        }
    }

    public function getInstagramInsights()
    {
        try {
            $media_insights_query = \Facebook::get('/'.$this->facebook_media_id.'/insights?metric=reach,engagement,impressions,saved', $this->deliverable->record->facebook_page_access_token);
            $insights = [];
            foreach ($media_insights_query->getGraphEdge() as $insight) {
                $insights[$insight->getField('name')] = $insight;
            }
            return $insights;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            Log::error('App\Link getInstagramPostInsights: '.$e->getMessage());
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            Log::error('App\Link getInstagramPostInsights: '.$e->getMessage());
            return [];
        }
    }

    public function getFacebookMediaIdAttribute()
    {
        if ($this->deliverable->type == 'Story') {
            return $this->getPostId();
        }
        $instagram_account_id = $this->getInstagramAccountId();
        $instagram_media_objects = $this->getInstagramMediaObjects($instagram_account_id);
        $media_id = $this->getMediaIdFromInstagramUrl($this->url);
        return $this->getFacebookMediaId($instagram_media_objects, $media_id);
    }

    private function getInstagramAccountId()
    {
        $facebook_page_id = $this->deliverable->record->facebook_page_id;
        $instagram_account_query = \Facebook::get('/'.$facebook_page_id.'?fields=instagram_business_account', $this->deliverable->record->facebook_page_access_token);
        return $instagram_account_query->getDecodedBody()['instagram_business_account']['id'];
    }

    private function getInstagramMediaObjects($instagram_account_id)
    {
        $instagram_media_objects = [];
        $instagram_media_query = \Facebook::get('/'.$instagram_account_id.'/media?fields=ig_id,shortcode,permalink', $this->deliverable->record->facebook_page_access_token);

        foreach ($instagram_media_query->getGraphEdge() as $instagram_media) {
            $instagram_media_objects[$instagram_media->getField('ig_id')] = $instagram_media;
        }
        return $instagram_media_objects;
    }

    private function getMediaIdFromInstagramUrl($url)
    {
        $api = file_get_contents('http://api.instagram.com/oembed?url='.$url);
        $apiObj = json_decode($api, true);
        $media_id = $apiObj['media_id'];
        $pieces = explode('_', $media_id);
        $media_id = $pieces[0];
        return $media_id;
    }

    private function getFacebookMediaId($instagram_media_objects, $media_id)
    {
        return $instagram_media_objects[$media_id]->getField('id');
    }

    public function getProperties()
    {
        try {
            $fields = 'likes.summary(total_count),shares,comments.summary(total_count),permalink_url,created_time';
            $objectId = $this->getObjectId();
            if ($this->isFacebookPhoto()) {
                $objectId = $this->deliverable->record->facebook_page_id.'_'.$this->getPostId();
            }

            $pagePostQuery =
            \Facebook::get('/'.$objectId.'?fields='.$fields, $this->deliverable->record->facebook_page_access_token);
            $insights = [];

            return $pagePostQuery->getDecodedBody();

        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            Log::error('App\Link getProperties: '.$e->getMessage());
            return [];
        }
    }

    public function getInsights()
    {
        try {
            $insight_metrics = 'post_activity,post_impressions,post_impressions_unique,post_story_adds_unique,post_engaged_users,post_clicks,post_reactions_like_total';
            $objectId = $this->getObjectId();

            $pagePostInsightsQuery =
            \Facebook::get('/'.$objectId.'/insights?metric='.$insight_metrics, $this->deliverable->record->facebook_page_access_token);
            $insights = [];
            foreach ($pagePostInsightsQuery->getGraphEdge() as $insight) {
                $insights[$insight->getField('name')] = $insight;
            }

            return $insights;

        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            Log::error('App\Link getInsights: '.$e->getMessage());
            return [];
        }
    }
}
