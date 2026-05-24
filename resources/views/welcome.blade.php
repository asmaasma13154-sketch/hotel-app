@extends('layouts.app')
@section('title', 'Accueil')
@section('content')
<div class="text-center py-5">
    <h1 class="display-4 fw-bold" style="color: var(--primary);">
        <i class="bi bi-building me-2"></i>Bienvenue sur HotelApp
    </h1>
    <p class="lead text-muted mt-3">Trouvez et réservez votre chambre idéale en quelques clics.</p>
    <a href="{{ route('hotels.index') }}" class="btn btn-hotel btn-lg mt-4">
        <i class="bi bi-search me-2"></i>Voir nos hôtels
    </a>
</div>
<div class="row g-4 mt-5">
    <div class="col-md-4 text-center">
        <i class="bi bi-building fs-1" style="color: var(--gold);"></i>
        <h4 class="mt-3">3 Hôtels</h4>
        <p class="text-muted">Paris, Lyon, Marseille</p>
    </div>
    <div class="col-md-4 text-center">
        <i class="bi bi-door-open fs-1" style="color: var(--gold);"></i>
        <h4 class="mt-3">30 Chambres</h4>
        <p class="text-muted">Simple, Double, Suite</p>
    </div>
    <div class="col-md-4 text-center">
        <i class="bi bi-robot fs-1" style="color: var(--gold);"></i>
        <h4 class="mt-3">Assistant IA</h4>
        <p class="text-muted">Chatbot disponible 24h/24</p>
    </div>
</div>
@endsection