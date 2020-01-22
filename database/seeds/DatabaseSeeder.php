<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(CountriesSeeder::class);
        $this->call(LanguageSeeder::class);
        $this->call(InstagramSeeder::class);
        $this->call(TagSeeder::class);
        $this->call(RecordSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(CampaignSeeder::class);
    }
}
