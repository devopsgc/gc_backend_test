<?php

namespace App\Models;

use Carbon\Carbon;
use Storage;
use Jenssegers\Mongodb\Eloquent\HybridRelations;

class Record extends Model
{
    use HybridRelations;

    protected $fillable = [
        'country_code', 'name', 'gender', 'date_of_birth', 'age', 'description', 'email', 'calling_code', 'phone', 'verticals', 'campaigns', 'photo', 'affiliations', 'facebook_id', 'instagram_id', 'blog_url', 'youtube_id', 'weibo_id', 'xiaohongshu_id', 'miaopai_id', 'yizhibo_id', 'socapi_user_id'
    ];

    protected $hidden = [
        'deleted_at',
        'facebook_page_access_token',
    ];

    protected $dates = [
        'date_of_birth',
        'facebook_updated_at',
        'facebook_update_succeeded_at',
        'facebook_update_disabled_at',
        'instagram_updated_at',
        'instagram_update_succeeded_at',
        'instagram_update_disabled_at',
        'instagram_socapi_updated_at',
        'blog_updated_at',
        'blog_update_succeeded_at',
        'blog_update_disabled_at',
        'youtube_updated_at',
        'youtube_update_succeeded_at',
        'youtube_update_disabled_at',
        'twitter_updated_at',
        'twitter_update_succeeded_at',
        'twitter_update_disabled_at',
        'tiktok_updated_at',
        'tiktok_update_succeeded_at',
        'tiktok_update_disabled_at',
        'weibo_updated_at',
        'weibo_update_succeeded_at',
        'weibo_update_disabled_at',
        'xiaohongshu_updated_at',
        'xiaohongshu_update_succeeded_at',
        'xiaohongshu_update_disabled_at',
        'miaopai_updated_at',
        'miaopai_update_succeeded_at',
        'miaopai_update_disabled_at',
        'yizhibo_updated_at',
        'yizhibo_update_succeeded_at',
        'yizhibo_update_disabled_at',
        'facebook_page_access_token_updated_at',
    ];

    public function instagramSocapiData()
    {
        return $this->hasOne('App\Models\Data\InstagramSocialData');
    }

    public function descriptions()
    {
        return $this->hasMany('App\Models\Description');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag', 'record_tags', 'record_id', 'tag_id')
            ->whereNull('record_tags.deleted_at')
            ->withTimestamps()
            ->withPivot('deleted_at', 'type');
    }

    public function interestsCore()
    {
        return $this->belongsToMany('App\Models\Tag', 'record_tags', 'record_id', 'tag_id')
            ->whereNull('record_tags.deleted_at')
            ->where('record_tags.type', 'interest_core')
            ->withTimestamps()->withPivot('deleted_at');
    }

    public function professionsCore()
    {
        return $this->belongsToMany('App\Models\Tag', 'record_tags', 'record_id', 'tag_id')
            ->whereNull('record_tags.deleted_at')
            ->where('record_tags.type', 'profession_core')
            ->withTimestamps()->withPivot('deleted_at');
    }

    public function pptVertical()
    {
        return $this->belongsToMany('App\Models\Tag', 'record_tags', 'record_id', 'tag_id')
            ->whereNull('record_tags.deleted_at')
            ->where('record_tags.type', 'ppt_vertical')
            ->withTimestamps()->withPivot('deleted_at');
    }

    public function affiliationTags()
    {
        return $this->belongsToMany('App\Models\Tag', 'record_tags', 'record_id', 'tag_id')
            ->whereNull('record_tags.deleted_at')
            ->where('record_tags.type', 'affiliation')
            ->withTimestamps()->withPivot('deleted_at');
    }

    public function country()
    {
        return $this->hasOne('App\Models\Country', 'iso_3166_2', 'country_code');
    }

    public function getInterestsDisplayAttribute($value)
    {
        return implode('|', $this->interestsCore->pluck('name')->toArray());
    }

    public function getProfessionsDisplayAttribute($value)
    {
        return implode('|', $this->professionsCore->pluck('name')->toArray());
    }

    public function getInterestsDisplayForSelectAttribute($value)
    {
        return implode('|', $this->interestsCore->pluck('name')->toArray());
    }

    public function getProfessionsDisplayForSelectAttribute($value)
    {
        return implode('|', $this->professionsCore->pluck('name')->toArray());
    }

    public function getAffiliationsDisplayForSelectAttribute($value)
    {
        return implode(':: ', $this->affiliationTags->pluck('name')->toArray());
    }

    public function getGenderAttribute($value)
    {
        switch ($value) {
            case 'M': return 'Male'; break;
            case 'F': return 'Female'; break;
            case 'N': return 'Non-binary'; break;
            default: return null;
        }
    }

    public function setDateOfBirthAttribute($value)
    {
        $this->attributes['date_of_birth'] = $value ? Carbon::parse($value) : null;
    }

    public function getDescriptionPpt($locale = 'en')
    {
        $token = explode('_', $locale);
        $filter = ['record_id' => $this->id];
        if (isset($token[0])) {
            $filter['iso_639_1'] = $token[0];
        }
        if (isset($token[1])) {
            $filter['iso_3166_2'] = $token[1];
        }
        if ($description = Description::where($filter)->first()) {
            return $description->description;
        } elseif (isset($token[0]) && $token[0] == 'en') {
            return $this->description_ppt ? $this->description_ppt : ($this->description ? $this->description : null);
        } else {
            return null;
        }
    }

    public function getPhotoUrlAttribute($value)
    {
        if ($this->photo) {
            return Storage::temporaryUrl(
                str_replace('images/', 'images/thumbnail/', $this->photo),
                now()->addMinutes(5)
            );
        }
        return url('img/profile-placeholder.png');
    }

    public function getInstagramPhotoUrlAttribute($value)
    {
        if ($this->instagram_photo) {
            return Storage::temporaryUrl(
                str_replace('images/instagram/', 'images/instagram/thumbnail/', $this->instagram_photo),
                now()->addMinutes(5)
            );
        }
        return url('img/profile-placeholder.png');
    }

    public function getYoutubePhotoUrlAttribute($value)
    {
        if ($this->youtube_photo) {
            return Storage::temporaryUrl(
                str_replace('images/youtube/', 'images/youtube/thumbnail/', $this->youtube_photo),
                now()->addMinutes(5)
            );
        }
        return url('img/profile-placeholder.png');
    }

    public function getFacebookPhotoUrlAttribute($value)
    {
        if ($this->facebook_photo) {
            return Storage::temporaryUrl(
                str_replace('images/facebook/', 'images/facebook/thumbnail/', $this->facebook_photo),
                now()->addMinutes(5)
            );
        }
        return url('img/profile-placeholder.png');
    }

    public function getTwitterPhotoUrlAttribute($value)
    {
        if ($this->twitter_photo) {
            return Storage::temporaryUrl(
                str_replace('images/twitter/', 'images/twitter/thumbnail/', $this->twitter_photo),
                now()->addMinutes(5)
            );
        }
        return url('img/profile-placeholder.png');
    }

    public function getTiktokPhotoUrlAttribute($value)
    {
        if ($this->tiktok_photo) {
            return Storage::temporaryUrl(
                str_replace('images/tiktok/', 'images/tiktok/thumbnail/', $this->tiktok_photo),
                now()->addMinutes(5)
            );
        }
        return url('img/profile-placeholder.png');
    }

    public function getWeiboPhotoUrlAttribute($value)
    {
        if ($this->weibo_photo) {
            return Storage::temporaryUrl(
                str_replace('images/weibo/', 'images/weibo/thumbnail/', $this->weibo_photo),
                now()->addMinutes(5)
            );
        }
        return url('img/profile-placeholder.png');
    }

    public function getXiaoHongShuPhotoUrlAttribute($value)
    {
        if ($this->xiaohongshu_photo) {
            return Storage::temporaryUrl(
                str_replace('images/xiaohongshu/', 'images/xiaohongshu/thumbnail/', $this->xiaohongshu_photo),
                now()->addMinutes(5)
            );
        }
        return url('img/profile-placeholder.png');
    }

    public function getDisplayPhotoAttribute($value)
    {
        if ($this->photo_default == 'instagram' && $this->instagram_photo) {
            return Storage::temporaryUrl(
                str_replace('images/instagram/', 'images/instagram/thumbnail/', $this->instagram_photo),
                now()->addMinutes(5)
            );
        } elseif ($this->photo_default == 'facebook' && $this->facebook_photo) {
            return Storage::temporaryUrl(
                str_replace('images/facebook/', 'images/facebook/thumbnail/', $this->facebook_photo),
                now()->addMinutes(5)
            );
        } elseif ($this->photo_default == 'youtube' && $this->youtube_photo) {
            return Storage::temporaryUrl(
                str_replace('images/youtube/', 'images/youtube/thumbnail/', $this->youtube_photo),
                now()->addMinutes(5)
            );
        } elseif ($this->photo_default == 'twitter' && $this->twitter_photo) {
            return Storage::temporaryUrl(
                str_replace('images/twitter/', 'images/twitter/thumbnail/', $this->twitter_photo),
                now()->addMinutes(5)
            );
        } elseif ($this->photo_default == 'tiktok' && $this->tiktok_photo) {
            return Storage::temporaryUrl(
                str_replace('images/tiktok/', 'images/tiktok/thumbnail/', $this->tiktok_photo),
                now()->addMinutes(5)
            );
        } elseif ($this->photo_default == 'weibo' && $this->weibo_photo) {
            return Storage::temporaryUrl(
                str_replace('images/weibo/', 'images/weibo/thumbnail/', $this->weibo_photo),
                now()->addMinutes(5)
            );
        } elseif ($this->photo_default == 'xiaohongshu' && $this->xiaohongshu_photo) {
            return Storage::temporaryUrl(
                str_replace('images/xiaohongshu/', 'images/xiaohongshu/thumbnail/', $this->xiaohongshu_photo),
                now()->addMinutes(5)
            );
        } elseif ($this->photo) {
            return Storage::temporaryUrl(
                str_replace('images/', 'images/thumbnail/', $this->photo),
                now()->addMinutes(5)
            );
        } elseif ($this->instagram_photo) {
            return Storage::temporaryUrl(
                str_replace('images/instagram/', 'images/instagram/thumbnail/', $this->instagram_photo),
                now()->addMinutes(5)
            );
        } elseif ($this->facebook_photo) {
            return Storage::temporaryUrl(
                str_replace('images/facebook/', 'images/facebook/thumbnail/', $this->facebook_photo),
                now()->addMinutes(5)
            );
        } elseif ($this->youtube_photo) {
            return Storage::temporaryUrl(
                str_replace('images/youtube/', 'images/youtube/thumbnail/', $this->youtube_photo),
                now()->addMinutes(5)
            );
        } elseif ($this->twitter_photo) {
            return Storage::temporaryUrl(
                str_replace('images/twitter/', 'images/twitter/thumbnail/', $this->twitter_photo),
                now()->addMinutes(5)
            );
        } elseif ($this->tiktok_photo) {
            return Storage::temporaryUrl(
                str_replace('images/tiktok/', 'images/tiktok/thumbnail/', $this->tiktok_photo),
                now()->addMinutes(5)
            );
        } elseif ($this->weibo_photo) {
            return Storage::temporaryUrl(
                str_replace('images/weibo/', 'images/weibo/thumbnail/', $this->weibo_photo),
                now()->addMinutes(5)
            );
        } elseif ($this->xiaohongshu_photo) {
            return Storage::temporaryUrl(
                str_replace('images/xiaohongshu/', 'images/xiaohongshu/thumbnail/', $this->xiaohongshu_photo),
                now()->addMinutes(5)
            );
        }

        return url('img/profile-placeholder.png');
    }

    public function getTotalFollowersAttribute()
    {
        return $this->facebook_followers + $this->instagram_followers + $this->twitter_followers + $this->youtube_subscribers;
    }

    public function getPostEngagementRateRawAttribute()
    {
        if ($this->facebook_followers + $this->instagram_followers + $this->twitter_followers) {
            return round(
                ($this->facebook_engagement_rate_post + $this->instagram_engagement_rate_post + $this->twitter_engagement_rate)
                /
                ($this->facebook_followers + $this->instagram_followers + $this->twitter_followers)
                * 100,
                2
            );
        } else {
            return null;
        }
    }

    public function getPostEngagementRateAttribute()
    {
        if ($this->facebook_followers + $this->instagram_followers + $this->twitter_followers) {
            return number_format(
                ($this->facebook_engagement_rate_post + $this->instagram_engagement_rate_post + $this->twitter_engagement_rate)
                /
                ($this->facebook_followers + $this->instagram_followers + $this->twitter_followers)
                * 100,
                2
            );
        } else {
            return null;
        }
    }

    public function getVideoEngagementRateRawAttribute()
    {
        if ($this->facebook_followers + $this->instagram_followers + $this->youtube_subscribers) {
            return round(
                ($this->facebook_engagement_rate_video + $this->instagram_engagement_rate_video + $this->youtube_view_rate)
                /
                ($this->facebook_followers + $this->instagram_followers + $this->youtube_subscribers)
                * 100,
                2
            );
        } else {
            return null;
        }
    }

    public function getVideoEngagementRateAttribute()
    {
        if ($this->facebook_followers + $this->instagram_followers + $this->youtube_subscribers) {
            return number_format(
                ($this->facebook_engagement_rate_video + $this->instagram_engagement_rate_video + $this->youtube_view_rate)
                /
                ($this->facebook_followers + $this->instagram_followers + $this->youtube_subscribers)
                * 100,
                2
            );
        } else {
            return null;
        }
    }

    public function campaignsAuthUserCanView()
    {
        $query = $this->belongsToMany('App\Models\Campaign')
            ->whereIn('status', [Campaign::STATUS_ACCEPTED, Campaign::STATUS_COMPLETED])
            ->withTimestamps();

        $query = Campaign::addQueryForUserRestriction($query);

        return $query;
    }

    public function campaigns()
    {
        return $this->belongsToMany('App\Models\Campaign')->withTimestamps();
    }

    public function getRequiredFieldsForCampaignCreate()
    {
        $errors = [];
        if (!$this->getDescriptionPpt()) {
            $errors[] = 'description';
        }
        if (!$this->interestsCore->count() > 0) {
            $errors[] = 'interests';
        }
        if (!$this->professionsCore->count() > 0) {
            $errors[] = 'professions';
        }
        if (!$this->gender) {
            $errors[] = 'gender';
        }
        return $errors;
    }

    public function getInitInstagramSocapiData()
    {
        if ($this->instagramSocapiData) {
            $initData = $this->instagramSocapiData->toArray();

            if (!isset($initData['user_profile']['geo'])) {
                $initData['user_profile']['geo'] = [];
            }
            if (!isset($initData['user_profile']['contacts'])) {
                $initData['user_profile']['contacts'] = [];
            }
            if (!isset($initData['user_profile']['top_hashtags'])) {
                $initData['user_profile']['top_hashtags'] = [];
            }
            if (!isset($initData['user_profile']['stat_history'])) {
                $initData['user_profile']['stat_history'] = [];
            }
            if (!isset($initData['user_profile']['top_mentions'])) {
                $initData['user_profile']['top_mentions'] = [];
            }
            if (!isset($initData['user_profile']['brand_affinity'])) {
                $initData['user_profile']['brand_affinity'] = [];
            }
            if (!isset($initData['user_profile']['interests'])) {
                $initData['user_profile']['interests'] = [];
            }
            if (!isset($initData['user_profile']['relevant_tags'])) {
                $initData['user_profile']['relevant_tags'] = [];
            }
            if (!isset($initData['user_profile']['similar_users'])) {
                $initData['user_profile']['similar_users'] = [];
            }
            if (!isset($initData['user_profile']['fullname'])) {
                $initData['user_profile']['fullname'] = [];
            }
            if (!isset($initData['user_profile']['posts_count'])) {
                $initData['user_profile']['posts_count'] = [];
            }
            if (!isset($initData['user_profile']['engagements'])) {
                $initData['user_profile']['engagements'] = [];
            }
            if (!isset($initData['user_profile']['engagement_rate'])) {
                $initData['user_profile']['engagement_rate'] = [];
            }
            if (!isset($initData['user_profile']['avg_likes'])) {
                $initData['user_profile']['avg_likes'] = [];
            }
            if (!isset($initData['user_profile']['avg_comments'])) {
                $initData['user_profile']['avg_comments'] = [];
            }
            if (!isset($initData['user_profile']['avg_views'])) {
                $initData['user_profile']['avg_views'] = [];
            }
            return $initData;
        }

        return [];
    }
}
