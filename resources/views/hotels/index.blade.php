@extends('layouts.app')
@section('title', 'Nos Hôtels')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-building me-2"></i>Nos Hôtels</h2>
</div>

<!-- Filtres -->
<form method="GET" class="row g-2 mb-4">
    <div class="col-md-4">
        <select name="city" class="form-select">
            <option value="">Toutes les villes</option>
            @foreach($cities as $city)
                <option value="{{ $city }}" {{ request('city')==$city?'selected':'' }}>{{ $city }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select name="stars" class="form-select">
            <option value="">Étoiles</option>
            @foreach([5,4,3,2,1] as $s)
                <option value="{{ $s }}" {{ request('stars')==$s?'selected':'' }}>{{ $s }} étoiles</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-hotel w-100">Filtrer</button>
    </div>
    <div class="col-md-2">
        <a href="{{ route('hotels.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
    </div>
</form>

<div class="row g-4">
    @forelse($hotels as $hotel)
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="card-title mb-0">{{ $hotel->name }}</h5>
                    <span class="badge bg-warning text-dark">
                        @for($i=0;$i<$hotel->stars;$i++) ★ @endfor
                    </span>
                </div>
                <p class="text-muted mb-1"><i class="bi bi-geo-alt me-1"></i>{{ $hotel->city }}</p>
                <p class="text-muted small mb-3">{{ Str::limit($hotel->description, 80) }}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge bg-success">{{ $hotel->rooms_count }} chambres</span>
                    <a href="{{ route('hotels.show', $hotel) }}" class="btn btn-sm btn-hotel">
                        Voir les chambres <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center text-muted py-5">
        <i class="bi bi-building fs-1"></i>
        <p class="mt-2">Aucun hôtel trouvé.</p>
    </div>
    @endforelse
</div>

<div class="mt-4">{{ $hotels->links() }}</div>
@endsection