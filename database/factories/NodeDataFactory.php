<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\NodeData;
use Faker\Generator as Faker;

$factory->define(NodeData::class, function (Faker $faker) {
    return [
        'latitude' => $faker->latitude($min = -90, $max = 90),
        'longitude' => $faker->longitude($min = -180, $max = 180),
        'payload' => $faker->uuid(),
        'snr' => $faker->randomFloat($nbMaxDecimals = 2, $min = -5.75, $max = 11.00),
        'rssi' => $faker->numberBetween($min = -110, $max = -30),
        'node_id' => $faker->numberBetween($min = 1, $max = 20),
    ];
});