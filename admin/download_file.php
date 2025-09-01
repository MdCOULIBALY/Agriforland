<?php
session_start();

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['admin'])) {
    header('HTTP/1.1 403 Forbidden');
    echo 'Accès non autorisé.';
    exit();
}

// Vérifier le paramètre file
if (!isset($_GET['file'])) {
    header('HTTP/1.1 400 Bad Request');
    echo 'Fichier non spécifié.';
    exit();
}

$file_path = $_GET['file'];
// Sécuriser le chemin pour éviter les attaques de traversal
$base_dir = realpath('back/documents_recrutement');
$full_path = realpath('back/' . $file_path);

if ($full_path === false || strpos($full_path, $base_dir) !== 0 || !file_exists($full_path)) {
    header('HTTP/1.1 404 Not Found');
    echo 'Fichier introuvable.';
    exit();
}

// Vérifier l'extension
$ext = strtolower(pathinfo($full_path, PATHINFO_EXTENSION));
if ($ext !== 'pdf') {
    header('HTTP/1.1 403 Forbidden');
    echo 'Type de fichier non autorisé.';
    exit();
}

// Envoyer le fichier
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($full_path) . '"');
header('Content-Length: ' . filesize($full_path));
header('Cache-Control: no-cache, must-revalidate');
header('Expires: 0');

readfile($full_path);
exit();
?>