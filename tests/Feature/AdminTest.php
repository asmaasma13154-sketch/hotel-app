<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Hotel, Room, Reservation};
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    public function test_client_cannot_access_admin_dashboard(): void
    {
        $client = User::factory()->create();
        $client->assignRole('client');

        $response = $this->actingAs($client)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_admin_can_view_reservations(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.reservations'));

        $response->assertStatus(200);
    }

    public function test_admin_can_confirm_reservation(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $user  = User::factory()->create();
        $room  = Room::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'status'  => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.reservations.confirm', $reservation));

        $response->assertRedirect();
        $this->assertEquals('confirmed', $reservation->fresh()->status);
    }

    public function test_admin_can_create_hotel(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.hotels.store'), [
            'name'    => 'Hotel Test',
            'city'    => 'Tunis',
            'address' => '123 Rue Test',
            'stars'   => 4,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('hotels', ['name' => 'Hotel Test']);
    }

    public function test_admin_can_delete_hotel(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $hotel = Hotel::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('admin.hotels.destroy', $hotel));

        $response->assertRedirect();
        $this->assertDatabaseMissing('hotels', ['id' => $hotel->id]);
    }
}