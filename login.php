<?php
session_start();

$motdepasse_admin = "Ndiougahmad16";
$erreur = "";

if (isset($_POST['password'])) {
    if ($_POST['password'] === $motdepasse_admin) {
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        $_SESSION['admin_logged'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $erreur = "Mot de passe incorrect.";
    }
}

// Si déjà connecté, rediriger vers l'admin
if (isset($_SESSION['admin']) && $_SESSION['admin']) {
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Sixteen Prod</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        .login-container {
            max-width: 360px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        .login-container h2 {
            color: #6a0dad;
            margin-bottom: 20px;
        }
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background: #6a0dad;
            color: #fff;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
            transition: 0.3s;
        }
        .login-container button:hover {
            background: #4b0082;
        }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="login-container">
    <h2>Espace Administrateur</h2>
    <?php if ($erreur): ?>
        <p class="error"><?php echo htmlspecialchars($erreur); ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>
</div>

</body>
</html>
