@extends('layouts.app')
@section('title', 'Carte des Livraisons')
@section('page-title', 'Carte des Livraisons')

@push('styles')
<link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    #carte-livraisons {
        height: 520px;
        border-radius: 12px;
        z-index: 1;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    .popup-titre {
        font-weight: 700;
        font-size: 0.95rem;
        color: #1a3c5e;
        margin-bottom: 6px;
    }
    .popup-info {
        font-size: 0.82rem;
        color: #6b7280;
        margin-bottom: 3px;
    }
    .legende-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        margin-bottom: 6px;
    }
    .legende-dot {
        width: 14px; height: 14px;
        border-radius: 50%;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">🗺️ Carte des Livraisons</h4>
        <p class="text-muted mb-0">
            Visualisez toutes les livraisons sur la carte
        </p>
    </div>
    <a href="{{ route('livraisons.index') }}"
       class="btn btn-outline-secondary">
        <i class="bi bi-list-ul me-1"></i> Liste
    </a>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-geo-alt fs-2 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
                    <div class="small opacity-75">Total livraisons</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-dark">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-hourglass fs-2 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $stats['en_attente'] }}</div>
                    <div class="small opacity-75">En attente</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-truck fs-2 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $stats['en_cours'] }}</div>
                    <div class="small opacity-75">En cours</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-check-circle fs-2 opacity-75"></i>
                <div>
                    <div class="fs-3 fw-bold">{{ $stats['livrees'] }}</div>
                    <div class="small opacity-75">Livrées</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">

    {{-- Carte --}}
    <div class="col-md-9">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-3 d-flex
                        justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-map text-primary me-2"></i>
                    Carte interactive
                    <span class="badge bg-primary ms-1">
                        {{ $livraisons->count() }} localisées
                    </span>
                </h6>
                <button class="btn btn-sm btn-outline-primary"
                        id="btn-geocoder-tout">
                    <i class="bi bi-geo me-1"></i>
                    Localiser toutes les adresses
                </button>
            </div>
            <div class="card-body p-2">
                <div id="carte-livraisons"></div>
            </div>
        </div>
    </div>

    {{-- Légende + liste sans coords --}}
    <div class="col-md-3">

        {{-- Légende --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-bold mb-0">Légende</h6>
            </div>
            <div class="card-body">
                <div class="legende-item">
                    <div class="legende-dot" style="background:#ffc107"></div>
                    <span>En attente</span>
                </div>
                <div class="legende-item">
                    <div class="legende-dot" style="background:#0d6efd"></div>
                    <span>En cours</span>
                </div>
                <div class="legende-item">
                    <div class="legende-dot" style="background:#198754"></div>
                    <span>Livrée</span>
                </div>
            </div>
        </div>

        {{-- Adresses sans coordonnées --}}
        @if($livraisonsSansCoord->isNotEmpty())
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-bold mb-0 text-warning">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Non localisées
                    <span class="badge bg-warning text-dark ms-1">
                        {{ $livraisonsSansCoord->count() }}
                    </span>
                </h6>
            </div>
            <div class="card-body p-0">
                @foreach($livraisonsSansCoord as $liv)
                <div class="p-3 border-bottom" id="liv-{{ $liv->id }}">
                    <div class="fw-semibold small">
                        {{ $liv->vente->numero_vente }}
                    </div>
                    <div class="text-muted" style="font-size:0.78rem">
                        {{ Str::limit($liv->adresse_livraison, 35) }}
                    </div>
                    <button class="btn btn-sm btn-outline-primary mt-1 w-100"
                            style="font-size:0.75rem"
                            onclick="geocoderLivraison(
                                {{ $liv->id }},
                                '{{ addslashes($liv->adresse_livraison) }}'
                            )">
                        <i class="bi bi-geo me-1"></i> Localiser
                    </button>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="card shadow-sm border-0">
            <div class="card-body text-center text-muted py-4">
                <i class="bi bi-check-circle text-success fs-2 d-block mb-2"></i>
                <small>Toutes les livraisons sont localisées !</small>
            </div>
        </div>
        @endif

    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── Données livraisons ──
    const livraisons = [
        @foreach($livraisons as $l)
        {
            id:      {{ $l->id }},
            vente:   "{{ $l->vente->numero_vente }}",
            client:  "{{ addslashes($l->client->nom) }}",
            adresse: "{{ addslashes($l->adresse_livraison) }}",
            statut:  "{{ $l->statut }}",
            lat:     {{ $l->latitude ?? 'null' }},
            lng:     {{ $l->longitude ?? 'null' }},
            date:    "{{ $l->date_livraison ? $l->date_livraison->format('d/m/Y') : '—' }}",
            url:     "{{ route('livraisons.show', $l->id) }}",
        },
        @endforeach
    ];

    // ── Couleurs par statut ──
    const couleurs = {
        'en_attente': '#ffc107',
        'en_cours':   '#0d6efd',
        'livree':     '#198754',
    };

    // ── Initialiser la carte centrée sur Yaoundé ──
    const carte = L.map('carte-livraisons').setView([3.8480, 11.5021], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(carte);

    // ── Marqueur personnalisé ──
    function creerIcone(couleur) {
        return L.divIcon({
            html: '<div style="width:28px;height:28px;background:' + couleur + ';' +
                  'border:3px solid white;border-radius:50%;' +
                  'box-shadow:0 2px 8px rgba(0,0,0,0.3);"></div>',
            iconSize: [28, 28],
            iconAnchor: [14, 14],
            className: '',
        });
    }

    // ── Ajouter les marqueurs ──
    const marqueurs = [];

    livraisons.forEach(function(liv) {
        if (!liv.lat || !liv.lng) return;

        const couleur  = couleurs[liv.statut] || '#6c757d';
        const marqueur = L.marker([liv.lat, liv.lng], {
            icon: creerIcone(couleur)
        }).addTo(carte);

        const statutLabel = {
            'en_attente': '⏳ En attente',
            'en_cours':   '🚚 En cours',
            'livree':     '✅ Livrée',
        };

        marqueur.bindPopup(
            '<div style="min-width:200px">' +
                '<div style="font-weight:700;font-size:0.95rem;color:#1a3c5e;margin-bottom:6px">' +
                    liv.vente +
                '</div>' +
                '<div style="font-size:0.82rem;color:#6b7280;margin-bottom:3px">' +
                    '👤 ' + liv.client +
                '</div>' +
                '<div style="font-size:0.82rem;color:#6b7280;margin-bottom:3px">' +
                    '📍 ' + liv.adresse +
                '</div>' +
                '<div style="font-size:0.82rem;color:#6b7280;margin-bottom:8px">' +
                    '📅 ' + liv.date +
                '</div>' +
                '<span style="background:' + couleur + ';color:white;' +
                    'padding:2px 10px;border-radius:20px;font-size:0.78rem;font-weight:600">' +
                    (statutLabel[liv.statut] || liv.statut) +
                '</span>' +
                '<a href="' + liv.url + '" style="display:block;margin-top:8px;' +
                    'text-align:center;background:#1a3c5e;color:white;' +
                    'padding:4px;border-radius:6px;text-decoration:none;font-size:0.8rem">' +
                    'Voir détail' +
                '</a>' +
            '</div>'
        );

        marqueurs.push(marqueur);
    });

    // Ajuster la vue sur tous les marqueurs
    if (marqueurs.length > 0) {
        const groupe = L.featureGroup(marqueurs);
        carte.fitBounds(groupe.getBounds().pad(0.1));
    }

    // ── Géocoder une livraison ──
    window.geocoderLivraison = function(id, adresse) {
        const btn = document.querySelector('#liv-' + id + ' button');
        if (btn) {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Localisation...';
            btn.disabled  = true;
        }

        fetch('/livraisons/geocoder?adresse=' + encodeURIComponent(adresse))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                fetch('/livraisons/' + id + '/coordonnees', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector(
                            'meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        latitude:  data.latitude,
                        longitude: data.longitude,
                    })
                })
                .then(function() {
                    const marqueur = L.marker(
                        [data.latitude, data.longitude],
                        { icon: creerIcone('#ffc107') }
                    ).addTo(carte);

                    marqueur.bindPopup(
                        '<div><strong>Livraison #' + id + '</strong>' +
                        '<br>' + adresse + '</div>'
                    ).openPopup();

                    carte.setView([data.latitude, data.longitude], 14);

                    const el = document.getElementById('liv-' + id);
                    if (el) el.remove();

                    alert('✅ Adresse localisée avec succès !');
                });
            } else {
                alert('❌ Impossible de localiser cette adresse.');
                if (btn) {
                    btn.innerHTML = '<i class="bi bi-geo me-1"></i> Réessayer';
                    btn.disabled  = false;
                }
            }
        })
        .catch(function() {
            alert('Erreur de connexion.');
            if (btn) {
                btn.innerHTML = '<i class="bi bi-geo me-1"></i> Réessayer';
                btn.disabled  = false;
            }
        });
    };

    // ── Géocoder toutes ──
    const btnTout = document.getElementById('btn-geocoder-tout');
    if (btnTout) {
        btnTout.addEventListener('click', function() {
            const btns = document.querySelectorAll('[id^="liv-"] button');
            if (btns.length === 0) {
                alert('Toutes les livraisons sont déjà localisées !');
                return;
            }
            btns.forEach(function(btn) {
                setTimeout(function() { btn.click(); }, 500);
            });
        });
    }

}); // fin DOMContentLoaded
</script>
@endpush