<?php
require_once "User.php";
session_start();

class Auth
{
    private PDO $db;
    private User $userModel;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->userModel = new User($db);
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->userModel->getUserByEmail($email);
        if ($user && $this->userModel->verifyPassword($password, $user['mdp'])) {
            $_SESSION['user'] = [
                'id'     => $user['id_user'],
                'email'  => $user['email'],
                'type'   => $user['type'],
                'prenom' => $user['prenom'],
                'pseudo' => $user['pseudo']
            ];
            return true;
        }
        return false;
    }

    public function logout(): void
    {
        session_destroy();
        unset($_SESSION['user']);
    }

    public function isLogged(): bool
    {
        return isset($_SESSION['user']);
    }

    public function userType(): ?string
    {
        return $this->isLogged() ? $_SESSION['user']['type'] : null;
    }
}
