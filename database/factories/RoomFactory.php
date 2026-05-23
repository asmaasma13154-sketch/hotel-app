<?php
namespace Database\Factories;

use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hotel_id'        => Hotel::factory(),
            'number'          => $this->faker->numerify('###'),
            'type'            => $this->faker->randomElement(['simple', 'double', 'suite']),
            'price_per_night' => $this->faker->randomFloat(2, 60, 500),
            'capacity'        => $this->faker->numberBetween(1, 4),
            'description'     => $this->faker->sentence(),
            'status'          => 'available',
        ];
    }
}