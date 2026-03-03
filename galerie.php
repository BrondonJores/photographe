<?php
$conn = null;
$result = null;
try {
    $conn = new mysqli("localhost", "root", "", "photographe_db");
    if (!$conn->connect_error) {
        $result = $conn->query("SELECT * FROM galerie ORDER BY id DESC");
    }
} catch (Exception $e) {
    // DB unavailable — page renders with empty gallery
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Galerie - Sixteen Prod</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root { --brand: #6a0dad; }
    .gallery-img { width: 100%; height: 220px; object-fit: cover; border-radius: 8px;
                   cursor: pointer; transition: transform .25s, box-shadow .25s; }
    .gallery-img:hover { transform: scale(1.04); box-shadow: 0 8px 24px rgba(106,13,173,.3); }
    .section-title { color: var(--brand); }
  </style>
</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container py-5">
  <h2 class="text-center fw-bold section-title mb-4">
    <i class="fas fa-images me-2"></i>Notre Galerie
  </h2>

  <div class="row g-3">
    <?php
    if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
            $file = 'uploads/' . trim($row['images']);
            if (file_exists($file)):
    ?>
    <div class="col-6 col-md-4 col-lg-3">
      <img src="<?php echo htmlspecialchars($file); ?>"
           class="gallery-img"
           alt="Photo galerie"
           data-bs-toggle="modal" data-bs-target="#lightboxModal"
           data-src="<?php echo htmlspecialchars($file); ?>">
    </div>
    <?php
            endif;
        endwhile;
    else:
    ?>
    <div class="col-12 text-center text-muted py-5">
      <i class="fas fa-image fa-3x mb-3 d-block" style="color:var(--brand);"></i>
      Aucune photo pour le moment.
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Lightbox Modal -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content bg-dark border-0">
      <div class="modal-header border-0 pb-0">
        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center p-2">
        <img id="lightboxImg" src="" alt="Photo agrandie"
             class="img-fluid rounded" style="max-height:80vh;">
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
<script>
document.querySelectorAll('.gallery-img').forEach(function(img) {
  img.addEventListener('click', function() {
    document.getElementById('lightboxImg').src = this.getAttribute('data-src');
  });
});
</script>
</body>
</html>