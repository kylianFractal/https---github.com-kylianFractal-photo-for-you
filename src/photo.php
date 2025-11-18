<?php

class Photo
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Récupérer une photo par son ID
     */
    public function getPhotoById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM photos WHERE id_photos = :id");
        $stmt->execute(['id' => $id]);
        $photo = $stmt->fetch(PDO::FETCH_ASSOC);
        return $photo ?: null;
    }

    /**
     * Récupérer toutes les photos d'un utilisateur
     */
    public function getPhotosByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM photos WHERE id_user = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ajouter une nouvelle photo
     */
   public function createPhoto(array $data): bool
{
    $stmt = $this->db->prepare("
        INSERT INTO photos (nom_photos, taille_pixels_x, taille_pixels_y, poids, chemin, id_user)
        VALUES (:nom, :taille_x, :taille_y, :poids, :chemin, :id_user)
    ");
    return $stmt->execute([
        'nom'      => $data['nom_photos'],
        'taille_x' => $data['taille_pixels_x'],
        'taille_y' => $data['taille_pixels_y'],
        'poids'    => $data['poids'],
        'chemin'   => $data['chemin'],   // cohérent
        'id_user'  => $data['id_user']   // cohérent
    ]);
}


    /**
     * Supprimer une photo par ID
     */
    public function deletePhoto(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM photos WHERE id_photos = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Vérifier la résolution minimale d'une photo
     */
    public static function verifierResolution(int $x, int $y): bool
    {
        return $x >= 2400 && $y >= 1600;
    }
}
