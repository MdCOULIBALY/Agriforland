<?php
// Démarrer la mémoire tampon
ob_start();

// Configurer la journalisation
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
$logFile = dirname(__DIR__) . '/debug.log';
error_log("=== Début supprimer_consultant.php ===\n", 3, $logFile);
error_log("POST: " . print_r($_POST, true) . "\n", 3, $logFile);

// Définir le type de contenu JSON pour AJAX
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
}

// Démarrer la session
session_start();
error_log("CSRF token: " . ($_SESSION['csrf_token'] ?? 'non défini') . "\n", 3, $logFile);

// Messages d'erreurs
$errorMessages = [
    'csrf' => "Erreur de sécurité. Veuillez réessayer.",
    'method' => "Action non autorisée.",
    'id_invalid' => "Identifiant invalide.",
    'db_connect' => "Erreur de connexion à la base. Veuillez réessayer plus tard.",
    'db_query' => "Erreur lors de la suppression. Veuillez réessayer.",
    'record_not_found' => "Consultant introuvable.",
    'file_delete' => "Erreur lors de la suppression des fichiers.",
    'generic' => "Une erreur est survenue. Veuillez réessayer."
];

// Fonction pour envoyer une réponse
function send_response($success, $message, $errors = [], $httpCode = 200) {
    global $isAjax, $logFile;
    error_log("Réponse : success=$success, message=" . (is_array($message) ? implode(', ', $message) : $message) . "\n", 3, $logFile);
    if ($isAjax) {
        ob_clean();
        http_response_code($httpCode);
        echo json_encode(['success' => $success, 'message' => $message, 'errors' => $errors], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $_SESSION[$success ? 'success_message' : 'error_message'] = is_array($message) ? implode('<br>', array_map('htmlspecialchars', $message)) : htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    header('Location: ../admin/consultant_admin.php');
    exit;
}

try {
    // Vérification CSRF
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new Exception($errorMessages['csrf'], 403);
    }

    // Vérification de la méthode
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception($errorMessages['method'], 405);
    }

    // Validation des IDs
    if (isset($_POST['ids']) && is_array($_POST['ids'])) {
        $ids = array_filter($_POST['ids'], fn($id) => is_numeric($id) && $id > 0);
        if (empty($ids)) {
            throw new Exception($errorMessages['id_invalid'], 400);
        }
    } elseif (isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0) {
        $ids = [(int)$_POST['id']];
    } else {
        throw new Exception($errorMessages['id_invalid'], 400);
    }
    error_log("IDs à supprimer : " . implode(', ', $ids) . "\n", 3, $logFile);

    // Connexion à la base
    $db_path = dirname(__DIR__) . '/admin/includes/db.php';
    if (!file_exists($db_path)) {
        throw new Exception("Fichier db.php introuvable", 500);
    }
    require_once $db_path;
    global $conn;
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception($errorMessages['db_connect'], 500);
    }
    if (!mysqli_set_charset($conn, 'utf8mb4')) {
        throw new Exception('Erreur encodage UTF-8 : ' . mysqli_error($conn), 500);
    }
    error_log("Connexion DB réussie\n", 3, $logFile);

    // Traiter chaque ID
    foreach ($ids as $id) {
        // Récupérer les chemins des fichiers
        $stmt = $conn->prepare("SELECT cv_path, diploma_path FROM consultants WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Erreur préparation requête SELECT : ' . $conn->error, 500);
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            error_log("Consultant ID $id introuvable\n", 3, $logFile);
            continue;
        }
        $row = $result->fetch_assoc();
        $cv_path = $row['cv_path'];
        $diploma_path = $row['diploma_path'];
        $stmt->close();
        error_log("Fichiers à supprimer : cv=$cv_path, diploma=$diploma_path\n", 3, $logFile);

        // Supprimer les fichiers
        $base_dir = realpath(dirname(__DIR__));
        if ($cv_path && file_exists($base_dir . '/' . $cv_path)) {
            if (!unlink($base_dir . '/' . $cv_path)) {
                error_log("Échec suppression fichier CV : $cv_path\n", 3, $logFile);
                throw new Exception($errorMessages['file_delete'], 500);
            }
            error_log("Fichier CV supprimé : $cv_path\n", 3, $logFile);
        }
        if ($diploma_path && file_exists($base_dir . '/' . $diploma_path)) {
            if (!unlink($base_dir . '/' . $diploma_path)) {
                error_log("Échec suppression fichier diplôme : $diploma_path\n", 3, $logFile);
                throw new Exception($errorMessages['file_delete'], 500);
            }
            error_log("Fichier diplôme supprimé : $diploma_path\n", 3, $logFile);
        }

        // Supprimer l'enregistrement
        $stmt = $conn->prepare("DELETE FROM consultants WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Erreur préparation requête DELETE : ' . $conn->error, 500);
        }
        $stmt->bind_param('i', $id);
        if (!$stmt->execute()) {
            throw new Exception($errorMessages['db_query'], 500);
        }
        error_log("Consultant ID $id supprimé\n", 3, $logFile);
        $stmt->close();
    }

    // Réponse de succès
    send_response(true, count($ids) > 1 ? 'Consultants supprimés avec succès !' : 'Consultant supprimé avec succès !');

} catch (Exception $e) {
    error_log("Erreur : {$e->getMessage()}\nCode : {$e->getCode()}\nTrace : {$e->getTraceAsString()}\n", 3, $logFile);
    send_response(false, $e->getMessage(), [], $e->getCode() ?: 500);
} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
    error_log("=== Fin supprimer_consultant.php ===\n", 3, $logFile);
    ob_end_clean();
}
?>