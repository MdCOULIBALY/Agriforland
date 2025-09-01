<?php
// 🔧 Réglages
$source_dir = __DIR__ . '/images/';
$cache_dir  = __DIR__ . '/cache/';
$widths = [480, 800, 1200];
$supported_ext = ['jpg', 'jpeg', 'png', 'webp'];

function listImages($dir) {
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    $files = [];
    foreach ($rii as $file) {
        if ($file->isDir()) continue;
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($dir)));
            $files[] = $relative;
        }
    }
    return $files;
}

function cacheFileName($img, $width) {
    $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
    $dir = pathinfo($img, PATHINFO_DIRNAME);
    $name = pathinfo($img, PATHINFO_FILENAME);

    // Si $dir vaut '.' ou '', on utilise uniquement le nom du fichier
    if ($dir === '.' || $dir === '') {
        $flat_name = $name;
    } else {
        // Remplace les slash et anti-slash par underscore dans $dir, sans ajouter de '_' initial
        $clean_dir = str_replace(['/', '\\'], '_', trim($dir, '/\\'));
        $flat_name = $clean_dir . '_' . $name;
    }

    return $flat_name . '-' . $width . '.' . $ext;
}

$images = listImages($source_dir);
$total = count($images);
echo "<h2>🔍 $total image(s) trouvée(s) dans 'images/'</h2>";

$regenerated = 0;

foreach ($images as $img) {
    echo "<p><strong>$img</strong><br>";
    foreach ($widths as $w) {
        $cache_file = $cache_dir . cacheFileName($img, $w);
        if (file_exists($cache_file)) {
            echo "✅ <span style='color:green'>Déjà présent [$w px]</span><br>";
            continue;
        }

        $url = "http://localhost/agriforlands/resize.php?img=" . urlencode($img) . "&width=$w";

        // Lancement de la génération
        $context = stream_context_create(['http' => ['timeout' => 10]]);
        $result = @file_get_contents($url, false, $context);

        if ($result !== false && file_exists($cache_file)) {
            echo "🆕 <span style='color:blue'>Généré avec succès [$w px]</span><br>";
            $regenerated++;
        } else {
            echo "❌ <span style='color:red'>Échec de génération [$w px]</span><br>";
        }

        flush();
        usleep(300000); // Pause 0,3 sec
    }
    echo "</p>";
}

echo "<hr><strong>✔️ Terminé. $regenerated image(s) régénérée(s).</strong>";