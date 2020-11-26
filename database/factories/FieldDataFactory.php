<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\FieldData;
use Faker\Generator as Faker;

$factory->define(FieldData::class, function (Faker $faker) {
    return [
        'node_data_id' => $faker->numberBetween($min = 1, $max = 2000),
        'field_id' => $faker->numberBetween($min = 1, $max = 40),
        'value' => $faker->randomFloat($nbMaxDecimals = 2, $min = -5.75, $max = 11.00),
    ];
});