<?php

namespace App\Http\Controllers;

use App\Exports\RecordsExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Traits\CampaignSessionHelper;
use App\Helpers\GenerateDeckPptHelper;
use App\Helpers\RecordQueryHelper;
use App\Helpers\TagHelper;
use App\Models\Description;
use App\Models\Language;
use App\Models\Country;
use App\Models\Record;
use App\Models\Tag;
use Carbon\Carbon;
use Validator;
use Session;
use Image;
use Auth;

class RecordController extends Controller
{
    use CampaignSessionHelper;

    public function index(Request $request)
    {
        $this->authorize('index', Record::class);

        $countries = Auth::user()->countries->pluck('iso_3166_2')->toArray();
        $this->data['countries'] = Country::getAllEnabledCountries($countries);
        $this->data['affiliations'] = Tag::affiliationTags()->pluck('name')->toArray();
        $this->data['professions'] = Tag::professionsTags()->orderBy('name')->pluck('name')->toArray();
        $this->data['interests'] = Tag::interestTags()->orderBy('name')->pluck('name')->toArray();

        $query = RecordQueryHelper::generateQuery($request, $countries);

        if ($request->submit == 'xls') {
            $fileName = 'influencers_'.Carbon::now()->format('Ymd_his').'.xlsx';
            return (new RecordsExport($query->take(2000)->pluck('id')->toArray()))->download($fileName);
        }

        if ($request->submit == 'pptx')
        {
            $selected = session('selected');

            if (!GenerateDeckPptHelper::validateRecordsHaveDescription($selected)) {
                return redirect()->back()->with('warning', '<strong>Download failed.</strong> All records must have a PowerPoint description in english before they can be exported.');
            }
            GenerateDeckPptHelper::createDeckReport($selected, $request->language);

            $request->merge(['submit' => '']);
            return redirect()->back()->with('message', '<strong>Generating report.</strong> An email will be send to you once your report is ready to be downloaded.');
        }

        $this->data['records'] = $this->customPagination($query);
        $this->data['records']->appends($request->except('page'));
        $this->data['title'] = 'Influencers';

        return view('recordIndex', $this->data);
    }

    protected function customPagination($query)
    {
        // todo: hack for slow query when trying to paginate with page > 1000
        $recordCollection = $query->select('id')->paginate(20);
        $newRecordCollection = [];
        foreach ($recordCollection as &$recordId) {
            $newRecordCollection[] = Record::find($recordId->id)->load(['affiliationTags', 'interestsCore', 'professionsCore', 'country']);
        }
        $recordCollection->setCollection(collect($newRecordCollection));
        return $recordCollection;
    }

    public function create(Request $request)
    {
        $this->authorize('create', Record::class);

        if ($request->q)
        {
            $records = Record::orderBy('id', 'desc')
                ->where('name', 'LIKE', '%'.$request->q.'%')
                ->paginate(10);
            foreach ($records as &$record)
            {
                $record->photo = $record->display_photo;
            }
            return $records;
        }

        $countries = Auth::user()->countries->pluck('iso_3166_2')->toArray();
        $this->data['countries'] = Country::when($countries, function ($query) use ($countries) {
            return $query->whereIn('iso_3166_2', $countries);
        })->get();

        $this->data['languages'] = Language::get();
        $this->data['title'] = 'Add New Influencer';
        $this->data['interests'] = Tag::getAllInterests();
        $this->data['professions'] = Tag::getAllProfessions();

        return view('recordCreate', $this->data);
    }

    public function store()
    {
        $this->authorize('create', Record::class);
        return $this->create_update(request(), new Record, false);
    }

    public function show(Record $record)
    {
        $this->authorize('view', $record);
        return redirect('records/'.$record->id.'/edit');
    }

    public function edit(Record $record)
    {
        $this->authorize('update', $record);
        if (request('language')) return $record->getDescriptionPpt(request('language'));
        $this->data['record'] = $record;
        $countries = Auth::user()->countries->pluck('iso_3166_2')->toArray();
        $this->data['countries'] = Country::when($countries, function ($query) use ($countries) {
            return $query->whereIn('iso_3166_2', $countries);
        })->get();
        $this->data['languages'] = Language::get();
        $this->data['title'] = $record->name;
        $this->data['interests'] = Tag::getAllInterests();
        $this->data['professions'] = Tag::getAllProfessions();
        $this->data['affiliations'] = Tag::getAllAffiliations();

        $this->data['socialData'] = $record->getInitInstagramSocapiData();
        $this->data['socialMediaPhotos'] = $this->getDifferentSocialMediaPhotos();

        return view('recordEdit', $this->data);
    }
    public function update(Record $record)
    {
        $this->authorize('update', $record);
        return $this->create_update(request(), $record, true);
    }

    public function create_update(Request $request, Record $record, $update)
    {
        if ( ! $update)
        {
            $validator = Validator::make($request->all(), [
                'country_code' => 'required|max:2',
                'name' => [
                    'max:80',
                    'required',
                ],
                'gender' => 'required|max:1',
                'interests' => 'required',
                'professions' => 'required',
                'instagram_id' => [
                    'nullable',
                    'max:160',
                    'required_without_all:youtube_id,facebook_id,twitter_id,tiktok_id',
                    function($attribute, $value, $fail) {
                        if (strpos($value, '/') !== false || strpos($value, '?') !== false) {
                            return $fail('The Instagram ID should not contain question marks or slashes (e.g. The Instagram ID of https://www.instagram.com/xxx/?hl=en is \'xxx\')');
                        }
                    },
                    Rule::unique('records')->where(function ($query) {
                        return $query->whereNull('deleted_at');
                    }),
                ],
                'youtube_id' => [
                    'nullable',
                    'max:160',
                    'required_without_all:instagram_id,facebook_id,twitter_id,tiktok_id',
                    function($attribute, $value, $fail) {
                        if (strpos($value, '/') !== false || strpos($value, '?') !== false) {
                            return $fail('The YouTube ID should not contain question marks or slashes (e.g. The YouTube ID of https://www.youtube.com/channel/xxx/videos is \'xxx\')');
                        }
                    },
                    Rule::unique('records')->where(function ($query) {
                        return $query->whereNull('deleted_at');
                    }),
                ],
                'facebook_id' => [
                    'nullable',
                    'max:160',
                    'required_without_all:instagram_id,youtube_id,twitter_id,tiktok_id',
                    function($attribute, $value, $fail) {
                        if (strpos($value, '/') !== false || strpos($value, '?') !== false) {
                            return $fail('The Facebook ID should not contain question marks or slashes (e.g. The Facebook ID of https://www.facebook.com/xxx/about?lst=zzz is \'xxx\')');
                        }
                    },
                    Rule::unique('records')->where(function ($query) {
                        return $query->whereNull('deleted_at');
                    }),
                ],
                'twitter_id' => [
                    'nullable',
                    'max:160',
                    'required_without_all:instagram_id,youtube_id,facebook_id,tiktok_id',
                    Rule::unique('records')->where(function ($query) {
                        return $query->whereNull('deleted_at');
                    }),
                ],
                'tiktok_id' => [
                    'nullable',
                    'max:160',
                    'required_without_all:instagram_id,youtube_id,facebook_id,twitter_id',
                    Rule::unique('records')->where(function ($query) {
                        return $query->whereNull('deleted_at');
                    }),
                ],
            ]);
        }
        else
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:80',
                'second_name' => 'nullable|max:80',
                'country_code' => 'required|size:2',
                'state' => 'nullable|max:160',
                'city' => 'nullable|max:160',
                'interests' => 'required',
                'professions' => 'required',
                'gender' => 'required|size:1',
                'race' => 'nullable|max:80',
                'date_of_birth' => 'nullable|date',
                'private_notes' => 'nullable|max:4000',
                'email' => 'nullable|email|max:80',
                'phone' => 'nullable|max:240',
                'phone_remarks' => 'nullable|max:4000',
                'line' => 'nullable|max:240',
                'wechat' => 'nullable|max:240',
                'campaigns' => 'nullable|max:4000',
                'affiliations' => 'nullable|max:4000',
                'recommended' => 'nullable|boolean',
                'language' => 'required_with:description_ppt|size:2',
                'description_ppt' => 'required|max:240',
                'instagram_id' => [
                    'nullable',
                    'max:160',
                    'required_without_all:youtube_id,facebook_id,twitter_id,tiktok_id',
                    function($attribute, $value, $fail) {
                        if (strpos($value, '/') !== false || strpos($value, '?') !== false) {
                            return $fail('The Instagram ID should not contain question marks or slashes (e.g. The Instagram ID of https://www.instagram.com/xxx/?hl=en is \'xxx\')');
                        }
                    },
                    Rule::unique('records')->ignore($record->id)->where(function ($query) {
                        return $query->whereNull('deleted_at');
                    }),
                ],
                'instagram_external_rate_post' => 'nullable|numeric',
                'instagram_external_rate_video' => 'nullable|numeric',
                'instagram_external_rate_story' => 'nullable|numeric',
                'youtube_id' => [
                    'nullable',
                    'max:160',
                    'required_without_all:instagram_id,facebook_id,twitter_id,tiktok_id',
                    function($attribute, $value, $fail) {
                        if (strpos($value, '/') !== false || strpos($value, '?') !== false) {
                            return $fail('The YouTube ID should not contain question marks or slashes (e.g. The YouTube ID of https://www.youtube.com/channel/xxx/videos is \'xxx\')');
                        }
                    },
                    Rule::unique('records')->ignore($record->id)->where(function ($query) {
                        return $query->whereNull('deleted_at');
                    }),
                ],
                'youtube_external_rate_video' => 'nullable|numeric',
                'facebook_id' => [
                    'nullable',
                    'max:160',
                    'required_without_all:instagram_id,youtube_id,twitter_id,tiktok_id',
                    function($attribute, $value, $fail) {
                        if (strpos($value, '/') !== false || strpos($value, '?') !== false) {
                            return $fail('The Facebook ID should not contain question marks or slashes (e.g. The Facebook ID of https://www.facebook.com/xxx/about?lst=zzz is \'xxx\')');
                        }
                    },
                    Rule::unique('records')->ignore($record->id)->where(function ($query) {
                        return $query->whereNull('deleted_at');
                    }),
                ],
                'facebook_name' => 'nullable',
                'facebook_followers' => 'nullable|numeric',
                'facebook_engagement_rate_post' => 'nullable|numeric',
                'facebook_engagement_rate_video' => 'nullable|numeric',
                'facebook_external_rate_post' => 'nullable|numeric',
                'facebook_external_rate_video' => 'nullable|numeric',
                'facebook_external_rate_story' => 'nullable|numeric',
                'twitter_id' => [
                    'nullable',
                    'max:160',
                    'required_without_all:instagram_id,youtube_id,facebook_id,tiktok_id',
                    Rule::unique('records')->ignore($record->id)->where(function ($query) {
                        return $query->whereNull('deleted_at');
                    }),
                ],
                'blog_url' => 'max:160',
                'blog_external_rate_post' => 'nullable|numeric',
                'tiktok_id' => [
                    'nullable',
                    'max:160',
                    'required_without_all:instagram_id,youtube_id,facebook_id,twitter_id',
                    Rule::unique('records')->ignore($record->id)->where(function ($query) {
                        return $query->whereNull('deleted_at');
                    }),
                ],
                'weibo_id' => [
                    'nullable',
                    'max:160',
                    Rule::unique('records')->ignore($record->id)->where(function ($query) {
                        return $query->whereNull('deleted_at');
                    }),
                ],
                'weibo_followers' => 'nullable|numeric',
                'weibo_engagement_rate_post' => 'nullable|numeric',
                'weibo_engagement_rate_livestream' => 'nullable|numeric',
                'weibo_external_rate_post' => 'nullable|numeric',
                'weibo_external_rate_livestream' => 'nullable|numeric',
                'xiaohongshu_id' => [
                    'nullable',
                    'max:160',
                    Rule::unique('records')->ignore($record->id)->where(function ($query) {
                        return $query->whereNull('deleted_at');
                    }),
                ],
                'xiaohongshu_followers' => 'nullable|numeric',
                'xiaohongshu_engagements' => 'nullable|numeric',
                'xiaohongshu_engagement_rate' => 'nullable|numeric',
                'xiaohongshu_external_rate' => 'nullable|numeric',
                'miaopai_id' => [
                    'nullable',
                    'max:160',
                    Rule::unique('records')->ignore($record->id)->where(function ($query) {
                        return $query->whereNull('deleted_at');
                    }),
                ],
                'miaopai_external_rate_livestream' => 'nullable|numeric',
                'yizhibo_id' => [
                    'nullable',
                    'max:160',
                    Rule::unique('records')->ignore($record->id)->where(function ($query) {
                        return $query->whereNull('deleted_at');
                    }),
                ],
                'yizhibo_external_rate_livestream' => 'nullable|numeric',
                'wikipedia_id' => 'max:160',
                'photo' => 'nullable|image|max:20000',
                'photo_default' => ['nullable', Rule::in(['instagram', 'youtube', 'facebook', 'twitter', 'tiktok', 'weibo', 'xiaohongshu'])],
            ]);
        }

        $interestErrors = '';
        $interestsRequest = array_map('trim', explode('|', $request->interests));
        foreach ($interestsRequest as $interest) {
            if (! Tag::where('type', 'interest_core')->where('name', $interest)->first()){
                $interestErrors .= $interest;
            }
        }

        $professionErrors = '';
        $professionsRequest = array_map('trim', explode('|', $request->professions));
        foreach ($professionsRequest as $profession) {
            if (! Tag::where('type', 'profession_core')->where('name', $profession)->first()){
                $professionErrors .= $profession;
            }
        }
        $affiliationErrors = '';
        $affiliationsRequest = $request->filled('affiliations') ? array_map('trim', explode('::', $request->affiliations)) : [];
        foreach ($affiliationsRequest as $affiliation) {
            if (! Tag::where('type', 'affiliation')->where('name', $affiliation)->first()){
                $affiliationErrors .= $affiliation;
            }
        }

        $validator->after(function ($validator) use ($interestErrors, $professionErrors, $affiliationErrors) {
            if ($interestErrors) {
                $validator->errors()->add('interests', $interestErrors.' does not exists.');
            }
            if ($professionErrors) {
                $validator->errors()->add('professions', $professionErrors.' does not exists.');
            }
            if ($affiliationErrors) {
                $validator->errors()->add('affiliations', $affiliationErrors.' does not exists.');
            }
        });

        if ($validator->fails()) {
            if ($update) {
                return redirect('records/'.$record->id.'/edit')
                    ->withErrors($validator)
                    ->withInput();
            } else {
                return redirect('records/create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $record->country_code = $request->country_code;
        $record->name = $request->name;
        $record->second_name = $request->second_name ? $request->second_name : null;
        $record->race = $request->race ? $request->race : null;
        $record->state = $request->state ? $request->state : null;
        $record->city = $request->city ? $request->city : null;
        $record->gender = $request->gender ? $request->gender : null;
        $record->date_of_birth = $request->date_of_birth ? $request->date_of_birth : null;
        $record->private_notes = $request->private_notes ? $request->private_notes : null;
        if ($request->language == 'en') {
            $record->description_ppt = $request->description_ppt ? $request->description_ppt : null;
        }
        if ($request->description_ppt) {
            $token = explode('_', $request->language);
            $where['record_id'] = $record->id;
            if (isset($token[0])) $where['iso_639_1'] = $token[0];
            if (isset($token[1])) $where['iso_3166_2'] = $token[1];
            Description::updateOrCreate($where, ['description' => $request->description_ppt]);
        }
        $record->email = $request->email ? $request->email : null;
        $record->phone = $request->phone ? $request->phone : null;
        $record->phone_remarks = $request->phone_remarks ? $request->phone_remarks : null;
        $record->line = $request->line ? $request->line : null;
        $record->wechat = $request->wechat ? $request->wechat : null;
        $record->recommended = $request->recommended ? $request->recommended : null;
        $record->campaigns = $request->campaigns ? $request->campaigns : null;

        $record->photo_default = $request->photo_default ? $request->photo_default : null;
        if ($request->hasFile('photo'))
        {
            if ($record->photo)
            {
                Storage::delete($record->photo);
                Storage::delete(str_replace('images/', 'images/thumbnail/', $record->photo));
            }

            $record->photo = $request->photo->store('images');
            $image = Image::make($request->photo->getPathName());
            $image->fit(400, 400, function ($constraint) {
                $constraint->upsize();
            });
            $image->save($request->photo->getPathName());
            $request->photo->store('images/thumbnail');
        }

        // Facebook
        $record->facebook_id = $request->facebook_id ? $request->facebook_id : null;
        if ($record->facebook_user_page && $record->facebook_update_disabled_at)
        {
            $record->facebook_name = $request->facebook_name ? $request->facebook_name : null;
            $record->facebook_followers = $request->facebook_followers ? $request->facebook_followers : null;
            $record->facebook_engagement_rate_post = $request->facebook_engagement_rate_post ? $request->facebook_engagement_rate_post : null;
            $record->facebook_engagement_rate_video = $request->facebook_engagement_rate_video ? $request->facebook_engagement_rate_video : null;
        }
        if (( ! $record->facebook_user_page && $record->facebook_update_disabled_at) ||
            $record->facebook_id != $record->getOriginal('facebook_id'))
        {
            $record->facebook_followers = null;
            $record->facebook_engagement_rate_post = null;
            $record->facebook_engagement_rate_video = null;
            $record->facebook_updated_at = null;
            $record->facebook_update_succeeded_at = null;
            $record->facebook_update_disabled_at = null;
        }
        $record->facebook_external_rate_post = $request->facebook_external_rate_post ? $request->facebook_external_rate_post : null;
        $record->facebook_external_rate_video = $request->facebook_external_rate_video ? $request->facebook_external_rate_video : null;
        $record->facebook_external_rate_story = $request->facebook_external_rate_story ? $request->facebook_external_rate_story : null;

        // Instagram
        $record->instagram_id = $request->instagram_id ? $request->instagram_id : null;
        if ($record->instagram_update_disabled_at ||
            $record->instagram_id != $record->getOriginal('instagram_id'))
        {
            $record->instagram_followers = null;
            $record->instagram_engagement_rate_post = null;
            $record->instagram_engagement_rate_video = null;
            $record->instagram_updated_at = null;
            $record->instagram_update_succeeded_at = null;
            $record->instagram_update_disabled_at = null;
        }
        $record->instagram_external_rate_post = $request->instagram_external_rate_post ? $request->instagram_external_rate_post : null;
        $record->instagram_external_rate_video = $request->instagram_external_rate_video ? $request->instagram_external_rate_video : null;
        $record->instagram_external_rate_story = $request->instagram_external_rate_story ? $request->instagram_external_rate_story : null;

        // Blog
        $record->blog_url = $request->blog_url ? $request->blog_url : null;
        $record->blog_external_rate_post = $request->blog_external_rate_post ? $request->blog_external_rate_post : null;

        // YouTube
        $record->youtube_id = $request->youtube_id ? $request->youtube_id : null;
        if ($record->youtube_update_disabled_at ||
            $record->youtube_id != $record->getOriginal('youtube_id'))
        {
            $record->youtube_subscribers = null;
            $record->youtube_views = null;
            $record->youtube_view_rate = null;
            $record->youtube_updated_at = null;
            $record->youtube_update_succeeded_at = null;
            $record->youtube_update_disabled_at = null;
        }
        $record->youtube_external_rate_video = $request->youtube_external_rate_video ? $request->youtube_external_rate_video : null;

        // Twitter
        $record->twitter_id = $request->twitter_id ? $request->twitter_id : null;
        if ($record->twitter_update_disabled_at)
        {
            $record->twitter_followers = $request->twitter_followers ? $request->twitter_followers : null;
            $record->twitter_tweets = $request->twitter_tweets ? $request->twitter_tweets : null;
            $record->twitter_engagement_rate = $request->twitter_engagement_rate ? $request->twitter_engagement_rate : null;
        }
        if ($record->twitter_id != $record->getOriginal('twitter_id'))
        {
            $record->twitter_followers = null;
            $record->twitter_tweets = null;
            $record->twitter_engagement_rate = null;
            $record->twitter_updated_at = null;
            $record->twitter_update_succeeded_at = null;
            $record->twitter_update_disabled_at = null;
        }

        // TikTok
        $record->tiktok_id = $request->tiktok_id ? $request->tiktok_id : null;
        if ($record->tiktok_update_disabled_at)
        {
            $record->tiktok_followers = $request->tiktok_followers ? $request->tiktok_followers : null;
            $record->tiktok_engagements = $request->tiktok_engagements ? $request->tiktok_engagements : null;
            $record->tiktok_engagement_rate_post = $request->tiktok_engagement_rate_post ? $request->tiktok_engagement_rate_post : null;
        }
        if ($record->tiktok_id != $record->getOriginal('tiktok_id'))
        {
            $record->tiktok_followers = null;
            $record->tiktok_engagements = null;
            $record->tiktok_engagement_rate_post = null;
            $record->tiktok_updated_at = null;
            $record->tiktok_update_succeeded_at = null;
            $record->tiktok_update_disabled_at = null;
        }
        $record->tiktok_external_rate_post = $request->tiktok_external_rate_post ? $request->tiktok_external_rate_post : null;

        // Weibo
        $record->weibo_id = $request->weibo_id ? $request->weibo_id : null;
        if ($record->weibo_update_disabled_at)
        {
            $record->weibo_followers = $request->weibo_followers ? $request->weibo_followers : null;
            $record->weibo_engagement_rate_post = $request->weibo_engagement_rate_post ? $request->weibo_engagement_rate_post : null;
            $record->weibo_engagement_rate_livestream = $request->weibo_engagement_rate_livestream ? $request->weibo_engagement_rate_livestream : null;
        }
        if ($record->weibo_id != $record->getOriginal('weibo_id'))
        {
            $record->weibo_followers = null;
            $record->weibo_engagement_rate_post = null;
            $record->weibo_engagement_rate_livestream = null;
            $record->weibo_updated_at = null;
            $record->weibo_update_succeeded_at = null;
            $record->weibo_update_disabled_at = null;
        }
        $record->weibo_external_rate_post = $request->weibo_external_rate_post ? $request->weibo_external_rate_post : null;
        $record->weibo_external_rate_livestream = $request->weibo_external_rate_livestream ? $request->weibo_external_rate_livestream : null;

        // XiaoHongShu
        $record->xiaohongshu_id = $request->xiaohongshu_id ? $request->xiaohongshu_id : null;
        if ($record->xiaohongshu_update_disabled_at)
        {
            $record->xiaohongshu_followers = $request->xiaohongshu_followers ? $request->xiaohongshu_followers : null;
            $record->xiaohongshu_engagement_rate = $request->xiaohongshu_engagement_rate ? $request->xiaohongshu_engagement_rate : null;
        }
        if ($record->xiaohongshu_id != $record->getOriginal('xiaohongshu_id'))
        {
            $record->xiaohongshu_followers = null;
            $record->xiaohongshu_engagements = null;
            $record->xiaohongshu_engagement_rate = null;
            $record->xiaohongshu_updated_at = null;
            $record->xiaohongshu_update_succeeded_at = null;
            $record->xiaohongshu_update_disabled_at = null;
        }
        $record->xiaohongshu_external_rate = $request->xiaohongshu_external_rate ? $request->xiaohongshu_external_rate : null;

        // MiaoPai
        $record->miaopai_id = $request->miaopai_id ? $request->miaopai_id : null;
        $record->miaopai_external_rate_livestream = $request->miaopai_external_rate_livestream ? $request->miaopai_external_rate_livestream : null;

        // YiZhiBo
        $record->yizhibo_id = $request->yizhibo_id ? $request->yizhibo_id : null;
        $record->yizhibo_external_rate_livestream = $request->yizhibo_external_rate_livestream ? $request->yizhibo_external_rate_livestream : null;

        // Wikipedia
        $record->wikipedia_id = $request->wikipedia_id ? $request->wikipedia_id : null;
        $record->save();

        TagHelper::createOrRestoreInterestTags($record, $interestsRequest);
        TagHelper::createOrRestoreProfessionTags($record, $professionsRequest);
        TagHelper::createOrRestoreAffiliationTags($record, $affiliationsRequest);

        Session::forget('photo');

        return redirect('records/'.$record->id.'/edit')->with('status', 'The data has been saved.');
    }

    public function destroy(Record $record)
    {
        $this->authorize('delete', $record);
        $this->removeSelection($record->id);
        $record->delete();
        return redirect('records');
    }

    public function socapi($id)
    {
        $record = Record::findOrFail($id);
        $this->authorize('socapi', $record);
        return json_decode(json_encode($record->instagramSocapiData));
    }

    protected function getDifferentSocialMediaPhotos()
    {
        return [
            [
                'type' => 'instagram',
                'photoAttribute' => 'instagram_photo_url',
                'tag' => 'IG',
            ],
            [
                'type' => 'youtube',
                'photoAttribute' => 'youtube_photo_url',
                'tag' => 'YT',
            ],
            [
                'type' => 'facebook',
                'photoAttribute' => 'facebook_photo_url',
                'tag' => 'FB',
            ],
            [
                'type' => 'twitter',
                'photoAttribute' => 'twitter_photo_url',
                'tag' => 'TW',
            ],
            [
                'type' => 'tiktok',
                'photoAttribute' => 'tiktok_photo_url',
                'tag' => 'TT',
            ],
            [
                'type' => 'weibo',
                'photoAttribute' => 'weibo_photo_url',
                'tag' => 'WB',
            ],
            [
                'type' => 'xiaohongshu',
                'photoAttribute' => 'xiaohongshu_photo_url',
                'tag' => 'XHS',
            ],
        ];
    }

}
