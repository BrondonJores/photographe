<?php
$conn = new mysqli("localhost", "root", "", "photographe_db");

if ($conn->connect_error) {
    die("Erreur de connexion à la base de données");
}

// Si le formulaire est soumis
if(isset($_POST['envoyer'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $message = htmlspecialchars($_POST['message']);
    $note = intval($_POST['note']);

    $stmt = $conn->prepare("INSERT INTO avis (nom, message, note) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $nom, $message, $note);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Avis Clients</title>
    <style>
        body { font-family: Arial; background:#f4f4f4; padding:20px; }
        .container { max-width:700px; margin:auto; background:white; padding:20px; border-radius:10px; }
        h2 { color:#6a0dad; }
        input, textarea, select { width:100%; padding:10px; margin:10px 0; }
        button { background:#6a0dad; color:white; padding:10px; border:none; cursor:pointer; }
        .avis { background:#fafafa; padding:15px; margin-top:15px; border-radius:8px; }
        .stars { color:gold; font-size:18px; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">

<h2>Laisser un avis</h2>

<form method="POST">
    <input type="text" name="nom" placeholder="Votre nom" required>
    <textarea name="message" placeholder="Votre avis" required></textarea>
    <label>Note :</label>
    <select name="note" required>
        <option value="5">⭐⭐⭐⭐⭐ (5)</option>
        <option value="4">⭐⭐⭐⭐ (4)</option>
        <option value="3">⭐⭐⭐ (3)</option>
        <option value="2">⭐⭐ (2)</option>
        <option value="1">⭐ (1)</option>
    </select>
    <button type="submit" name="envoyer">Envoyer</button>
</form>

<h2>Avis des clients</h2>

<?php
$result = $conn->query("SELECT * FROM avis ORDER BY id DESC");

while($row = $result->fetch_assoc()) {
    echo "<div class='avis'>";
    echo "<strong>".htmlspecialchars($row['nom'])."</strong><br>";
    echo "<div class='stars'>".str_repeat("⭐", (int)$row['note'])."</div>";
    echo "<p>".htmlspecialchars($row['message'])."</p>";
    echo "</div>";
}
?>

</div>

</body>
</html>
