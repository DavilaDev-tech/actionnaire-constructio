<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Actionnaire Construction</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d2137 0%, #1a3c5e 50%, #0d2137 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Particules décoratives */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(
                circle at 30% 40%,
                rgba(232,160,32,0.05) 0%,
                transparent 50%
            ),
            radial-gradient(
                circle at 70% 60%,
                rgba(26,60,94,0.3) 0%,
                transparent 50%
            );
            pointer-events: none;
        }

        .login-wrapper {
            width: 100%;
            max-width: 900px;
            position: relative;
            z-index: 1;
        }

        .login-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.4);
        }

        /* Panneau gauche */
        .login-left {
            background: linear-gradient(160deg, #e8a020 0%, #c8781a 100%);
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 520px;
        }
        .login-left h2 {
            color: white;
            font-weight: 700;
            font-size: 1.6rem;
            line-height: 1.3;
        }
        .login-left p {
            color: rgba(255,255,255,0.85);
            font-size: 0.9rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }
        .feature-icon {
            width: 38px; height: 38px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .feature-icon i { color: white; font-size: 1rem; }
        .feature-text { color: rgba(255,255,255,0.9); font-size: 0.85rem; }

        /* Panneau droit */
        .login-right {
            background: #fff;
            padding: 50px 45px;
        }

        .login-logo {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #1a3c5e, #0d2137);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px;
        }

        .login-right h3 {
            font-weight: 700;
            color: #1a3c5e;
            font-size: 1.5rem;
        }

        .form-label { font-weight: 500; font-size: 0.875rem; color: #374151; }

        .form-control {
            border-radius: 10px;
            padding: 11px 16px;
            border: 1.5px solid #e5e7eb;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: #1a3c5e;
            box-shadow: 0 0 0 3px rgba(26,60,94,0.1);
        }

        .input-group .form-control { border-right: none; }
        .input-group .btn-toggle-password {
            background: #f9fafb;
            border: 1.5px solid #e5e7eb;
            border-left: none;
            border-radius: 0 10px 10px 0;
            color: #9ca3af;
            padding: 0 14px;
        }
        .input-group .btn-toggle-password:hover { color: #1a3c5e; }

        .btn-login {
            background: linear-gradient(135deg, #1a3c5e, #2d6a9f);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
            color: white;
            width: 100%;
            transition: all 0.2s;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #0d2137, #1a3c5e);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(26,60,94,0.35);
            color: white;
        }

        .divider {
            text-align: center;
            position: relative;
            margin: 20px 0;
            color: #9ca3af;
            font-size: 0.8rem;
        }
        .divider::before, .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 42%;
            height: 1px;
            background: #e5e7eb;
        }
        .divider::before { left: 0; }
        .divider::after { right: 0; }

        .compte-test {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 0.78rem;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
            text-align: left;
            width: 100%;
            display: block;
        }
        .compte-test:hover {
            background: #e8f0fe;
            border-color: #1a3c5e;
            color: #1a3c5e;
        }
        .compte-test strong { display: block; color: #1a3c5e; }

        .footer-note {
            text-align: center;
            color: #9ca3af;
            font-size: 0.75rem;
            margin-top: 24px;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="card login-card">
        <div class="row g-0">

            {{-- ── Panneau gauche ── --}}
            <div class="col-md-5 login-left">
                <div>
                    <!-- Logo -->
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div style="width:44px;height:44px;background:rgba(255,255,255,0.2);
                                    border-radius:12px;display:flex;align-items:center;
                                    justify-content:center;">
                            <i class="bi bi-building text-white fs-4"></i>
                        </div>
                        <div>
                            <div style="color:white;font-weight:700;font-size:1rem">
                                Actionnaire
                            </div>
                            <div style="color:rgba(255,255,255,0.7);font-size:0.75rem">
                                Construction
                            </div>
                        </div>
                    </div>

                    <h2>Gérez votre activité en toute simplicité</h2>
                    <p class="mt-2 mb-4">
                        Plateforme complète de gestion des ventes, stocks
                        et finances pour votre entreprise.
                    </p>
                </div>

                <!-- Fonctionnalités -->
                <div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="bi bi-cart3"></i>
                        </div>
                        <span class="feature-text">Gestion des ventes & factures</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <span class="feature-text">Suivi des stocks en temps réel</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="bi bi-truck"></i>
                        </div>
                        <span class="feature-text">Livraisons & approvisionnements</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <span class="feature-text">Tableau de bord & statistiques</span>
                    </div>
                </div>

                <div style="color:rgba(255,255,255,0.4);font-size:0.72rem">
                    © {{ date('Y') }} Actionnaire Construction
                </div>
            </div>

            {{-- ── Panneau droit ── --}}
            <div class="col-md-7 login-right">
                <div class="login-logo">
                    <i class="bi bi-shield-lock text-white fs-4"></i>
                </div>

                <h3>Connexion</h3>
                <p class="text-muted mb-4" style="font-size:0.875rem">
                    Entrez vos identifiants pour accéder à votre espace
                </p>

                {{-- Erreurs --}}
                @if($errors->any())
                <div class="alert alert-danger border-0 rounded-3 mb-3" style="font-size:0.85rem">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    {{ $errors->first() }}
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">Adresse email</label>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               placeholder="exemple@actionnaire.com"
                               required autofocus>
                    </div>

                    <!-- Mot de passe -->
                    <div class="mb-3">
                        <label class="form-label">Mot de passe</label>
                        <div class="input-group">
                            <input type="password" name="password"
                                   id="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="••••••••" required>
                            <button type="button"
                                    class="btn btn-toggle-password"
                                    onclick="toggleMdp()">
                                <i class="bi bi-eye" id="icon-mdp"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Se souvenir -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check mb-0">
                            <input type="checkbox" name="remember"
                                   class="form-check-input" id="remember">
                            <label class="form-check-label text-muted"
                                   for="remember"
                                   style="font-size:0.85rem">
                                Se souvenir de moi
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Se connecter
                    </button>
                </form>

                <!-- Comptes de test -->
                <div class="divider">Comptes de démonstration</div>

                <div class="row g-2">
                    <div class="col-6">
                        <button class="compte-test"
                                onclick="remplir('admin@actionnaire.com')">
                            <strong>👑 Admin</strong>
                            admin@actionnaire.com
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="compte-test"
                                onclick="remplir('vendeur@actionnaire.com')">
                            <strong>🛒 Vendeur</strong>
                            vendeur@actionnaire.com
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="compte-test"
                                onclick="remplir('magasinier@actionnaire.com')">
                            <strong>📦 Magasinier</strong>
                            magasinier@actionnaire.com
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="compte-test"
                                onclick="remplir('comptable@actionnaire.com')">
                            <strong>💰 Comptable</strong>
                            comptable@actionnaire.com
                        </button>
                    </div>
                </div>

                <div class="footer-note">
                    <i class="bi bi-info-circle me-1"></i>
                    Mot de passe par défaut : <strong>password</strong>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleMdp() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('icon-mdp');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

function remplir(email) {
    document.querySelector('input[name="email"]').value    = email;
    document.querySelector('input[name="password"]').value = 'password';
    document.getElementById('password').type = 'password';
    document.getElementById('icon-mdp').className = 'bi bi-eye';
}
</script>
</body>
</html>