<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class HotelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => $this->faker->company() . ' Hotel',
            'city'        => $this->faker->city(),
            'address'     => $this->faker->address(),
            'description' => $this->faker->paragraph(),
            'stars'       => $this->faker->numberBetween(1, 5),
            'phone'       => $this->faker->phoneNumber(),
            'email'       => $this->faker->companyEmail(),
        ];
    }
}