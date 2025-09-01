<?php
require_once '../admin/includes/db.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../admin/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Récupérer les chemins des fichiers
    $stmt = $conn->prepare("SELECT cv_path, diploma_path FROM consultants WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Supprimer les fichiers
        $base_path = dirname(__DIR__) . '/';
        if (file_exists($base_path . $row['cv_path'])) {
            unlink($base_path . $row['cv_path']);
        }
        if (file_exists($base_path . $row['diploma_path'])) {
            unlink($base_path . $row['diploma_path']);
        }
        
        // Supprimer l'enregistrement
        $deleteStmt = $conn->prepare("DELETE FROM consultants WHERE id = ?");
        $deleteStmt->bind_param("i", $id);
        if ($deleteStmt->execute()) {
            $_SESSION['success_message'] = "Consultant supprimé avec succès.";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la suppression.";
        }
        $deleteStmt->close();
    } else {
        $_SESSION['error_message'] = "Consultant introuvable.";
    }
    $stmt->close();
} else {
    $_SESSION['error_message'] = "Requête invalide.";
}

header("Location: ../admin/consultant_admin.php");
exit();
?>