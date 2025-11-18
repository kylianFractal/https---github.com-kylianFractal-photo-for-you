<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Empêche l’accès si l’utilisateur n’est pas client
if (!isset($_SESSION['user']) || ($_SESSION['user']['type'] ?? '') !== 'client') {
    header("Location: /tpap/p4u/photo_for_you/pages/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Crédits — PhotoForYou</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #0b0b0b;
            color: white;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
        }
        .card {
            background: #111;
            border: 1px solid #333;
            border-radius: 15px;
            padding: 30px;
            max-width: 600px;
        }
        .loader {
            width: 60px;
            height: 60px;
            border: 6px solid #444;
            border-top-color: #ffc107;
            border-radius: 50%;
            margin: 20px auto;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <div class="card shadow-lg">
        <h1 class="text-warning fw-bold">Crédits</h1>
        <p class="mt-3 fs-5">Cette fonctionnalité est actuellement en cours de développement.</p>

        <div class="loader"></div>

        <p class="text-muted mt-3">
            Revenez plus tard, ou consultez les autres sections du site !
        </p>

        <a href="/tpap/p4u/photo_for_you" class="btn btn-warning mt-3 px-4">Retour à l’accueil</a>
    </div>

</body>
</html>
