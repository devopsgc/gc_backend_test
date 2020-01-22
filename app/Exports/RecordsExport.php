<?php

namespace App\Exports;

use App\Models\Record;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RecordsExport implements FromQuery, WithHeadings, ShouldQueue
{
    use Exportable;

    protected $recordIds;

    public function __construct(array $recordIds = [])
    {
        $this->recordIds = $recordIds;
    }

    public function query()
    {
        return Record::select($this->getAttributesToExport())->whereIn('id', $this->recordIds);
    }

    public function headings(): array
    {
        return $this->getAttributesToExport();
    }

    protected function getAttributesToExport()
    {
        return [
            'id',
            'country_code',
            'name',
            'second_name',
            'race',
            'state',
            'city',
            'address',
            'postal_code',
            'gender',
            'date_of_birth',
            // 'age',
            'description',
            'description_ppt',
            'private_notes',
            'email',
            'calling_code',
            'phone',
            'phone_remarks',
            'line',
            'wechat',
            'bank_name',
            'bank_code',
            'bank_account_number',
            // 'verticals',
            'campaigns',
            // 'photo_default',
            // 'photo',
            // 'affiliations',
            // 'recommended',
            'facebook_id',
            'facebook_name',
            // 'facebook_photo',
            'facebook_followers',
            'facebook_engagement_rate_post',
            'facebook_engagement_rate_video',
            'facebook_external_rate_post',
            'facebook_external_rate_video',
            'facebook_external_rate_story',
            'facebook_user_page',
            // 'facebook_updated_at',
            // 'facebook_update_succeeded_at',
            // 'facebook_update_disabled_at',
            'instagram_id',
            'instagram_name',
            // 'instagram_photo',
            'instagram_followers',
            'instagram_engagement_rate_post',
            'instagram_engagement_rate_video',
            'instagram_external_rate_post',
            'instagram_external_rate_video',
            'instagram_external_rate_story',
            // 'instagram_updated_at',
            // 'instagram_update_succeeded_at',
            // 'instagram_update_disabled_at',
            // 'instagram_socapi',
            // 'instagram_socapi_updated_at',
            'blog_url',
            'blog_followers',
            'blog_engagement_rate',
            'blog_external_rate_post',
            // 'blog_updated_at',
            // 'blog_update_succeeded_at',
            // 'blog_update_disabled_at',
            'youtube_id',
            'youtube_name',
            // 'youtube_photo',
            'youtube_subscribers',
            'youtube_views',
            'youtube_view_rate',
            'youtube_external_rate_video',
            // 'youtube_updated_at',
            // 'youtube_update_succeeded_at',
            // 'youtube_update_disabled_at',
            'twitter_id',
            'twitter_name',
            // 'twitter_photo',
            'twitter_followers',
            'twitter_tweets',
            'twitter_engagement_rate',
            // 'twitter_updated_at',
            // 'twitter_update_succeeded_at',
            // 'twitter_update_disabled_at',
            'tiktok_id',
            'tiktok_name',
            // 'tiktok_photo',
            'tiktok_followers',
            'tiktok_engagements',
            'tiktok_engagement_rate_post',
            'tiktok_external_rate_post',
            // 'tiktok_updated_at',
            // 'tiktok_update_succeeded_at',
            // 'tiktok_update_disabled_at',
            'weibo_id',
            // 'weibo_photo',
            'weibo_followers',
            'weibo_engagement_rate_post',
            'weibo_engagement_rate_livestream',
            'weibo_external_rate_post',
            'weibo_external_rate_livestream',
            // 'weibo_updated_at',
            // 'weibo_update_succeeded_at',
            // 'weibo_update_disabled_at',
            'xiaohongshu_id',
            'xiaohongshu_photo',
            'xiaohongshu_followers',
            'xiaohongshu_engagements',
            'xiaohongshu_engagement_rate',
            'xiaohongshu_external_rate',
            // 'xiaohongshu_updated_at',
            // 'xiaohongshu_update_succeeded_at',
            // 'xiaohongshu_update_disabled_at',
            'miaopai_id',
            'miaopai_followers',
            'miaopai_engagement_rate',
            'miaopai_external_rate_livestream',
            // 'miaopai_updated_at',
            // 'miaopai_update_succeeded_at',
            // 'miaopai_update_disabled_at',
            'yizhibo_id',
            'yizhibo_followers',
            'yizhibo_engagement_rate',
            'yizhibo_external_rate_livestream',
            // 'yizhibo_updated_at',
            // 'yizhibo_update_succeeded_at',
            // 'yizhibo_update_disabled_at',
            'wikipedia_id',
            // 'deleted_at', // do not have this field when exporting
            // 'created_at',
            // 'updated_at',
        ];
    }
}
