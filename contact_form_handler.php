<?php

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

// Récupérer et valider les données du formulaire
$nom = htmlspecialchars(trim($_POST['nom']));
$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$sujet = htmlspecialchars(trim($_POST['sujet']));
$message_form = htmlspecialchars(trim($_POST['message']));

if (!$email) {
    header("Location: contact.php?success=0");
    exit();
}

// Mail du photographe (celui qui reçoit)
$to = "sixteenprod2001@gmail.com";

// Sujet du mail
$subject = "Message depuis le formulaire Contact: $sujet";

// Corps du mail
$message = "Nom : $nom\n";
$message .= "Email : $email\n";
$message .= "Sujet : $sujet\n";
$message .= "Message :\n$message_form";

// Headers pour que le mail soit bien formaté
$headers = "From: $email\r\n";
$headers .= "Reply-To: $email\r\n";

// Envoyer le mail
if(mail($to, $subject, $message, $headers)) {
    // Redirection vers une page de confirmation ou retour
    header("Location: contact.php?success=1");
    exit();
} else {
    header("Location: contact.php?success=0");
    exit();
}
?>