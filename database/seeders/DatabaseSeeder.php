<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Room;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les rôles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'client']);

        // Créer l'admin
        $admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@hotel.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        // Créer un client de test
        $client = User::create([
            'name'     => 'Client Test',
            'email'    => 'client@hotel.com',
            'password' => bcrypt('password'),
        ]);
        $client->assignRole('client');

        // Créer 3 hôtels avec chambres
        $hotels = [
            ['name' => 'Le Grand Palace', 'city' => 'Paris',    'stars' => 5],
            ['name' => 'Hôtel du Soleil', 'city' => 'Lyon',     'stars' => 4],
            ['name' => 'La Belle Vue',    'city' => 'Marseille', 'stars' => 3],
        ];

        foreach ($hotels as $hotelData) {
            $hotel = Hotel::create(array_merge($hotelData, [
                'address'     => '123 Rue Exemple, ' . $hotelData['city'],
                'description' => 'Un magnifique hôtel situé au cœur de ' . $hotelData['city'],
                'phone'       => '+33 1 23 45 67 89',
                'email'       => 'contact@' . strtolower(str_replace(' ', '', $hotelData['name'])) . '.com',
            ]));

            // Chambres simples
            for ($i = 1; $i <= 5; $i++) {
                Room::create([
                    'hotel_id'       => $hotel->id,
                    'number'         => '10' . $i,
                    'type'           => 'simple',
                    'price_per_night'=> 80 + ($i * 5),
                    'capacity'       => 1,
                    'description'    => 'Chambre simple confortable',
                    'status'         => 'available',
                ]);
            }

            // Chambres doubles
            for ($i = 1; $i <= 4; $i++) {
                Room::create([
                    'hotel_id'       => $hotel->id,
                    'number'         => '20' . $i,
                    'type'           => 'double',
                    'price_per_night'=> 120 + ($i * 10),
                    'capacity'       => 2,
                    'description'    => 'Chambre double avec vue',
                    'status'         => 'available',
                ]);
            }

            // Suite
            Room::create([
                'hotel_id'       => $hotel->id,
                'number'         => '301',
                'type'           => 'suite',
                'price_per_night'=> 300,
                'capacity'       => 4,
                'description'    => 'Suite luxueuse avec salon',
                'status'         => 'available',
            ]);
        }
    }
}