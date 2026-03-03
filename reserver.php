<?php
// Inclure la connexion à la base de données
include("connexion.php");

// Vérifier si le formulaire est soumis
if(isset($_POST['reserver'])){
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $pack = $_POST['pack'];
    $date = $_POST['date'];

    // Requête pour insérer les données dans la table reservations
    $sql = "INSERT INTO reservations (nom, email, telephone, pack, date_reservation, statut)
            VALUES ('$nom','$email','$telephone','$pack','$date','confirmé')";

    if($conn->query($sql) === TRUE){
        echo "<p style='color:green;'>Réservation réussie !</p>";

        // Envoi email au photographe
        $to = "sixteenprod2001@gmail.com"; // mail du photographe
        $subject = "Nouvelle réservation";
        $message = "Nom: $nom\nEmail: $email\nTéléphone: $telephone\nPack: $pack\nDate: $date";
        $headers = "From: sixteenprod2001@gmail.com";

        // Décommenter la ligne ci-dessous si le serveur est configuré pour envoyer des mails
        // mail($to, $subject, $message, $headers);
    } else {
        echo "<p style='color:red;'>Erreur : " . $conn->error . "</p>";
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

<h2>Réserver un rendez-vous</h2>

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