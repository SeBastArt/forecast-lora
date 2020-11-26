<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Facility;
use Faker\Generator as Faker;

$factory->define(Facility::class, function (Faker $faker) {
    return [
        'name' => $faker->username,
        'location' => $faker->citySuffix,
        'company_id' => $faker->numberBetween($min = 1, $max = 2),
    ];
});
