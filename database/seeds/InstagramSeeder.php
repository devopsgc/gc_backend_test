<?php

use Illuminate\Database\Seeder;
use App\Models\Instagram;

class InstagramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Instagram::create([
            'session_id' => '26429129%3AjtCkC8mG3dfOOj%3A26',
            'query_hash' => '66eb9403e44cc12e5b5ecda48b667d41'
        ]);
        Instagram::create([
            'session_id' => '18831097576%3AkueJjwp00K5RpU%3A25',
            'query_hash' => 'f2405b236d85e8296cf30347c9f08c2a'
        ]);
        Instagram::create([
            'session_id' => '18790806411%3AUqz9c5UhbGlaPT%3A13',
            'query_hash' => 'f2405b236d85e8296cf30347c9f08c2a'
        ]);
    }
}
