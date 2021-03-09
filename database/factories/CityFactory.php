<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Model::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'api_id' => 2935022,
            'name' => 'Dresden',
            'country' => 'DE',
            'lat' => 51.05,
            'lon' => 13.74,
        ];
    }
}
