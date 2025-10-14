<?php include "../includes/header.php"; ?>

<div class="container my-5">
  <h2 class="mb-4">Créer un compte</h2>
  
  <form method="post" action="register.php" class="row g-3">
    <div class="col-md-6">
      <label for="email" class="form-label">Email</label>
      <input type="email" name="email" id="email" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label for="pseudo" class="form-label">Pseudo</label>
      <input type="text" name="pseudo" id="pseudo" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label for="nom" class="form-label">Nom</label>
      <input type="text" name="nom" id="nom" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label for="prenom" class="form-label">Prénom</label>
      <input type="text" name="prenom" id="prenom" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label for="password" class="form-label">Mot de passe</label>
      <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label for="role" class="form-label">Rôle</label>
      <select name="role" id="role" class="form-select" required>
        <option value="">-- Sélectionnez un rôle --</option>
        <option value="Client">Client</option>
        <option value="Photographe">Photographe</option>
      </select>
    </div>
    <div class="col-12">
      <button type="submit" class="btn btn-primary">S'inscrire</button>
    </div>
  </form>
</div>

<?php include "../includes/footer.php"; ?>
