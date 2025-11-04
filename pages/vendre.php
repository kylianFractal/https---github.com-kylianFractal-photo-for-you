<?php
require_once "../src/auth_check.php";

// Seuls les photographes peuvent accéder
checkAccess('photographe');

include "../includes/header.php";
?>

<div class="container py-5">
    <h2>Vendre vos photos</h2>
    <p>Cette page est réservée aux photographes.</p>
</div>

<?php include "../includes/footer.php"; ?>
