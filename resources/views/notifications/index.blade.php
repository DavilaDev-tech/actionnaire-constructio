@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"> Notifications</h4>
        <p class="text-muted mb-0">Toutes vos alertes et notifications</p>
    </div>
    <form method="POST" action="{{ route('notifications.tout-lire') }}">
        @csrf
        <button class="btn btn-outline-primary">
            <i class="bi bi-check-all me-1"></i> Tout marquer lu
        </button>
    </form>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        @forelse($notifications as $notif)
        @php
            $data = $notif->data;
            $lue  = $notif->read_at !== null;
        @endphp
        <div class="d-flex align-items-start gap-3 p-3 border-bottom
                    {{ !$lue ? 'bg-light' : '' }}">

            <!-- Icône -->
            <div style="width:42px;height:42px;border-radius:10px;flex-shrink:0;
                        background:{{ !$lue ? '#fff3e0' : '#f8f9fa' }};
                        display:flex;align-items:center;justify-content:center">
                <i class="bi bi-exclamation-triangle
                          {{ !$lue ? 'text-warning' : 'text-muted' }} fs-5"></i>
            </div>

            <!-- Contenu -->
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between">
                    <span class="fw-semibold {{ !$lue ? '' : 'text-muted' }}">
                        {{ $data['titre'] ?? 'Notification' }}
                        @if(!$lue)
                        <span class="badge bg-warning text-dark ms-1"
                              style="font-size:0.65rem">Nouveau</span>
                        @endif
                    </span>
                    <small class="text-muted">
                        {{ $notif->created_at->diffForHumans() }}
                    </small>
                </div>
                <div class="text-muted small mt-1">
                    {{ $data['message'] ?? '' }}
                </div>

                <!-- Produits concernés -->
                @if(!empty($data['produits']))
                <div class="mt-2 d-flex flex-wrap gap-1">
                    @foreach($data['produits'] as $p)
                    <span class="badge bg-{{
                        $p['statut'] == 'epuise' ? 'danger' : 'warning'
                    }} rounded-pill" style="font-size:0.72rem">
                        {{ $p['nom'] }} : {{ $p['quantite_stock'] }}
                        / {{ $p['seuil_alerte'] }}
                    </span>
                    @endforeach
                </div>
                @endif

                <!-- Actions -->
                <div class="d-flex gap-2 mt-2">
                    @if(!$lue)
                    <form method="POST"
                          action="{{ route('notifications.lire', $notif->id) }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-primary"
                                style="font-size:0.78rem">
                            <i class="bi bi-check me-1"></i>Marquer lu
                        </button>
                    </form>
                    @endif
                    <a href="{{ $data['url'] ?? route('dashboard') }}"
                       class="btn btn-sm btn-outline-success"
                       style="font-size:0.78rem">
                        <i class="bi bi-eye me-1"></i>Voir
                    </a>
                    <form method="POST"
                          action="{{ route('notifications.destroy', $notif->id) }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"
                                style="font-size:0.78rem">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-5">
            <i class="bi bi-bell-slash fs-1 d-block mb-3 opacity-50"></i>
            <h6>Aucune notification</h6>
            <p class="small">Vous êtes à jour !</p>
        </div>
        @endforelse
    </div>
    @if($notifications->hasPages())
    <div class="card-footer">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection