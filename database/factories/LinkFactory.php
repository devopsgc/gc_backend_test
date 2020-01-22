<?php

use Faker\Generator as Faker;
use App\Models\Deliverable;
use App\Models\Link;

$factory->define(Link::class, function (Faker $faker) {
    return [
        'deliverable_id' => factory(Deliverable::class),
    ];
});
