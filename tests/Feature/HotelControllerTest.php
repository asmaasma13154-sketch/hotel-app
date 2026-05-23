<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Hotel, Room};
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_hotels_list(): void
    {
        Hotel::factory()->count(3)->create();

        $response = $this->get(route('hotels.index'));

        $response->assertStatus(200);
        $response->assertViewIs('hotels.index');
    }

    public function test_guest_can_view_hotel_detail(): void
{
    $hotel = Hotel::factory()->create();
    Room::factory()->count(3)->create(['hotel_id' => $hotel->id, 'status' => 'available']);

    $response = $this->get(route('hotels.show', $hotel));

    $response->assertStatus(200);
    $response->assertViewIs('hotels.show');
    $response->assertSee($hotel->name);
}

    public function test_hotel_list_is_filterable_by_city(): void
    {
        Hotel::factory()->create(['city' => 'Paris']);
        Hotel::factory()->create(['city' => 'Lyon']);

        $response = $this->get(route('hotels.index', ['city' => 'Paris']));

        $response->assertStatus(200);
        $response->assertSee('Paris');
    }

    public function test_guest_cannot_access_admin(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }
}