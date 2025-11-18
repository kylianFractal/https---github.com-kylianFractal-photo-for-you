<?php
require_once "../src/auth_check.php";
require_once "../src/photo.php";
require_once "../src/controller.php";

// Vérifie que l'utilisateur est photographe
checkAccess('photographe');

// Connexion à la BDD via le singleton
$db = Database::getInstance();

// Crée l'objet Photo
$photoModel = new Photo($db);

include "../includes/header.php";

// Traitement du formulaire d'upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $file = $_FILES['photo'];
    $nomPhoto = $_POST['nom_photos'] ?? 'photo_sans_nom';
    $userId = $_SESSION['user']['userId'] ?? null;

    if ($userId === null) {
        echo "<div class='alert alert-danger'>Utilisateur non connecté.</div>";
        exit;
    }

    // Vérification de la résolution minimale
    list($width, $height) = getimagesize($file['tmp_name']);
    if (!Photo::verifierResolution($width, $height)) {
        echo "<div class='alert alert-danger'>Erreur : résolution minimale 2400x1600 pixels.</div>";
    } else {
        // Dossier cible (chemin absolu pour move_uploaded_file)
        $uploadDir = __DIR__ . "/../assets/image/";

        // Crée le dossier s'il n'existe pas
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Chemin relatif (pour la BDD et l'affichage web)
        $cheminRelatif = "assets/image/" . basename($file['name']);
        // Chemin absolu (pour déplacer le fichier)
        $cheminAbsolu = $uploadDir . basename($file['name']);

        if (move_uploaded_file($file['tmp_name'], $cheminAbsolu)) {
            $photoModel->createPhoto([
                'nom_photos'      => $nomPhoto,
                'taille_pixels_x' => $width,
                'taille_pixels_y' => $height,
                'poids'           => $file['size'],
                'chemin'          => $cheminRelatif, // chemin relatif stocké en BDD
                'id_user'         => $userId
            ]);
            echo "<div class='alert alert-success'>Photo ajoutée avec succès !</div>";
        } else {
            echo "<div class='alert alert-danger'>Erreur lors de l'upload du fichier.</div>";
        }
    }
}
?>

<div class="container py-5">
    <h2>Vendre vos photos</h2>
    <p>Cette page est réservée aux photographes.</p>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nom_photos" class="form-label">Nom de la photo</label>
            <input type="text" name="nom_photos" class="form-control" id="nom_photos" required>
        </div>
        <div class="mb-3">
            <label for="photo" class="form-label">Sélectionner la photo</label>
            <input type="file" name="photo" class="form-control" id="photo" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter la photo</button>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
