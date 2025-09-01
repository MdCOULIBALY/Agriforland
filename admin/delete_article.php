<!-- delete_article.php -->

<?php
    session_start();
    include('includes/db.php');

    // Vérifiez si l'utilisateur est connecté
    if (!isset($_SESSION['admin'])) {
        header("Location: login.php");
        exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);

    // Récupérer le chemin de l'image
    $sql = "SELECT image FROM articles WHERE id = $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $image_path = $row['image'];

    // Supprimer l'image du dossier uploads si elle existe
    if (file_exists($image_path)) {
        unlink($image_path);
    }

    // Supprimer l'enregistrement de la base de données
    $sql = "DELETE FROM articles WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}


// Redirigez vers la page de gestion des articles
$conn->close();
header("Location: blog.php");
exit();