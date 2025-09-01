<!-- add_actualite.php -->
<?php
include('includes/db.php');
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'] ?? '';
    $resume = $_POST['resume'] ?? '';
    $lien = $_POST['lien'] ?? '';
    $contenu = $_POST['contenu'] ?? '';
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titre)));
    $image = null;

    // Gestion de l'upload d'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($file_extension, $allowed_extensions)) {
            $image = 'uploads/' . uniqid() . '.' . $file_extension; // Ajout d'un identifiant unique pour éviter les conflits
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $image)) {
                $_SESSION['error'] = "Erreur lors de l'upload de l'image.";
                header('Location: actualite.php');
                exit();
            }
        } else {
            $_SESSION['error'] = "Extension de fichier non autorisée.";
            header('Location: actualite.php');
            exit();
        }
    }

    // Requête SQL incluant le champ contenu
    $stmt = $conn->prepare("INSERT INTO a_la_une (titre, resume, image, lien, contenu, date_publication, slug) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
    
    if (!$stmt) {
        die("Erreur dans la requête SQL : " . $conn->error);
    }

    // Liaison des paramètres (6 placeholders, 6 variables)
    $stmt->bind_param("ssssss", $titre, $resume, $image, $lien, $contenu, $slug);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Actualité ajoutée avec succès.";
    } else {
        $_SESSION['error'] = "Erreur lors de l'ajout : " . $stmt->error;
    }

    $stmt->close();
    header('Location: actualite.php');
    exit();
}
?>
