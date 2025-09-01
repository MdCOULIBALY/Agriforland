<?php
session_start();

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['admin'])) {
    header('HTTP/1.1 403 Forbidden');
    error_log("Accès interdit : utilisateur non admin", 3, "../debug.log");
    exit('Accès interdit.');
}

// Vérifier si le paramètre 'file' est fourni
if (!isset($_GET['file']) || empty($_GET['file'])) {
    header('HTTP/1.1 400 Bad Request');
    error_log("Fichier non spécifié", 3, "../debug.log");
    exit('Fichier non spécifié.');
}

// Définir le chemin de base pour les fichiers
$base_upload_path = dirname(__DIR__) . '/Uploads/consultants/';
$file_path = urldecode($_GET['file']);
$full_path = $base_upload_path . basename($file_path);

// Vérifier que le fichier existe et est lisible
if (!file_exists($full_path) || !is_readable($full_path)) {
    header('HTTP/1.1 404 Not Found');
    error_log("Fichier introuvable ou non lisible : $full_path", 3, "../debug.log");
    exit('Fichier introuvable.');
}

// Vérifier le type MIME
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $full_path);
finfo_close($finfo);
$allowed_types = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'image/jpeg',
    'image/png'
];
if (!in_array($mime_type, $allowed_types)) {
    header('HTTP/1.1 403 Forbidden');
    error_log("Type MIME non autorisé : $mime_type pour $full_path", 3, "../debug.log");
    exit('Type de fichier non autorisé.');
}

// Vérifier la taille du fichier (limite à 10 Mo)
$max_size = 10 * 1024 * 1024; // 10 MB
if (filesize($full_path) > $max_size) {
    header('HTTP/1.1 403 Forbidden');
    error_log("Fichier trop volumineux : $full_path", 3, "../debug.log");
    exit('Fichier trop volumineux.');
}

// Déterminer si c'est un téléchargement ou un affichage inline
$disposition = isset($_GET['inline']) ? 'inline' : 'attachment';

// Envoyer les en-têtes HTTP
header('Content-Type: ' . $mime_type);
header('Content-Disposition: ' . $disposition . '; filename="' . basename($full_path) . '"');
header('Content-Length: ' . filesize($full_path));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Envoyer le fichier
readfile($full_path);
error_log("Fichier envoyé : $full_path", 3, "../debug.log");
exit;
?>