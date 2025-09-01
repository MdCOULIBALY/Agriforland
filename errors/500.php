<?php
header('HTTP/1.1 500 Internal Server Error');
header('Content-Type: text/html; charset=utf-8');
error_log("Erreur 500 à " . date('Y-m-d H:i:s') . " - URL: " . $_SERVER['REQUEST_URI'] . " - IP: " . $_SERVER['REMOTE_ADDR'], 3, '/errors/error.log');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erreur serveur - Agrifordland</title>
    <link rel="stylesheet" href="/errors/error.css">
</head>
<body>
    <div class="error-container">
        <img src="/images/logo-agrifordland.png" alt="Logo Agrifordland" class="logo">
        <h1 role="alert">Erreur interne du serveur (500)</h1>
        <p>Une erreur s’est produite de notre côté. Nos équipes ont été alertées et travaillent à résoudre le problème.</p>
        <p>
            <a href="https://www.agrifordland.com" class="btn">Retour à l’accueil</a>
        </p>
        <p>Si le problème persiste, <a href="mailto:support@agrifordland.com" class="contact-link">contactez-nous</a>.</p>
    </div>
</body>
</html>