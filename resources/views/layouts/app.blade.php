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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')

    <style>
        /* ══════════════════════════════════════
           VARIABLES & BASE
        ══════════════════════════════════════ */
        :root {
            --orange:        #F97316;
            --orange-dark:   #EA580C;
            --orange-light:  #FFF7ED;
            --orange-glow:   rgba(249, 115, 22, 0.15);
            --gray-900:      #111827;
            --gray-800:      #1F2937;
            --gray-700:      #374151;
            --gray-600:      #4B5563;
            --gray-400:      #9CA3AF;
            --gray-200:      #E5E7EB;
            --gray-100:      #F3F4F6;
            --gray-50:       #F9FAFB;
            --white:         #FFFFFF;
            --sidebar-w:     265px;
            --topbar-h:      64px;
            --radius:        12px;
            --shadow-sm:     0 1px 3px rgba(0,0,0,0.08);
            --shadow-md:     0 4px 16px rgba(0,0,0,0.10);
            --shadow-lg:     0 8px 32px rgba(0,0,0,0.12);
        }

        * {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        body {
            background: var(--gray-100);
            min-height: 100vh;
            color: var(--gray-800);
        }

        /* ══════════════════════════════════════
           SCROLLBAR
        ══════════════════════════════════════ */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover { background: var(--orange); }

        /* ══════════════════════════════════════
           SIDEBAR
        ══════════════════════════════════════ */
        #sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(160deg, var(--gray-900) 0%, var(--gray-800) 60%, #2D1810 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
            overflow-y: auto;
            overflow-x: hidden;
            padding-bottom: 80px;
            border-right: 1px solid rgba(255,255,255,0.04);
        }

        /* Logo */
        .sidebar-brand {
            padding: 22px 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            background: rgba(0,0,0,0.15);
        }

        .sidebar-logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(249,115,22,0.4);
            flex-shrink: 0;
        }

        .sidebar-brand h5 {
            color: var(--white);
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 1px;
            letter-spacing: -0.3px;
        }

        .sidebar-brand small {
            color: var(--gray-400);
            font-size: 0.68rem;
            letter-spacing: 0.5px;
        }

        /* Profil sidebar */
        .sidebar-profile {
            padding: 14px 16px;
            margin: 10px 12px;
            background: rgba(255,255,255,0.04);
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.06);
        }

        .sidebar-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.85rem;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(249,115,22,0.35);
        }

        /* Sections nav */
        .nav-section {
            color: rgba(255,255,255,0.25);
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 18px 20px 6px;
        }

        /* Liens nav */
        #sidebar .nav-link {
            color: rgba(255,255,255,0.55);
            padding: 9px 16px;
            margin: 1px 10px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 400;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        #sidebar .nav-link i {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        #sidebar .nav-link:hover {
            background: rgba(255,255,255,0.07);
            color: var(--white);
            transform: translateX(2px);
        }

        #sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--orange-glow), rgba(249,115,22,0.08));
            color: var(--orange);
            font-weight: 600;
            border: 1px solid rgba(249,115,22,0.2);
            box-shadow: 0 2px 8px rgba(249,115,22,0.1);
        }

        #sidebar .nav-link.active i {
            color: var(--orange);
        }

        /* Déconnexion */
        .sidebar-logout {
            margin: 8px 10px;
            padding: 9px 16px;
            border-radius: 8px;
            color: rgba(255,255,255,0.45);
            background: none;
            border: none;
            width: calc(100% - 20px);
            text-align: left;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            transition: all 0.2s;
            cursor: pointer;
        }

        .sidebar-logout:hover {
            background: rgba(239,68,68,0.1);
            color: #FCA5A5;
        }

        /* ══════════════════════════════════════
           TOPBAR
        ══════════════════════════════════════ */
        #topbar {
            margin-left: var(--sidebar-w);
            height: var(--topbar-h);
            background: var(--gray-200);
            border-bottom: 1px solid var(--gray-200);
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: var(--shadow-sm);
        }

        .topbar-title {
            font-size: 0.875rem;
            color: var(--gray-500);
            font-weight: 400;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .topbar-title i { color: var(--orange); }

        /* Barre de recherche topbar */
        .topbar-search .input-group-text {
            background: var(--gray-200);
            border-color: var(--gray-200);
            border-right: none;
            border-radius: 10px 0 0 10px;
        }

        .topbar-search .form-control {
            background: var(--gray-50);
            border-color: var(--gray-700);
            border-left: none;
            border-right: none;
            font-size: 0.85rem;
        }

        .topbar-search .form-control:focus {
            box-shadow: none;
            border-color: var(--orange);
            background: var(--white);
        }

        .topbar-search .btn-search {
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            border: none;
            border-radius: 0 10px 10px 0;
            color: white;
            font-size: 0.85rem;
            padding: 0 16px;
            transition: all 0.2s;
        }

        .topbar-search .btn-search:hover {
            background: linear-gradient(135deg, var(--orange-dark), #C2410C);
            transform: translateX(1px);
        }

        /* Avatar topbar */
        .topbar-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.85rem;
            box-shadow: 0 2px 8px rgba(249,115,22,0.3);
        }

        /* Bouton cloche */
        .btn-notif {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-600);
            transition: all 0.2s;
            position: relative;
        }

        .btn-notif:hover {
            background: var(--orange-light);
            border-color: var(--orange);
            color: var(--orange);
        }

        /* ══════════════════════════════════════
           CONTENU PRINCIPAL
        ══════════════════════════════════════ */
        #main-content {
            margin-left: var(--sidebar-w);
            padding: 28px;
            min-height: calc(100vh - var(--topbar-h));
        }

        /* ══════════════════════════════════════
           CARTES
        ══════════════════════════════════════ */
        .card {
            border: 1px solid var(--gray-200) !important;
            border-radius: var(--radius) !important;
            box-shadow: var(--shadow-sm) !important;
            transition: box-shadow 0.2s, transform 0.2s;
        }

        .card:hover { box-shadow: var(--shadow-md) !important; }

        .card-header {
            border-bottom: 1px solid var(--gray-200) !important;
            border-radius: var(--radius) var(--radius) 0 0 !important;
            padding: 16px 20px !important;
            font-weight: 600;
        }

        /* ══════════════════════════════════════
           BOUTONS
        ══════════════════════════════════════ */
        .btn-primary {
            background: linear-gradient(135deg, var(--orange), var(--orange-dark)) !important;
            border: none !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
            box-shadow: 0 2px 8px rgba(249,115,22,0.3) !important;
            transition: all 0.2s !important;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--orange-dark), #C2410C) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 16px rgba(249,115,22,0.4) !important;
        }

        .btn-outline-primary {
            border-color: var(--orange) !important;
            color: var(--orange) !important;
            border-radius: 8px !important;
        }

        .btn-outline-primary:hover {
            background: var(--orange) !important;
            color: white !important;
        }

        .btn-success {
            background: linear-gradient(135deg, #10B981, #059669) !important;
            border: none !important;
            border-radius: 8px !important;
        }

        .btn-danger {
            background: linear-gradient(135deg, #EF4444, #DC2626) !important;
            border: none !important;
            border-radius: 8px !important;
        }

        .btn-warning {
            background: linear-gradient(135deg, #F59E0B, #D97706) !important;
            border: none !important;
            border-radius: 8px !important;
            color: white !important;
        }

        .btn-info {
            background: linear-gradient(135deg, #3B82F6, #2563EB) !important;
            border: none !important;
            border-radius: 8px !important;
            color: white !important;
        }

        /* ══════════════════════════════════════
           TABLEAUX
        ══════════════════════════════════════ */
        .table {
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        .table thead th {
            background: var(--gray-800) !important;
            color: var(--gray-100) !important;
            font-weight: 600 !important;
            font-size: 0.8rem !important;
            letter-spacing: 0.5px !important;
            text-transform: uppercase !important;
            padding: 12px 16px !important;
            border: none !important;
        }

        .table thead th:first-child {
            border-radius: 8px 0 0 0 !important;
        }

        .table thead th:last-child {
            border-radius: 0 8px 0 0 !important;
        }

        .table tbody td {
            padding: 13px 16px !important;
            vertical-align: middle !important;
            border-bottom: 1px solid var(--gray-100) !important;
            border-top: none !important;
            font-size: 0.875rem !important;
        }

        .table tbody tr:hover td {
            background: var(--orange-light) !important;
        }

        .table tbody tr:last-child td {
            border-bottom: none !important;
        }

        /* ══════════════════════════════════════
           BADGES
        ══════════════════════════════════════ */
        .badge {
            border-radius: 6px !important;
            font-weight: 500 !important;
            font-size: 0.72rem !important;
            padding: 4px 10px !important;
        }

        /* ══════════════════════════════════════
           FORMULAIRES
        ══════════════════════════════════════ */
        .form-control, .form-select {
            border-color: var(--gray-200) !important;
            border-radius: 8px !important;
            font-size: 0.875rem !important;
            padding: 10px 14px !important;
            transition: all 0.2s !important;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--orange) !important;
            box-shadow: 0 0 0 3px rgba(249,115,22,0.1) !important;
        }

        .form-label {
            font-weight: 500 !important;
            font-size: 0.875rem !important;
            color: var(--gray-700) !important;
            margin-bottom: 6px !important;
        }

        /* ══════════════════════════════════════
           ALERTS FLASH
        ══════════════════════════════════════ */
        .flash-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            width: 360px;
        }

        .alert {
            border-radius: 10px !important;
            border: none !important;
            box-shadow: var(--shadow-lg) !important;
            font-size: 0.875rem !important;
            padding: 14px 18px !important;
        }

        .alert-success {
            background: linear-gradient(135deg, #ECFDF5, #D1FAE5) !important;
            color: #065F46 !important;
            border-left: 4px solid #10B981 !important;
        }

        .alert-danger {
            background: linear-gradient(135deg, #FEF2F2, #FEE2E2) !important;
            color: #991B1B !important;
            border-left: 4px solid #EF4444 !important;
        }

        /* ══════════════════════════════════════
           PAGE TITLE
        ══════════════════════════════════════ */
        .page-header {
            margin-bottom: 24px;
        }

        .page-header h4 {
            font-weight: 700;
            font-size: 1.4rem;
            color: var(--gray-900);
            margin-bottom: 4px;
        }

        .page-header p {
            color: var(--gray-500);
            font-size: 0.875rem;
            margin: 0;
        }

        /* ══════════════════════════════════════
           STAT CARDS
        ══════════════════════════════════════ */
        .stat-card {
            border-radius: var(--radius) !important;
            border: none !important;
            overflow: hidden;
            position: relative;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            background: rgba(255,255,255,0.15);
        }

        /* ══════════════════════════════════════
           PAGINATION
        ══════════════════════════════════════ */
        .pagination .page-link {
            border-radius: 6px !important;
            margin: 0 2px !important;
            border-color: var(--gray-200) !important;
            color: var(--gray-600) !important;
            font-size: 0.85rem !important;
            padding: 6px 12px !important;
            transition: all 0.2s !important;
        }

        .pagination .page-item.active .page-link {
            background: var(--orange) !important;
            border-color: var(--orange) !important;
            color: white !important;
            box-shadow: 0 2px 8px rgba(249,115,22,0.3) !important;
        }

        .pagination .page-link:hover {
            background: var(--orange-light) !important;
            border-color: var(--orange) !important;
            color: var(--orange) !important;
        }

        /* ══════════════════════════════════════
           ANIMATIONS
        ══════════════════════════════════════ */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes clocheAnim {
            0%   { transform: rotate(0deg);   }
            15%  { transform: rotate(15deg);  }
            30%  { transform: rotate(-15deg); }
            45%  { transform: rotate(10deg);  }
            60%  { transform: rotate(-10deg); }
            75%  { transform: rotate(5deg);   }
            100% { transform: rotate(0deg);   }
        }

        .cloche-anim i { animation: clocheAnim 0.8s ease; }

        .fade-in-up {
            animation: fadeInUp 0.4s ease forwards;
        }

        /* ══════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════ */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #topbar, #main-content { margin-left: 0; }
        }
    </style>
</head>

@stack('scripts')

{{-- Script suggestions recherche --}}
<script>
(function () {
    const input = document.getElementById('input-recherche');
    const box   = document.getElementById('suggestions-box');
    const form  = document.getElementById('form-recherche');

    if (!input || !box) return;

    const types = {
        client:      { couleur: '#F97316', bg: '#FFF7ED', libelle: 'Client' },
        produit:     { couleur: '#10B981', bg: '#ECFDF5', libelle: 'Produit' },
        vente:       { couleur: '#3B82F6', bg: '#EFF6FF', libelle: 'Vente' },
        facture:     { couleur: '#8B5CF6', bg: '#F5F3FF', libelle: 'Facture' },
        fournisseur: { couleur: '#6B7280', bg: '#F9FAFB', libelle: 'Fournisseur' },
    };

    let timer = null;
    let indexActif = -1;

    input.addEventListener('input', function () {
        const q = this.value.trim();
        clearTimeout(timer);
        indexActif = -1;

        if (q.length < 2) { fermerBox(); return; }

        box.innerHTML = `<div class="px-4 py-3 text-muted d-flex align-items-center gap-2" style="font-size:0.85rem">
            <div class="spinner-border spinner-border-sm" style="color:#F97316"></div>
            Recherche en cours...</div>`;
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
            box.innerHTML = `<div class="px-4 py-3 text-center text-muted" style="font-size:0.85rem">
                <i class="bi bi-search me-2"></i>Aucun résultat pour <strong>"${escHtml(query)}"</strong></div>`;
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
            html += `<div class="px-3 py-1" style="background:#F9FAFB;font-size:0.68rem;font-weight:700;
                letter-spacing:1px;text-transform:uppercase;color:#9CA3AF;border-bottom:1px solid #F3F4F6;">
                ${cfg.libelle}s</div>`;

            groupes[type].forEach(item => {
                const labelHigh = highlighter(escHtml(item.label), escHtml(query));
                html += `<a href="${item.url}" class="suggestion-item d-flex align-items-center gap-3 px-3 py-2 text-decoration-none"
                    style="color:#1F2937;transition:background 0.15s;"
                    onmouseover="this.style.background='#FFF7ED'"
                    onmouseout="this.style.background='white'">
                    <div style="width:32px;height:32px;background:${cfg.bg};border-radius:8px;
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi ${item.icone}" style="color:${cfg.couleur};font-size:0.9rem"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-semibold text-truncate" style="font-size:0.875rem">${labelHigh}</div>
                        <div class="text-truncate" style="font-size:0.75rem;color:#9CA3AF">${escHtml(item.info)}</div>
                    </div></a>`;
            });
        });

        html += `<div style="border-top:1px solid #F3F4F6;">
            <button type="submit" form="form-recherche" class="w-100 py-2 text-center border-0 bg-white
                d-flex align-items-center justify-content-center gap-2"
                style="font-size:0.82rem;color:#F97316;font-weight:600;cursor:pointer;transition:background 0.15s;"
                onmouseover="this.style.background='#FFF7ED'"
                onmouseout="this.style.background='white'">
                <i class="bi bi-search"></i> Voir tous les résultats pour <strong>"${escHtml(query)}"</strong>
            </button></div>`;

        box.innerHTML = html;
        box.style.display = 'block';
        indexActif = -1;
    }

    input.addEventListener('keydown', function (e) {
        const items = box.querySelectorAll('.suggestion-item');
        if (!items.length) return;
        if (e.key === 'ArrowDown') { e.preventDefault(); indexActif = Math.min(indexActif+1, items.length-1); majActif(items); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); indexActif = Math.max(indexActif-1, -1); majActif(items); }
        else if (e.key === 'Enter' && indexActif >= 0) { e.preventDefault(); items[indexActif].click(); }
        else if (e.key === 'Escape') fermerBox();
    });

    function majActif(items) {
        items.forEach((el, i) => { el.style.background = i === indexActif ? '#FFF7ED' : 'white'; });
        if (indexActif >= 0) input.value = items[indexActif].querySelector('.fw-semibold').textContent.trim();
    }

    document.addEventListener('click', e => { if (!form.contains(e.target)) fermerBox(); });
    input.addEventListener('focus', function() {
        if (this.value.trim().length >= 2 && box.innerHTML.trim()) box.style.display = 'block';
    });

    function fermerBox() { box.style.display = 'none'; indexActif = -1; }
    function highlighter(text, query) {
        return text.replace(new RegExp(`(${query})`, 'gi'),
            '<mark style="background:#FED7AA;padding:0 2px;border-radius:3px;font-weight:700;">$1</mark>');
    }
    function escHtml(str) { const d = document.createElement('div'); d.textContent = str; return d.innerHTML; }
})();
</script>

{{-- Script notifications --}}
<script>
(function () {
    const badge = document.getElementById('notif-badge');
    const liste = document.getElementById('notif-liste');
    if (!badge || !liste) return;

    function chargerNotifications() {
        fetch('/notifications/non-lues', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.count > 0) {
                badge.style.display = 'flex';
                badge.textContent = data.count > 9 ? '9+' : data.count;
                document.getElementById('btn-cloche').classList.add('cloche-anim');
                setTimeout(() => document.getElementById('btn-cloche').classList.remove('cloche-anim'), 1000);
            } else {
                badge.style.display = 'none';
            }

            if (data.notifications.length === 0) {
                liste.innerHTML = `<div class="text-center text-muted py-4">
                    <i class="bi bi-bell-slash fs-2 d-block mb-2 opacity-50"></i>
                    <small>Aucune nouvelle notification</small></div>`;
                return;
            }

            liste.innerHTML = data.notifications.map(n => `
            <a href="/notifications/${n.id}/lire"
               class="d-flex align-items-start gap-3 p-3 border-bottom text-decoration-none text-dark"
               style="transition:background 0.15s"
               onmouseover="this.style.background='#FFF7ED'"
               onmouseout="this.style.background='white'">
                <div style="width:36px;height:36px;border-radius:8px;background:#FFF7ED;flex-shrink:0;
                    display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-exclamation-triangle" style="color:#F97316"></i>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="fw-semibold small text-truncate">${escHtml(n.titre)}</div>
                    <div class="text-muted" style="font-size:0.78rem">${escHtml(n.message)}</div>
                    <div style="font-size:0.72rem;color:#9CA3AF">${escHtml(n.date)}</div>
                </div>
                <div style="width:7px;height:7px;background:#F97316;border-radius:50%;flex-shrink:0;margin-top:6px"></div>
            </a>`).join('');
        })
        .catch(() => {});
    }

    function escHtml(str) { const d = document.createElement('div'); d.textContent = str || ''; return d.innerHTML; }
    chargerNotifications();
    setInterval(chargerNotifications, 60000);
})();
</script>

{{-- Pagination AJAX --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        const link = e.target.closest('.ajax-page');
        if (!link) return;
        e.preventDefault();
        const page = link.dataset.page;
        const url  = new URL(window.location.href);
        url.searchParams.set('page', page);

        const tableau = document.getElementById('tableau-ventes') ||
                        document.getElementById('tableau-clients') ||
                        document.getElementById('tableau-produits') ||
                        document.getElementById('tableau-factures');

        if (tableau) { tableau.style.opacity = '0.5'; tableau.style.transition = 'opacity 0.2s'; }

        fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            if (data.html && tableau) { tableau.innerHTML = data.html; tableau.style.opacity = '1'; }
            if (data.pagination) {
                const pEl = document.getElementById('pagination-ventes') ||
                            document.getElementById('pagination-clients') ||
                            document.getElementById('pagination-produits') ||
                            document.getElementById('pagination-factures');
                if (pEl) pEl.innerHTML = data.pagination;
            }
            window.history.pushState({}, '', url.toString());
            if (tableau) tableau.scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(() => { if (tableau) tableau.style.opacity = '1'; });
    });
});
</script>

<body>

{{-- ════════════ SIDEBAR ════════════ --}}
<nav id="sidebar">

    {{-- Logo --}}
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-3">
            <div class="sidebar-logo-icon">
                <i class="bi bi-building-fill text-white"></i>
            </div>
            <div>
                <h5 class="mb-0">Actionnaire</h5>
                <small>Construction · Gestion</small>
            </div>
        </div>
    </div>

    {{-- Profil --}}
    <div class="px-3 pt-3">
        <div class="sidebar-profile">
            <div class="d-flex align-items-center gap-2">
                <div class="sidebar-avatar">
                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                </div>
                <div class="overflow-hidden flex-grow-1">
                    <div class="text-white small fw-semibold text-truncate" style="font-size:0.82rem">
                        {{ auth()->user()->nom_complet }}
                    </div>
                    @php
                        $roleColors = [
                            'admin'      => '#F97316',
                            'vendeur'    => '#10B981',
                            'magasinier' => '#3B82F6',
                            'comptable'  => '#8B5CF6',
                        ];
                        $rc = $roleColors[auth()->user()->role] ?? '#6B7280';
                    @endphp
                    <span style="font-size:0.65rem;color:{{ $rc }};font-weight:600;letter-spacing:0.5px">
                        ● {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <div class="py-1">

        <div class="nav-section">Principal</div>
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Tableau de bord
        </a>

        @if(auth()->user()->isAdmin() || auth()->user()->isMagasinier())
        <div class="nav-section">Catalogue</div>

        @if(Route::has('categories.index'))
        <a href="{{ route('categories.index') }}"
           class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i> Catégories
        </a>
        @endif

        @if(Route::has('produits.index'))
        <a href="{{ route('produits.index') }}"
           class="nav-link {{ request()->routeIs('produits.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i> Produits & Stock
        </a>
        @endif

        @if(Route::has('fournisseurs.index'))
        <a href="{{ route('fournisseurs.index') }}"
           class="nav-link {{ request()->routeIs('fournisseurs.*') ? 'active' : '' }}">
            <i class="bi bi-truck"></i> Fournisseurs
        </a>
        @endif
        @endif

        @if(auth()->user()->isAdmin() || auth()->user()->isVendeur())
        <div class="nav-section">Ventes</div>

        @if(Route::has('clients.index'))
        <a href="{{ route('clients.index') }}"
           class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Clients
        </a>
        @endif

        @if(Route::has('ventes.index'))
        <a href="{{ route('ventes.index') }}"
           class="nav-link {{ request()->routeIs('ventes.*') ? 'active' : '' }}">
            <i class="bi bi-cart3"></i> Ventes
        </a>
        @endif

        @if(Route::has('factures.index'))
        <a href="{{ route('factures.index') }}"
           class="nav-link {{ request()->routeIs('factures.*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Factures
        </a>
        @endif
        @endif

        @if(auth()->user()->isComptable())
        <div class="nav-section">Finance</div>

        @if(Route::has('factures.index'))
        <a href="{{ route('factures.index') }}"
           class="nav-link {{ request()->routeIs('factures.*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Factures
        </a>
        @endif

        @if(Route::has('paiements.index'))
        <a href="{{ route('paiements.index') }}"
           class="nav-link {{ request()->routeIs('paiements.*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i> Paiements
        </a>
        @endif

        @if(Route::has('paiements.rapport'))
        <a href="{{ route('paiements.rapport') }}"
           class="nav-link {{ request()->routeIs('paiements.rapport') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i> Rapport financier
        </a>
        @endif
        @endif

        @if(auth()->user()->isAdmin())
        <div class="nav-section">Finance</div>

        @if(Route::has('paiements.index'))
        <a href="{{ route('paiements.index') }}"
           class="nav-link {{ request()->routeIs('paiements.*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i> Paiements
        </a>
        @endif

        @if(Route::has('paiements.rapport'))
        <a href="{{ route('paiements.rapport') }}"
           class="nav-link {{ request()->routeIs('paiements.rapport') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i> Rapport financier
        </a>
        @endif
        @endif

        @if(auth()->user()->isAdmin() || auth()->user()->isMagasinier())
        <div class="nav-section">Logistique</div>

        <a href="{{ route('livraisons.index') }}"
           class="nav-link {{ request()->routeIs('livraisons.index') ? 'active' : '' }}">
            <i class="bi bi-geo-alt"></i> Livraisons
        </a>

        <a href="{{ route('livraisons.carte') }}"
           class="nav-link {{ request()->routeIs('livraisons.carte') ? 'active' : '' }}"
           style="padding-left:38px;font-size:0.82rem">
            <i class="bi bi-map"></i> Carte livraisons
        </a>

        <a href="{{ route('approvisionnements.index') }}"
           class="nav-link {{ request()->routeIs('approvisionnements.*') ? 'active' : '' }}">
            <i class="bi bi-arrow-down-circle"></i> Approvisionnements
        </a>
        @endif

        @if(auth()->user()->isVendeur())
        <div class="nav-section">Logistique</div>
        @if(Route::has('livraisons.index'))
        <a href="{{ route('livraisons.index') }}"
           class="nav-link {{ request()->routeIs('livraisons.*') ? 'active' : '' }}">
            <i class="bi bi-geo-alt"></i> Livraisons
        </a>
        @endif
        @endif

        @if(auth()->user()->isAdmin())
        <div class="nav-section">Administration</div>

        @if(Route::has('users.index'))
        <a href="{{ route('users.index') }}"
           class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-person-gear"></i> Utilisateurs
        </a>
        @endif

        @if(Route::has('activites.index'))
        <a href="{{ route('activites.index') }}"
           class="nav-link {{ request()->routeIs('activites.*') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i> Journal activités
        </a>
        @endif

        @if(Route::has('backups.index'))
        <a href="{{ route('backups.index') }}"
           class="nav-link {{ request()->routeIs('backups.*') ? 'active' : '' }}">
            <i class="bi bi-archive"></i> Sauvegardes
        </a>
        @endif
        @endif

    </div>

    {{-- Déconnexion --}}
    <div style="border-top:1px solid rgba(255,255,255,0.06);margin-top:8px;padding-top:8px">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-logout">
                <i class="bi bi-box-arrow-left"></i> Déconnexion
            </button>
        </form>
    </div>

</nav>

{{-- ════════════ TOPBAR ════════════ --}}
<div id="topbar">

    {{-- Gauche --}}
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-sm d-md-none"
                style="background:var(--gray-100);border:1px solid var(--gray-200);border-radius:8px"
                onclick="document.getElementById('sidebar').classList.toggle('open')">
            <i class="bi bi-list fs-5"></i>
        </button>
        <div class="topbar-title d-none d-md-flex">
            <i class="bi bi-house-fill"></i>
            @yield('page-title', 'Dashboard')
        </div>
    </div>

    {{-- Centre : Recherche --}}
    <form action="{{ route('recherche') }}" method="GET"
          style="position:relative;width:380px" id="form-recherche"
          class="topbar-search">
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-search" style="color:#9CA3AF;font-size:0.85rem"></i>
            </span>
            <input type="text" name="q" id="input-recherche"
                   class="form-control"
                   placeholder="Rechercher client, produit, vente..."
                   value="{{ request('q') }}"
                   autocomplete="off"
                   minlength="2">
            <button type="submit" class="btn-search">
                <i class="bi bi-search me-1"></i>
                <span class="d-none d-lg-inline">Chercher</span>
            </button>
        </div>
        <div id="suggestions-box"
             style="display:none;position:absolute;top:calc(100% + 6px);left:0;
                    width:100%;background:white;border-radius:12px;
                    box-shadow:0 8px 30px rgba(0,0,0,0.12);z-index:9999;
                    border:1px solid var(--gray-200);overflow:hidden;
                    max-height:420px;overflow-y:auto;">
        </div>
    </form>

    {{-- Droite --}}
    <div class="d-flex align-items-center gap-2">

        {{-- Cloche --}}
        <div class="dropdown">
            <button class="btn-notif" data-bs-toggle="dropdown" id="btn-cloche">
                <i class="bi bi-bell" style="font-size:1rem"></i>
                <span id="notif-badge"
                      class="position-absolute"
                      style="display:none;top:-4px;right:-4px;
                             background:#EF4444;color:white;
                             border-radius:50%;width:18px;height:18px;
                             font-size:0.65rem;display:none;
                             align-items:center;justify-content:center;
                             font-weight:700">0</span>
            </button>

            <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0"
                 style="width:360px;border-radius:12px;overflow:hidden;
                        border:1px solid var(--gray-200)">
                <div class="d-flex justify-content-between align-items-center px-3 py-2"
                     style="background:linear-gradient(135deg,var(--orange),var(--orange-dark));color:white">
                    <span class="fw-semibold small">
                        <i class="bi bi-bell me-2"></i>Notifications
                    </span>
                    <form method="POST" action="{{ route('notifications.tout-lire') }}">
                        @csrf
                        <button class="btn btn-sm text-white opacity-75 p-0 border-0"
                                style="font-size:0.75rem">
                            Tout marquer lu
                        </button>
                    </form>
                </div>
                <div id="notif-liste" style="max-height:320px;overflow-y:auto">
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-bell-slash fs-2 d-block mb-2 opacity-50"></i>
                        <small>Aucune nouvelle notification</small>
                    </div>
                </div>
                <div style="border-top:1px solid var(--gray-200)" class="text-center py-2">
                    <a href="{{ route('notifications.index') }}"
                       class="text-decoration-none small fw-semibold"
                       style="color:var(--orange)">
                        Voir toutes les notifications
                    </a>
                </div>
            </div>
        </div>

        {{-- Séparateur --}}
        <div style="width:1px;height:24px;background:var(--gray-200)"></div>

        {{-- Avatar --}}
        <div class="dropdown">
            <button class="btn d-flex align-items-center gap-2 p-0 border-0"
                    data-bs-toggle="dropdown">
                <div class="topbar-avatar">
                    {{ strtoupper(substr(auth()->user()->prenom, 0, 1)) }}
                </div>
                <div class="d-none d-md-block text-start" style="line-height:1.2">
                    <div class="small fw-semibold" style="color:var(--gray-800);font-size:0.82rem">
                        {{ auth()->user()->prenom }}
                    </div>
                    <div style="font-size:0.7rem;color:var(--gray-400)">
                        {{ ucfirst(auth()->user()->role) }}
                    </div>
                </div>
                <i class="bi bi-chevron-down" style="font-size:0.7rem;color:var(--gray-400)"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0"
                style="border-radius:12px;border:1px solid var(--gray-200);min-width:220px">
                <li class="px-3 py-3 border-bottom">
                    <div class="fw-semibold" style="font-size:0.875rem">
                        {{ auth()->user()->nom_complet }}
                    </div>
                    <small class="text-muted">{{ auth()->user()->email }}</small>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item d-flex align-items-center gap-2 py-2"
                                style="color:#EF4444;font-size:0.875rem">
                            <i class="bi bi-box-arrow-left"></i> Déconnexion
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

</div>

{{-- ════════════ FLASH MESSAGES ════════════ --}}
<div class="flash-container">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
</div>

{{-- ════════════ CONTENU ════════════ --}}
<div id="main-content">
    @yield('content')
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            bootstrap.Alert.getOrCreateInstance(el).close();
        });
    }, 4000);
</script>

@stack('scripts')

</body>
</html>