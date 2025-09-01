<?php
header('Content-Type: text/html; charset=utf-8');
$code = isset($_GET['code']) ? (int)$_GET['code'] : 400;

// Détecter la langue
$language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'fr', 0, 2);
$language = in_array($language, ['fr', 'en', 'es']) ? $language : 'fr';

// Messages multilingues
$messages = [
    400 => [
        'fr' => "La requête envoyée n’est pas correcte.",
        'en' => "The request sent is not correct.",
        'es' => "La solicitud enviada no es correcta."
    ],
    401 => [
        'fr' => "Vous devez être connecté pour accéder à cette page.",
        'en' => "You must be logged in to access this page.",
        'es' => "Debes estar conectado para acceder a esta página."
    ],
    408 => [
        'fr' => "Votre requête a mis trop de temps. Merci de réessayer.",
        'en' => "Your request took too long. Please try again.",
        'es' => "Tu solicitud tomó demasiado tiempo. Por favor, intenta de nuevo."
    ],
    429 => [
        'fr' => "Vous avez effectué trop d’actions en peu de temps. Merci de patienter.",
        'en' => "You have performed too many actions in a short time. Please wait.",
        'es' => "Has realizado demasiadas acciones en poco tiempo. Por favor, espera."
    ]
];
$message = $messages[$code][$language] ?? $messages[$code]['fr'] ?? "Une erreur s’est produite.";

// En-têtes HTTP
$statusHeaders = [
    400 => '400 Bad Request',
    401 => '401 Unauthorized',
    408 => '408 Request Timeout',
    429 => '429 Too Many Requests'
];
header('HTTP/1.1 ' . ($statusHeaders[$code] ?? '400 Bad Request'));

// Actions
$actions = [
    400 => '<a href="https://www.agrifordland.com" class="btn">Retour à l’accueil</a>',
    401 => '<a href="https://www.agrifordland.com/login.php" class="btn">Se connecter</a> <a href="https://www.agrifordland.com/register.php" class="btn">Créer un compte</a>',
    408 => '<a href="javascript:location.reload();" class="btn">Rafraîchir la page</a>',
    429 => '<a href="https://www.agrifordland.com" class="btn">Retour à l’accueil</a>'
];
$action = $actions[$code] ?? '<a href="https://www.agrifordland.com" class="btn">Retour à l’accueil</a>';

error_log("Erreur $code à " . date('Y-m-d H:i:s') . " - URL: " . $_SERVER['REQUEST_URI'] . " - IP: " . $_SERVER['REMOTE_ADDR'], 3, '/errors/error.log');
?>
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erreur <?php echo $code; ?> - Agrifordland</title>
    <link rel="stylesheet" href="/errors/error.css">
</head>
<body>
    <div class="error-container">
        <img src="/images/logo-agrifordland.png" alt="Logo Agrifordland" class="logo">
        <h1 role="alert">Erreur <?php echo $code; ?></h1>
        <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
        <p><?php echo $action; ?></p>
        <p>Si le problème persiste, <a href="mailto:support@agrifordland.com" class="contact-link">contactez-nous</a>.</p>
    </div>
</body>
</html>