<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajout de photo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f8f8f8; }
        form { background: white; padding: 20px; border-radius: 10px; width: 350px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        input, button { width: 100%; margin-bottom: 10px; padding: 8px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>Ajouter une photo</h2>
    <form method="post" action="">
        <input type="text" name="nom_photos" placeholder="Nom de la photo" required>
        <input type="number" name="taille_pixels_x" placeholder="Largeur (px)" required>
        <input type="number" name="taille_pixels_y" placeholder="Hauteur (px)" required>
        <input type="number" name="poids" placeholder="Poids (Ko)" required>
        <input type="text" name="chemin" placeholder="Chemin / URL" required>
        <input type="number" name="id_user" placeholder="ID utilisateur" required>
        <button type="submit" name="submit">Envoyer</button>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        try {
            // Connexion à la base
            $pdo = new PDO("mysql:host=localhost;dbname=photo4u;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Préparation de l’insertion
            $sql = "INSERT INTO photos (nom_photos, taille_pixels_x, taille_pixels_y, poids, chemin, id_user)
                    VALUES (:nom, :x, :y, :poids, :chemin, :id_user)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $_POST['nom_photos'],
                ':x' => $_POST['taille_pixels_x'],
                ':y' => $_POST['taille_pixels_y'],
                ':poids' => $_POST['poids'],
                ':chemin' => $_POST['chemin'],
                ':id_user' => $_POST['id_user']
            ]);

            echo "<p class='success'>✅ Photo ajoutée avec succès !</p>";

        } catch (PDOException $e) {
            // Récupération du code et message SQLSTATE
            $sqlState = $e->getCode();
            $message = $e->getMessage();

            if ($sqlState == '45000') {
                echo "<p class='error'>⚠️ Erreur MySQL : " . htmlspecialchars($message) . "</p>";
            } else {
                echo "<p class='error'>Erreur inattendue : " . htmlspecialchars($message) . "</p>";
            }
        }
    }
    ?>
</body>
</html>
