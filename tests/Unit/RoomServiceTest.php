<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\{Room, Hotel};
use App\Services\RoomService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoomServiceTest extends TestCase
{
    use RefreshDatabase;

    private RoomService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RoomService();
    }

    public function test_returns_only_available_rooms(): void
    {
        Room::factory()->count(3)->create(['status' => 'available']);
        Room::factory()->count(2)->create(['status' => 'occupied']);

        $result = $this->service->getAvailableRooms();

        $this->assertCount(3, $result);
        $result->each(fn($r) => $this->assertEquals('available', $r->status));
    }

    public function test_filters_rooms_by_type(): void
    {
        Room::factory()->count(3)->create(['status' => 'available', 'type' => 'simple']);
        Room::factory()->count(2)->create(['status' => 'available', 'type' => 'suite']);

        $result = $this->service->getAvailableRooms('suite');

        $this->assertCount(2, $result);
        $result->each(fn($r) => $this->assertEquals('suite', $r->type));
    }

    public function test_calculates_total_price_correctly(): void
    {
        $room = Room::factory()->create(['price_per_night' => 100]);

        $total = $this->service->calculateTotalPrice($room, '2025-01-01', '2025-01-04');

        $this->assertEquals(300.0, $total); // 3 nuits × 100€
    }

    public function test_returns_empty_when_no_rooms_available(): void
    {
        Room::factory()->count(3)->create(['status' => 'occupied']);

        $result = $this->service->getAvailableRooms();

        $this->assertCount(0, $result);
    }
    public function test_get_room_stats_by_hotel(): void
{
    $hotel = Hotel::factory()->create();
    Room::factory()->count(3)->create([
        'hotel_id' => $hotel->id,
        'status'   => 'available'
    ]);

    $result = $this->service->getRoomStatsByHotel();

    $this->assertNotEmpty($result);
    $this->assertGreaterThanOrEqual(1, $result->count());
}
}