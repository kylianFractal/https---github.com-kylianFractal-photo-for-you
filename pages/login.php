<?php
require_once "../src/Database.php";
require_once "../src/User.php";
require_once "../src/Auth.php";

session_start();

$db = Database::getInstance();
$auth = new Auth($db);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mdp   = $_POST['mdp'] ?? '';

    if ($auth->login($email, $mdp)) {
        // Redirection selon le rôle
        $type = $_SESSION['user']['type'];
        if ($type === 'admin') {
            header("Location: admin.php");
        } elseif ($type === 'photographe') {
            header("Location: vendre.php");
        } elseif ($type === 'client') {
            header("Location: acheter.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $errors[] = "Email ou mot de passe incorrect.";
    }
}
?>

<?php include "../includes/header.php"; ?>

<div class="container py-5">
    <h2>Connexion</h2>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Mot de passe</label>
            <input type="password" name="mdp" class="form-control" required>
        </div>
        <button class="btn btn-warning">Se connecter</button>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
