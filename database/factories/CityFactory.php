<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\City;
use Faker\Generator as Faker;

$factory->define(City::class, function (Faker $faker) {
    return [
        'api_id' => 2935022,
        'name' => 'Dresden',
        'country' => 'DE',
        'lat' => 51.05,
        'lon' => 13.74,
    ];
});