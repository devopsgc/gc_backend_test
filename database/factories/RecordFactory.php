<?php

use Faker\Generator as Faker;
use App\Models\Record;

$factory->define(App\Models\Record::class, function (Faker $faker) {
    return [
        'country_code' => 'SG',
        'name' => $faker->name,
        'gender' => $faker->randomElement(['M', 'F']),
        'description' => $faker->text(100),
        'description_ppt' => $faker->text(100),
        'email' => $faker->email,
    ];
});
