@extends('layouts.app')
@section('title', 'Accueil')
@section('content')

<div class="text-center py-5">
    <h1>Bienvenue sur HotelApp</h1>
    <p class="text-muted">Trouvez et réservez votre chambre idéale</p>
    <a href="{{ route('hotels.index') }}" class="btn btn-hotel btn-lg mt-3">
        <i class="bi bi-building me-2"></i>Voir nos hôtels
    </a>
</div>

@endsection