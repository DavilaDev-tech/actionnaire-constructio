<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Actionnaire Construction')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f0f2f5;
            min-height: 100vh;
        }

        /* ── Sidebar ── */
        #sidebar {
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, #1a3c5e 0%, #0d2137 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: scroll;
            padding-bottom: 80px;
        }

        .sidebar-brand {
            padding: 20px 20px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand h5 {
            color: #e8a020;
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 2px;
        }

        .sidebar-brand small {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.7rem;
        }

        .nav-section {
            color: rgba(255, 255, 255, 0.35);
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 16px 20px 6px;
        }

        #sidebar .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 9px 20px;
            border-radius: 0;
            font-size: 0.875rem;
            font-weight: 400;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        #sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.07);
            color: #fff;
            border-left-color: rgba(232, 160, 32, 0.5);
        }

        #sidebar .nav-link.active {
            background: rgba(232, 160, 32, 0.15);
            color: #e8a020;
            border-left-color: #e8a020;
            font-weight: 600;
        }

        #sidebar .nav-link i {
            width: 18px;
            text-align: center;
        }

        /* ── Topbar ── */
        #topbar {
            margin-left: 260px;
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        }

        /* ── Contenu ── */
        #main-content {
            margin-left: 260px;
            padding: 28px;
            min-height: calc(100vh - 62px);
        }

        /* ── Alertes flash ── */
        .flash-container {
            position: fixed;
            top: 75px;
            right: 20px;
            z-index: 9999;
            width: 380px;
        }

        /* ── Avatar ── */
        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e8a020, #c8881a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.875rem;
        }

        /* ── Badge rôle ── */
        .role-badge {
            font-size: 0.65rem;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: 600;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            #sidebar {
                transform: translateX(-100%);
            }

            #sidebar.open {
                transform: translateX(0);
            }

            #topbar,
            #main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

@stack('scripts')

{{-- ── Script suggestions live recherche ── --}}
<script>
    (function () {
        const input = document.getElementById('input-recherche');
        const box = document.getElementById('suggestions-box');
        const form = document.getElementById('form-recherche');

        if (!input || !box) return;

        const types = {
            client: { couleur: '#0d6efd', bg: '#e7f0ff', libelle: 'Client' },
            produit: { couleur: '#198754', bg: '#e6f4ea', libelle: 'Produit' },
            vente: { couleur: '#e8a020', bg: '#fff8e6', libelle: 'Vente' },
            facture: { couleur: '#0dcaf0', bg: '#e4f9fc', libelle: 'Facture' },
            fournisseur: { couleur: '#6c757d', bg: '#f0f1f2', libelle: 'Fournisseur' },
        };

        let timer = null;
        let indexActif = -1;

        input.addEventListener('input', function () {
            const q = this.value.trim();
            clearTimeout(timer);
            indexActif = -1;

            if (q.length < 2) {
                fermerBox();
                return;
            }

            box.innerHTML = `
            <div class="px-4 py-3 text-muted d-flex align-items-center gap-2"
                 style="font-size:0.85rem">
                <div class="spinner-border spinner-border-sm text-secondary"></div>
                Recherche en cours...
            </div>`;
            box.style.display = 'block';

            timer = setTimeout(() => {
                fetch(`/recherche/suggestions?q=${encodeURIComponent(q)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(r => r.json())
                    .then(data => afficherSuggestions(data, q))
                    .catch(() => fermerBox());
            }, 300);
        });

        function afficherSuggestions(data, query) {
            if (data.length === 0) {
                box.innerHTML = `
                <div class="px-4 py-3 text-center text-muted"
                     style="font-size:0.85rem">
                    <i class="bi bi-search me-2"></i>
                    Aucun résultat pour <strong>"${escHtml(query)}"</strong>
                </div>`;
                box.style.display = 'block';
                return;
            }

            const groupes = {};
            data.forEach(item => {
                if (!groupes[item.type]) groupes[item.type] = [];
                groupes[item.type].push(item);
            });

            let html = '';

            Object.keys(groupes).forEach(type => {
                const cfg = types[type] || { couleur: '#333', bg: '#f8f9fa', libelle: type };

                html += `
                <div class="px-3 py-1"
                     style="background:#f8f9fa;font-size:0.68rem;font-weight:700;
                            letter-spacing:1px;text-transform:uppercase;
                            color:#9ca3af;border-bottom:1px solid #f0f0f0;">
                    ${cfg.libelle}s
                </div>`;

                groupes[type].forEach(item => {
                    const labelHigh = highlighter(escHtml(item.label), escHtml(query));
                    html += `
                    <a href="${item.url}"
                       class="suggestion-item d-flex align-items-center gap-3
                              px-3 py-2 text-decoration-none"
                       style="color:#1f2937;transition:background 0.15s;"
                       onmouseover="this.style.background='#f8f9fa'"
                       onmouseout="this.style.background='white'">
                        <div style="width:32px;height:32px;background:${cfg.bg};
                                    border-radius:8px;display:flex;align-items:center;
                                    justify-content:center;flex-shrink:0;">
                            <i class="bi ${item.icone}"
                               style="color:${cfg.couleur};font-size:0.9rem"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="fw-semibold text-truncate"
                                 style="font-size:0.875rem">${labelHigh}</div>
                            <div class="text-truncate"
                                 style="font-size:0.75rem;color:#9ca3af">
                                ${escHtml(item.info)}
                            </div>
                        </div>
                    </a>`;
                });
            });

            html += `
            <div style="border-top:1px solid #f0f0f0;">
                <button type="submit" form="form-recherche"
                        class="w-100 py-2 text-center border-0 bg-white
                               d-flex align-items-center justify-content-center gap-2"
                        style="font-size:0.82rem;color:#1a3c5e;font-weight:600;
                               cursor:pointer;transition:background 0.15s;"
                        onmouseover="this.style.background='#f0f4ff'"
                        onmouseout="this.style.background='white'">
                    <i class="bi bi-search"></i>
                    Voir tous les résultats pour
                    <strong>"${escHtml(query)}"</strong>
                </button>
            </div>`;

            box.innerHTML = html;
            box.style.display = 'block';
            indexActif = -1;
        }

        input.addEventListener('keydown', function (e) {
            const items = box.querySelectorAll('.suggestion-item');
            if (!items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                indexActif = Math.min(indexActif + 1, items.length - 1);
                majActif(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                indexActif = Math.max(indexActif - 1, -1);
                majActif(items);
            } else if (e.key === 'Enter' && indexActif >= 0) {
                e.preventDefault();
                items[indexActif].click();
            } else if (e.key === 'Escape') {
                fermerBox();
            }
        });

        function majActif(items) {
            items.forEach((el, i) => {
                el.style.background = i === indexActif ? '#f0f4ff' : 'white';
            });
            if (indexActif >= 0) {
                input.value = items[indexActif]
                    .querySelector('.fw-semibold')
                    .textContent.trim();
            }
        }

        document.addEventListener('click', function (e) {
            if (!form.contains(e.target)) fermerBox();
        });

        input.addEventListener('focus', function () {
            if (this.value.trim().length >= 2 && box.innerHTML.trim()) {
                box.style.display = 'block';
            }
        });

        function fermerBox() {
            box.style.display = 'none';
            indexActif = -1;
        }

        function highlighter(text, query) {
            const regex = new RegExp(`(${query})`, 'gi');
            return text.replace(regex,
                `<mark style="background:#fff3cd;padding:0 2px;
                              border-radius:3px;font-weight:700;">$1</mark>`
            );
        }

        function escHtml(str) {
            const d = document.createElement('div');
            d.textContent = str;
            return d.innerHTML;
        }
    })();
</script>

{{-- ── Script notifications live ── --}}
<script>
    (function () {
        const badge = document.getElementById('notif-badge');
        const liste = document.getElementById('notif-liste');

        if (!badge || !liste) return;

        function chargerNotifications() {
            fetch('/notifications/non-lues', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.count > 0) {
                        badge.style.display = 'flex';
                        badge.textContent = data.count > 9 ? '9+' : data.count;
                        document.getElementById('btn-cloche').classList.add('cloche-anim');
                        setTimeout(() => {
                            document.getElementById('btn-cloche').classList.remove('cloche-anim');
                        }, 1000);
                    } else {
                        badge.style.display = 'none';
                    }

                    if (data.notifications.length === 0) {
                        liste.innerHTML = `
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-bell-slash fs-2 d-block mb-2 opacity-50"></i>
                            <small>Aucune nouvelle notification</small>
                        </div>`;
                        return;
                    }

                    liste.innerHTML = data.notifications.map(n => `
                    <a href="/notifications/${n.id}/lire"
                       class="d-flex align-items-start gap-3 p-3 border-bottom
                              text-decoration-none text-dark notif-item"
                       style="transition:background 0.15s"
                       onmouseover="this.style.background='#f8f9fa'"
                       onmouseout="this.style.background='white'">
                        <div style="width:38px;height:38px;border-radius:9px;
                                    background:#fff3e0;flex-shrink:0;
                                    display:flex;align-items:center;justify-content:center">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-semibold small text-truncate">
                                ${escHtml(n.titre)}
                            </div>
                            <div class="text-muted" style="font-size:0.78rem">
                                ${escHtml(n.message)}
                            </div>
                            <div style="font-size:0.72rem;color:#9ca3af">
                                ${escHtml(n.date)}
                            </div>
                        </div>
                        <div style="width:8px;height:8px;background:#e8a020;
                                    border-radius:50%;flex-shrink:0;margin-top:6px">
                        </div>
                    </a>
                `).join('');
                })
                .catch(() => { });
        }

        function escHtml(str) {
            const d = document.createElement('div');
            d.textContent = str || '';
            return d.innerHTML;
        }

        chargerNotifications();
        setInterval(chargerNotifications, 60000);
    })();
</script>

{{-- Animation cloche --}}
<style>
    @keyframes clocheAnim {
        0% {
            transform: rotate(0deg);
        }

        15% {
            transform: rotate(15deg);
        }

        30% {
            transform: rotate(-15deg);
        }

        45% {
            transform: rotate(10deg);
        }

        60% {
            transform: rotate(-10deg);
        }

        75% {
            transform: rotate(5deg);
        }

        100% {
            transform: rotate(0deg);
        }
    }

    .cloche-anim i {
        animation: clocheAnim 0.8s ease;
    }
</style>

<body>

    {{-- ══════════════════════════ SIDEBAR ══════════════════════════ --}}
    <nav id="sidebar">

        <!-- Logo -->
        <div class="sidebar-brand">
            <div class="d-flex align-items-center gap-2 mb-1">
                <div style="width:36px;height:36px;background:#e8a020;border-radius:8px;
                    display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-building text-white fw-bold"></i>
                </div>
                <div>
                    <h5 class="mb-0">Actionnaire</h5>
                    <small>Construction</small>
                </div>
            </div>
        </div>

        <!-- Profil utilisateur -->
        <div class="px-3 py-2 border-bottom border-white border-opacity-10">
            <div class="d-flex align-items-center gap-2">
                <div class="user-avatar flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <div class="text-white small fw-semibold text-truncate">
                        {{ auth()->user()->nom_complet }}
                    </div>
                    @php
                        $roleColors = [
                            'admin' => 'warning',
                            'vendeur' => 'success',
                            'magasinier' => 'info',
                            'comptable' => 'primary',
                        ];
                        $roleColor = $roleColors[auth()->user()->role] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $roleColor }} role-badge">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="py-2">

            <!-- Dashboard -->
            <div class="nav-section">Principal</div>
            <a href="{{ route('dashboard') }}" class="nav-link d-flex align-items-center gap-2
                  {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Tableau de bord
            </a>

            <!-- Catalogue -->
            @if(auth()->user()->isAdmin() || auth()->user()->isMagasinier())
                <div class="nav-section">Catalogue</div>

                @if(Route::has('categories.index'))
                    <a href="{{ route('categories.index') }}" class="nav-link d-flex align-items-center gap-2
                          {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i> Catégories
                    </a>
                @endif

                @if(Route::has('produits.index'))
                    <a href="{{ route('produits.index') }}" class="nav-link d-flex align-items-center gap-2
                          {{ request()->routeIs('produits.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i> Produits & Stock
                    </a>
                @endif

                @if(Route::has('fournisseurs.index'))
                    <a href="{{ route('fournisseurs.index') }}" class="nav-link d-flex align-items-center gap-2
                          {{ request()->routeIs('fournisseurs.*') ? 'active' : '' }}">
                        <i class="bi bi-truck"></i> Fournisseurs
                    </a>
                @endif
            @endif

            <!-- Ventes -->
            @if(auth()->user()->isAdmin() || auth()->user()->isVendeur())
                <div class="nav-section">Ventes</div>

                @if(Route::has('clients.index'))
                    <a href="{{ route('clients.index') }}" class="nav-link d-flex align-items-center gap-2
                          {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Clients
                    </a>
                @endif

                @if(Route::has('ventes.index'))
                    <a href="{{ route('ventes.index') }}" class="nav-link d-flex align-items-center gap-2
                          {{ request()->routeIs('ventes.*') ? 'active' : '' }}">
                        <i class="bi bi-cart3"></i> Ventes
                    </a>
                @endif

                @if(Route::has('factures.index'))
                    <a href="{{ route('factures.index') }}" class="nav-link d-flex align-items-center gap-2
                          {{ request()->routeIs('factures.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i> Factures
                    </a>
                @endif
            @endif

            <!-- Finance Comptable -->
            @if(auth()->user()->isComptable())
                <div class="nav-section">Finance</div>

                @if(Route::has('factures.index'))
                    <a href="{{ route('factures.index') }}" class="nav-link d-flex align-items-center gap-2
                          {{ request()->routeIs('factures.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i> Factures
                    </a>
                @endif

                @if(Route::has('paiements.index'))
                    <a href="{{ route('paiements.index') }}" class="nav-link d-flex align-items-center gap-2
                          {{ request()->routeIs('paiements.*') ? 'active' : '' }}">
                        <i class="bi bi-cash-coin"></i> Paiements
                    </a>
                @endif

                @if(Route::has('paiements.rapport'))
                    <a href="{{ route('paiements.rapport') }}" class="nav-link d-flex align-items-center gap-2
                          {{ request()->routeIs('paiements.rapport') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i> Rapport financier
                    </a>
                @endif
            @endif

            <!-- Finance Admin -->
            @if(auth()->user()->isAdmin())
                <div class="nav-section">Finance</div>

                @if(Route::has('paiements.index'))
                    <a href="{{ route('paiements.index') }}" class="nav-link d-flex align-items-center gap-2
                          {{ request()->routeIs('paiements.*') ? 'active' : '' }}">
                        <i class="bi bi-cash-coin"></i> Paiements
                    </a>
                @endif

                @if(Route::has('paiements.rapport'))
                    <a href="{{ route('paiements.rapport') }}" class="nav-link d-flex align-items-center gap-2
                          {{ request()->routeIs('paiements.rapport') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i> Rapport financier
                    </a>
                @endif
            @endif

            <!-- Logistique -->
            @if(auth()->user()->isAdmin() || auth()->user()->isMagasinier())
                <div class="nav-section">Logistique</div>

                <a href="{{ route('livraisons.index') }}" class="nav-link d-flex align-items-center gap-2
                      {{ request()->routeIs('livraisons.index') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt"></i> Livraisons
                </a>

                <a href="{{ route('livraisons.carte') }}" class="nav-link d-flex align-items-center gap-2
                      {{ request()->routeIs('livraisons.carte') ? 'active' : '' }}"
                    style="padding-left:35px;font-size:0.82rem">
                    <i class="bi bi-map"></i> Carte livraisons
                </a>

                <a href="{{ route('approvisionnements.index') }}" class="nav-link d-flex align-items-center gap-2
                      {{ request()->routeIs('approvisionnements.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-down-circle"></i> Approvisionnements
                </a>
            @endif

            <!-- Vendeur voit livraisons -->
            @if(auth()->user()->isVendeur())
                <div class="nav-section">Logistique</div>
                @if(Route::has('livraisons.index'))
                    <a href="{{ route('livraisons.index') }}" class="nav-link d-flex align-items-center gap-2
                          {{ request()->routeIs('livraisons.*') ? 'active' : '' }}">
                        <i class="bi bi-geo-alt"></i> Livraisons
                    </a>
                @endif
            @endif

            <!-- Administration -->
            @if(auth()->user()->isAdmin())
                <div class="nav-section">Administration</div>

                @if(Route::has('users.index'))
                    <a href="{{ route('users.index') }}" class="nav-link d-flex align-items-center gap-2
                          {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-person-gear"></i> Utilisateurs
                    </a>
                @endif
                @if(Route::has('activites.index'))
                        <a href="{{ route('activites.index') }}" class="nav-link d-flex align-items-center gap-2
                      {{ request()->routeIs('activites.*') ? 'active' : '' }}">
                            <i class="bi bi-clock-history"></i> Journal activités
                        </a>
                @endif
                {{-- ← NOUVEAU : Sauvegardes --}}
                @if(Route::has('backups.index'))
                    <a href="{{ route('backups.index') }}" class="nav-link d-flex align-items-center gap-2
                          {{ request()->routeIs('backups.*') ? 'active' : '' }}">
                        <i class="bi bi-archive"></i> Sauvegardes
                    </a>
                @endif

            @endif

        </div>

        <!-- Déconnexion -->
        <div class="mt-auto p-3 border-top border-white border-opacity-10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm w-100 text-start d-flex align-items-center gap-2" style="color:rgba(255,255,255,0.6);background:none;border:none;
                           padding:8px 12px;border-radius:6px;"
                    onmouseover="this.style.background='rgba(255,255,255,0.08)';this.style.color='white'"
                    onmouseout="this.style.background='none';this.style.color='rgba(255,255,255,0.6)'">
                    <i class="bi bi-box-arrow-left"></i> Déconnexion
                </button>
            </form>
        </div>

    </nav>

    {{-- ══════════════════════════ TOPBAR ══════════════════════════ --}}
    <div id="topbar">

        {{-- Gauche --}}
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-md-none"
                onclick="document.getElementById('sidebar').classList.toggle('open')">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h6 class="mb-0 text-muted fw-normal d-none d-md-block">
                <i class="bi bi-house me-1"></i>
                @yield('page-title', 'Dashboard')
            </h6>
        </div>

        {{-- Centre : Barre de recherche --}}
        <form action="{{ route('recherche') }}" method="GET" style="position:relative;width:360px" id="form-recherche">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"
                    style="border-radius:10px 0 0 10px;border-color:#e5e7eb;">
                    <i class="bi bi-search text-muted small"></i>
                </span>
                <input type="text" name="q" id="input-recherche" class="form-control border-start-0 border-end-0"
                    placeholder="Rechercher client, produit, vente..." value="{{ request('q') }}" autocomplete="off"
                    style="border-color:#e5e7eb;font-size:0.85rem;" minlength="2">
                <button type="submit" class="btn text-white" style="background:#1a3c5e;border-radius:0 10px 10px 0;
                           border:none;font-size:0.85rem;">
                    <i class="bi bi-search me-1"></i>
                    <span class="d-none d-lg-inline">Chercher</span>
                </button>
            </div>

            {{-- Box suggestions --}}
            <div id="suggestions-box" style="display:none;position:absolute;top:calc(100% + 6px);left:0;
                    width:100%;background:white;border-radius:12px;
                    box-shadow:0 8px 30px rgba(0,0,0,0.12);z-index:9999;
                    border:1px solid #e5e7eb;overflow:hidden;
                    max-height:420px;overflow-y:auto;">
            </div>
        </form>

        {{-- Droite --}}
        <div class="d-flex align-items-center gap-3">

            <!-- Cloche notifications -->
            <div class="dropdown" id="notif-dropdown">
                <button class="btn position-relative p-2 border-0" data-bs-toggle="dropdown" id="btn-cloche"
                    style="background:none">
                    <i class="bi bi-bell fs-5 text-muted"></i>
                    <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle
                             badge rounded-pill bg-danger" style="display:none;font-size:0.65rem">0</span>
                </button>

                <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0"
                    style="width:360px;border-radius:12px;overflow:hidden">
                    <div class="d-flex justify-content-between align-items-center px-3 py-2"
                        style="background:#1a3c5e;color:white">
                        <span class="fw-semibold">
                            <i class="bi bi-bell me-2"></i>Notifications
                        </span>
                        <form method="POST" action="{{ route('notifications.tout-lire') }}">
                            @csrf
                            <button class="btn btn-sm text-white opacity-75 p-0 border-0" style="font-size:0.78rem">
                                Tout marquer lu
                            </button>
                        </form>
                    </div>

                    <div id="notif-liste" style="max-height:320px;overflow-y:auto">
                        <div class="text-center text-muted py-4" id="notif-vide">
                            <i class="bi bi-bell-slash fs-2 d-block mb-2 opacity-50"></i>
                            <small>Aucune nouvelle notification</small>
                        </div>
                    </div>

                    <div class="border-top text-center py-2">
                        <a href="{{ route('notifications.index') }}" class="text-decoration-none small fw-semibold"
                            style="color:#1a3c5e">
                            Voir toutes les notifications
                        </a>
                    </div>
                </div>
            </div>

            <!-- Avatar + dropdown -->
            <div class="dropdown">
                <button class="btn d-flex align-items-center gap-2 p-0 border-0" data-bs-toggle="dropdown">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                    </div>
                    <span class="d-none d-md-block small fw-semibold text-muted">
                        {{ auth()->user()->prenom }}
                    </span>
                    <i class="bi bi-chevron-down small text-muted"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li class="px-3 py-2 border-bottom">
                        <div class="fw-semibold">{{ auth()->user()->nom_complet }}</div>
                        <small class="text-muted">{{ auth()->user()->email }}</small>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger d-flex align-items-center gap-2">
                                <i class="bi bi-box-arrow-left"></i> Déconnexion
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════ FLASH MESSAGES ══════════════════════════ --}}
    <div class="flash-container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible shadow-sm fade show border-0" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible shadow-sm fade show border-0" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════ CONTENU ══════════════════════════ --}}
    <div id="main-content">
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(el => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
                bsAlert.close();
            });
        }, 4000);
    </script>

    @stack('scripts')

</body>

</html>