<?php
$conn = null;
$result = null;
try {
    $conn = new mysqli("localhost", "root", "", "photographe_db");
    if (!$conn->connect_error) {
        $result = $conn->query("SELECT * FROM tarifs ORDER BY id DESC");
    }
} catch (Exception $e) {
    // DB unavailable — page renders with empty tarifs
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Tarifs - Sixteen Prod</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root { --brand: #6a0dad; --brand-dark: #4b0082; }
    body { background: #f8f4ff; }
    .section-title { color: var(--brand); }
    .tarif-card { border: none; border-radius: 12px; box-shadow: 0 4px 18px rgba(106,13,173,.1);
                  transition: transform .25s, box-shadow .25s; }
    .tarif-card:hover { transform: translateY(-6px); box-shadow: 0 10px 30px rgba(106,13,173,.22); }
    .tarif-card .badge-pack { background: var(--brand); color: #fff; font-size:.95rem; }
    .tarif-card .prix { font-size: 1.7rem; font-weight: 700; color: var(--brand); }
    .btn-brand { background: var(--brand); color: #fff; border: none; }
    .btn-brand:hover { background: var(--brand-dark); color: #fff; }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-5">
  <h2 class="text-center fw-bold section-title mb-2">
    <i class="fas fa-tags me-2"></i>Nos Tarifs
  </h2>
  <p class="text-center text-muted mb-5">Choisissez le pack qui correspond à votre événement.</p>

  <div class="row g-4 justify-content-center">
    <?php
    if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
    ?>
    <div class="col-sm-6 col-lg-4">
      <div class="card tarif-card h-100 text-center p-4">
        <div class="mb-2">
          <span class="badge badge-pack rounded-pill px-3 py-2">
            <i class="fas fa-box-open me-1"></i><?php echo htmlspecialchars($row['pack']); ?>
          </span>
        </div>
        <p class="text-muted mt-3"><?php echo htmlspecialchars($row['description']); ?></p>
        <div class="prix mt-auto"><?php echo htmlspecialchars($row['prix']); ?> FCFA</div>
        <a href="reserver.php" class="btn btn-brand mt-3">
          <i class="fas fa-calendar-check me-1"></i>Réserver ce pack
        </a>
      </div>
    </div>
    <?php
        endwhile;
    else:
    ?>
    <div class="col-12 text-center text-muted py-5">
      <i class="fas fa-tags fa-3x mb-3 d-block" style="color:var(--brand);"></i>
      Aucun tarif disponible pour le moment.
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