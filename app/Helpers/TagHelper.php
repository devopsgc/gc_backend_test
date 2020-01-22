<?php

namespace App\Helpers;

use App\Models\Tag;
use Carbon\Carbon;

class TagHelper
{
    public static function createOrRestoreInterestTags($record, $interestsRequest)
    {
        self::softDeleteAllTagRecordPivot($record->interestsCore, $record);
        self::softDeleteAllTagRecordPivot($record->pptVertical, $record);
        foreach ($interestsRequest as $interest) {
            $interestCoreTag = self::getTagOrCreate([
                'name' => $interest,
                'description' => '',
                'type' => 'interest_core',
            ]);
            self::restoreOrCreateTagRecordPivot($interestCoreTag, $record);
            if ($pptVertical = self::getMappedPptVerticalFromInterest($interest)) {

                $pptVerticalTag = self::getTagOrCreate([
                    'name' => $pptVertical,
                    'description' => '',
                    'type' => 'ppt_vertical',
                ]);
                self::restoreOrCreateTagRecordPivot($pptVerticalTag, $record);
            }
        }
    }

    public static function createOrRestoreProfessionTags($record, $professionsRequest)
    {
        self::softDeleteAllTagRecordPivot($record->professionsCore, $record);
        foreach ($professionsRequest as $profession) {
            $professionCoreTag = self::getTagOrCreate([
                'name' => $profession,
                'description' => '',
                'type' => 'profession_core',
            ]);
            self::restoreOrCreateTagRecordPivot($professionCoreTag, $record);
        }
    }

    public static function createOrRestoreAffiliationTags($record, $affiliationsRequest)
    {
        self::softDeleteAllTagRecordPivot($record->affiliationTags, $record);
        foreach ($affiliationsRequest as $affiliation) {
            $affiliationTag = self::getTagOrCreate([
                'name' => $affiliation,
                'description' => '',
                'type' => 'affiliation',
            ]);
            self::restoreOrCreateTagRecordPivot($affiliationTag, $record);
        }
    }

    public static function getMappedPptVerticalFromInterest($interest)
    {
        $interest = trim($interest);

        if (!array_key_exists($interest, InterestVerticalToPptVerticalHelper::getTransformedData())) {
            return [];
        }

        return InterestVerticalToPptVerticalHelper::getTransformedData()[$interest];
    }

    public static function getMappedInterestCore($vertical, $tranformedArray)
    {
        $vertical = mb_strtolower(trim($vertical));

        if (!array_key_exists($vertical, $tranformedArray)) {
            return [];
        }

        return $tranformedArray[$vertical];
    }

    public static function getMappedProfessionCore($vertical)
    {
        $vertical = mb_strtolower(trim($vertical));

        if (!array_key_exists($vertical, ProfessionTagData::getVerticalToProfessionCore())) {
            return [];
        }

        return ProfessionTagData::getVerticalToProfessionCore()[$vertical];
    }

    public static function getTagOrCreate($tagArray)
    {
        return Tag::firstOrCreate([
            'name' => trim($tagArray['name']),
            'description' => trim($tagArray['description']),
            'type' => trim($tagArray['type']),
        ]);
    }

    public static function softDeleteAllTagRecordPivot($tags, $record)
    {
        foreach ($tags as $tag) {
            \DB::table('record_tags')
                ->where('record_id', $record->id)
                ->where('tag_id', $tag->id)
                ->where('type', $tag->type)
                ->update(['deleted_at' => Carbon::now()]);
        }
    }

    public static function restoreOrCreateTagRecordPivot($tag, $record)
    {
        if (\DB::table('record_tags')
            ->where('record_id', $record->id)
            ->where('tag_id', $tag->id)
            ->where('type', $tag->type)
            ->first()) {
            \DB::table('record_tags')
                ->where('record_id', $record->id)
                ->where('tag_id', $tag->id)
                ->where('type', $tag->type)
                ->update(['deleted_at' => null]);
        } else {
            $record->tags()->attach($tag->id, ['type' => $tag->type]);
        }
    }
}
