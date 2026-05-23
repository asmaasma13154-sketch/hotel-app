<?php
namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReservationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private ReservationService $reservationService) {}

    public function index()
    {
        $reservations = $this->reservationService->getUserReservations(auth()->id());
        return view('reservations.index', compact('reservations'));
    }

    public function create(Room $room)
    {
        return view('reservations.create', compact('room'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id'   => 'required|exists:rooms,id',
            'check_in'  => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
            'guests'    => 'required|integer|min:1|max:10',
            'notes'     => 'nullable|string|max:500',
        ]);

        try {
            $reservation = $this->reservationService->create($validated, auth()->id());
            return redirect()->route('reservations.show', $reservation)
                ->with('success', 'Réservation créée avec succès !');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);
        $reservation->load(['room.hotel', 'user']);
        return view('reservations.show', compact('reservation'));
    }

    public function cancel(Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        $this->reservationService->cancel($reservation);
        return back()->with('success', 'Réservation annulée.');
    }
}