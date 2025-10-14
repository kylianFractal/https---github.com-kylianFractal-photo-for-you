<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si la session contient un objet __PHP_Incomplete_Class, le transformer en tableau
if (isset($_SESSION['user']) && is_object($_SESSION['user'])) {
    $_SESSION['user'] = (array) $_SESSION['user'];
}

// Initialisation sécurisée pour éviter les warnings
$userType = $_SESSION['user']['type'] ?? null;
$userPrenom = $_SESSION['user']['prenom'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>PhotoForYou</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-warning" href="#">PhotoForYou</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if ($userType): ?>
                        <?php if ($userType === 'photographe'): ?>
                            <li class="nav-item"><a class="nav-link" href="vendre.php">Vendre</a></li>
                        <?php elseif ($userType === 'client'): ?>
                            <li class="nav-item"><a class="nav-link" href="acheter.php">Acheter</a></li>
                        <?php elseif ($userType === 'admin'): ?>
                            <li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="#">Photos</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Tarifs</a></li>
                    <?php endif; ?>
                </ul>

                <form class="d-flex me-3" role="search">
                    <input class="form-control me-2" type="search" placeholder="Votre recherche">
                    <button class="btn btn-outline-light" type="submit">Rechercher</button>
                </form>

                <?php if ($userType): ?>
                    <span class="navbar-text text-white me-3">Bonjour, <?= htmlspecialchars($userPrenom ?? '') ?></span>
                    <a href="logout.php" class="btn btn-outline-danger">Déconnexion</a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-outline-light me-2">S’inscrire</a>
                    <a href="login.php" class="btn btn-warning">S’identifier</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>
<main class="container-fluid p-0">
