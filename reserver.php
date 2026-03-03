<?php
// Inclure la connexion à la base de données
include("connexion.php");

// Vérifier si le formulaire est soumis
if(isset($_POST['reserver'])){
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $telephone = htmlspecialchars(trim($_POST['telephone']));
    $pack = htmlspecialchars(trim($_POST['pack']));
    $date = htmlspecialchars(trim($_POST['date']));

    if (!$email) {
        $erreur = "Adresse email invalide.";
    } elseif (empty($nom) || empty($telephone) || empty($pack) || empty($date)) {
        $erreur = "Tous les champs sont obligatoires.";
    } else {
        // Requête sécurisée avec prepared statement
        $stmt = $conn->prepare("INSERT INTO reservations (nom, email, telephone, pack, date_reservation, statut) VALUES (?, ?, ?, ?, ?, 'confirmé')");
        $stmt->bind_param("sssss", $nom, $email, $telephone, $pack, $date);

        if($stmt->execute()){
            $succes = "Réservation réussie !";

            // Envoi email au photographe
            $to = "sixteenprod2001@gmail.com";
            $subject = "Nouvelle réservation";
            $message = "Nom: $nom\nEmail: $email\nTéléphone: $telephone\nPack: $pack\nDate: $date";
            $headers = "From: sixteenprod2001@gmail.com";

            // Décommenter la ligne ci-dessous si le serveur est configuré pour envoyer des mails
            // mail($to, $subject, $body, $headers);
        } else {
            $erreur = "Erreur lors de la réservation.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Réserver un rendez-vous</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<h2>Réserver un rendez-vous</h2>

<?php if(isset($succes)): ?>
    <p style='color:green;'><?php echo htmlspecialchars($succes); ?></p>
<?php endif; ?>
<?php if(isset($erreur)): ?>
    <p style='color:red;'><?php echo htmlspecialchars($erreur); ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="nom" placeholder="Votre nom" required><br>
    <input type="email" name="email" placeholder="Votre email" required><br>
    <input type="text" name="telephone" placeholder="Votre téléphone" required><br>

    <label>Choisissez un pack :</label>
    <select name="pack">
        <option>Photos</option>
        <option>Korité</option>
        <option>Videos</option>
    </select><br>

    <label>Date de réservation :</label>
    <input type="date" name="date" required><br>

    <button type="submit" name="reserver">Réserver</button>
</form>

</body>
</html>