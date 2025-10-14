<?php

class User
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getUserByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function createUser(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO user (email, type, prenom, nom, pseudo, mdp)
            VALUES (:email, :type, :prenom, :nom, :pseudo, :mdp)
        ");
        return $stmt->execute([
            'email' => $data['email'],
            'type'  => $data['type'],
            'prenom'=> $data['prenom'],
            'nom'   => $data['nom'],
            'pseudo'=> $data['pseudo'],
            'mdp'   => password_hash($data['mdp'], PASSWORD_BCRYPT)
        ]);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
