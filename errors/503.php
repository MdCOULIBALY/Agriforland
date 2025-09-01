<?php
header('HTTP/1.1 503 Service Unavailable');
header('Content-Type: text/html; charset=utf-8');
error_log("Erreur 503 à " . date('Y-m-d H:i:s') . " - URL: " . $_SERVER['REQUEST_URI'] . " - IP: " . $_SERVER['REMOTE_ADDR'], 3, '/errors/error.log');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Service indisponible - Agrifordland</title>
    <link rel="stylesheet" href="/errors/error.css">
</head>
<body>
    <div class="error-container">
        <img src="/images/logo-agrifordland.png" alt="Logo Agrifordland" class="logo">
        <h1 role="alert">Service temporairement indisponible (503)</h1>
        <p>Notre site est momentanément indisponible, probablement en raison d’une maintenance ou d’une surcharge. Merci de revenir plus tard.</p>
        <p>
            <a href="https://www.agrifordland.com" class="btn">Retour à l’accueil</a>
        </p>
        <p>Pour plus d’informations, <a href="mailto:support@agrifordland.com" class="contact-link">contactez-nous</a>.</p>
    </div>
</body>
</html>