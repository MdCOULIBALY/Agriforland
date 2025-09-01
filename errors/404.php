
<?php
header('HTTP/1.1 404 Not Found');
header('Content-Type: text/html; charset=utf-8');
error_log("Erreur 404 à " . date('Y-m-d H:i:s') . " - URL: " . $_SERVER['REQUEST_URI'] . " - IP: " . $_SERVER['REMOTE_ADDR'], 3, '/errors/error.log');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page non trouvée - Agrifordland</title>
    <link rel="stylesheet" href="/errors/error.css">
</head>
<body>
    <div class="error-container">
        <img src="/images/logo-agrifordland.png" alt="Logo Agrifordland" class="logo">
        <h1 role="alert">Oups ! Cette page n’existe pas</h1>
        <p>La page que vous cherchez semble introuvable. Peut-être a-t-elle été déplacée ou supprimée.</p>
        <img src="/images/404-illustration.png" alt="Illustration d'erreur 404" class="error-image">
        <p>
            <a href="https://www.agrifordland.com" class="btn">Retour à l’accueil</a>
            <a href="https://www.agrifordland.com/candidature.php" class="btn">Déposer une candidature</a>
        </p>
        <p>Vous cherchez autre chose ? <a href="mailto:support@agrifordland.com" class="contact-link">Contactez-nous</a>.</p>
    </div>
</body>
</html>
