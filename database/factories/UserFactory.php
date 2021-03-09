<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'username' => $this->faker->userName,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'status'  => $this->faker->boolean(80) ? 'active' : $this->faker->randomElement($array = array ('close')),
            'language' => $this->faker->numberBetween($min = 1, $max = 4),
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->streetAddress(),
            'country' => $this->faker->country,
            'roles' => 
                collect([ 
                    $this->faker->randomElement($array = array ('ROLE_SUPPORT','ROLE_ADMIN','ROLE_MANAGEMENT', 'ROLE_FINANCE', 'ROLE_ACCOUNT_MANAGER')
                )])
        ];
    }
}
