<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "photographe_db";

// Connexion à la base
$conn = new mysqli($host, $user, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}
?>