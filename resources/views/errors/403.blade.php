<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accès refusé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d2137, #1a3c5e);
            display: flex; align-items: center; justify-content: center;
        }
    </style>
</head>
<body>
<div class="text-center text-white px-4">
    <i class="bi bi-shield-x" style="font-size:5rem;color:#e8a020;opacity:0.8"></i>
    <h1 class="display-4 fw-bold mt-3">403</h1>
    <h4 class="fw-normal mb-3">Accès non autorisé</h4>
    <p class="text-white-50 mb-4">
        Vous n'avez pas les permissions nécessaires pour accéder à cette page.
    </p>
    <a href="{{ url()->previous() }}"
       class="btn btn-outline-light me-2">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
    <a href="{{ route('dashboard') }}"
       class="btn"
       style="background:#e8a020;color:white;border:none">
        <i class="bi bi-speedometer2 me-1"></i> Dashboard
    </a>
</div>
</body>
</html>