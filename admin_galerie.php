<?php
session_start();
$ADMIN_PASSWORD = "Ndiougahmad16";

if(isset($_POST['password'])){
    if($_POST['password'] === $ADMIN_PASSWORD){
        $_SESSION['admin_logged'] = true;
    } else {
        $error = "Mot de passe incorrect";
    }
}

if(!isset($_SESSION['admin_logged'])){
?>
    <!DOCTYPE html>
    <html lang="fr">
    <head><meta charset="UTF-8"><title>Connexion Admin Galerie</title></head>
    <body>
        <h2>Connexion Admin Galerie</h2>
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="post">
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
    </body>
    </html>
<?php
    exit;
}

$conn = new mysqli("localhost", "root", "", "photographe_db");
if($conn->connect_error){
    die("Erreur de connexion: " . $conn->connect_error);
}

// Ajouter photo
if(isset($_FILES['image'])){
    $file_name = basename($_FILES['image']['name']);
    $target_file = "uploads/" . $file_name;

    if(move_uploaded_file($_FILES['image']['tmp_name'], $target_file)){
        $stmt = $conn->prepare("INSERT INTO galerie (images) VALUES (?)");
        $stmt->bind_param("s", $file_name);
        $stmt->execute();
        $stmt->close();
        $success = "Photo ajoutée avec succès!";
    } else {
        $error = "Erreur lors de l'ajout de la photo.";
    }
}

// Supprimer photo
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $res = $conn->query("SELECT images FROM galerie WHERE id=$id");
    if($res && $res->num_rows > 0){
        $row = $res->fetch_assoc();
        $file = "uploads/" . $row['images'];
        if(file_exists($file)) unlink($file);
        $conn->query("DELETE FROM galerie WHERE id=$id");
        $success = "Photo supprimée avec succès!";
    }
}

$result = $conn->query("SELECT * FROM galerie ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin Galerie</title>
<style>
body{ font-family:Arial; background:#f5f5f5; padding:20px; }
h2{ text-align:center; }
form{ text-align:center; margin-bottom:20px; }
.gallery{ display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:15px; }
.gallery img{ width:100%; height:150px; object-fit:cover; border-radius:5px; }
button.delete{ background:red; color:white; border:none; padding:5px; cursor:pointer; margin-top:5px; border-radius:3px; }
.message{ text-align:center; color:green; }
.error{ text-align:center; color:red; }
</style>
</head>
<body>

<h2>Admin Galerie</h2>

<?php if(isset($success)) echo "<p class='message'>$success</p>"; ?>
<?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

<!-- Formulaire upload -->
<form method="post" enctype="multipart/form-data">
    <input type="file" name="image" accept="image/*" required>
    <button type="submit">Ajouter Photo</button>
</form>

<!-- Galerie admin avec option supprimer -->
<div class="gallery">
<?php
if($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $file = "uploads/" . $row['images'];
        if(file_exists($file)){
?>
        <div>
            <img src="<?= $file ?>">
            <form method="get" style="text-align:center;">
                <input type="hidden" name="delete" value="<?= $row['id'] ?>">
                <button type="submit" class="delete" onclick="return confirm('Supprimer cette photo ?')">Supprimer</button>
            </form>
        </div>
<?php
        }
    }
} else {
    echo "<p style='text-align:center;'>Aucune photo pour le moment.</p>";
}
?>
</div>

</body>
</html>