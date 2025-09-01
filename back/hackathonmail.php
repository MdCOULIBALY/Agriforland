<?php
ob_start(); // Démarrer le buffer de sortie
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0); // Désactiver l'affichage des erreurs
ini_set('log_errors', 1); // Activer le journal des erreurs
ini_set('error_log', dirname(__DIR__) . '/logs/php_errors.log'); // Chemin du fichier de log

// Charger PHPMailer
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';
require __DIR__ . '/phpmailer/src/Exception.php';

// Fonction pour envoyer une réponse JSON
function sendJsonResponse($success, $message, $httpCode = 200) {
    http_response_code($httpCode);
    header('Content-Type: application/json; charset=utf-8');
    
    echo json_encode([
        'success' => $success,
        'message' => $message
    ], JSON_UNESCAPED_UNICODE);
    
    ob_end_flush(); // Vider et envoyer le buffer
    exit;
}

// Fonction de journalisation
function logDebug($message) {
    error_log("[HACKATHON] " . date('Y-m-d H:i:s') . " - " . $message . "\n", 3, dirname(__DIR__) . '/logs/hackathon.log');
}

logDebug("=== DÉBUT TRAITEMENT HACKATHON ===");

// Vérification de la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logDebug("Méthode HTTP non autorisée: " . $_SERVER['REQUEST_METHOD']);
    sendJsonResponse(false, 'Méthode non autorisée.', 405);
}

// Vérification du token CSRF
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    logDebug("Échec de la vérification CSRF");
    sendJsonResponse(false, 'Token de sécurité invalide. Veuillez recharger la page et réessayer.', 403);
}

// Récupérer et nettoyer les données du formulaire
$nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
$prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';
$portfolio = isset($_POST['portfolio']) ? trim($_POST['portfolio']) : '';
$hackathon_id = isset($_POST['hackathon_id']) ? (int)$_POST['hackathon_id'] : 0;

// Validation des champs
if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) || empty($portfolio)) {
    logDebug("Champs manquants: nom=$nom, prenom=$prenom, email=$email, telephone=$telephone, portfolio=$portfolio");
    sendJsonResponse(false, 'Veuillez remplir tous les champs obligatoires.', 400);
}

if (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\s\-]{2,50}$/', $nom)) {
    logDebug("Nom invalide: $nom");
    sendJsonResponse(false, 'Le nom contient des caractères invalides.', 400);
}

if (!preg_match('/^[A-Za-zÀ-ÖØ-öø-ÿ\s\-]{2,50}$/', $prenom)) {
    logDebug("Prénom invalide: $prenom");
    sendJsonResponse(false, 'Le prénom contient des caractères invalides.', 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    logDebug("Email invalide: $email");
    sendJsonResponse(false, 'Email invalide.', 400);
}

if (!preg_match('/^[\+]?[0-9\s\-\(\)]{8,20}$/', $telephone)) {
    logDebug("Numéro de téléphone invalide: $telephone");
    sendJsonResponse(false, 'Numéro de téléphone invalide.', 400);
}

if (!filter_var($portfolio, FILTER_VALIDATE_URL)) {
    logDebug("URL du portfolio invalide: $portfolio");
    sendJsonResponse(false, 'URL du portfolio invalide.', 400);
}

// Charger les données des hackathons
$hackathons_file = dirname(__DIR__) . '/data/hackathons.json';
if (!file_exists($hackathons_file)) {
    logDebug("Fichier hackathons.json introuvable à: $hackathons_file");
    sendJsonResponse(false, 'Fichier de données non trouvé.', 500);
}

$hackathons_data = file_get_contents($hackathons_file);
$hackathons = json_decode($hackathons_data, true);
if (!$hackathons || !isset($hackathons['hackathons'])) {
    logDebug("Erreur lors du décodage de hackathons.json");
    sendJsonResponse(false, 'Données des hackathons invalides.', 500);
}

$hackathon_title = 'Hackathon inconnu';
foreach ($hackathons['hackathons'] as $h) {
    if ($h['id'] === $hackathon_id) {
        $hackathon_title = $h['title'];
        break;
    }
}

// Fonction d'envoi d'email
function sendHackathonEmail($nom, $prenom, $email, $telephone, $portfolio, $hackathon_title, $hackathon_id) {
    logDebug("Tentative d'envoi email avec configuration Agriforland");
    
    $mail = new PHPMailer(true);
    
    try {
        // Configuration SMTP
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host = 'mail.agriforland.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'info@agriforland.com';
        $mail->Password = 'Moh@med2904';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        
        // Options pour contourner les problèmes SSL
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];
        
        // Expéditeur et destinataire
        $mail->setFrom('info@agriforland.com', 'Hackathon AGRIFORLAND');
        $mail->addAddress('assistance@agriforland.com', 'Équipe AGRIFORLAND');
        $mail->addReplyTo($email, "$prenom $nom");
        
        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = "🏆 Inscription Hackathon - $hackathon_title";
        
        $date_inscription = date('d/m/Y à H:i:s');
        
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #f9f9f9; padding: 20px;'>
            <div style='background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                <h2 style='color: #a9cf46; text-align: center; margin-bottom: 30px;'>
                    🏆 Nouvelle inscription Hackathon
                </h2>
                
                <div style='background-color: #f1ffcd; padding: 20px; border-radius: 8px; margin-bottom: 20px;'>
                    <h3 style='color: #759916; margin-top: 0;'>Détails du Hackathon</h3>
                    <p><strong>Hackathon :</strong> $hackathon_title</p>
                    <p><strong>ID :</strong> $hackathon_id</p>
                    <p><strong>Date d'inscription :</strong> $date_inscription</p>
                </div>
                
                <div style='background-color: #ffffff; border: 2px solid #a9cf46; padding: 20px; border-radius: 8px; margin-bottom: 20px;'>
                    <h3 style='color: #759916; margin-top: 0;'>Informations du participant</h3>
                    <p><strong>Nom :</strong> $nom</p>
                    <p><strong>Prénom :</strong> $prenom</p>
                    <p><strong>Email :</strong> $email</p>
                    <p><strong>Téléphone :</strong> $telephone</p>
                    <p><strong>Portfolio :</strong> <a href='$portfolio' style='color: #a9cf46; text-decoration: underline;'>$portfolio</a></p>
                </div>
                
                <p style='text-align: center; color: #666;'>
                    Cet email a été envoyé automatiquement. Veuillez ne pas y répondre directement.
                </p>
            </div>
        </div>";
        
        $mail->AltBody = "Nouvelle inscription Hackathon\n\n" .
                         "Hackathon: $hackathon_title\n" .
                         "ID: $hackathon_id\n" .
                         "Date d'inscription: $date_inscription\n\n" .
                         "Nom: $nom\n" .
                         "Prénom: $prenom\n" .
                         "Email: $email\n" .
                         "Téléphone: $telephone\n" .
                         "Portfolio: $portfolio";
        
        $mail->send();
        logDebug("✅ EMAIL ENVOYÉ AVEC SUCCÈS");
        return true;
    } catch (Exception $e) {
        logDebug("❌ Erreur envoi email: " . $e->getMessage());
        error_log("PHPMailer Error: " . $e->getMessage() . " | Stack trace: " . $e->getTraceAsString());
        return false;
    }
}

// Sauvegarde dans un fichier JSON comme backup
function saveToBackup($nom, $prenom, $email, $telephone, $portfolio, $hackathon_id, $hackathon_title) {
    $backup_dir = dirname(__DIR__) . '/data/';
    $backup_file = $backup_dir . 'inscriptions_hackathon_' . date('Y-m') . '.json';
    
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir, 0755, true);
    }
    
    $inscriptions = [];
    if (file_exists($backup_file)) {
        $content = file_get_contents($backup_file);
        $inscriptions = json_decode($content, true) ?: [];
    }
    
    $inscriptions[] = [
        'hackathon_id' => $hackathon_id,
        'hackathon_title' => $hackathon_title,
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'telephone' => $telephone,
        'portfolio' => $portfolio,
        'date_inscription' => date('c')
    ];
    
    $result = file_put_contents($backup_file, json_encode($inscriptions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    if ($result === false) {
        logDebug("Erreur lors de l'écriture dans le fichier de backup: $backup_file");
        return false;
    }
    
    logDebug("Inscription sauvegardée dans: $backup_file");
    return true;
}

// Envoi de l'email et sauvegarde
$emailSent = sendHackathonEmail($nom, $prenom, $email, $telephone, $portfolio, $hackathon_title, $hackathon_id);
$backupSaved = saveToBackup($nom, $prenom, $email, $telephone, $portfolio, $hackathon_id, $hackathon_title);

if ($emailSent) {
    logDebug("Succès: Inscription enregistrée et email envoyé.");
    sendJsonResponse(true, 'Inscription réussie ! Vous recevrez une confirmation par e-mail.');
} else {
    if ($backupSaved) {
        logDebug("Échec de l'envoi email, mais backup sauvegardé.");
        sendJsonResponse(true, 'Inscription enregistrée, mais l\'envoi de l\'email a échoué. Nous vous contacterons bientôt.');
    } else {
        logDebug("Échec total: ni email ni backup.");
        sendJsonResponse(false, 'Erreur lors de l\'inscription. Veuillez réessayer plus tard.', 500);
    }
}

ob_end_flush();
?>