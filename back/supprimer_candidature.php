<?php
// Connexion à la base de données (ajuste selon ta configuration)
include('../admin/includes/db.php');

// Vérifier si un ID de candidature a été passé
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Requête pour supprimer la candidature de la base de données
    $deleteQuery = "DELETE FROM candidatures WHERE id = $id";

    // Exécution de la requête
    if (mysqli_query($conn, $deleteQuery)) {
        // Optionnel: Supprimer les fichiers associés (CV et lettre)
        $candidature = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM candidatures WHERE id = $id"));
        if ($candidature) {
            $cvPath = '../back/' . $candidature['cv']; // Assure-toi que le chemin est relatif à la racine du serveur web
            $lettrePath = '../back/' . $candidature['lettre'];
            if (file_exists($cvPath)) {
                unlink($cvPath); // Supprimer le fichier CV
            }
            if (file_exists($lettrePath)) {
                unlink($lettrePath); // Supprimer le fichier Lettre
            }
        }

        // Redirection vers la page de gestion des candidatures après la suppression
        header('Location: ../admin/candidature_admin.php?message=success');
        exit(); // Assure-toi que le script s'arrête après la redirection
    } else {
        // En cas d'erreur, rediriger avec un message d'erreur
        header('Location: ../admin/candidature_admin.php?message=error');
        exit();
    }
} else {
    // Si l'ID n'est pas défini, rediriger avec un message d'erreur
    header('Location: ../admin/candidature_admin.php?message=invalid');
    exit();
}
