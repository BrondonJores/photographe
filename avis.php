<?php
$conn = null;
$succes = '';
$erreur = '';

try {
    $conn = new mysqli("localhost", "root", "", "photographe_db");
    if ($conn->connect_error) $conn = null;
} catch (Exception $e) {
    $conn = null;
}

if ($conn && isset($_POST['envoyer'])) {
    $nom     = htmlspecialchars(trim($_POST['nom'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));
    $note    = intval($_POST['note'] ?? 0);

    if (empty($nom) || empty($message) || $note < 1 || $note > 5) {
        $erreur = "Tous les champs sont obligatoires et la note doit être entre 1 et 5.";
    } else {
        $stmt = $conn->prepare("INSERT INTO avis (nom, message, note) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $nom, $message, $note);
        $stmt->execute();
        $stmt->close();
        $succes = "Votre avis a bien été enregistré, merci !";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Avis Clients - Sixteen Prod</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root { --brand: #6a0dad; --brand-dark: #4b0082; }
    body { background: #f8f4ff; }
    .section-title { color: var(--brand); }
    .avis-card { border-left: 4px solid var(--brand); border-radius: 8px; }
    .star { color: gold; }
    .btn-brand { background: var(--brand); color: #fff; border: none; }
    .btn-brand:hover { background: var(--brand-dark); color: #fff; }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-5">
  <h2 class="text-center fw-bold section-title mb-2">
    <i class="fas fa-star me-2"></i>Avis Clients
  </h2>
  <p class="text-center text-muted mb-5">Partagez votre expérience avec Sixteen Prod.</p>

  <div class="row justify-content-center">
    <div class="col-lg-6 mb-5">
      <div class="card shadow-sm border-0 p-4">
        <h5 class="fw-bold mb-4" style="color:var(--brand);">
          <i class="fas fa-pen me-2"></i>Laisser un avis
        </h5>

        <?php if ($succes): ?>
          <div class="alert alert-success"><i class="fas fa-check-circle me-1"></i><?php echo $succes; ?></div>
        <?php endif; ?>
        <?php if ($erreur): ?>
          <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-1"></i><?php echo $erreur; ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="mb-3">
            <label class="form-label fw-semibold">Votre nom</label>
            <input type="text" name="nom" class="form-control" placeholder="Ex : Marie Dupont" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Votre avis</label>
            <textarea name="message" class="form-control" rows="4"
                      placeholder="Décrivez votre expérience..." required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Note</label>
            <select name="note" class="form-select" required>
              <option value="5">⭐⭐⭐⭐⭐ Excellent (5/5)</option>
              <option value="4">⭐⭐⭐⭐ Très bien (4/5)</option>
              <option value="3">⭐⭐⭐ Bien (3/5)</option>
              <option value="2">⭐⭐ Passable (2/5)</option>
              <option value="1">⭐ Décevant (1/5)</option>
            </select>
          </div>
          <button type="submit" name="envoyer" class="btn btn-brand w-100">
            <i class="fas fa-paper-plane me-1"></i>Envoyer mon avis
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Liste des avis -->
  <h4 class="fw-bold section-title mb-4 text-center">
    <i class="fas fa-comments me-2"></i>Ce que disent nos clients
  </h4>
  <div class="row g-4">
    <?php
    $result = $conn ? $conn->query("SELECT * FROM avis ORDER BY id DESC") : null;
    if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
    ?>
    <div class="col-md-6 col-lg-4">
      <div class="card avis-card h-100 shadow-sm p-4">
        <div class="mb-2">
          <?php for ($i = 0; $i < (int)$row['note']; $i++): ?>
            <span class="star">&#9733;</span>
          <?php endfor; ?>
        </div>
        <p class="fst-italic text-muted">"<?php echo htmlspecialchars($row['message']); ?>"</p>
        <div class="mt-auto fw-bold" style="color:var(--brand);">
          <i class="fas fa-user-circle me-1"></i><?php echo htmlspecialchars($row['nom']); ?>
        </div>
      </div>
    </div>
    <?php
        endwhile;
    else:
    ?>
    <div class="col-12 text-center text-muted py-4">
      <i class="fas fa-comment-slash fa-2x mb-2 d-block"></i>Aucun avis pour le moment.
    </div>
    <?php endif; ?>
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
