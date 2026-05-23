<?php
namespace Database\Factories;

use App\Models\{User, Room};
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    public function definition(): array
    {
        $checkIn  = $this->faker->dateTimeBetween('+1 day', '+30 days');
        $checkOut = $this->faker->dateTimeBetween($checkIn, '+40 days');

        return [
            'user_id'     => User::factory(),
            'room_id'     => Room::factory(),
            'check_in'    => $checkIn,
            'check_out'   => $checkOut,
            'guests'      => $this->faker->numberBetween(1, 3),
            'total_price' => $this->faker->randomFloat(2, 100, 2000),
            'status'      => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
        ];
    }
}