<?php
namespace App\Http\Controllers;

use App\Models\{Hotel, Room, Reservation, User};
use App\Services\ReservationService;

class AdminController extends Controller
{
    public function __construct(private ReservationService $reservationService) {}

    public function dashboard()
    {
        $stats = [
            'hotels'       => Hotel::count(),
            'rooms'        => Room::count(),
            'available'    => Room::where('status', 'available')->count(),
            'reservations' => Reservation::count(),
            'clients'      => User::role('client')->count(),
            'revenue'      => Reservation::where('status', 'confirmed')->sum('total_price'),
            'pending'      => Reservation::where('status', 'pending')->count(),
            'monthly'      => $this->reservationService->getMonthlyStats(),
        ];

        $recentReservations = Reservation::with(['user', 'room.hotel'])
            ->latest()->limit(10)->get();

        $topRooms = $this->reservationService->getMostBookedRooms();

        return view('admin.dashboard', compact('stats', 'recentReservations', 'topRooms'));
    }

    public function reservations()
    {
        $reservations = Reservation::with(['user', 'room.hotel'])->latest()->paginate(20);
        return view('admin.reservations', compact('reservations'));
    }

    public function confirmReservation(Reservation $reservation)
    {
        $reservation->update(['status' => 'confirmed']);
        return back()->with('success', 'Réservation confirmée.');
    }
}