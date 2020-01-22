<?php

use Faker\Generator as Faker;
use Illuminate\Support\Carbon;
use App\Models\Campaign;
use App\Models\Report;
use App\Models\User;

$factory->define(Report::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'campaign_id' => factory(Campaign::class),
        'generated_at' => Carbon::now(),
        'records' => '123'
    ];
});
