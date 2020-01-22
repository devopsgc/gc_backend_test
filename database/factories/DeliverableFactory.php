<?php

use Faker\Generator as Faker;
use App\Models\Deliverable;
use App\Models\Campaign;
use App\Models\Record;

$factory->define(Deliverable::class, function (Faker $faker) {
    return [
        'campaign_id' => factory(Campaign::class),
        'record_id' => factory(Record::class),
    ];
});
