<?php
require_once "../src/controller.php";
require_once "../src/User.php";

session_start();

$db = Database::getInstance();
$userModel = new User($db);

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $prenom   = trim($_POST['prenom'] ?? '');
    $nom      = trim($_POST['nom'] ?? '');
    $pseudo   = trim($_POST['pseudo'] ?? '');
    $mdp      = $_POST['mdp'] ?? '';
    $type     = $_POST['type'] ?? '';

    // Validation simple
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
    if (!$prenom) $errors[] = "Le prénom est requis.";
    if (!$nom) $errors[] = "Le nom est requis.";
    if (!$pseudo) $errors[] = "Le pseudo est requis.";
    if (!$mdp || strlen($mdp) < 6) $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    if (!in_array($type, ['client', 'photographe'])) $errors[] = "Type d'utilisateur invalide.";

    if (empty($errors)) {
        if ($userModel->getUserByEmail($email)) {
            $errors[] = "Cet email est déjà utilisé.";
        } else {
            $userModel->createUser([
                'email' => $email,
                'prenom' => $prenom,
                'nom' => $nom,
                'pseudo' => $pseudo,
                'mdp' => $mdp,
                'type' => $type
            ]);
            $success = "Inscription réussie, vous pouvez maintenant vous connecter.";
        }
    }
}
?>

<?php include "../includes/header.php"; ?>

<div class="container py-5">
    <h2>Inscription</h2>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Prénom</label>
            <input type="text" name="prenom" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Nom</label>
            <input type="text" name="nom" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Pseudo</label>
            <input type="text" name="pseudo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Mot de passe</label>
            <input type="password" name="mdp" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Type d'utilisateur</label>
            <select name="type" class="form-control" required>
                <option value="client">Client (peut acheter)</option>
                <option value="photographe">Photographe (peut vendre)</option>
            </select>
        </div>
        <button class="btn btn-primary">S’inscrire</button>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
