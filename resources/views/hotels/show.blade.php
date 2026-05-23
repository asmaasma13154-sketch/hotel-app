@extends('layouts.app')
@section('title', $hotel->name)
@section('content')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('hotels.index') }}">Hôtels</a></li>
        <li class="breadcrumb-item active">{{ $hotel->name }}</li>
    </ol>
</nav>

<div class="row mb-4">
    <div class="col-md-8">
        <h1>{{ $hotel->name }}</h1>
        <p class="text-muted">
            <i class="bi bi-geo-alt"></i> {{ $hotel->address }}
            &nbsp;|&nbsp;
            @for($i=0;$i<$hotel->stars;$i++)<span class="star-gold">★</span>@endfor
        </p>
        <p>{{ $hotel->description }}</p>
    </div>
    <div class="col-md-4 text-md-end">
        <p><i class="bi bi-telephone"></i> {{ $hotel->phone }}</p>
        <p><i class="bi bi-envelope"></i> {{ $hotel->email }}</p>
    </div>
</div>

<h3 class="mb-3">Chambres disponibles ({{ $rooms->count() }})</h3>

<div class="row g-4">
    @forelse($rooms as $room)
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <h5>Chambre {{ $room->number }}</h5>
                    <span class="badge bg-secondary text-capitalize">{{ $room->type }}</span>
                </div>
                <p class="text-muted small">{{ $room->description }}</p>
                <ul class="list-unstyled small">
                    <li><i class="bi bi-people me-1"></i> {{ $room->capacity }} personne(s)</li>
                    <li><i class="bi bi-currency-euro me-1"></i> <strong>{{ $room->price_per_night }}€</strong>/nuit</li>
                </ul>
                @auth
                    <a href="{{ route('reservations.create', $room) }}" class="btn btn-hotel w-100 mt-2">
                        <i class="bi bi-calendar-check me-1"></i> Réserver
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary w-100 mt-2">
                        Connectez-vous pour réserver
                    </a>
                @endauth
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center text-muted py-4">
        Aucune chambre disponible actuellement.
    </div>
    @endforelse
</div>
@endsections