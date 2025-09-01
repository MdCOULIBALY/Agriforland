<?php
// resize.php?img=projet_realise/image5.jpg&width=800

// 🔧 DEBUG : Afficher les erreurs (à désactiver en prod)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 📁 Répertoires
$source_dir = __DIR__ . '/images/';
$cache_dir  = __DIR__ . '/cache/';

$img   = isset($_GET['img']) ? $_GET['img'] : '';
$width = isset($_GET['width']) ? (int) $_GET['width'] : 800;

// 🚨 Sécurité : Bloque les chemins suspects
if (strpos($img, '..') !== false || !preg_match('/^[a-zA-Z0-9_\/\.-]+\.(jpg|jpeg|png|webp)$/i', $img)) {
    http_response_code(400);
    exit('Chemin non autorisé.');
}

$src_path = realpath($source_dir . $img);
if (!$src_path || !file_exists($src_path)) {
    http_response_code(404);
    exit('Image source non trouvée.');
}

// 🔄 Prépare le chemin du cache
$ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
$dirname = pathinfo($img, PATHINFO_DIRNAME);
$basename = pathinfo($img, PATHINFO_FILENAME);

// Nettoyage du répertoire pour éviter les '_.' ou '_' initial
if ($dirname === '.' || $dirname === '') {
    $clean_basename = $basename;
} else {
    $clean_dir = str_replace(['/', '\\'], '_', trim($dirname, '/\\'));
    $clean_basename = $clean_dir . '_' . $basename;
}

$cached_name = $clean_basename . '-' . $width . '.' . $ext;
$cached_path = $cache_dir . $cached_name;

// ✅ Si l'image existe déjà en cache, on l'envoie
if (file_exists($cached_path)) {
    header('Content-Type: image/' . ($ext === 'jpg' ? 'jpeg' : $ext));
    readfile($cached_path);
    exit;
}

// 🔢 Dimensions d’origine
list($original_width, $original_height) = getimagesize($src_path);
$ratio = $original_height / $original_width;
$new_height = intval($width * $ratio);

// 📷 Ouvre l'image source selon le type
switch ($ext) {
    case 'jpg':
    case 'jpeg':
        $src = @imagecreatefromjpeg($src_path);
        break;
    case 'png':
        $src = @imagecreatefrompng($src_path);
        break;
    case 'webp':
        $src = @imagecreatefromwebp($src_path);
        break;
    default:
        http_response_code(415);
        exit('Type d’image non supporté.');
}

// ❌ Si image illisible
if (!$src) {
    http_response_code(500);
    exit('Impossible de lire l’image source.');
}

// ✂️ Redimensionnement
$dst = imagecreatetruecolor($width, $new_height);

// Gestion de la transparence pour PNG
if ($ext === 'png') {
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefilledrectangle($dst, 0, 0, $width, $new_height, $transparent);
}

imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $new_height, $original_width, $original_height);

// 📁 S'assure que le dossier cache existe
if (!is_dir($cache_dir)) {
    mkdir($cache_dir, 0777, true);
}

// 💾 Sauvegarde dans le cache
$success = false;
switch ($ext) {
    case 'jpg':
    case 'jpeg':
        $success = imagejpeg($dst, $cached_path, 80);
        break;
    case 'png':
        $success = imagepng($dst, $cached_path);
        break;
    case 'webp':
        $success = imagewebp($dst, $cached_path, 80);
        break;
}

imagedestroy($src);
imagedestroy($dst);

// 🧪 Vérifie si la sauvegarde a fonctionné
if (!$success || !file_exists($cached_path)) {
    http_response_code(500);
    exit("❌ Échec de génération de l’image [$cached_path]");
}

// ✅ Affiche l’image générée
header('Content-Type: image/' . ($ext === 'jpg' ? 'jpeg' : $ext));
readfile($cached_path);