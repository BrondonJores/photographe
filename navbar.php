<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-0">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php" style="color:#6a0dad;">
      <i class="fas fa-camera me-1"></i>Sixteen Prod
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
            aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarMenu">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
        <li class="nav-item">
          <a class="nav-link" href="reserver.php"><i class="fas fa-calendar-check me-1"></i>Réserver</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="galerie.php"><i class="fas fa-images me-1"></i>Galerie</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="tarifs.php"><i class="fas fa-tags me-1"></i>Tarifs</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="contact.php"><i class="fas fa-envelope me-1"></i>Contact</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="avis.php"><i class="fas fa-star me-1"></i>Avis Clients</a>
        </li>
        <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] === true): ?>
          <li class="nav-item ms-lg-2">
            <a class="btn btn-sm text-white" href="admin_dashboard.php"
               style="background:#6a0dad;"><i class="fas fa-tachometer-alt me-1"></i>Dashboard</a>
          </li>
          <li class="nav-item ms-lg-1">
            <a class="btn btn-sm btn-outline-secondary" href="logout.php">
              <i class="fas fa-sign-out-alt me-1"></i>Déconnexion</a>
          </li>
        <?php else: ?>
          <li class="nav-item ms-lg-2">
            <a class="btn btn-sm text-white" href="login.php"
               style="background:#6a0dad;"><i class="fas fa-lock me-1"></i>Connexion</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>