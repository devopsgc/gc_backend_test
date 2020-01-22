<?php

namespace App\Models;

use Validator;

class Campaign extends Model
{
    const STATUS_DRAFT = 'Draft';
    const STATUS_ACCEPTED = 'Accepted';
    const STATUS_REJECTED = 'Rejected';
    const STATUS_CANCELLED = 'Cancelled';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_DELETED = 'Delete Campaign';

    public static function getAllStatuses()
    {
        return [
            Campaign::STATUS_DRAFT,
            Campaign::STATUS_ACCEPTED,
            Campaign::STATUS_REJECTED,
            Campaign::STATUS_CANCELLED,
            Campaign::STATUS_COMPLETED,
            Campaign::STATUS_DELETED
        ];
    }

    protected $guarded = [];

    protected $dates = [
        'start_at',
        'end_at',
        'notify_completed_at',
    ];

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by_user_id');
    }

    public function country()
    {
        return $this->hasOne('App\Models\Country', 'iso_3166_2', 'country_code');
    }

    public function records()
    {
        return $this->belongsToMany('App\Models\Record')->withPivot('package_price')->withTimestamps();
    }

    public function links()
    {
        return $this->hasMany('App\Models\Deliverable')->has('record');
    }

    public function valueAddedPosts()
    {
        return $this->links()->where('billing_type', 'value_added');
    }

    public function deliverables()
    {
        return $this->links()->where('billing_type', 'deliverable');
    }

    public function interestsCore()
    {
        return $this->belongsToMany('App\Models\Tag', 'campaign_tags', 'campaign_id', 'tag_id')
            ->whereNull('campaign_tags.deleted_at')
            ->where('campaign_tags.type', 'interest_core')
            ->withTimestamps()->withPivot('deleted_at');
    }

    public function getInterestsDisplayAttribute($value)
    {
        return implode('|', $this->interestsCore->pluck('name')->toArray());
    }

    public function getInterestsDisplayForSelectAttribute($value)
    {
        return implode('|', $this->interestsCore->pluck('name')->toArray());
    }

    public function canEdit()
    {
        return $this->status == Campaign::STATUS_DRAFT ||
            $this->status == Campaign::STATUS_ACCEPTED;
    }

    public function canAccessLinks()
    {
        return $this->status == Campaign::STATUS_ACCEPTED || $this->status == Campaign::STATUS_COMPLETED;
    }

    public function getTotalInfluencerFollowersAttribute()
    {
        return $this->records->sum('total_followers');
    }

    public function getInfluencerPostEngagementRateAttribute()
    {
        return $this->records->avg('post_engagement_rate_raw');
    }

    public function getInfluencerVideoEngagementRateAttribute()
    {
        return $this->records->avg('video_engagement_rate_raw');
    }

    public function getValidNextStatuses()
    {
        switch ($this->status) {
            case Campaign::STATUS_DRAFT:
                return [Campaign::STATUS_ACCEPTED, Campaign::STATUS_REJECTED, Campaign::STATUS_CANCELLED, Campaign::STATUS_DELETED];
            case Campaign::STATUS_ACCEPTED:
                return [Campaign::STATUS_COMPLETED, Campaign::STATUS_CANCELLED];
            case Campaign::STATUS_REJECTED:
                return [Campaign::STATUS_ACCEPTED, Campaign::STATUS_DELETED];
            case Campaign::STATUS_CANCELLED:
                return [Campaign::STATUS_DELETED];
            case Campaign::STATUS_COMPLETED:
                return [Campaign::STATUS_ACCEPTED];
            default:
                return [];
        }
    }

    public function getDownloadPptPath()
    {
        return 'campaigns/' . $this->id . '/ppt';
    }

    public function getDownloadExcelPath()
    {
        return 'campaigns/' . $this->id . '/xls';
    }

    public function getPath()
    {
        return url('campaigns/' . $this->id);
    }

    public static function validateCampaignFails($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|max:255',
            'currency_code' => 'required|string',
            'package_price' => 'array',
            'package_price.*' => 'numeric',
            'total_price' => ['numeric', function ($attribute, $value, $fail) use ($request) {
                if ($value < 0) {
                    $fail('This should not be negative.');
                }
                if ($value > array_sum($request->package_price)) {
                    $fail('This should not be greater than total nett cost.');
                }
            }]
        ]);

        $validator->after(function ($validator) {
            $selected = session('selected');

            $errors = [];
            $records = Record::find($selected);

            foreach ($records as $record) {
                $errors = array_merge($errors, $record->getRequiredFieldsForCampaignCreate());
            }

            if ($errors) {
                session()->flash('warning', '<strong>Create campaign failed.</strong> All records must have a ' . implode(', ', array_unique($errors)) . '.');
                $validator->errors()->add('error', 'fail this validator for for checking required field in records');
            }
        });

        if ($validator->fails()) {
            return $validator;
        }

        return false;
    }

    public function isDraft()
    {
        return $this->status === Campaign::STATUS_DRAFT;
    }

    public static function addQueryForUserRestriction($query)
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAdmin()) {
            if (auth()->user()->isManager()) {
                $query->where(function ($query1) {
                    return $query1->whereIn('country_code', auth()->user()->getRestrictedCountries()->pluck('iso_3166_2')->toArray())
                        ->orWhere('created_by_user_id', auth()->user()->id);
                });
            } else {
                $query = $query->where('created_by_user_id', auth()->user()->id);
            }
        }

        return $query;
    }
}
