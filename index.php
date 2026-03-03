<?php
$conn = null;
$avis_result = null;
try {
    $conn = new mysqli("localhost", "root", "", "photographe_db");
    if (!$conn->connect_error) {
        $avis_result = $conn->query("SELECT * FROM avis ORDER BY id DESC LIMIT 3");
    }
} catch (Exception $e) {
    // DB unavailable — page still renders without dynamic avis
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Accueil - Sixteen Prod</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root { --brand: #6a0dad; --brand-dark: #4b0082; }
    body { font-family: Arial, sans-serif; }
    .navbar-brand, .text-brand { color: var(--brand) !important; }
    .btn-brand { background: var(--brand); color: #fff; border: none; }
    .btn-brand:hover { background: var(--brand-dark); color: #fff; }
    .btn-outline-brand { border-color: var(--brand); color: var(--brand); }
    .btn-outline-brand:hover { background: var(--brand); color: #fff; }
    /* Hero */
    #heroCarousel .carousel-item { height: 480px; background: #1a0033; }
    #heroCarousel .carousel-item img { width: 100%; height: 480px; object-fit: cover; opacity: .65; }
    .carousel-caption h1 { font-size: 2.6rem; font-weight: 700; text-shadow: 2px 2px 6px rgba(0,0,0,.7); }
    .carousel-caption p  { font-size: 1.15rem; text-shadow: 1px 1px 4px rgba(0,0,0,.6); }
    @media(max-width:576px){ #heroCarousel .carousel-item, #heroCarousel .carousel-item img { height: 300px; } .carousel-caption h1 { font-size:1.5rem; } }
    /* Services */
    .service-card { border: none; border-radius: 12px; box-shadow: 0 4px 18px rgba(106,13,173,.12); transition: transform .25s, box-shadow .25s; }
    .service-card:hover { transform: translateY(-6px); box-shadow: 0 10px 30px rgba(106,13,173,.22); }
    .service-card .icon { font-size: 2.5rem; color: var(--brand); }
    /* Avis */
    .avis-card { border-left: 4px solid var(--brand); border-radius: 8px; }
    .star { color: gold; }
    /* CTA */
    .cta-section { background: linear-gradient(135deg, var(--brand), var(--brand-dark)); }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<!-- ===== CAROUSEL ===== -->
<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
  </div>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="Images/photographe.jpg" alt="Sixteen Prod">
      <div class="carousel-caption d-flex flex-column align-items-center justify-content-center h-100">
        <h1>Bienvenue chez Sixteen Prod</h1>
        <p>Capturer l'émotion, la lumière et les instants vrais.</p>
        <a href="reserver.php" class="btn btn-brand btn-lg mt-2 px-4">
          <i class="fas fa-calendar-check me-2"></i>Réserver maintenant
        </a>
      </div>
    </div>
    <div class="carousel-item">
      <img src="uploads/photo1.jpg" alt="Galerie">
      <div class="carousel-caption d-flex flex-column align-items-center justify-content-center h-100">
        <h1>Notre Galerie</h1>
        <p>Découvrez nos plus belles réalisations.</p>
        <a href="galerie.php" class="btn btn-outline-light btn-lg mt-2 px-4">
          <i class="fas fa-images me-2"></i>Voir la galerie
        </a>
      </div>
    </div>
    <div class="carousel-item">
      <img src="Images/fond.jpg.jpeg" alt="Nos Packs">
      <div class="carousel-caption d-flex flex-column align-items-center justify-content-center h-100">
        <h1>Nos Packs &amp; Tarifs</h1>
        <p>Des offres adaptées à chaque événement et budget.</p>
        <a href="tarifs.php" class="btn btn-outline-light btn-lg mt-2 px-4">
          <i class="fas fa-tags me-2"></i>Voir les tarifs
        </a>
      </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>

<!-- ===== À PROPOS ===== -->
<section class="py-5 bg-white">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-6">
        <h2 class="fw-bold mb-3" style="color:var(--brand);">
          <i class="fas fa-camera me-2"></i>Notre histoire
        </h2>
        <p class="text-muted">
          La photographie est devenue pour moi une façon de raconter ce que les mots ne savent pas dire.
          Capturer un regard, une émotion ou un instant vrai, c'est ce qui m'anime depuis mes débuts.
          Avec le temps, j'ai appris à comprendre la lumière, à écouter les silences et à laisser parler
          les images à ma place.
        </p>
        <p class="text-muted">
          Ce qui n'était au départ qu'une curiosité est devenu une passion profonde façonnée par des années
          d'essais et d'apprentissage. Aujourd'hui, chaque photo reflète un peu de mon parcours, de ma vision
          et de ma manière de voir le monde : authentique, sensible et vraie.
        </p>
        <p class="text-muted">
          De cette aventure est née <strong>Sixteen Prod</strong>, un projet qui incarne mon univers.
          C'est plus qu'un nom, c'est un regard, une émotion, une promesse de raconter chaque histoire
          avec sincérité et lumière.
        </p>
        <div class="d-flex flex-wrap gap-2 mt-3">
          <a href="reserver.php" class="btn btn-brand"><i class="fas fa-calendar-check me-1"></i>Réserver</a>
          <a href="galerie.php"  class="btn btn-outline-brand"><i class="fas fa-images me-1"></i>Galerie</a>
          <a href="contact.php"  class="btn btn-outline-secondary"><i class="fas fa-envelope me-1"></i>Contact</a>
        </div>
      </div>
      <div class="col-lg-6 text-center">
        <img src="Images/photographe.jpg" alt="Photographe Sixteen Prod"
             class="img-fluid rounded-3 shadow" style="max-height:380px;object-fit:cover;">
      </div>
    </div>
  </div>
</section>

<!-- ===== SERVICES / PACKS ===== -->
<section class="py-5" style="background:#f8f4ff;">
  <div class="container">
    <h2 class="text-center fw-bold mb-2" style="color:var(--brand);">
      <i class="fas fa-box-open me-2"></i>Nos Packs &amp; Services
    </h2>
    <p class="text-center text-muted mb-5">Des prestations sur-mesure pour immortaliser vos moments.</p>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card service-card h-100 text-center p-4">
          <div class="icon mb-3"><i class="fas fa-camera-retro"></i></div>
          <h5 class="fw-bold">Pack Photos</h5>
          <p class="text-muted">Séances photo professionnelles, portraits, événements familiaux et bien plus.</p>
          <a href="tarifs.php" class="btn btn-brand mt-auto">Voir les tarifs</a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card service-card h-100 text-center p-4">
          <div class="icon mb-3"><i class="fas fa-video"></i></div>
          <h5 class="fw-bold">Pack Vidéo</h5>
          <p class="text-muted">Captation vidéo HD de vos cérémonies, clips et événements spéciaux.</p>
          <a href="tarifs.php" class="btn btn-brand mt-auto">Voir les tarifs</a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card service-card h-100 text-center p-4">
          <div class="icon mb-3"><i class="fas fa-star-and-crescent"></i></div>
          <h5 class="fw-bold">Pack Korité</h5>
          <p class="text-muted">Couverture complète de vos célébrations religieuses et culturelles.</p>
          <a href="tarifs.php" class="btn btn-brand mt-auto">Voir les tarifs</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== AVIS CLIENTS ===== -->
<section class="py-5 bg-white">
  <div class="container">
    <h2 class="text-center fw-bold mb-2" style="color:var(--brand);">
      <i class="fas fa-comments me-2"></i>Avis de nos clients
    </h2>
    <p class="text-center text-muted mb-5">Ce que nos clients disent de nous.</p>
    <div class="row g-4 justify-content-center">
      <?php
      if ($avis_result && $avis_result->num_rows > 0):
          while ($row = $avis_result->fetch_assoc()):
      ?>
      <div class="col-md-4">
        <div class="card avis-card h-100 p-4 shadow-sm">
          <div class="mb-2">
            <?php for ($i = 0; $i < (int)$row['note']; $i++): ?><span class="star">&#9733;</span><?php endfor; ?>
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
      <div class="col-12 text-center text-muted">Aucun avis pour le moment.</div>
      <?php endif; ?>
    </div>
    <div class="text-center mt-4">
      <a href="avis.php" class="btn btn-outline-brand">
        <i class="fas fa-star me-1"></i>Voir tous les avis &amp; laisser le vôtre
      </a>
    </div>
  </div>
</section>

<!-- ===== CTA ===== -->
<section class="cta-section py-5 text-white text-center">
  <div class="container">
    <h2 class="fw-bold mb-3"><i class="fas fa-calendar-alt me-2"></i>Prêt à réserver ?</h2>
    <p class="mb-4 fs-5">Réservez votre séance dès aujourd'hui et immortalisez vos plus beaux moments.</p>
    <a href="reserver.php" class="btn btn-light btn-lg px-5 fw-bold" style="color:var(--brand);">
      <i class="fas fa-calendar-check me-2"></i>Réserver maintenant
    </a>
  </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="bg-dark text-white py-4">
  <div class="container text-center">
    <p class="mb-1">
      <i class="fas fa-camera me-1"></i><strong>Sixteen Prod</strong> &mdash; Photographe professionnel
    </p>
    <p class="mb-1 small text-muted">
      <i class="fas fa-envelope me-1"></i>sixteenprod2001@gmail.com &nbsp;|&nbsp;
      <i class="fas fa-phone me-1"></i>779090053
    </p>
    <p class="mb-0 small">
      <a href="https://www.instagram.com/Sixteen_prod/" target="_blank" class="text-white me-3">
        <i class="fab fa-instagram me-1"></i>Instagram
      </a>
      <a href="https://www.tiktok.com/@Sixteenprod" target="_blank" class="text-white me-3">
        <i class="fab fa-tiktok me-1"></i>TikTok
      </a>
      <a href="https://wa.me/221779090053" target="_blank" class="text-white">
        <i class="fab fa-whatsapp me-1"></i>WhatsApp
      </a>
    </p>
    <p class="mt-2 mb-0 small text-muted">&copy; <?php echo date('Y'); ?> Sixteen Prod. Tous droits réservés.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>