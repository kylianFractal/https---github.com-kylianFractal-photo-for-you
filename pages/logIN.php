<?php include "../includes/header.php"; ?>

<div class="container my-5">
  <h2 class="mb-4">Connexion</h2>

  <form method="post" action="login.php" class="row g-3">
    <div class="col-12">
      <label for="pseudo" class="form-label">Pseudo</label>
      <input 
        type="text" 
        name="pseudo" 
        id="pseudo" 
        class="form-control" 
        placeholder="Entrez votre pseudo"
        required
      >
    </div>
    <div class="col-12">
      <label for="password" class="form-label">Mot de passe</label>
      <input 
        type="password" 
        name="password" 
        id="password" 
        class="form-control" 
        placeholder="Votre mot de passe"
        required
      >
    </div>
    <div class="col-12">
      <button type="submit" class="btn btn-primary w-100">Se connecter</button>
    </div>
  </form>

  <div class="mt-3 text-center">
    <p>Pas encore de compte ? <a href="register.php">Inscrivez-vous ici</a>.</p>
  </div>
</div>

<?php include "../includes/footer.php"; ?>
