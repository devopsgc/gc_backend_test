<?php

namespace App\Imports;

use App\Models\Record;
use App\Models\Country;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RecordsImport implements
    ToModel,
    WithHeadingRow,
    WithChunkReading,
    WithBatchInserts
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $country = Country::where('iso_3166_2', '=', $this->country_code)->first();


        /*
        $row['youtube_id']
        $row['youtube_subscribers']
        $row['youtube_viewthrough_rates']
        $row['youtube_external_rates_cost_per_video']
        $row['weibo_id']
        $row['weibo_followers']
        $row['weibo_engagement_rates']
        $row['weibo_external_rates_cost_per_post']
        $row['weibo_external_rates_cost_per_livestream']
        $row['miaopai_id']
        $row['miaopai_followers']
        $row['miaopai_engagement_rates']
        $row['miaopai_external_rates_cost_per_livestream']
        $row['yizhibo_id']
        $row['yizhibo_followers']
        $row['yizhibo_engagement_rates']
        $row['yizhibo_external_rates_cost_per_livestream']
        $row['audience_gender_male']
        $row['audience_gender_female']
        $row['audience_age_13_17']
        $row['audience_age_18_24']
        $row['audience_age_25_34']
        $row['audience_age_35_44']
        $row['audience_age45_64']
        $row['audience_interest']
        */
        /*
    protected $fillable = ['affiliations', 'facebook_id', 'instagram_id', 'blog_url', 'youtube_id', 'weibo_id', 'xiaohongshu_id', 'miaopai_id', 'yizhibo_id',
    ];
        */

        $record = new Record;
        $record->country_code = $country->iso_3166_2;
        $record->name = trim($row['name']);

        $record->gender = null;
        if (isset($row['gender'])) {
            $gender = strtolower(trim($row['gender']));
            if ($gender === 'male') $record->gender = 'M';
            if ($gender === 'female') $record->gender = 'F';
        }

        $record->date_of_birth = null;
        if (isset($row['date_of_birth'])) {
            $record->date_of_birth = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_of_birth']);
        }

        $record->description = null;
        if (isset($row['description'])) {
            $record->description = trim($row['description']);
        }

        $record->email = null;
        if (isset($row['email'])) {
            if (strpos($row['email'], '@') !== false) {
                $record->email = trim($row['email']);
            }
        }

        $record->calling_code = null;
        $record->phone = null;
        if (isset($row['phone'])) {
            $record->calling_code = $country->calling_code;
            $record->phone = trim($row['phone']);
        }

        $record->line = null;
        if (isset($row['line'])) {
            $record->line = trim($row['line']);
        }

        $record->verticals = null;
        if (isset($row['vertical'])) {
            $record->verticals = trim($row['vertical']);
        }

        $record->affiliations = null;
        if (isset($row['company_affliation'])) {
            $record->affiliations = trim($row['company_affliation']);
        }

        $record->campaigns = null;
        if (isset($row['past_campaigns'])) {
            $record->campaigns = trim($row['past_campaigns']);
        }

        $record->facebook_id = null;
        if (isset($row['facebook_id'])) {
            if (strpos($row['facebook_id'], 'facebook.com/') !== false) {
                $tokens = explode('facebook.com/', $row['facebook_id'], 2);
                if (isset($tokens[1])) {
                    $tokens = explode('/', $tokens[1]);
                    if (isset($tokens[0])) {
                        $tokens = explode('?', $tokens[0], 2);
                        if (isset($tokens[0])) {
                            $record->facebook_id = urldecode($tokens[0]);
                        }
                    }
                }
            }
        }

        $record->instagram_id = null;
        if (isset($row['instagram_id'])) {
            if (strpos($row['instagram_id'], 'instagram.com/') !== false) {
                $tokens = explode('instagram.com/', $row['instagram_id'], 2);
                if (isset($tokens[1])) {
                    $tokens = explode('/', $tokens[1]);
                    if (isset($tokens[0])) {
                        $tokens = explode('?', $tokens[0], 2);
                        if (isset($tokens[0])) {
                            $record->instagram_id = urldecode($tokens[0]);
                        }
                    }
                }
            }
        }

        $record->blog_url = null;
        if (isset($row['blog_website_id'])) {
            $record->blog_url = trim($row['blog_website_id']);
        }

        $record->youtube_id = null;
        if (isset($row['youtube_id'])) {
            if (strpos($row['youtube_id'], 'youtube.com/') !== false) {
                $tokens = explode('youtube.com/', $row['youtube_id'], 2);
                if (isset($tokens[1])) {
                    $tokens = explode('?', $tokens[1], 2);
                    if (isset($tokens[0])) {
                        $record->youtube_id = urldecode($tokens[0]);
                    }
                }
            }
        }

        /*
        $record->weibo_id = $row->weibo_id ? $row->weibo_id : null;
        if ($record->weibo_id == 'NA') $record->weibo_id = null;
        if ($record->weibo_id == '-') $record->weibo_id = null;
        //$record->weibo_followers = is_integer($row->weibo_followers) ? $row->weibo_followers : null;
        //$record->weibo_engagement_rate = is_double($row->weibo_engagement_rate) ? $row->weibo_engagement_rate : null;
        //$record->weibo_external_rate_post = is_double($row->weibo_external_rate_post) ? $row->weibo_external_rate_post : null;
        //$record->weibo_external_rate_livestream = is_double($row->weibo_external_rate_livestream) ? $row->weibo_external_rate_livestream : null;

        $record->miaopai_id = $row->miaopai_id ? $row->miaopai_id : null;
        if ($record->miaopai_id == 'NA') $record->miaopai_id = null;
        if ($record->miaopai_id == '-') $record->miaopai_id = null;
        $record->miaopai_id = str_replace('http://www.miaopai.com/u/', '', $record->miaopai_id);
        if ( ! $record->miaopai_id) $record->miaopai_id = null;
        //$record->miaopai_followers = is_integer($row->miaopai_followers) ? $row->miaopai_followers : null;
        //$record->miaopai_engagement_rate = is_double($row->miaopai_engagement_rate) ? $row->miaopai_engagement_rate : null;
        //$record->miaopai_external_rate_livestream = is_double($row->miaopai_external_rate_livestream) ? $row->miaopai_external_rate_livestream : null;

        $record->yizhibo_id = $row->yizhibo_id ? $row->yizhibo_id : null;
        if ($record->yizhibo_id == 'NA') $record->yizhibo_id = null;
        if ($record->yizhibo_id == '-') $record->yizhibo_id = null;

        $record->yizhibo_id = str_replace('http://new.yizhibo.com/member/personel/user_info?memberid=', '', $record->yizhibo_id);
        $record->yizhibo_id = str_replace('http://www.yizhibo.com/member/personel/user_info?memberid=', '', $record->yizhibo_id);
        $record->yizhibo_id = str_replace('&nest_url=', '', $record->yizhibo_id);
        if ( ! $record->yizhibo_id) $record->yizhibo_id = null;
        //$record->yizhibo_followers = is_integer($row->yizhibo_followers) ? $row->yizhibo_followers : null;
        //$record->yizhibo_engagement_rate = is_double($row->yizhibo_engagement_rate) ? $row->yizhibo_engagement_rate : null;
        //$record->yizhibo_external_rate_livestream = is_double($row->yizhibo_external_rate_livestream) ? $row->yizhibo_external_rate_livestream : null;
        */
        return $record;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function batchSize(): int
    {
        return 100;
    }
}
