@extends('layouts.app')
@section('title', 'Nouvelle Réservation')
@section('content')

<div class="row justify-content-center">
<div class="col-md-7">
<div class="card shadow-sm border-0">
<div class="card-header" style="background: var(--primary); color: white;">
    <h5 class="mb-0"><i class="bi bi-calendar-plus me-2"></i>Réserver — Chambre {{ $room->number }}</h5>
</div>
<div class="card-body p-4">
    <div class="alert alert-info mb-4">
        <strong>{{ $room->hotel->name }}</strong> — {{ $room->type }} —
        <strong>{{ $room->price_per_night }}€/nuit</strong> — {{ $room->capacity }} pers.
    </div>

    <form method="POST" action="{{ route('reservations.store') }}">
        @csrf
        <input type="hidden" name="room_id" value="{{ $room->id }}">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Check-in *</label>
                <input type="date" name="check_in" class="form-control @error('check_in') is-invalid @enderror"
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ old('check_in') }}" required>
                @error('check_in')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Check-out *</label>
                <input type="date" name="check_out" class="form-control @error('check_out') is-invalid @enderror"
                       value="{{ old('check_out') }}" required>
                @error('check_out')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Nombre de personnes *</label>
                <input type="number" name="guests" class="form-control" value="{{ old('guests',1) }}"
                       min="1" max="{{ $room->capacity }}" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Notes / Demandes spéciales</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div id="price-preview" class="alert alert-success mt-4 d-none">
            <i class="bi bi-calculator me-1"></i>
            <strong>Estimation :</strong> <span id="estimated-price">0</span>€
            (<span id="nb-nights">0</span> nuit(s) × {{ $room->price_per_night }}€)
        </div>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-hotel flex-grow-1">
                <i class="bi bi-check-circle me-1"></i> Confirmer la réservation
            </button>
            <a href="{{ route('hotels.show', $room->hotel) }}" class="btn btn-outline-secondary">Annuler</a>
        </div>
    </form>
</div>
</div>
</div>
</div>

@push('scripts')
<script>
const pricePerNight = {{ $room->price_per_night }};

function updatePrice() {
    const ci = document.querySelector('[name="check_in"]').value;
    const co = document.querySelector('[name="check_out"]').value;
    if (!ci || !co) return;
    const nights = Math.round((new Date(co) - new Date(ci)) / 86400000);
    if (nights > 0) {
        document.getElementById('nb-nights').textContent = nights;
        document.getElementById('estimated-price').textContent = (nights * pricePerNight).toFixed(2);
        document.getElementById('price-preview').classList.remove('d-none');
    }
}

document.querySelectorAll('[name="check_in"], [name="check_out"]').forEach(el => {
    el.addEventListener('change', updatePrice);
});
</script>
@endpush
@endsection