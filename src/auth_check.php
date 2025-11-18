<?php
require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/Auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Création de l'instance Auth ici
$auth = new Auth(Database::getInstance());

/**
 * Vérifie que l'utilisateur est connecté et a le rôle autorisé
 *
 * @param string|array $roles
 */
function checkAccess($roles) {
    global $auth;

    if (!$auth->isLogged()) {
        header("Location: ../pages/login.php");
        exit;
    }

    $userType = $auth->userType();

    if (is_array($roles)) {
        if (!in_array($userType, $roles)) {
            header("Location: ../pages/login.php");
            exit;
        }
    } else {
        if ($userType !== $roles) {
            header("Location: ../pages/login.php");
            exit;
        }
    }
}
