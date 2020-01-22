<?php

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->getInterestsTagNames() as $tagName) {
            Tag::create(['name' => $tagName, 'type' => 'interest_core']);
        }

        foreach ($this->getProfessionTagNames() as $tagName) {
            Tag::create(['name' => $tagName, 'type' => 'profession_core']);
        }

        foreach ($this->getAffliationTagNames() as $tagName) {
            Tag::create(['name' => $tagName, 'type' => 'affliation']);
        }
    }

    protected function getInterestsTagNames()
    {
        return [
            'Beauty & Cosmetics',
            'Restaurants, Food & Grocery',
            'Friends, Family & Relationships',
            'Toys, Children & Baby',
            'Travel, Tourism & Aviation',
            'Clothes, Shoes, Handbags & Accessories',
            'Fitness & Yoga',
            'Healthy Lifestyle',
            'Music',
            'Pop Culture & Entertainment',
            'Gaming',
            'Activewear',
            'Sports',
            'Electronics & Computers',
            'Camera & Photography',
            'Charity & Social Cause',
            'Business, Finance & Careers',
            'Cars & Motorbikes',
            'Science or Education',
            'Shopping & Retail',
            'Coffee, Tea & Beverages',
            'Beer, Wine & Spirits',
            'Business & Careers',
            'Healthcare & Medicine',
            'Wedding',
            'Tobacco & Smoking',
            'Luxury Goods',
            'Home Decor, Furniture & Garden',
            'Television & Film',
            'Pets',
            'Art & Design',
            'Dance',
            'Anime & Cosplay',
            'Jewellery & Watches',
        ];
    }

    protected function getProfessionTagNames()
    {
        return [
            'Any Sport related jobs',
            'Actor/Actress',
            'Any Business or Finance related jobs',
            'Any Music/ Musician related jobs',
            'Any Commercial related jobs (Doctors, Lawyers, Tea...',
            'Any Entertainer related jobs (TV host, MC, etc)',
            'Any Writer related jobs (Author, Journalist, Write...',
            'Model',
            'Any Media related jobs (Blogger,Iinfluencer, Youtu...',
            'Director/ Producer',
            'Photographer / Videographer',
            'Travel',
            'Any Pageant related jobs (Miss universe/country/ci...',
            'Any Beauty related job (Beauty, Shopping, Fashion ...',
            'Any Culinery related jobs',
            'Any Art related jobs',
            'Gamer',
            'Any Design or Artwork related jobs',
            'Musician',
            'Entertainer',
            'Artist',
            'Content Creator',
            'Food & Beverage',
            'Beauty',
            'Professional Occupation',
        ];
    }

    protected function getAffliationTagNames()
    {
        return [
            'Gushcloud',
            'Ribbit',
            'GTA',
            'GCS',
            'GCX',
            'MADE',
            'Summer',
            'Atelier',
            'PLUS',
        ];
    }
}
