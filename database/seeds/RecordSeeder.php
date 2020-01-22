<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\Helpers\TagHelper;
use App\Models\Record;
use App\Models\Tag;

class RecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        Artisan::call('import:records', ['file_name' => 'GC Data List - Australia.xlsx', 'country_code' => 'AU']);
        Artisan::call('import:records', ['file_name' => 'GC Data List - Hong Kong.xlsx', 'country_code' => 'HK']);
        // Artisan::call('import:records', ['file_name' => 'GC Data List - Japan.xlsx', 'country_code' => 'JP']);
        // Artisan::call('import:records', ['file_name' => 'GC Data List - Singapore.xlsx', 'country_code' => 'SG']);
        // Artisan::call('import:records', ['file_name' => 'GC Data List - Korea.xlsx', 'country_code' => 'KR']);
        // Artisan::call('import:records', ['file_name' => 'GC Data List - Philippines.xlsx', 'country_code' => 'PH']);
        // Artisan::call('import:records', ['file_name' => 'GC Data List - Taiwan.xlsx', 'country_code' => 'TW']);
        // Artisan::call('import:records', ['file_name' => 'GC Data List - Thailand.xlsx', 'country_code' => 'TH']);
        // Artisan::call('import:records', ['file_name' => 'GC Data List - USA.xlsx', 'country_code' => 'US']);
        // Artisan::call('import:records', ['file_name' => 'GC Data List - Malaysia.xlsx', 'country_code' => 'MY']);
        // Artisan::call('import:records', ['file_name' => 'GC Data List - Vietnam.xlsx', 'country_code' => 'VN']);

        // Artisan::call('import:records', ['file_name' => 'GC Data List - Testing Singapore.xlsx', 'country_code' => 'SG']);
        // Artisan::call('import:records', ['file_name' => 'GC Data List - Testing Thailand.xlsx', 'country_code' => 'TH']);
        // Artisan::call('import:records', ['file_name' => 'GC Data List - Testing Vietnam.xlsx', 'country_code' => 'VN']);

        $tags = Tag::all();
        Record::chunk(100, function ($records) use ($tags, $faker) {
            foreach ($records as $record) {
                TagHelper::restoreOrCreateTagRecordPivot($tags->where('type', 'interest_core')->random(), $record);
                TagHelper::restoreOrCreateTagRecordPivot($tags->where('type', 'profession_core')->random(), $record);
                if ( ! $record->description) {
                    // $record->update(['description' => $faker->text(100)]);
                    // Description::updateOrCreate($where, ['description' => $request->description_ppt]);
                    $record->descriptions()->create(['description' => $faker->text(100), 'iso_639_1' => 'en']);
                }
            }
        });

        Record::inRandomOrder()->take(50)->chunk(10, function ($records) use ($tags) {
            foreach ($records as $record) {
                TagHelper::restoreOrCreateTagRecordPivot($tags->where('type', 'affliation')->random(), $record);
            }
        });
    }
}
