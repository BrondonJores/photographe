<?php
session_start();

// Auth guard - use admin_dashboard.php instead
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
if ($conn->connect_error) {
    die("Erreur de connexion: " . htmlspecialchars($conn->connect_error));
}

$success = '';
$error   = '';

// Ajouter photo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Token CSRF invalide.");
    }
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
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
                $success = "Photo ajoutee avec succes!";
            } else {
                $error = "Erreur lors de l'upload.";
            }
        } else {
            $error = "Format de fichier non autorise (JPEG, PNG, GIF, WEBP uniquement).";
        }
    } else {
        $error = "Erreur lors de l'upload du fichier.";
    }
}

// Supprimer photo
if (isset($_GET['delete'])) {
    $id  = intval($_GET['delete']);
    $res = $conn->query("SELECT images FROM galerie WHERE id=$id");
    if ($res && $res->num_rows > 0) {
        $row  = $res->fetch_assoc();
        $file = "uploads/" . $row['images'];
        if (file_exists($file)) unlink($file);
        $conn->query("DELETE FROM galerie WHERE id=$id");
        $success = "Photo supprimee avec succes!";
    }
}

$result = $conn->query("SELECT * FROM galerie ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin Galerie - Sixteen Prod</title>
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

<nav class="navbar navbar-dark py-2 mb-4" style="background:var(--brand-dark);">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold"><i class="fas fa-images me-2"></i>Admin Galerie</span>
    <div class="d-flex gap-2">
      <a href="admin_dashboard.php?tab=galerie" class="btn btn-sm btn-outline-light">
        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
      </a>
      <a href="logout.php" class="btn btn-sm btn-light" style="color:var(--brand);">
        <i class="fas fa-sign-out-alt me-1"></i>Deconnexion
      </a>
    </div>
  </div>
</nav>

<div class="container pb-5">
  <h4 class="fw-bold mb-4" style="color:var(--brand);"><i class="fas fa-images me-2"></i>Gestion de la Galerie</h4>

  <?php if ($success): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle me-1"></i><?php echo htmlspecialchars($success); ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-1"></i><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header fw-bold" style="background:var(--brand);color:#fff;">
      <i class="fas fa-upload me-1"></i>Ajouter une photo
    </div>
    <div class="card-body">
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        <div class="row g-2 align-items-end">
          <div class="col-md-8">
            <input type="file" name="image" class="form-control"
                   accept="image/jpeg,image/png,image/gif,image/webp" required>
            <small class="text-muted">Formats acceptes : JPEG, PNG, GIF, WEBP</small>
          </div>
          <div class="col-md-4">
            <button type="submit" class="btn btn-brand w-100">
              <i class="fas fa-upload me-1"></i>Ajouter la photo
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="row g-3">
    <?php
    if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
            $file = "uploads/" . trim($row['images']);
            if (file_exists($file)):
    ?>
    <div class="col-6 col-md-4 col-lg-3">
      <div class="card border-0 shadow-sm">
        <img src="<?php echo htmlspecialchars($file); ?>"
             class="card-img-top" style="height:150px;object-fit:cover;" alt="Photo">
        <div class="card-body p-2 text-center">
          <a href="?delete=<?php echo (int)$row['id']; ?>"
             class="btn btn-sm btn-danger w-100"
             onclick="return confirm('Supprimer cette photo ?')">
            <i class="fas fa-trash me-1"></i>Supprimer
          </a>
        </div>
      </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
