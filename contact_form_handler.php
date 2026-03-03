<?php
require_once 'connexion.php';

// Validate required POST fields
if (
    empty($_POST['nom']) ||
    empty($_POST['email']) ||
    empty($_POST['sujet']) ||
    empty($_POST['message'])
) {
    header("Location: contact.php?success=0");
    exit();
}

// Sanitize and validate inputs
$nom     = htmlspecialchars(trim($_POST['nom']), ENT_QUOTES, 'UTF-8');
$email   = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$sujet   = htmlspecialchars(trim($_POST['sujet']), ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars(trim($_POST['message']), ENT_QUOTES, 'UTF-8');

if (!$email || !$nom || !$sujet || !$message) {
    header("Location: contact.php?success=0");
    exit();
}

// Length limits
if (mb_strlen($nom) > 100 || mb_strlen($sujet) > 200 || mb_strlen($message) > 5000) {
    header("Location: contact.php?success=0");
    exit();
}

if (!$conn) {
    header("Location: contact.php?success=0");
    exit();
}

// Insert into messages table using prepared statement
$stmt = $conn->prepare(
    "INSERT INTO messages (nom, email, sujet, message, date_envoi, lu) VALUES (?, ?, ?, ?, NOW(), 0)"
);
if (!$stmt) {
    header("Location: contact.php?success=0");
    exit();
}

$stmt->bind_param("ssss", $nom, $email, $sujet, $message);
$ok = $stmt->execute();
$stmt->close();
$conn->close();

if ($ok) {
    header("Location: contact.php?success=1");
} else {
    header("Location: contact.php?success=0");
}
exit();
?>