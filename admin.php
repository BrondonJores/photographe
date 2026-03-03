<?php
session_start();

// Mot de passe admin
$motdepasse_admin = "Ndiougahmad16";

// Si le formulaire est soumis
if(isset($_POST['password'])) {
    if($_POST['password'] === $motdepasse_admin) {
        $_SESSION['admin'] = true;
    } else {
        $erreur = "Mot de passe incorrect !";
    }
}

// Si déconnexion
if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

// Si pas connecté → afficher formulaire
if(!isset($_SESSION['admin'])):
?>

<!DOCTYPE html>
<html>
<head>
    <title>Connexion Admin</title>
    <style>
        body { font-family: Arial; background:#f4f4f4; text-align:center; padding-top:100px; }
        form { background:white; padding:30px; display:inline-block; border-radius:10px; }
        input { padding:10px; margin:10px; width:200px; }
        button { padding:10px 20px; background:#6a0dad; color:white; border:none; }
    </style>
</head>
<body>

<h2>Espace Administrateur</h2>

<form method="POST">
    <input type="password" name="password" placeholder="Mot de passe" required><br>
    <button type="submit">Connexion</button>
</form>

<?php if(isset($erreur)) echo "<p style='color:red;'>$erreur</p>"; ?>

</body>
</html>

<?php
exit();
endif;

// === Si connecté ===

$conn = new mysqli("localhost", "root", "", "photographe_db");

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM reservations ORDER BY date_reservation DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Réservations</title>
    <style>
        body { font-family: Arial; background:#f4f4f4; padding:20px; }
        table { width:100%; border-collapse: collapse; background:white; }
        th, td { padding:10px; border:1px solid #ccc; text-align:center; }
        th { background:#6a0dad; color:white; }
        .logout { float:right; margin-bottom:10px; }
    </style>
</head>
<body>

<h2>Liste des Réservations</h2>

<a class="logout" href="admin.php?logout=true">Se déconnecter</a>

<table>
<tr>
    <th>Nom</th>
    <th>Email</th>
    <th>Téléphone</th>
    <th>Pack</th>
    <th>Date</th>
</tr>

<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>
        <td>".$row['nom']."</td>
        <td>".$row['email']."</td>
        <td>".$row['telephone']."</td>
        <td>".$row['pack']."</td>
        <td>".$row['date_reservation']."</td>
        </tr>";
    }
}
?>

</table>

</body>
</html>