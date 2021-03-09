<?php

namespace Database\Factories;

use App\Models\Field;
use Illuminate\Database\Eloquent\Factories\Factory;

class FieldFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Field::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            //'node_id' => $this->faker->numberBetween($min = 1, $max = 80),
            'visible' => $this->faker->numberBetween($min = 0, $max = 1),
            'unit' => $this->faker->randomElement($array = array ('C°','V','bar')),
            'primary_color' => $this->faker->hexcolor,
            'secondary_color' => $this->faker->hexcolor,
            'is_dashed' => $this->faker->numberBetween($min = 0, $max = 1),
            'is_filled' => $this->faker->numberBetween($min = 0, $max = 1),
            'error_level' => $this->faker->numberBetween($min = 0, $max = 1),
        ];
    }
}