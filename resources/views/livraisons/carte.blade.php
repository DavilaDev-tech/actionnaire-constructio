@extends('layouts.app')
@section('title', 'Carte des Livraisons')
@section('page-title', 'Carte des Livraisons')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .hector-stat {
        background: white;
        border-radius: 12px;
        border: 1px solid #F3F4F6;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.2s;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }
    .hector-stat:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.10);
        transform: translateY(-2px);
    }
    .hector-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .hector-stat-value {
        font-size: 1.6rem;
        font-weight: 700;
        line-height: 1;
        color: #111827;
    }
    .hector-stat-label {
        font-size: 0.78rem;
        color: #9CA3AF;
        margin-top: 3px;
        font-weight: 500;
    }

    #carte-livraisons {
        height: 520px;
        border-radius: 0 0 12px 12px;
        z-index: 1;
    }

    .leaflet-popup-content-wrapper {
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        border: none;
    }

    .leaflet-popup-tip { background: white; }

    .popup-header {
        background: linear-gradient(135deg, #F97316, #EA580C);
        color: white;
        padding: 10px 14px;
        border-radius: 8px 8px 0 0;
        font-weight: 700;
        font-size: 0.9rem;
        margin: -10px -15px 10px -15px;
    }

    .popup-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        color: #6B7280;
        margin-bottom: 5px;
    }

    .popup-row i { color: #F97316; width: 14px; }

    .legende-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.82rem;
        color: #374151;
        padding: 8px 0;
        border-bottom: 1px solid #F9FAFB;
    }
    .legende-item:last-child { border-bottom: none; }

    .legende-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        flex-shrink: 0;
        border: 2px solid white;
        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
    }

    .liv-card {
        padding: 12px 16px;
        border-bottom: 1px solid #F9FAFB;
        transition: background 0.15s;
    }
    .liv-card:hover { background: #FFFBF5; }
    .liv-card:last-child { border-bottom: none; }

    .btn-localiser {
        margin-top: 8px;
        width: 100%;
        padding: 6px;
        border-radius: 8px;
        border: 1px solid #FED7AA;
        background: #FFF7ED;
        color: #F97316;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-localiser:hover {
        background: #F97316;
        color: white;
        border-color: #F97316;
    }
</style>
@endpush

@section('content')

{{-- ── En-tête ── --}}
<div class="d-flex justify-content-between align-items-start mb-5">
    <div>
        <h4 class="mb-1 fw-bold" style="color:#111827;font-size:1.3rem">
            Carte des Livraisons
        </h4>
        <p style="color:#9CA3AF;font-size:0.875rem;margin:0">
            <i class="bi bi-geo-alt me-1" style="color:#F97316"></i>
            Douala, Cameroun — {{ $livraisons->count() }} livraison{{ $livraisons->count() > 1 ? 's' : '' }} localisée{{ $livraisons->count() > 1 ? 's' : '' }}
        </p>
    </div>
    <a href="{{ route('livraisons.index') }}"
       class="btn btn-sm"
       style="background:white;border:1px solid #E5E7EB;color:#374151;
              border-radius:8px;font-size:0.85rem;padding:8px 16px;
              box-shadow:0 1px 3px rgba(0,0,0,0.06)">
        <i class="bi bi-list-ul me-1"></i> Liste des livraisons
    </a>
</div>

{{-- ── Stats ── --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#FFF7ED">
                <i class="bi bi-geo-alt-fill" style="color:#F97316"></i>
            </div>
            <div>
                <div class="hector-stat-value">{{ $stats['total'] }}</div>
                <div class="hector-stat-label">Total livraisons</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#FFFBEB">
                <i class="bi bi-hourglass-split" style="color:#D97706"></i>
            </div>
            <div>
                <div class="hector-stat-value">{{ $stats['en_attente'] }}</div>
                <div class="hector-stat-label">En attente</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#EFF6FF">
                <i class="bi bi-truck" style="color:#3B82F6"></i>
            </div>
            <div>
                <div class="hector-stat-value">{{ $stats['en_cours'] }}</div>
                <div class="hector-stat-label">En cours</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="hector-stat">
            <div class="hector-stat-icon" style="background:#F0FDF4">
                <i class="bi bi-check-circle-fill" style="color:#10B981"></i>
            </div>
            <div>
                <div class="hector-stat-value">{{ $stats['livrees'] }}</div>
                <div class="hector-stat-label">Livrées</div>
            </div>
        </div>
    </div>
</div>

{{-- ── Carte + Sidebar ── --}}
<div class="row g-3">

    {{-- Carte --}}
    <div class="col-md-9">
        <div class="card" style="border:1px solid #F3F4F6;border-radius:12px;
                                  box-shadow:0 1px 4px rgba(0,0,0,0.06);overflow:hidden">

            {{-- Header carte --}}
            <div class="d-flex justify-content-between align-items-center px-4 py-3"
                 style="border-bottom:1px solid #F9FAFB">
                <div>
                    <span class="fw-semibold" style="color:#111827;font-size:0.9rem">
                        <i class="bi bi-map me-2" style="color:#F97316"></i>
                        Carte interactive — Douala
                    </span>
                    <span style="background:#FFF7ED;color:#F97316;border:1px solid #FED7AA;
                                 border-radius:20px;padding:2px 10px;font-size:0.72rem;
                                 font-weight:600;margin-left:8px">
                        {{ $livraisons->count() }} localisées
                    </span>
                </div>
                <button id="btn-geocoder-tout"
                        style="background:linear-gradient(135deg,#F97316,#EA580C);
                               color:white;border:none;border-radius:8px;
                               padding:7px 14px;font-size:0.82rem;font-weight:600;
                               cursor:pointer;transition:all 0.2s;
                               box-shadow:0 2px 8px rgba(249,115,22,0.3)">
                    <i class="bi bi-geo me-1"></i> Localiser toutes
                </button>
            </div>

            {{-- Carte Leaflet --}}
            <div id="carte-livraisons"></div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-md-3">

        {{-- Légende --}}
        <div class="card mb-3"
             style="border:1px solid #F3F4F6;border-radius:12px;
                    box-shadow:0 1px 4px rgba(0,0,0,0.06)">
            <div class="px-4 py-3" style="border-bottom:1px solid #F9FAFB">
                <span class="fw-semibold" style="color:#111827;font-size:0.875rem">
                    Légende
                </span>
            </div>
            <div class="px-4 py-2">
                <div class="legende-item">
                    <div class="legende-dot" style="background:#D97706"></div>
                    <span>En attente</span>
                </div>
                <div class="legende-item">
                    <div class="legende-dot" style="background:#3B82F6"></div>
                    <span>En cours</span>
                </div>
                <div class="legende-item">
                    <div class="legende-dot" style="background:#10B981"></div>
                    <span>Livrée</span>
                </div>
            </div>
        </div>

        {{-- Adresses non localisées --}}
        <div class="card"
             style="border:1px solid #F3F4F6;border-radius:12px;
                    box-shadow:0 1px 4px rgba(0,0,0,0.06)">
            <div class="px-4 py-3" style="border-bottom:1px solid #F9FAFB">
                @if($livraisonsSansCoord->isNotEmpty())
                <span class="fw-semibold" style="color:#111827;font-size:0.875rem">
                    <i class="bi bi-exclamation-triangle me-1" style="color:#D97706"></i>
                    Non localisées
                    <span style="background:#FFFBEB;color:#D97706;border:1px solid #FDE68A;
                                 border-radius:20px;padding:1px 8px;font-size:0.7rem;
                                 font-weight:700;margin-left:4px">
                        {{ $livraisonsSansCoord->count() }}
                    </span>
                </span>
                @else
                <span class="fw-semibold" style="color:#111827;font-size:0.875rem">
                    <i class="bi bi-check-circle me-1" style="color:#10B981"></i>
                    Toutes localisées
                </span>
                @endif
            </div>

            <div style="max-height:420px;overflow-y:auto">
                @forelse($livraisonsSansCoord as $liv)
                <div class="liv-card" id="liv-{{ $liv->id }}">
                    <div class="fw-semibold" style="font-size:0.82rem;color:#111827">
                        {{ $liv->vente->numero_vente }}
                    </div>
                    <div style="font-size:0.75rem;color:#9CA3AF;margin-top:2px">
                        <i class="bi bi-person me-1"></i>
                        {{ $liv->client->nom }}
                    </div>
                    <div style="font-size:0.75rem;color:#9CA3AF;margin-top:2px">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ Str::limit($liv->adresse_livraison, 35) }}
                    </div>
                    <button class="btn-localiser"
                            onclick="geocoderLivraison(
                                {{ $liv->id }},
                                '{{ addslashes($liv->adresse_livraison) }}'
                            )">
                        <i class="bi bi-geo me-1"></i> Localiser
                    </button>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="bi bi-check-circle-fill"
                       style="font-size:2rem;color:#10B981;display:block;margin-bottom:8px"></i>
                    <div style="font-size:0.82rem;color:#9CA3AF">
                        Toutes les livraisons sont localisées !
                    </div>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Limites de Douala ──
    const DOUALA_CENTER = [4.0511, 9.7679];
    const DOUALA_BOUNDS = L.latLngBounds(
        [3.8829, 9.5462],  // Sud-Ouest
        [4.2029, 9.8662]   // Nord-Est
    );

    // ── Données livraisons ──
    @php $livraisonsJson = json_encode($livraisons->map(fn($l) => [
        'id'      => $l->id,
        'vente'   => $l->vente->numero_vente,
        'client'  => $l->client->nom,
        'adresse' => $l->adresse_livraison,
        'statut'  => $l->statut,
        'lat'     => $l->latitude,
        'lng'     => $l->longitude,
        'date'    => $l->date_livraison ? $l->date_livraison->format('d/m/Y') : '—',
        'url'     => route('livraisons.show', $l->id),
    ])); @endphp

    const livraisons = {!! $livraisonsJson !!};

    // ── Couleurs ──
    const couleurs = {
        'en_attente': '#D97706',
        'en_cours':   '#3B82F6',
        'livree':     '#10B981',
    };

    const statutLabels = {
        'en_attente': '⏳ En attente',
        'en_cours':   '🚚 En cours',
        'livree':     '✅ Livrée',
    };

    // ── Initialiser la carte LIMITÉE à Douala ──
    const carte = L.map('carte-livraisons', {
        center:    DOUALA_CENTER,
        zoom:      13,
        minZoom:   11,
        maxZoom:   18,
        maxBounds: DOUALA_BOUNDS,
        maxBoundsViscosity: 1.0,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(carte);

    // ── Marqueur personnalisé ──
    function creerIcone(couleur) {
        return L.divIcon({
            html: '<div style="width:28px;height:28px;background:' + couleur + ';' +
                  'border:3px solid white;border-radius:50%;' +
                  'box-shadow:0 2px 10px rgba(0,0,0,0.25);' +
                  'transition:transform 0.2s"></div>',
            iconSize:   [28, 28],
            iconAnchor: [14, 14],
            className:  '',
        });
    }

    // ── Ajouter les marqueurs ──
    const marqueurs = [];

    livraisons.forEach(function(liv) {
        if (!liv.lat || !liv.lng) return;

        const couleur  = couleurs[liv.statut] || '#6B7280';
        const marqueur = L.marker([liv.lat, liv.lng], {
            icon: creerIcone(couleur)
        }).addTo(carte);

        marqueur.bindPopup(
            '<div style="min-width:210px;font-family:Inter,sans-serif">' +
                '<div style="background:linear-gradient(135deg,#F97316,#EA580C);' +
                     'color:white;padding:10px 14px;border-radius:8px 8px 0 0;' +
                     'font-weight:700;font-size:0.9rem;margin:-10px -15px 12px -15px">' +
                    '📦 ' + liv.vente +
                '</div>' +
                '<div style="display:flex;align-items:center;gap:8px;' +
                     'font-size:0.82rem;color:#6B7280;margin-bottom:5px">' +
                    '<i style="color:#F97316">👤</i> ' + liv.client +
                '</div>' +
                '<div style="display:flex;align-items:center;gap:8px;' +
                     'font-size:0.82rem;color:#6B7280;margin-bottom:5px">' +
                    '<i style="color:#F97316">📍</i> ' + liv.adresse +
                '</div>' +
                '<div style="display:flex;align-items:center;gap:8px;' +
                     'font-size:0.82rem;color:#6B7280;margin-bottom:10px">' +
                    '<i style="color:#F97316">📅</i> ' + liv.date +
                '</div>' +
                '<div style="display:flex;justify-content:space-between;align-items:center">' +
                    '<span style="background:' + couleur + '20;color:' + couleur + ';' +
                         'border:1px solid ' + couleur + '40;' +
                         'padding:3px 10px;border-radius:20px;' +
                         'font-size:0.75rem;font-weight:600">' +
                        (statutLabels[liv.statut] || liv.statut) +
                    '</span>' +
                    '<a href="' + liv.url + '" style="background:#F97316;color:white;' +
                         'padding:4px 12px;border-radius:8px;text-decoration:none;' +
                         'font-size:0.78rem;font-weight:600">' +
                        'Détail →' +
                    '</a>' +
                '</div>' +
            '</div>'
        );

        marqueurs.push(marqueur);
    });

    // Si des marqueurs existent et sont dans Douala → ajuster la vue
    if (marqueurs.length > 0) {
        const groupe = L.featureGroup(marqueurs);
        const bounds = groupe.getBounds();
        // Vérifier que les bounds restent dans Douala
        if (DOUALA_BOUNDS.contains(bounds)) {
            carte.fitBounds(bounds.pad(0.1));
        }
    }

    // ── Géocoder une livraison ──
    window.geocoderLivraison = function(id, adresse) {
        const btn = document.querySelector('#liv-' + id + ' button');
        if (btn) {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Localisation...';
            btn.disabled  = true;
        }

        fetch('{{ route("livraisons.geocoder") }}?adresse=' + encodeURIComponent(adresse + ', Douala, Cameroun'))
        .then(r => r.json())
        .then(function(data) {
            if (data.success) {

                // Vérifier que les coords sont dans Douala
                const lat = parseFloat(data.latitude);
                const lng = parseFloat(data.longitude);

                if (!DOUALA_BOUNDS.contains([lat, lng])) {
                    alert('⚠️ L\'adresse trouvée semble être hors de Douala. Vérifiez l\'adresse.');
                    if (btn) { btn.innerHTML = '<i class="bi bi-geo me-1"></i> Réessayer'; btn.disabled = false; }
                    return;
                }

                fetch('/livraisons/' + id + '/coordonnees', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ latitude: lat, longitude: lng })
                })
                .then(function() {
                    const couleur  = '#D97706';
                    const marqueur = L.marker([lat, lng], {
                        icon: creerIcone(couleur)
                    }).addTo(carte);

                    marqueur.bindPopup(
                        '<div style="font-family:Inter,sans-serif">' +
                        '<div style="font-weight:700;color:#F97316">Livraison #' + id + '</div>' +
                        '<div style="font-size:0.82rem;color:#6B7280;margin-top:4px">📍 ' + adresse + '</div>' +
                        '</div>'
                    ).openPopup();

                    carte.setView([lat, lng], 15);

                    const el = document.getElementById('liv-' + id);
                    if (el) {
                        el.style.background = '#F0FDF4';
                        setTimeout(() => el.remove(), 1000);
                    }
                });

            } else {
                alert('❌ Impossible de localiser cette adresse à Douala.');
                if (btn) { btn.innerHTML = '<i class="bi bi-geo me-1"></i> Réessayer'; btn.disabled = false; }
            }
        })
        .catch(function() {
            alert('Erreur de connexion.');
            if (btn) { btn.innerHTML = '<i class="bi bi-geo me-1"></i> Réessayer'; btn.disabled = false; }
        });
    };

    // ── Géocoder toutes ──
    document.getElementById('btn-geocoder-tout').addEventListener('click', function() {
        const btns = document.querySelectorAll('.btn-localiser:not(:disabled)');
        if (btns.length === 0) {
            alert('✅ Toutes les livraisons sont déjà localisées !');
            return;
        }
        let delay = 0;
        btns.forEach(function(btn) {
            setTimeout(() => btn.click(), delay);
            delay += 1500;
        });
    });

});
</script>
@endpush