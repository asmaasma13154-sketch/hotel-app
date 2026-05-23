<?php
namespace App\Policies;

use App\Models\{User, Reservation};

class ReservationPolicy
{
    public function view(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id || $user->hasRole('admin');
    }

    public function update(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id
            && $reservation->status === 'pending';
    }
}