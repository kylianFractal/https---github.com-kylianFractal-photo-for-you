<?php
require_once "../src/auth_check.php";

// Seuls les clients peuvent accéder
checkAccess('client');

include "../includes/header.php";
?>

<div class="container py-5">
    <h2>Acheter des photos</h2>
    <p>Cette page est réservée aux clients.</p>
</div>

<?php include "../includes/footer.php"; ?>
