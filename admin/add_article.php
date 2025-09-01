<?php
session_start();
include('includes/db.php');

// Activer le mode débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier la connexion à la base de données
if (!$conn) {
    $_SESSION['error'] = "Erreur de connexion à la base de données.";
    header("Location: blog.php");
    exit();
}

function generateSlug($title) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    return $slug;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et valider les données
    $title = trim($_POST['title'] ?? '');
    $resume = trim($_POST['resume'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $author = "agriforland";

    // Validation des champs obligatoires
    if (empty($title) || empty($resume) || empty($contenu)) {
        $_SESSION['error'] = "Tous les champs obligatoires (titre, résumé, contenu) doivent être remplis.";
        header("Location: blog.php");
        exit();
    }

    // Générer le slug
    $slug = generateSlug($title);

    // Gestion de l'image
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "img_blog/";
        // Vérifier si le dossier existe, sinon le créer
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0755, true)) {
                $_SESSION['error'] = "Impossible de créer le dossier de téléchargement.";
                header("Location: blog.php");
                exit();
            }
        }

        // Vérifier si le dossier est accessible en écriture
        if (!is_writable($target_dir)) {
            $_SESSION['error'] = "Le dossier de téléchargement n'est pas accessible en écriture.";
            header("Location: blog.php");
            exit();
        }

        // Valider le type et la taille de l'image
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 5 * 1024 * 1024; // 5MB
        $file_type = mime_content_type($_FILES['image']['tmp_name']);
        $file_size = $_FILES['image']['size'];

        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['error'] = "Seuls les fichiers JPG et PNG sont autorisés.";
            header("Location: blog.php");
            exit();
        }

        if ($file_size > $max_size) {
            $_SESSION['error'] = "L'image ne doit pas dépasser 5 Mo.";
            header("Location: blog.php");
            exit();
        }

        $image_name = basename($_FILES['image']['name']);
        $image_name = str_replace(' ', '_', $image_name);
        $unique_name = uniqid() . '_' . $image_name;
        $image_path = $target_dir . $unique_name;

        // Déplacer le fichier
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            $_SESSION['error'] = "Erreur lors du téléchargement de l'image : " . $_FILES['image']['error'];
            header("Location: blog.php");
            exit();
        }

        $image = $image_path;
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Gérer les erreurs de téléversement
        $_SESSION['error'] = "Erreur lors du téléversement de l'image : " . $_FILES['image']['error'];
        header("Location: blog.php");
        exit();
    }

    // Vérifier si le slug existe déjà
    $checkSlug = $conn->prepare("SELECT id FROM articles WHERE slug = ?");
    if ($checkSlug === false) {
        $_SESSION['error'] = "Erreur lors de la vérification du slug : " . $conn->error;
        header("Location: blog.php");
        exit();
    }
    $checkSlug->bind_param("s", $slug);
    $checkSlug->execute();
    $checkSlug->store_result();

    if ($checkSlug->num_rows > 0) {
        $slug .= '-' . uniqid();
    }
    $checkSlug->close();

    // Préparer l'insertion avec la colonne 'contenu'
    $sql = "INSERT INTO articles (title, resume, contenu, image, slug, author, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);

    // Vérifier si la préparation de la requête a réussi
    if ($stmt === false) {
        $_SESSION['error'] = "Erreur lors de la préparation de la requête : " . $conn->error;
        header("Location: blog.php");
        exit();
    }

    // Lier les paramètres
    $stmt->bind_param("ssssss", $title, $resume, $contenu, $image, $slug, $author);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Article créé avec succès.";
    } else {
        // Supprimer l'image si elle a été téléversée mais que l'insertion échoue
        if ($image && file_exists($image)) {
            unlink($image);
        }
        $_SESSION['error'] = "Erreur lors de la création de l'article : " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: blog.php");
    exit();
} else {
    // Si la requête n'est pas POST, rediriger
    $_SESSION['error'] = "Méthode de requête non autorisée.";
    header("Location: blog.php");
    exit();
}
?>