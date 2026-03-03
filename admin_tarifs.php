<?php
session_start();

// Auth guard
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: login.php");
    exit();
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$conn = new mysqli("localhost", "root", "", "photographe_db");
if ($conn->connect_error) die("Erreur de connexion: " . htmlspecialchars($conn->connect_error));

function validateCsrf(): void {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        die("Token CSRF invalide.");
    }
}

// Ajouter un tarif
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pack'], $_POST['description'], $_POST['prix'])
    && !isset($_POST['edit_id'])) {
    validateCsrf();
    $pack = htmlspecialchars(trim($_POST['pack']));
    $desc = htmlspecialchars(trim($_POST['description']));
    $prix = intval($_POST['prix']);
    if ($pack && $desc && $prix > 0) {
        $stmt = $conn->prepare("INSERT INTO tarifs (pack, description, prix) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $pack, $desc, $prix);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin_tarifs.php?ok=1");
    exit();
}

// Modifier un tarif
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'], $_POST['edit_pack'],
    $_POST['edit_description'], $_POST['edit_prix'])) {
    validateCsrf();
    $id   = intval($_POST['edit_id']);
    $pack = htmlspecialchars(trim($_POST['edit_pack']));
    $desc = htmlspecialchars(trim($_POST['edit_description']));
    $prix = intval($_POST['edit_prix']);
    if ($id && $pack && $desc && $prix > 0) {
        $stmt = $conn->prepare("UPDATE tarifs SET pack=?, description=?, prix=? WHERE id=?");
        $stmt->bind_param("ssii", $pack, $desc, $prix, $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin_tarifs.php?ok=1");
    exit();
}

// Supprimer un tarif
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM tarifs WHERE id=$id");
    header("Location: admin_tarifs.php?ok=1");
    exit();
}

$result = $conn->query("SELECT * FROM tarifs ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin Tarifs - Sixteen Prod</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root { --brand: #6a0dad; --brand-dark: #4b0082; }
    body { background: #f8f4ff; }
    .btn-brand { background: var(--brand); color: #fff; border: none; }
    .btn-brand:hover { background: var(--brand-dark); color: #fff; }
    .table th { background: var(--brand); color: #fff; }
  </style>
</head>
<body>

<nav class="navbar navbar-dark py-2 mb-4" style="background:var(--brand-dark);">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold"><i class="fas fa-tags me-2"></i>Admin Tarifs</span>
    <div class="d-flex gap-2">
      <a href="admin_dashboard.php?tab=tarifs" class="btn btn-sm btn-outline-light">
        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
      </a>
      <a href="logout.php" class="btn btn-sm btn-light" style="color:var(--brand);">
        <i class="fas fa-sign-out-alt me-1"></i>Deconnexion
      </a>
    </div>
  </div>
</nav>

<div class="container pb-5">
  <h4 class="fw-bold mb-4" style="color:var(--brand);"><i class="fas fa-tags me-2"></i>Gestion des Tarifs</h4>

  <?php if (isset($_GET['ok'])): ?>
    <div class="alert alert-success alert-dismissible fade show py-2">
      <i class="fas fa-check-circle me-1"></i>Operation effectuee.
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Add tarif -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header fw-bold" style="background:var(--brand);color:#fff;">
      <i class="fas fa-plus me-1"></i>Ajouter un tarif
    </div>
    <div class="card-body">
      <form method="post">
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

  <!-- Table tarifs -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead><tr><th>#</th><th>Pack</th><th>Description</th><th>Prix</th><th>Actions</th></tr></thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo (int)$row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['pack']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['prix']); ?> FCFA</td>
                <td>
                  <button class="btn btn-sm btn-warning me-1"
                          onclick="openEdit(<?php echo (int)$row['id']; ?>,'<?php echo addslashes(htmlspecialchars($row['pack'])); ?>','<?php echo addslashes(htmlspecialchars($row['description'])); ?>',<?php echo (int)$row['prix']; ?>)">
                    <i class="fas fa-edit"></i>
                  </button>
                  <a href="?delete=<?php echo (int)$row['id']; ?>"
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
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:var(--brand);color:#fff;">
        <h5 class="modal-title"><i class="fas fa-edit me-1"></i>Modifier le tarif</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="post">
        <div class="modal-body">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
          <input type="hidden" name="edit_id" id="edit_id">
          <div class="mb-3">
            <label class="form-label fw-semibold">Pack</label>
            <input type="text" name="edit_pack" id="edit_pack" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Description</label>
            <input type="text" name="edit_description" id="edit_description" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Prix (FCFA)</label>
            <input type="number" name="edit_prix" id="edit_prix" class="form-control" min="1" required>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openEdit(id, pack, description, prix) {
  document.getElementById('edit_id').value = id;
  document.getElementById('edit_pack').value = pack;
  document.getElementById('edit_description').value = description;
  document.getElementById('edit_prix').value = prix;
  new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>
</body>
</html>
