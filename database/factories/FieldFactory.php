<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Field;
use Faker\Generator as Faker;

$factory->define(Field::class, function (Faker $faker) {
    return [
        'name' => $faker->isbn10,
        'node_id' => $faker->numberBetween($min = 1, $max = 20),
        'visible' => $faker->numberBetween($min = 0, $max = 1),
        'unit' => $faker->randomElement($array = array ('C°','V','bar')),
        'primarycolor' => $faker->hexcolor,
        'secondarycolor' => $faker->hexcolor,
        'isdashed' => $faker->numberBetween($min = 0, $max = 1),
        'isfilled' => $faker->numberBetween($min = 0, $max = 1),
        'exceeded' => $faker->numberBetween($min = 0, $max = 1)

    ];
});