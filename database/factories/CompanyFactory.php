<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Company;
use Faker\Generator as Faker;

$factory->define(Company::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'city' => $faker->city,
        'country' => $faker->country,
        'user_id' => $faker->numberBetween($min = 1, $max = 2),
    ];
});
