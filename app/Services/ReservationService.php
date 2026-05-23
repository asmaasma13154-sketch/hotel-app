<?php
namespace App\Services;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    public function __construct(private RoomService $roomService) {}

    public function create(array $data, int $userId): Reservation
    {
        $room = Room::findOrFail($data['room_id']);

        if (!$room->isAvailableFor($data['check_in'], $data['check_out'])) {
            throw new \Exception('Cette chambre n\'est pas disponible pour ces dates.');
        }

        $totalPrice = $this->roomService->calculateTotalPrice(
            $room, $data['check_in'], $data['check_out']
        );

        return DB::transaction(function () use ($data, $userId, $totalPrice, $room) {
            $reservation = Reservation::create([
                'user_id'     => $userId,
                'room_id'     => $data['room_id'],
                'check_in'    => $data['check_in'],
                'check_out'   => $data['check_out'],
                'guests'      => $data['guests'],
                'total_price' => $totalPrice,
                'status'      => 'pending',
                'notes'       => $data['notes'] ?? null,
            ]);

            $room->update(['status' => 'occupied']);

            return $reservation;
        });
    }

    public function cancel(Reservation $reservation): void
    {
        DB::transaction(function () use ($reservation) {
            $reservation->update(['status' => 'cancelled']);
            $reservation->room->update(['status' => 'available']);
        });
    }

    public function getUserReservations(int $userId)
    {
        return Reservation::with(['room.hotel'])
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getMonthlyStats()
    {
        return Reservation::thisMonth()
            ->selectRaw('COUNT(*) as total, SUM(total_price) as revenue, AVG(total_price) as avg_price')
            ->first();
    }

    public function getMostBookedRooms(int $limit = 5)
    {
        return Room::withCount('reservations')
            ->with('hotel')
            ->orderByDesc('reservations_count')
            ->limit($limit)
            ->get();
    }
}