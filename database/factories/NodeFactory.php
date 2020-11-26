<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Node;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Node::class, function (Faker $faker) {
    return [
        'name' => $faker->streetName,
        'facility_id' => $faker->numberBetween($min = 1, $max = 10),
        'dev_eui' => $faker->uuid(),
        'node_type_id' => $faker->numberBetween($min = 1, $max = 4),
        'city_id' => $faker->optional($weight = 0.1, $default = 0)->numberBetween($min = 0, $max = 1),
        'errorLevel' => $faker->optional($weight = 0.05, $default = 1)->numberBetween($min = 1, $max = 3),
    ];
});
