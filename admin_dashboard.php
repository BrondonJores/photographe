<?php
session_start();

// --- Auth guard ---
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: login.php");
    exit();
}

// --- DB connection ---
try {
    $conn = new mysqli("localhost", "root", "", "photographe_db");
    if ($conn->connect_error) {
        die("Connexion échouée: " . htmlspecialchars($conn->connect_error));
    }
} catch (Exception $e) {
    die("Erreur de connexion à la base de données.");
}

// --- CSRF token ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

function validateCsrf(): void {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        die("Token CSRF invalide.");
    }
}

// ============================================================
//  ACTIONS POST
// ============================================================

$msg = '';

// --- Supprimer réservation ---
if (isset($_GET['del_resa'])) {
    $id = intval($_GET['del_resa']);
    $conn->query("DELETE FROM reservations WHERE id=$id");
    header("Location: admin_dashboard.php?tab=reservations&ok=1");
    exit();
}

// --- Supprimer avis ---
if (isset($_GET['del_avis'])) {
    $id = intval($_GET['del_avis']);
    $conn->query("DELETE FROM avis WHERE id=$id");
    header("Location: admin_dashboard.php?tab=avis&ok=1");
    exit();
}

// --- Ajouter tarif ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_tarif') {
    validateCsrf();
    $pack  = htmlspecialchars(trim($_POST['pack'] ?? ''));
    $desc  = htmlspecialchars(trim($_POST['description'] ?? ''));
    $prix  = intval($_POST['prix'] ?? 0);
    if ($pack && $desc && $prix > 0) {
        $stmt = $conn->prepare("INSERT INTO tarifs (pack, description, prix) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $pack, $desc, $prix);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin_dashboard.php?tab=tarifs&ok=1");
    exit();
}

// --- Modifier tarif ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_tarif') {
    validateCsrf();
    $id   = intval($_POST['edit_id'] ?? 0);
    $pack = htmlspecialchars(trim($_POST['pack'] ?? ''));
    $desc = htmlspecialchars(trim($_POST['description'] ?? ''));
    $prix = intval($_POST['prix'] ?? 0);
    if ($id && $pack && $desc && $prix > 0) {
        $stmt = $conn->prepare("UPDATE tarifs SET pack=?, description=?, prix=? WHERE id=?");
        $stmt->bind_param("ssii", $pack, $desc, $prix, $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin_dashboard.php?tab=tarifs&ok=1");
    exit();
}

// --- Supprimer tarif ---
if (isset($_GET['del_tarif'])) {
    $id = intval($_GET['del_tarif']);
    $conn->query("DELETE FROM tarifs WHERE id=$id");
    header("Location: admin_dashboard.php?tab=tarifs&ok=1");
    exit();
}

// --- Ajouter photo galerie ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_photo') {
    validateCsrf();
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $_FILES['image']['tmp_name']);
        finfo_close($finfo);
        if (in_array($mime, $allowed_types)) {
            $ext       = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('photo_', true) . '.' . strtolower($ext);
            $target    = "uploads/" . $file_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $stmt = $conn->prepare("INSERT INTO galerie (images) VALUES (?)");
                $stmt->bind_param("s", $file_name);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
    header("Location: admin_dashboard.php?tab=galerie&ok=1");
    exit();
}

// --- Supprimer photo galerie ---
if (isset($_GET['del_photo'])) {
    $id  = intval($_GET['del_photo']);
    $res = $conn->query("SELECT images FROM galerie WHERE id=$id");
    if ($res && $res->num_rows > 0) {
        $row  = $res->fetch_assoc();
        $file = "uploads/" . $row['images'];
        if (file_exists($file)) unlink($file);
        $conn->query("DELETE FROM galerie WHERE id=$id");
    }
    header("Location: admin_dashboard.php?tab=galerie&ok=1");
    exit();
}

// ============================================================
//  STATS
// ============================================================
$nb_resa    = $conn->query("SELECT COUNT(*) AS c FROM reservations")->fetch_assoc()['c'];
$nb_avis    = $conn->query("SELECT COUNT(*) AS c FROM avis")->fetch_assoc()['c'];
$nb_tarifs  = $conn->query("SELECT COUNT(*) AS c FROM tarifs")->fetch_assoc()['c'];
$nb_galerie = $conn->query("SELECT COUNT(*) AS c FROM galerie")->fetch_assoc()['c'];

// Chart data: reservations by pack
$chart_data = [];
$r = $conn->query("SELECT pack, COUNT(*) AS nb FROM reservations GROUP BY pack");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        $chart_data[] = $row;
    }
}

// ============================================================
//  DATA FOR TABS
// ============================================================
$reservations = $conn->query("SELECT * FROM reservations ORDER BY id DESC");
$avis_list    = $conn->query("SELECT * FROM avis ORDER BY id DESC");
$tarifs_list  = $conn->query("SELECT * FROM tarifs ORDER BY id DESC");
$galerie_list = $conn->query("SELECT * FROM galerie ORDER BY id DESC");

$active_tab = htmlspecialchars($_GET['tab'] ?? 'stats');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin - Sixteen Prod</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root { --brand: #6a0dad; --brand-dark: #4b0082; }
    body { background: #f8f4ff; }
    .sidebar { min-height: 100vh; background: var(--brand); }
    .sidebar .nav-link { color: rgba(255,255,255,.8); padding: .6rem 1rem; border-radius: 8px; margin-bottom: 2px; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,.18); color: #fff; }
    .sidebar .nav-link i { width: 20px; }
    .stat-card { border: none; border-radius: 12px; box-shadow: 0 4px 16px rgba(106,13,173,.12); }
    .stat-card .icon { font-size: 2rem; opacity: .85; }
    .stat-value { font-size: 2.2rem; font-weight: 700; }
    .btn-brand { background: var(--brand); color: #fff; border: none; }
    .btn-brand:hover { background: var(--brand-dark); color: #fff; }
    .table th { background: var(--brand); color: #fff; }
    .photo-thumb { width: 70px; height: 70px; object-fit: cover; border-radius: 6px; }
    @media(max-width:767px){ .sidebar { min-height: auto; } }
  </style>
</head>
<body>

<!-- TOP NAVBAR -->
<nav class="navbar navbar-dark py-2" style="background:var(--brand-dark);">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold">
      <i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin &mdash; Sixteen Prod
    </span>
    <div class="d-flex gap-2">
      <a href="index.php" class="btn btn-sm btn-outline-light">
        <i class="fas fa-home me-1"></i>Site public
      </a>
      <a href="logout.php" class="btn btn-sm btn-light" style="color:var(--brand);">
        <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
      </a>
    </div>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">

    <!-- SIDEBAR -->
    <nav class="col-md-2 col-lg-2 d-none d-md-block sidebar pt-4 pb-4 px-3">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link <?php echo $active_tab === 'stats'        ? 'active' : ''; ?>"
             href="?tab=stats"><i class="fas fa-chart-bar me-2"></i>Statistiques</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo $active_tab === 'reservations' ? 'active' : ''; ?>"
             href="?tab=reservations"><i class="fas fa-calendar-check me-2"></i>Réservations</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo $active_tab === 'avis'         ? 'active' : ''; ?>"
             href="?tab=avis"><i class="fas fa-star me-2"></i>Avis</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo $active_tab === 'tarifs'       ? 'active' : ''; ?>"
             href="?tab=tarifs"><i class="fas fa-tags me-2"></i>Tarifs</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo $active_tab === 'galerie'      ? 'active' : ''; ?>"
             href="?tab=galerie"><i class="fas fa-images me-2"></i>Galerie</a>
        </li>
      </ul>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="col-md-10 col-lg-10 ms-sm-auto px-md-4 py-4">

      <!-- Mobile nav tabs -->
      <ul class="nav nav-tabs d-md-none mb-4">
        <li class="nav-item"><a class="nav-link <?php echo $active_tab==='stats'?'active':''; ?>" href="?tab=stats"><i class="fas fa-chart-bar"></i></a></li>
        <li class="nav-item"><a class="nav-link <?php echo $active_tab==='reservations'?'active':''; ?>" href="?tab=reservations"><i class="fas fa-calendar-check"></i></a></li>
        <li class="nav-item"><a class="nav-link <?php echo $active_tab==='avis'?'active':''; ?>" href="?tab=avis"><i class="fas fa-star"></i></a></li>
        <li class="nav-item"><a class="nav-link <?php echo $active_tab==='tarifs'?'active':''; ?>" href="?tab=tarifs"><i class="fas fa-tags"></i></a></li>
        <li class="nav-item"><a class="nav-link <?php echo $active_tab==='galerie'?'active':''; ?>" href="?tab=galerie"><i class="fas fa-images"></i></a></li>
      </ul>

      <?php if (isset($_GET['ok'])): ?>
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
          <i class="fas fa-check-circle me-1"></i>Opération effectuée avec succès.
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- ===================== STATS ===================== -->
      <?php if ($active_tab === 'stats'): ?>
      <h4 class="fw-bold mb-4" style="color:var(--brand);"><i class="fas fa-chart-bar me-2"></i>Tableau de bord</h4>

      <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
          <div class="card stat-card text-white h-100 p-3" style="background:linear-gradient(135deg,#6a0dad,#9b30d9);">
            <div class="icon mb-2"><i class="fas fa-calendar-check"></i></div>
            <div class="stat-value"><?php echo $nb_resa; ?></div>
            <div>Reservations</div>
          </div>
        </div>
        <div class="col-6 col-xl-3">
          <div class="card stat-card text-white h-100 p-3" style="background:linear-gradient(135deg,#e67e22,#f39c12);">
            <div class="icon mb-2"><i class="fas fa-star"></i></div>
            <div class="stat-value"><?php echo $nb_avis; ?></div>
            <div>Avis clients</div>
          </div>
        </div>
        <div class="col-6 col-xl-3">
          <div class="card stat-card text-white h-100 p-3" style="background:linear-gradient(135deg,#27ae60,#2ecc71);">
            <div class="icon mb-2"><i class="fas fa-tags"></i></div>
            <div class="stat-value"><?php echo $nb_tarifs; ?></div>
            <div>Tarifs</div>
          </div>
        </div>
        <div class="col-6 col-xl-3">
          <div class="card stat-card text-white h-100 p-3" style="background:linear-gradient(135deg,#2980b9,#3498db);">
            <div class="icon mb-2"><i class="fas fa-images"></i></div>
            <div class="stat-value"><?php echo $nb_galerie; ?></div>
            <div>Photos galerie</div>
          </div>
        </div>
      </div>

      <!-- Chart -->
      <div class="row g-3">
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm p-3">
            <h6 class="fw-bold mb-3" style="color:var(--brand);">Reservations par pack</h6>
            <canvas id="chartPacks" height="200"></canvas>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm p-3">
            <h6 class="fw-bold mb-3" style="color:var(--brand);">Apercu rapide</h6>
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between">
                <span><i class="fas fa-calendar-check me-2" style="color:var(--brand);"></i>Total reservations</span>
                <span class="badge rounded-pill" style="background:var(--brand);"><?php echo $nb_resa; ?></span>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span><i class="fas fa-star me-2" style="color:#e67e22;"></i>Total avis</span>
                <span class="badge rounded-pill bg-warning text-dark"><?php echo $nb_avis; ?></span>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span><i class="fas fa-tags me-2" style="color:#27ae60;"></i>Total tarifs</span>
                <span class="badge rounded-pill bg-success"><?php echo $nb_tarifs; ?></span>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span><i class="fas fa-images me-2" style="color:#2980b9;"></i>Photos galerie</span>
                <span class="badge rounded-pill bg-info"><?php echo $nb_galerie; ?></span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <?php endif; /* end stats */ ?>

      <!-- ===================== RESERVATIONS ===================== -->
      <?php if ($active_tab === 'reservations'): ?>
      <h4 class="fw-bold mb-4" style="color:var(--brand);"><i class="fas fa-calendar-check me-2"></i>Reservations</h4>

      <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
              <thead>
                <tr>
                  <th>#</th><th>Nom</th><th>Email</th><th>Telephone</th>
                  <th>Pack</th><th>Date</th><th>Statut</th><th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($reservations && $reservations->num_rows > 0): ?>
                  <?php while ($row = $reservations->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo (int)$row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['nom']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['telephone']); ?></td>
                    <td><span class="badge" style="background:var(--brand);"><?php echo htmlspecialchars($row['pack']); ?></span></td>
                    <td><?php echo htmlspecialchars($row['date_reservation']); ?></td>
                    <td><span class="badge bg-success"><?php echo htmlspecialchars($row['statut']); ?></span></td>
                    <td>
                      <a href="?del_resa=<?php echo (int)$row['id']; ?>&tab=reservations"
                         class="btn btn-sm btn-danger"
                         onclick="return confirm('Supprimer cette reservation ?')">
                        <i class="fas fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="8" class="text-center text-muted py-4">Aucune reservation.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <?php endif; /* end reservations */ ?>

      <!-- ===================== AVIS ===================== -->
      <?php if ($active_tab === 'avis'): ?>
      <h4 class="fw-bold mb-4" style="color:var(--brand);"><i class="fas fa-star me-2"></i>Gestion des avis</h4>

      <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
              <thead>
                <tr><th>#</th><th>Nom</th><th>Note</th><th>Message</th><th>Action</th></tr>
              </thead>
              <tbody>
                <?php if ($avis_list && $avis_list->num_rows > 0): ?>
                  <?php while ($row = $avis_list->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo (int)$row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['nom']); ?></td>
                    <td>
                      <?php for ($i = 0; $i < (int)$row['note']; $i++): ?>
                        <i class="fas fa-star text-warning"></i>
                      <?php endfor; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['message']); ?></td>
                    <td>
                      <a href="?del_avis=<?php echo (int)$row['id']; ?>&tab=avis"
                         class="btn btn-sm btn-danger"
                         onclick="return confirm('Supprimer cet avis ?')">
                        <i class="fas fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="5" class="text-center text-muted py-4">Aucun avis.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <?php endif; /* end avis */ ?>

      <!-- ===================== TARIFS ===================== -->
      <?php if ($active_tab === 'tarifs'): ?>
      <h4 class="fw-bold mb-4" style="color:var(--brand);"><i class="fas fa-tags me-2"></i>Gestion des tarifs</h4>

      <!-- Add tarif form -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header fw-bold" style="background:var(--brand);color:#fff;">
          <i class="fas fa-plus me-1"></i>Ajouter un tarif
        </div>
        <div class="card-body">
          <form method="POST">
            <input type="hidden" name="action" value="add_tarif">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <div class="row g-2">
              <div class="col-md-4">
                <input type="text" name="pack" class="form-control" placeholder="Nom du pack" required>
              </div>
              <div class="col-md-5">
                <input type="text" name="description" class="form-control" placeholder="Description" required>
              </div>
              <div class="col-md-2">
                <input type="number" name="prix" class="form-control" placeholder="Prix (FCFA)" min="1" required>
              </div>
              <div class="col-md-1">
                <button type="submit" class="btn btn-brand w-100"><i class="fas fa-plus"></i></button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Tarifs table -->
      <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
              <thead><tr><th>#</th><th>Pack</th><th>Description</th><th>Prix</th><th>Actions</th></tr></thead>
              <tbody>
                <?php if ($tarifs_list && $tarifs_list->num_rows > 0): ?>
                  <?php while ($row = $tarifs_list->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo (int)$row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['pack']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['prix']); ?> FCFA</td>
                    <td>
                      <button class="btn btn-sm btn-warning me-1"
                              onclick="openEditTarif(<?php echo (int)$row['id']; ?>,'<?php echo addslashes(htmlspecialchars($row['pack'])); ?>','<?php echo addslashes(htmlspecialchars($row['description'])); ?>',<?php echo (int)$row['prix']; ?>)">
                        <i class="fas fa-edit"></i>
                      </button>
                      <a href="?del_tarif=<?php echo (int)$row['id']; ?>&tab=tarifs"
                         class="btn btn-sm btn-danger"
                         onclick="return confirm('Supprimer ce tarif ?')">
                        <i class="fas fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="5" class="text-center text-muted py-4">Aucun tarif.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Edit Tarif Modal -->
      <div class="modal fade" id="editTarifModal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header" style="background:var(--brand);color:#fff;">
              <h5 class="modal-title"><i class="fas fa-edit me-1"></i>Modifier le tarif</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
              <div class="modal-body">
                <input type="hidden" name="action" value="edit_tarif">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="mb-3">
                  <label class="form-label fw-semibold">Pack</label>
                  <input type="text" name="pack" id="edit_pack" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-semibold">Description</label>
                  <input type="text" name="description" id="edit_description" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label fw-semibold">Prix (FCFA)</label>
                  <input type="number" name="prix" id="edit_prix" class="form-control" min="1" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-brand"><i class="fas fa-save me-1"></i>Enregistrer</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <?php endif; /* end tarifs */ ?>

      <!-- ===================== GALERIE ===================== -->
      <?php if ($active_tab === 'galerie'): ?>
      <h4 class="fw-bold mb-4" style="color:var(--brand);"><i class="fas fa-images me-2"></i>Gestion de la galerie</h4>

      <!-- Upload form -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header fw-bold" style="background:var(--brand);color:#fff;">
          <i class="fas fa-upload me-1"></i>Ajouter une photo
        </div>
        <div class="card-body">
          <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_photo">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <div class="row g-2 align-items-end">
              <div class="col-md-8">
                <input type="file" name="image" class="form-control"
                       accept="image/jpeg,image/png,image/gif,image/webp" required>
                <small class="text-muted">Formats acceptés : JPEG, PNG, GIF, WEBP</small>
              </div>
              <div class="col-md-4">
                <button type="submit" class="btn btn-brand w-100">
                  <i class="fas fa-upload me-1"></i>Uploader
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Gallery grid -->
      <div class="row g-3">
        <?php if ($galerie_list && $galerie_list->num_rows > 0): ?>
          <?php while ($row = $galerie_list->fetch_assoc()): ?>
            <?php $file = 'uploads/' . trim($row['images']); ?>
            <?php if (file_exists($file)): ?>
            <div class="col-6 col-md-4 col-lg-3">
              <div class="card border-0 shadow-sm">
                <img src="<?php echo htmlspecialchars($file); ?>"
                     class="card-img-top" style="height:150px;object-fit:cover;" alt="Photo">
                <div class="card-body p-2 text-center">
                  <a href="?del_photo=<?php echo (int)$row['id']; ?>&tab=galerie"
                     class="btn btn-sm btn-danger w-100"
                     onclick="return confirm('Supprimer cette photo ?')">
                    <i class="fas fa-trash me-1"></i>Supprimer
                  </a>
                </div>
              </div>
            </div>
            <?php endif; ?>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="col-12 text-center text-muted py-4">Aucune photo.</div>
        <?php endif; ?>
      </div>
      <?php endif; /* end galerie */ ?>

    </main><!-- /main -->
  </div><!-- /row -->
</div><!-- /container-fluid -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
<?php if ($active_tab === 'stats'): ?>
(function() {
  var labels = <?php echo json_encode(array_column($chart_data, 'pack')); ?>;
  var values = <?php echo json_encode(array_map('intval', array_column($chart_data, 'nb'))); ?>;
  var ctx = document.getElementById('chartPacks');
  if (ctx) {
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels.length ? labels : ['Aucune donnée'],
        datasets: [{
          label: 'Reservations',
          data: values.length ? values : [0],
          backgroundColor: 'rgba(106,13,173,0.75)',
          borderColor: 'rgba(106,13,173,1)',
          borderWidth: 1,
          borderRadius: 6
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
      }
    });
  }
})();
<?php endif; ?>

function openEditTarif(id, pack, description, prix) {
  document.getElementById('edit_id').value = id;
  document.getElementById('edit_pack').value = pack;
  document.getElementById('edit_description').value = description;
  document.getElementById('edit_prix').value = prix;
  var modal = new bootstrap.Modal(document.getElementById('editTarifModal'));
  modal.show();
}
</script>
</body>
</html>
