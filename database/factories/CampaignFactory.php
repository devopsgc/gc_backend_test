<?php

use Faker\Generator as Faker;
use App\Models\Campaign;
use App\Models\User;
use Carbon\Carbon;

$factory->define(Campaign::class, function (Faker $faker) {
    return [
        'country_code' => 'SG',
        'currency_code' => 'SGD',
        'created_by_user_id' => factory(User::class),
        'name' => $faker->catchPhrase,
        'brand' => $faker->company,
        'status' => Campaign::STATUS_ACCEPTED,
        'start_at' => Carbon::now()->subMonth(1),
        'end_at' => Carbon::now()->addMonth(1),
    ];
});
