<?php
$cache_dir = __DIR__ . '/cache/';
$supported_ext = ['jpg', 'jpeg', 'png', 'webp'];

echo "<h2>📂 Liste des images dans /cache/</h2>";

if (!is_dir($cache_dir)) {
    die("<p style='color:red;'>❌ Le dossier /cache/ n'existe pas.</p>");
}

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cache_dir));
$total = 0;

echo "<table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse; font-family:sans-serif;'>
<thead>
<tr style='background:#f0f0f0;'>
    <th>#</th>
    <th>Nom de l’image</th>
    <th>Taille (Ko)</th>
    <th>Dimensions</th>
</tr>
</thead><tbody>";

foreach ($rii as $file) {
    if ($file->isDir()) continue;

    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (!in_array($ext, $supported_ext)) continue;

    $filename = $file->getPathname();
    $relative_path = str_replace('\\', '/', substr($filename, strlen($cache_dir)));

    $size_kb = round(filesize($filename) / 1024, 1);
    $dimensions = @getimagesize($filename);
    $wh = $dimensions ? "{$dimensions[0]} × {$dimensions[1]}" : "N/A";

    $total++;
    echo "<tr>
        <td>$total</td>
        <td>$relative_path</td>
        <td align='right'>$size_kb Ko</td>
        <td align='center'>$wh</td>
    </tr>";
}

echo "</tbody></table>";
echo "<p><strong>📦 Total : $total image(s) dans le cache.</strong></p>";
?>
