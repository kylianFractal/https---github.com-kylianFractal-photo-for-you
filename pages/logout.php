<?php
session_start(); // Démarre la session si ce n'est pas déjà fait

// Détruit toutes les variables de session
$_SESSION = [];

// Détruit la session côté serveur
session_destroy();

// Optionnel : supprime le cookie de session côté client
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirection vers la page d'accueil ou de connexion
header("Location: /tpap/p4u/photo_for_you/index.php");
exit();
?>
