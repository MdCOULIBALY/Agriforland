<?php
// 1. Démarrer immédiatement la mémoire tampon
ob_start();

// 2. Configurer la journalisation des erreurs (plus strict)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(0);

// 3. Définir le header JSON IMMÉDIATEMENT
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

$logFile = dirname(__DIR__) . '/debug.log';

// 4. Fonction pour nettoyer et envoyer une réponse JSON
function send_json_response($success, $message, $errors = [], $httpCode = 200) {
    global $logFile;
    
    // Nettoyer complètement le buffer
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Redéfinir les headers au cas où
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($httpCode);
    
    $response = [
        'success' => $success,
        'message' => $message,
        'errors' => $errors
    ];
    
    error_log("Réponse JSON: " . json_encode($response) . "\n", 3, $logFile);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// 5. Gestionnaire d'erreurs personnalisé
set_error_handler(function($severity, $message, $file, $line) use ($logFile) {
    error_log("Erreur PHP: $message dans $file à la ligne $line\n", 3, $logFile);
    // Ne pas afficher l'erreur, juste la logger
    return true;
});

try {
    error_log("=== Début consultant_submit.php ===\n", 3, $logFile);
    
    // Démarrer la session
    session_start();

    // Vérification de la méthode HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        send_json_response(false, "Méthode non autorisée", [], 405);
    }

    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
        $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        error_log("Erreur CSRF\n", 3, $logFile);
        send_json_response(false, "Erreur de sécurité. Veuillez recharger la page.", [], 403);
    }

    // Messages d'erreur
    $errorMessages = [
        'email_exists' => "Une candidature avec cet email existe déjà.",
        'phone_exists' => "Une candidature avec ce numéro existe déjà.",
        'email_invalid' => "L'adresse email est invalide.",
        'phone_invalid' => "Le numéro de téléphone est invalide.",
        'file_missing' => "Le fichier %s est requis.",
        'file_size' => "Le fichier %s dépasse 10 Mo.",
        'file_type' => "Format de fichier %s invalide.",
        'upload_error' => "Erreur lors du téléchargement du fichier %s.",
        'db_error' => "Erreur de base de données.",
        'required_field' => "Le champ %s est requis."
    ];

    // 6. Inclusion sécurisée de la base de données
    $db_path = dirname(__DIR__) . '/admin/includes/db.php';
    if (!file_exists($db_path)) {
        throw new Exception("Fichier de configuration introuvable");
    }
    
    // Capturer et ignorer toute sortie de db.php
    ob_start();
    require_once $db_path;
    $db_output = ob_get_clean();
    
    if (!empty($db_output)) {
        error_log("Sortie indésirable de db.php: " . $db_output . "\n", 3, $logFile);
    }
    
    // Vérifier la connexion
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Connexion à la base de données échouée");
    }
    
    mysqli_set_charset($conn, 'utf8mb4');
    
    // 7. Configuration du dossier d'upload
    $uploadDir = dirname(__DIR__) . '/Uploads/consultants/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception("Impossible de créer le dossier d'upload");
        }
    }
    
    // 8. Validation des données
    $errors = [];
    $data = [];
    
    // Champs requis
    $requiredFields = [
        'name' => 'Nom et Prénoms',
        'specialty' => 'Spécialité',
        'degree' => 'Diplôme',
        'degree_institution' => 'Intitulé du diplôme',
        'experience' => 'Expérience',
        'contract_type' => 'Type de contrat',
        'availability' => 'Date de disponibilité',
        'languages' => 'Langues',
        'phone' => 'Téléphone',
        'email' => 'Email',
        'give_trainings' => 'Formations'
    ];
    
    foreach ($requiredFields as $field => $label) {
        $data[$field] = trim($_POST[$field] ?? '');
        if (empty($data[$field])) {
            $errors[] = sprintf($errorMessages['required_field'], $label);
        }
    }
    
    // Validation email
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = $errorMessages['email_invalid'];
    }
    
    // Vérifier email unique
    if (!empty($data['email'])) {
        $stmt = $conn->prepare("SELECT id FROM consultants WHERE email = ?");
        $stmt->bind_param('s', $data['email']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = $errorMessages['email_exists'];
        }
        $stmt->close();
    }
    
    // Validation téléphone
    $data['phone'] = preg_replace('/[^0-9+]/', '', $data['phone']);
    if (!empty($data['phone']) && !preg_match('/^\+?[1-9]\d{1,14}$/', $data['phone'])) {
        $errors[] = $errorMessages['phone_invalid'];
    }
    
    // Vérifier téléphone unique
    if (!empty($data['phone'])) {
        $stmt = $conn->prepare("SELECT id FROM consultants WHERE phone = ?");
        $stmt->bind_param('s', $data['phone']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = $errorMessages['phone_exists'];
        }
        $stmt->close();
    }
    
    // Validation spécialité "Autre"
    $data['specialty_other'] = '';
    if ($data['specialty'] === 'Autre') {
        $data['specialty_other'] = trim($_POST['specialty_other'] ?? '');
        if (empty($data['specialty_other'])) {
            $errors[] = "Veuillez préciser votre spécialité.";
        }
    }
    
    // Validation formations
    $data['training_modules'] = trim($_POST['training_modules'] ?? '');
    if ($data['give_trainings'] === 'yes' && empty($data['training_modules'])) {
        $errors[] = "Veuillez préciser les modules de formation.";
    }
    
    // Validation politique de confidentialité
    $data['accept_conditions'] = isset($_POST['accept_conditions']) ? 1 : 0;
    if (!$data['accept_conditions']) {
        $errors[] = "Vous devez accepter la politique de confidentialité.";
    }
    
    // 9. Validation des fichiers
    $cvPath = null;
    $diplomaPath = null;
    $maxSize = 10 * 1024 * 1024; // 10 MB
    
    // Validation CV
    if (!isset($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = sprintf($errorMessages['file_missing'], 'CV');
    } else {
        $cvFile = $_FILES['cv'];
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $allowedExts = ['pdf', 'doc', 'docx'];
        
        if ($cvFile['size'] > $maxSize) {
            $errors[] = sprintf($errorMessages['file_size'], 'CV');
        } else {
            $ext = strtolower(pathinfo($cvFile['name'], PATHINFO_EXTENSION));
            if (!in_array($cvFile['type'], $allowedTypes) || !in_array($ext, $allowedExts)) {
                $errors[] = sprintf($errorMessages['file_type'], 'CV');
            } else {
                $cvName = 'cv_' . time() . '_' . uniqid() . '.' . $ext;
                $cvPath = $uploadDir . $cvName;
                if (!move_uploaded_file($cvFile['tmp_name'], $cvPath)) {
                    $errors[] = sprintf($errorMessages['upload_error'], 'CV');
                    $cvPath = null;
                }
            }
        }
    }
    
    // Validation Diplôme
    if (!isset($_FILES['diploma']) || $_FILES['diploma']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = sprintf($errorMessages['file_missing'], 'diplôme');
    } else {
        $diplomaFile = $_FILES['diploma'];
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        $allowedExts = ['pdf', 'jpg', 'jpeg', 'png'];
        
        if ($diplomaFile['size'] > $maxSize) {
            $errors[] = sprintf($errorMessages['file_size'], 'diplôme');
        } else {
            $ext = strtolower(pathinfo($diplomaFile['name'], PATHINFO_EXTENSION));
            if (!in_array($diplomaFile['type'], $allowedTypes) || !in_array($ext, $allowedExts)) {
                $errors[] = sprintf($errorMessages['file_type'], 'diplôme');
            } else {
                $diplomaName = 'diploma_' . time() . '_' . uniqid() . '.' . $ext;
                $diplomaPath = $uploadDir . $diplomaName;
                if (!move_uploaded_file($diplomaFile['tmp_name'], $diplomaPath)) {
                    $errors[] = sprintf($errorMessages['upload_error'], 'diplôme');
                    $diplomaPath = null;
                }
            }
        }
    }
    
    // Si erreurs, nettoyer les fichiers et renvoyer erreurs
    if (!empty($errors)) {
        if ($cvPath && file_exists($cvPath)) unlink($cvPath);
        if ($diplomaPath && file_exists($diplomaPath)) unlink($diplomaPath);
        send_json_response(false, "Veuillez corriger les erreurs.", $errors, 400);
    }
    
    // 10. Insertion en base de données
    $sql = "INSERT INTO consultants (
        name, email, phone, specialty, specialty_other, degree, degree_institution,
        experience, contract_type, availability, languages, cv_path, diploma_path,
        accept_conditions, give_trainings, training_modules, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erreur préparation requête: " . $conn->error);
    }
    
    // Chemins relatifs pour la base
    $cvRelPath = 'Uploads/consultants/' . basename($cvPath);
    $diplomaRelPath = 'Uploads/consultants/' . basename($diplomaPath);
    
    $stmt->bind_param(
        'sssssssssssssiss',
        $data['name'],
        $data['email'],
        $data['phone'],
        $data['specialty'],
        $data['specialty_other'],
        $data['degree'],
        $data['degree_institution'],
        $data['experience'],
        $data['contract_type'],
        $data['availability'],
        $data['languages'],
        $cvRelPath,
        $diplomaRelPath,
        $data['accept_conditions'],
        $data['give_trainings'],
        $data['training_modules']
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Erreur insertion: " . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();
    
    error_log("Candidature enregistrée avec succès pour: " . $data['email'] . "\n", 3, $logFile);
    
    // 11. Réponse de succès
    send_json_response(true, "Candidature enregistrée avec succès !");
    
} catch (Exception $e) {
    error_log("Erreur: " . $e->getMessage() . "\n", 3, $logFile);
    
    // Nettoyer les fichiers en cas d'erreur
    if (isset($cvPath) && $cvPath && file_exists($cvPath)) unlink($cvPath);
    if (isset($diplomaPath) && $diplomaPath && file_exists($diplomaPath)) unlink($diplomaPath);
    
    send_json_response(false, "Une erreur est survenue. Veuillez réessayer.", [], 500);
}
?>