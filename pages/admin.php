<?php
require_once "../src/auth_check.php";

// Seuls les admins peuvent accéder
checkAccess('admin');

include "../includes/header.php";
?>

<div class="container py-5">
    <h2>Administration</h2>
    <p>Gérer les utilisateurs et le site.</p>
</div>

<?php include "../includes/footer.php"; ?>
