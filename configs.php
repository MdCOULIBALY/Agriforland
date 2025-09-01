<?php
session_start();

// Définir la langue par défaut (français)
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'fr';
}

// Charger le fichier de langue en fonction de la session
$lang_file = 'lang/' . $_SESSION['lang'] . '.php';
if (file_exists($lang_file)) {
    $lang = include $lang_file;
} else {
    // Fallback au français si le fichier de langue n'existe pas
    $lang = include 'lang/fr.php';
}
?>