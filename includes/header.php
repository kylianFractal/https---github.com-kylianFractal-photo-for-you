<?php session_start(); ?>
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
      
      <!-- Bouton pour mobile -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Liens -->
      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="photosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Photos
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">Acheter</a></li>
              <li><a class="dropdown-item" href="#">Vendre</a></li>
              <li><a class="dropdown-item" href="#">Les plus populaires</a></li>
              <li><a class="dropdown-item" href="#">Les nouveautés</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Tarifs</a>
          </li>
        </ul>

        <!-- Barre de recherche + boutons -->
        <form class="d-flex me-3" role="search">
          <input class="form-control me-2" type="search" placeholder="Votre recherche" aria-label="Search">
          <button class="btn btn-outline-light" type="submit">Rechercher</button>
        </form>

        <?php if (isset($_SESSION['user'])): ?>
          <span class="navbar-text text-white me-3">Bonjour, <?= htmlspecialchars($_SESSION['user']['prenom']) ?></span>
          <a href="logout.php" class="btn btn-outline-danger">Déconnexion</a>
        <?php else: ?>
          <a href="photo_for_you/pages/inscription.php" class="btn btn-outline-light me-2">S’inscrire</a>
          <a href="login.php" class="btn btn-warning">S’identifier</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>
</header>

<main class="container-fluid p-0">
