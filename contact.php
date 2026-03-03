<!DOCTYPE html>
<html>
<head>
    <title>Contact - Sixteen Prod</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f5;
            color: #333;
        }

        h1 {
            text-align: center;
            margin-top: 30px;
            color: #6a0dad;
        }

        .contact-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
            padding: 30px 15px;
        }

        /* Formulaire */
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 95%;
            max-width: 400px;
            box-shadow: 1px 1px 10px rgba(0,0,0,0.1);
        }

        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            background-color: #6a0dad;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #4b0082;
        }

        /* Message de confirmation */
        .confirmation {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }

        /* Infos directes */
        .contact-info {
            max-width: 400px;
            width: 95%;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 1px 1px 10px rgba(0,0,0,0.1);
        }

        .contact-info h3 {
            color: #6a0dad;
            margin-bottom: 15px;
        }

        .contact-info p {
            margin: 8px 0;
            font-size: 15px;
        }

        .contact-info a {
            color: #6a0dad;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media screen and (max-width: 768px) {
            .contact-container {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>

<h1>Contactez-nous</h1>

<div class="contact-container">

    <!-- Message de confirmation -->
    <?php if(isset($_GET['success'])): ?>
        <p class="confirmation <?php echo $_GET['success'] == 1 ? 'success' : 'error'; ?>">
            <?php echo $_GET['success'] == 1 ? 'Votre message a été envoyé avec succès !' : 'Erreur lors de l’envoi, veuillez réessayer.'; ?>
        </p>
    <?php endif; ?>

    <!-- Formulaire -->
    <form action="contact_form_handler.php" method="post">
        <input type="text" name="nom" placeholder="Votre nom" required>
        <input type="email" name="email" placeholder="Votre email" required>
        <input type="text" name="sujet" placeholder="Sujet" required>
        <textarea name="message" placeholder="Votre message" rows="5" required></textarea>
        <button type="submit">Envoyer</button>
    </form>

    <!-- Infos directes -->
    <div class="contact-info">
        <h3>Infos Directes</h3>
        <p>📧 Email : sixteenprod2001@gmail.com</p>
        <p>📞 Téléphone : 779090053</p>
        <p>📸 Instagram : <a href="https://www.instagram.com/Sixteen_prod/" target="_blank">@Sixteen_prod</a></p>
        <p>🎵 TikTok : <a href="https://www.tiktok.com/@Sixteenprod" target="_blank">@Sixteenprod</a></p>
        <a href="https://wa.me/221779090053" target="_blank" style="color:green; font-weight:bold;">Envoyer un message sur whatsaap</a>
</p>
    </div>

</div>

</body>
</html>