<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Room, Reservation};
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_reservation(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create(['status' => 'available', 'price_per_night' => 100]);

        $response = $this->actingAs($user)->post(route('reservations.store'), [
            'room_id'   => $room->id,
            'check_in'  => '2026-07-01',
            'check_out' => '2026-07-04',
            'guests'    => 2,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'room_id' => $room->id,
        ]);
    }

    public function test_guest_cannot_create_reservation(): void
    {
        $room = Room::factory()->create();

        $response = $this->post(route('reservations.store'), [
            'room_id'   => $room->id,
            'check_in'  => '2026-07-01',
            'check_out' => '2026-07-04',
            'guests'    => 1,
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_cancel_own_reservation(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create(['status' => 'occupied']);
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'status'  => 'pending',
        ]);

        $response = $this->actingAs($user)->patch(route('reservations.cancel', $reservation));

        $response->assertRedirect();
        $this->assertEquals('cancelled', $reservation->fresh()->status);
    }

    public function test_user_cannot_cancel_others_reservation(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();
        $room  = Room::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $other->id,
            'room_id' => $room->id,
            'status'  => 'pending',
        ]);

        $response = $this->actingAs($user)->patch(route('reservations.cancel', $reservation));
        $response->assertStatus(403);
    }
    public function test_user_can_view_own_reservation(): void
{
    $user = User::factory()->create();
    $room = Room::factory()->create();
    $reservation = Reservation::factory()->create([
        'user_id' => $user->id,
        'room_id' => $room->id,
        'status'  => 'pending',
    ]);

    $response = $this->actingAs($user)->get(route('reservations.show', $reservation));

    $response->assertStatus(200);
}

public function test_user_can_view_reservations_list(): void
{
    $user = User::factory()->create();
    $room = Room::factory()->create();
    Reservation::factory()->count(2)->create([
        'user_id' => $user->id,
        'room_id' => $room->id,
    ]);

    $response = $this->actingAs($user)->get(route('reservations.index'));

    $response->assertStatus(200);
}

public function test_user_can_view_reservation_create_form(): void
{
    $user = User::factory()->create();
    $room = Room::factory()->create(['status' => 'available']);

    $response = $this->actingAs($user)->get(route('reservations.create', $room));

    $response->assertStatus(200);
}
}