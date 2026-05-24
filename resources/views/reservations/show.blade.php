@extends('layouts.app')
@section('title', 'Détail Réservation')
@section('content')

<div class="row justify-content-center">
<div class="col-md-8">

<div class="card border-0 shadow-sm">
    <div class="card-header" style="background: var(--primary); color: white;">
        <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Réservation #{{ $reservation->id }}</h5>
    </div>
    <div class="card-body p-4">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="text-muted small">Hôtel</label>
                <p class="fw-bold">{{ $reservation->room->hotel->name }}</p>
            </div>
            <div class="col-md-6">
                <label class="text-muted small">Chambre</label>
                <p class="fw-bold">N° {{ $reservation->room->number }} — {{ $reservation->room->type }}</p>
            </div>
            <div class="col-md-6">
                <label class="text-muted small">Check-in</label>
                <p class="fw-bold">{{ $reservation->check_in->format('d/m/Y') }}</p>
            </div>
            <div class="col-md-6">
                <label class="text-muted small">Check-out</label>
                <p class="fw-bold">{{ $reservation->check_out->format('d/m/Y') }}</p>
            </div>
            <div class="col-md-4">
                <label class="text-muted small">Nuits</label>
                <p class="fw-bold">{{ $reservation->nights }}</p>
            </div>
            <div class="col-md-4">
                <label class="text-muted small">Personnes</label>
                <p class="fw-bold">{{ $reservation->guests }}</p>
            </div>
            <div class="col-md-4">
                <label class="text-muted small">Total</label>
                <p class="fw-bold text-success fs-5">{{ $reservation->total_price }}€</p>
            </div>
            <div class="col-12">
                <label class="text-muted small">Statut</label>
                <p>
                    <span class="badge fs-6 bg-{{ match($reservation->status) {
                        'confirmed' => 'success',
                        'pending'   => 'warning',
                        'cancelled' => 'danger',
                        default     => 'secondary'
                    } }}">{{ ucfirst($reservation->status) }}</span>
                </p>
            </div>
            @if($reservation->notes)
            <div class="col-12">
                <label class="text-muted small">Notes</label>
                <p>{{ $reservation->notes }}</p>
            </div>
            @endif
        </div>

        <div class="d-flex gap-2 mt-4">
            <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Mes réservations
            </a>
            @if($reservation->status === 'pending')
            <form method="POST" action="{{ route('reservations.cancel', $reservation) }}">
                @csrf @method('PATCH')
                <button class="btn btn-danger" onclick="return confirm('Annuler cette réservation ?')">
                    <i class="bi bi-x-circle me-1"></i> Annuler
                </button>
            </form>
            @endif
        </div>

    </div>
</div>

</div>
</div>
@endsection