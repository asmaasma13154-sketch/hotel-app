@'
@extends('layouts.app')
@section('title', 'Mes Réservations')
@section('content')
<h2><i class="bi bi-calendar-check me-2"></i>Mes Réservations</h2>

@if($reservations->isEmpty())
    <div class="alert alert-info mt-4">
        Vous n'avez aucune réservation. <a href="{{ route('hotels.index') }}">Voir les hôtels</a>
    </div>
@else
<div class="table-responsive mt-4">
    <table class="table table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Hôtel</th>
                <th>Chambre</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Nuits</th>
                <th>Total</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservations as $r)
            <tr>
                <td>{{ $r->id }}</td>
                <td>{{ $r->room->hotel->name }}</td>
                <td>{{ $r->room->number }} ({{ $r->room->type }})</td>
                <td>{{ $r->check_in->format('d/m/Y') }}</td>
                <td>{{ $r->check_out->format('d/m/Y') }}</td>
                <td>{{ $r->nights }}</td>
                <td>{{ $r->total_price }}€</td>
                <td>
                    <span class="badge bg-{{ match($r->status) {
                        'confirmed' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'secondary'
                    } }}">{{ $r->status }}</span>
                </td>
                <td>
                    @if($r->status === 'pending')
                    <form method="POST" action="{{ route('reservations.cancel', $r) }}">
                        @csrf @method('PATCH')
                        <button class="btn btn-sm btn-danger">Annuler</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
'@ | Set-Content -Path "resources\views\reservations\index.blade.php" -Encoding UTF8