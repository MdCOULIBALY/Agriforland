<?php
header('HTTP/1.1 403 Forbidden');
header('Content-Type: text/html; charset=utf-8');
error_log("Erreur 403 à " . date('Y-m-d H:i:s') . " - URL: " . $_SERVER['REQUEST_URI'] . " - IP: " . $_SERVER['REMOTE_ADDR'], 3, '/errors/error.log');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accès interdit - Agrifordland</title>
    <link rel="stylesheet" href="/errors/error.css">
</head>
<body>
    <div class="error-container">
        <img src="/images/logo-agrifordland.png" alt="Logo Agrifordland" class="logo">
        <h1 role="alert">Accès interdit (403)</h1>
        <p>Vous n’avez pas les autorisations nécessaires pour accéder à cette page.</p>
        <p>
            <a href="https://www.agrifordland.com" class="btn">Retour à l’accueil</a>
        </p>
        <p>Si vous pensez qu’il s’agit d’une erreur, <a href="mailto:support@agrifordland.com" class="contact-link">contactez-nous</a>.</p>
    </div>
</body>
</html>