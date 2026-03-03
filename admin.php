<?php
session_start();

// Mot de passe admin
$motdepasse_admin = "Ndiougahmad16";

// Si le formulaire est soumis
if (isset($_POST['password'])) {
    if ($_POST['password'] === $motdepasse_admin) {
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        $_SESSION['admin_logged'] = true;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $erreur = "Mot de passe incorrect !";
    }
}

// Si déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Si déjà connecté → rediriger vers le dashboard
if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion Admin - Sixteen Prod</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root { --brand: #6a0dad; --brand-dark: #4b0082; }
    body { background: #f8f4ff; }
    .btn-brand { background: var(--brand); color: #fff; border: none; }
    .btn-brand:hover { background: var(--brand-dark); color: #fff; }
  </style>
</head>
<body>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-sm-8 col-md-5 col-lg-4">
      <div class="card shadow border-0 p-4 text-center">
        <div class="mb-3" style="color:var(--brand);font-size:3rem;"><i class="fas fa-user-shield"></i></div>
        <h4 class="fw-bold mb-4" style="color:var(--brand);">Espace Administrateur</h4>
        <?php if (isset($erreur)): ?>
          <div class="alert alert-danger py-2">
            <i class="fas fa-exclamation-circle me-1"></i><?php echo htmlspecialchars($erreur); ?>
          </div>
        <?php endif; ?>
        <form method="POST">
          <div class="mb-3 text-start">
            <label class="form-label fw-semibold">Mot de passe</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-lock"></i></span>
              <input type="password" name="password" class="form-control" placeholder="Mot de passe" required autofocus>
            </div>
          </div>
          <button type="submit" class="btn btn-brand w-100">
            <i class="fas fa-sign-in-alt me-1"></i>Se connecter
          </button>
        </form>
        <div class="mt-3">
          <a href="index.php" class="text-muted small"><i class="fas fa-arrow-left me-1"></i>Retour à l'accueil</a>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>