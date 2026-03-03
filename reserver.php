<?php
include("connexion.php");

$succes = '';
$erreur = '';

if (!$conn) {
    $erreur = "Service de réservation temporairement indisponible.";
} elseif (isset($_POST['reserver'])) {
    $nom       = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $email     = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $telephone = htmlspecialchars(trim($_POST['telephone'] ?? ''));
    $pack      = htmlspecialchars(trim($_POST['pack'] ?? ''));
    $date      = htmlspecialchars(trim($_POST['date'] ?? ''));

    if (!$email) {
        $erreur = "Adresse email invalide.";
    } elseif (empty($nom) || empty($telephone) || empty($pack) || empty($date)) {
        $erreur = "Tous les champs sont obligatoires.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO reservations (nom, email, telephone, pack, date_reservation, statut)
             VALUES (?, ?, ?, ?, ?, 'confirmé')"
        );
        $stmt->bind_param("sssss", $nom, $email, $telephone, $pack, $date);
        if ($stmt->execute()) {
            $succes = "Réservation réussie ! Nous vous contacterons bientôt.";
        } else {
            $erreur = "Erreur lors de la réservation.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Réserver - Sixteen Prod</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root { --brand: #6a0dad; --brand-dark: #4b0082; }
    body { background: #f8f4ff; }
    .btn-brand { background: var(--brand); color: #fff; border: none; }
    .btn-brand:hover { background: var(--brand-dark); color: #fff; }
    .section-title { color: var(--brand); }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
      <div class="card shadow-sm border-0 p-4">
        <h2 class="fw-bold section-title mb-4 text-center">
          <i class="fas fa-calendar-check me-2"></i>Réserver un rendez-vous
        </h2>

        <?php if ($succes): ?>
          <div class="alert alert-success"><i class="fas fa-check-circle me-1"></i><?php echo $succes; ?></div>
        <?php endif; ?>
        <?php if ($erreur): ?>
          <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-1"></i><?php echo $erreur; ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="mb-3">
            <label class="form-label fw-semibold">Votre nom</label>
            <input type="text" name="nom" class="form-control" placeholder="Prénom Nom" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Votre email</label>
            <input type="email" name="email" class="form-control" placeholder="exemple@email.com" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Votre téléphone</label>
            <input type="text" name="telephone" class="form-control" placeholder="Ex: 77 xxx xx xx" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Choisissez un pack</label>
            <select name="pack" class="form-select" required>
              <option value="">-- Sélectionnez un pack --</option>
              <option value="Photos">Photos</option>
              <option value="Korité">Korité</option>
              <option value="Videos">Vidéos</option>
            </select>
          </div>
          <div class="mb-4">
            <label class="form-label fw-semibold">Date de réservation</label>
            <input type="date" name="date" class="form-control" required>
          </div>
          <button type="submit" name="reserver" class="btn btn-brand w-100">
            <i class="fas fa-paper-plane me-1"></i>Confirmer la réservation
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white py-3 mt-5">
  <div class="container text-center">
    <p class="mb-0 small"><i class="fas fa-camera me-1"></i><strong>Sixteen Prod</strong> &mdash;
      <a href="index.php" class="text-white">Retour à l'accueil</a></p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>