<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Contact - Sixteen Prod</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root { --brand: #6a0dad; --brand-dark: #4b0082; }
    body { background: #f8f4ff; }
    .section-title { color: var(--brand); }
    .btn-brand { background: var(--brand); color: #fff; border: none; }
    .btn-brand:hover { background: var(--brand-dark); color: #fff; }
    .info-icon { color: var(--brand); font-size: 1.2rem; }
    .input-icon { background: var(--brand); color: #fff; border-color: var(--brand); }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container py-5">
  <h1 class="text-center fw-bold section-title mb-2">
    <i class="fas fa-envelope me-2"></i>Contactez-nous
  </h1>
  <p class="text-center text-muted mb-5">Une question ? Un projet ? Ecrivez-nous !</p>

  <?php if (isset($_GET['success'])): ?>
    <div class="alert <?php echo $_GET['success'] == '1' ? 'alert-success' : 'alert-danger'; ?> text-center">
      <?php echo $_GET['success'] == '1'
            ? '<i class="fas fa-check-circle me-1"></i>Votre message a été envoyé avec succès !'
            : '<i class="fas fa-exclamation-circle me-1"></i>Erreur lors de l\'envoi, veuillez réessayer.'; ?>
    </div>
  <?php endif; ?>

  <div class="row g-4 justify-content-center">

    <!-- Formulaire -->
    <div class="col-md-6">
      <div class="card shadow-sm border-0 p-4 h-100">
        <h5 class="fw-bold section-title mb-4"><i class="fas fa-paper-plane me-2"></i>Envoyez-nous un message</h5>
        <form action="contact_form_handler.php" method="post">
          <div class="mb-3">
            <label class="form-label fw-semibold">Votre nom</label>
            <div class="input-group">
              <span class="input-group-text input-icon">
                <i class="fas fa-user"></i>
              </span>
              <input type="text" name="nom" class="form-control" placeholder="Prenom Nom" maxlength="100" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Votre email</label>
            <div class="input-group">
              <span class="input-group-text input-icon">
                <i class="fas fa-envelope"></i>
              </span>
              <input type="email" name="email" class="form-control" placeholder="exemple@email.com" maxlength="150" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Sujet</label>
            <div class="input-group">
              <span class="input-group-text input-icon">
                <i class="fas fa-tag"></i>
              </span>
              <input type="text" name="sujet" class="form-control" placeholder="Sujet de votre message" maxlength="200" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Message</label>
            <div class="input-group">
              <span class="input-group-text input-icon">
                <i class="fas fa-comment-dots"></i>
              </span>
              <textarea name="message" class="form-control" rows="5"
                        placeholder="Votre message..." maxlength="5000" required></textarea>
            </div>
          </div>
          <button type="submit" class="btn btn-brand w-100">
            <i class="fas fa-paper-plane me-1"></i>Envoyer
          </button>
        </form>
      </div>
    </div>

    <!-- Infos directes -->
    <div class="col-md-5">
      <div class="card shadow-sm border-0 p-4 h-100">
        <h5 class="fw-bold section-title mb-4"><i class="fas fa-address-card me-2"></i>Informations directes</h5>
        <ul class="list-unstyled">
          <li class="mb-3">
            <i class="fas fa-envelope info-icon me-2"></i>
            <a href="mailto:sixteenprod2001@gmail.com" class="text-dark">sixteenprod2001@gmail.com</a>
          </li>
          <li class="mb-3">
            <i class="fas fa-phone info-icon me-2"></i>
            <a href="tel:+221779090053" class="text-dark">+221 77 909 00 53</a>
          </li>
          <li class="mb-3">
            <i class="fab fa-instagram info-icon me-2"></i>
            <a href="https://www.instagram.com/Sixteen_prod/" target="_blank" style="color:var(--brand);">
              @Sixteen_prod
            </a>
          </li>
          <li class="mb-3">
            <i class="fab fa-tiktok info-icon me-2"></i>
            <a href="https://www.tiktok.com/@Sixteenprod" target="_blank" style="color:var(--brand);">
              @Sixteenprod
            </a>
          </li>
          <li class="mb-3">
            <i class="fab fa-whatsapp me-2" style="color:#25D366;font-size:1.2rem;"></i>
            <a href="https://wa.me/221779090053" target="_blank" style="color:#25D366;font-weight:bold;">
              Envoyer un message WhatsApp
            </a>
          </li>
        </ul>
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
