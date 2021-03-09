<?php

namespace Database\Factories;

use App\Models\NodeData;
use Illuminate\Database\Eloquent\Factories\Factory;

class NodeDataFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NodeData::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'latitude' => $this->faker->latitude($min = -90, $max = 90),
            'longitude' => $this->faker->longitude($min = -180, $max = 180),
            'payload' => $this->faker->uuid(),
            'snr' => $this->faker->randomFloat($nbMaxDecimals = 2, $min = -5.75, $max = 11.00),
            'rssi' => $this->faker->numberBetween($min = -110, $max = -30),
            //'node_id' => $this->faker->numberBetween($min = 1, $max = 80),
            'created_at' => $this->faker->dateTimeBetween($startDate = '-1 days', $endDate = 'now', $timezone = 'Europe/Berlin')
        ];
    }
}
