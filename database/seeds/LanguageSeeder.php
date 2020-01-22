<?php

use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Language::create([
            'iso_639_1' => 'en',
            'name' => 'English',
            'native' => 'English',
        ]);
        Language::create([
            'iso_639_1' => 'id',
            'name' => 'Indonesian',
            'native' => 'Bahasa Indonesia',
        ]);
        Language::create([
            'iso_639_1' => 'ja',
            'name' => 'Japanese',
            'native' => '日本語',
        ]);
        Language::create([
            'iso_639_1' => 'ko',
            'name' => 'Korean',
            'native' => '한국어',
        ]);
        Language::create([
            'iso_639_1' => 'ms',
            'name' => 'Malay',
            'native' => 'Bahasa Melayu',
        ]);
        Language::create([
            'iso_639_1' => 'th',
            'name' => 'Thai',
            'native' => 'ไทย',
        ]);
        Language::create([
            'iso_639_1' => 'tl',
            'name' => 'Tagalog',
            'native' => 'Wikang Tagalog',
        ]);
        Language::create([
            'iso_639_1' => 'vi',
            'name' => 'Vietnamese',
            'native' => 'Tiếng Việt',
        ]);
        Language::create([
            'iso_639_1' => 'zh',
            'iso_3166_2' => 'cn',
            'name' => 'Mandarin (China)',
            'native' => '普通话',
        ]);
        Language::create([
            'iso_639_1' => 'zh',
            'iso_3166_2' => 'hk',
            'name' => 'Cantonese (Hong Kong)',
            'native' => '廣東話',
        ]);
        Language::create([
            'iso_639_1' => 'zh',
            'iso_3166_2' => 'tw',
            'name' => 'Hokkien (Taiwan)',
            'native' => '福建話',
        ]);
        Language::create([
            'iso_639_1' => 'my',
            'name' => 'Burmese',
            'native' => 'မြန်မာဘာသာ',
        ]);
        Language::create([
            'iso_639_1' => 'ru',
            'name' => 'Russian',
            'native' => 'Русский язык',
        ]);
        Language::create([
            'iso_639_1' => 'es',
            'name' => 'Spanish',
            'native' => 'Español',
        ]);
        Language::create([
            'iso_639_1' => 'pt',
            'name' => 'Portuguese',
            'native' => 'Português',
        ]);
    }
}
