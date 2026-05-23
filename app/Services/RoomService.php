<?php
namespace App\Services;

use App\Models\Room;
use App\Models\Hotel;

class RoomService
{
    public function getAvailableRooms(?string $type = null)
    {
        $query = Room::with('hotel')->available();
        if ($type) {
            $query->ofType($type);
        }
        return $query->get();
    }

    public function getAvailableRoomsForDates(string $checkIn, string $checkOut, ?string $type = null)
    {
        $rooms = Room::with('hotel')->available()->get();

        return $rooms->filter(function ($room) use ($checkIn, $checkOut) {
            return $room->isAvailableFor($checkIn, $checkOut);
        })->when($type, fn($col) => $col->where('type', $type));
    }

    public function calculateTotalPrice(Room $room, string $checkIn, string $checkOut): float
    {
        $nights = \Carbon\Carbon::parse($checkIn)->diffInDays(\Carbon\Carbon::parse($checkOut));
        return $nights * $room->price_per_night;
    }

    public function getRoomStatsByHotel()
    {
        return Hotel::withCount([
            'rooms',
            'rooms as available_rooms_count' => fn($q) => $q->where('status', 'available'),
            'rooms as occupied_rooms_count'  => fn($q) => $q->where('status', 'occupied'),
        ])->get();
    }
}