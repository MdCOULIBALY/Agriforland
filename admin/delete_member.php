<!-- delete_member.php -->

<?php
// Inclure la connexion à la base de données
include('includes/db.php');

// Vérifier si un ID est passé dans l'URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);

    // Récupérer l'image actuelle du membre pour la supprimer du serveur
    $sql = "SELECT image FROM team_members WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $member = $result->fetch_assoc();
        $imagePath = "assets/images/" . $member['image'];

        // Supprimer le membre de la base de données
        $deleteSql = "DELETE FROM team_members WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $id);

        if ($deleteStmt->execute()) {
            // Supprimer le fichier image du serveur si le fichier existe
            if (file_exists($imagePath) && is_file($imagePath)) {
                unlink($imagePath);
            }

            // Rediriger vers la page d'administration avec un message de succès
            header("Location: team.php?message=deleted");
            exit;
        } else {
            echo "Erreur lors de la suppression du membre.";
        }
    } else {
        echo "Membre introuvable.";
    }
} else {
    echo "ID invalide.";
}
?>
