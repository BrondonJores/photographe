<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "photographe_db";

// Connexion à la base
$conn = null;
try {
    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) {
        $conn = null;
    }
} catch (Exception $e) {
    $conn = null;
}
?>