<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\{User, Room, Reservation};
use App\Services\{ReservationService, RoomService};
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReservationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReservationService(new RoomService());
    }

    public function test_creates_reservation_successfully(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create(['status' => 'available', 'price_per_night' => 100]);

        $data = [
            'room_id'   => $room->id,
            'check_in'  => '2026-06-01',
            'check_out' => '2026-06-03',
            'guests'    => 1,
        ];

        $reservation = $this->service->create($data, $user->id);

        $this->assertDatabaseHas('reservations', ['id' => $reservation->id]);
        $this->assertEquals(200.0, $reservation->total_price);
        $this->assertEquals('pending', $reservation->status);
    }

    public function test_throws_exception_if_room_not_available(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create([
            'status' => 'available',
            'price_per_night' => 100,
        ]);

        Reservation::factory()->create([
            'room_id'  => $room->id,
            'check_in' => '2026-06-01',
            'check_out'=> '2026-06-03',
            'status'   => 'confirmed',
        ]);

        $this->expectException(\Exception::class);

        $this->service->create([
            'room_id'  => $room->id,
            'check_in' => '2026-06-01',
            'check_out'=> '2026-06-03',
            'guests'   => 1,
        ], $user->id);
    }

    public function test_cancel_reservation_frees_room(): void
    {
        $user = User::factory()->create();
        $room = Room::factory()->create(['status' => 'occupied']);
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'status'  => 'pending',
        ]);

        $this->service->cancel($reservation);

        $this->assertEquals('cancelled', $reservation->fresh()->status);
        $this->assertEquals('available', $room->fresh()->status);
    }
}