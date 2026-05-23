@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('content')

<h2 class="mb-4"><i class="bi bi-speedometer2 me-2"></i>Dashboard Administration</h2>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    @foreach([
        ['icon'=>'building','label'=>'Hôtels','value'=>$stats['hotels'],'color'=>'primary'],
        ['icon'=>'door-open','label'=>'Chambres','value'=>$stats['rooms'],'color'=>'secondary'],
        ['icon'=>'check-circle','label'=>'Disponibles','value'=>$stats['available'],'color'=>'success'],
        ['icon'=>'calendar-check','label'=>'Réservations','value'=>$stats['reservations'],'color'=>'info'],
        ['icon'=>'people','label'=>'Clients','value'=>$stats['clients'],'color'=>'warning'],
        ['icon'=>'cash-coin','label'=>'Revenus','value'=>number_format($stats['revenue'],2).'€','color'=>'danger'],
    ] as $stat)
    <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center py-3">
            <i class="bi bi-{{ $stat['icon'] }} fs-2 text-{{ $stat['color'] }}"></i>
            <div class="fw-bold fs-5 mt-1">{{ $stat['value'] }}</div>
            <div class="text-muted small">{{ $stat['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    <!-- Réservations récentes -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">Réservations récentes</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Client</th><th>Hôtel / Chambre</th><th>Check-in</th><th>Total</th><th>Statut</th><th></th></tr>
                    </thead>
                    <tbody>
                    @foreach($recentReservations as $r)
                    <tr>
                        <td>{{ $r->user->name }}</td>
                        <td>{{ $r->room->hotel->name }} #{{ $r->room->number }}</td>
                        <td>{{ $r->check_in->format('d/m/Y') }}</td>
                        <td>{{ $r->total_price }}€</td>
                        <td>
                            <span class="badge bg-{{ match($r->status) {
                                'confirmed'=>'success', 'pending'=>'warning',
                                'cancelled'=>'danger', default=>'secondary'
                            } }}">{{ $r->status }}</span>
                        </td>
                        <td>
                            @if($r->status === 'pending')
                            <form method="POST" action="{{ route('admin.reservations.confirm', $r) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-xs btn-success btn-sm">✓</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Chambres -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0">Chambres les plus réservées</h5>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($topRooms as $i => $room)
                <li class="list-group-item d-flex justify-content-between">
                    <span>
                        <span class="badge bg-secondary me-1">{{ $i+1 }}</span>
                        {{ $room->hotel->name }} #{{ $room->number }}
                    </span>
                    <span class="badge bg-primary">{{ $room->reservations_count }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection