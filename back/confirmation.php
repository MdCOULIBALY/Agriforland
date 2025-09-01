<?php
session_start(); // Démarrer la session pour stocker les données du formulaire
// Vérifier que la requête est bien une POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../recrutements.html");
    exit();
}

// Inclure la connexion à la base de données
include('../admin/includes/db.php');

// Initialiser un tableau pour collecter les erreurs
$errors = [];

// Charger le fichier JSON avec les informations des postes
$postes = json_decode(file_get_contents('../data/recrutement.json'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    $errors[] = "Erreur de lecture du fichier des offres d'emploi : " . json_last_error_msg();
}

// Récupérer et sanitiser le slug depuis le formulaire
$slug = isset($_POST['poste_slug']) ? htmlspecialchars(trim($_POST['poste_slug'])) : '';
if (empty($slug)) {
    $errors[] = "Aucun slug de poste fourni.";
}

// Trouver le titre du poste correspondant
$titre_poste = '';
if (empty($errors) && is_array($postes)) {
    foreach ($postes as $poste) {
        if ($poste['slug'] === $slug) {
            $titre_poste = $poste['titre_fr'];
            break;
        }
    }
    if (!$titre_poste) {
        $errors[] = "Aucun poste trouvé pour le slug '$slug'.";
    }
}

// Récupérer et valider les données du formulaire
$nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
$prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';

// Stocker les données du formulaire dans la session pour pré-remplir en cas d'erreur
$_SESSION['form_data'] = [
    'nom' => $nom,
    'prenom' => $prenom,
    'email' => $email,
    'telephone' => $telephone
];

// Validation des champs
if (empty($nom)) {
    $errors[] = "Le nom est requis.";
}
if (empty($prenom)) {
    $errors[] = "Le prénom est requis.";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "L'email est invalide.";
}
if (!preg_match('/^[\d\s+-]{10,}$/', $telephone)) {
    $errors[] = "Le numéro de téléphone est invalide.";
}

// Vérifier si le candidat a déjà postulé pour ce poste
if (empty($errors) && $conn->set_charset("utf8mb4")) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM candidatures WHERE nom = ? AND telephone = ? AND poste = ?");
    if ($stmt) {
        $stmt->bind_param("sss", $nom, $telephone, $titre_poste);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        if ($count > 0) {
            $errors[] = "Vous avez déjà postulé à cette offre.";
        }
    } else {
        $errors[] = "Erreur lors de la vérification de la candidature : " . htmlspecialchars($conn->error);
    }
}

// Dossier d'upload
$dossier = 'documents_recrutement/';
if (!is_dir($dossier)) {
    if (!mkdir($dossier, 0777, true)) {
        $errors[] = "Impossible de créer le dossier d'upload.";
    }
}

// Définir la taille maximale des fichiers (1 Mo pour cohérence avec le JavaScript)
$maxFileSize = 2 * 1024 * 1024;

// Fonction pour gérer les erreurs d'upload
function getUploadErrorMessage($errorCode, $fileInputName) {
    switch ($errorCode) {
        case UPLOAD_ERR_INI_SIZE:
            return "Le fichier $fileInputName dépasse la taille maximale autorisée par le serveur.";
        case UPLOAD_ERR_FORM_SIZE:
            return "Le fichier $fileInputName dépasse la taille maximale définie dans le formulaire.";
        case UPLOAD_ERR_PARTIAL:
            return "Le fichier $fileInputName n'a été que partiellement téléchargé.";
        case UPLOAD_ERR_NO_FILE:
            return "Aucun fichier n'a été téléchargé pour $fileInputName.";
        case UPLOAD_ERR_NO_TMP_DIR:
            return "Dossier temporaire manquant pour l'upload de $fileInputName.";
        case UPLOAD_ERR_CANT_WRITE:
            return "Échec de l'écriture du fichier $fileInputName sur le disque.";
        case UPLOAD_ERR_EXTENSION:
            return "Une extension PHP a arrêté l'upload du fichier $fileInputName.";
        default:
            return "Erreur inconnue lors de l'upload du fichier $fileInputName.";
    }
}

// Fonction pour uploader un fichier PDF avec gestion des erreurs
function uploadFile($fileInputName, $dossier, $obligatoire = false, &$errors = [], $maxFileSize) {
    if (!isset($_FILES[$fileInputName])) {
        if ($obligatoire) {
            $errors[] = "Le fichier $fileInputName est requis.";
        }
        return '';
    }

    $file = $_FILES[$fileInputName];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        if ($obligatoire && $file['error'] === UPLOAD_ERR_NO_FILE) {
            $errors[] = "Le fichier $fileInputName est obligatoire.";
        } else if ($file['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = getUploadErrorMessage($file['error'], $fileInputName);
        }
        return '';
    }

    if ($file['type'] !== 'application/pdf') {
        $errors[] = "Le fichier $fileInputName doit être un PDF.";
        return '';
    }
    if ($file['size'] > $maxFileSize) {
        $errors[] = "Le fichier $fileInputName est trop volumineux (max 2 Mo).";
        return '';
    }
    // Générer un nom unique pour éviter les collisions
    $nom = uniqid('file_', true) . "_" . preg_replace("/[^A-Za-z0-9._-]/", "_", basename($file['name']));
    $chemin = $dossier . $nom;
    if (move_uploaded_file($file['tmp_name'], $chemin)) {
        return $chemin;
    } else {
        $errors[] = "Erreur lors de l'enregistrement du fichier $fileInputName sur le serveur.";
        return '';
    }
}

// Uploader les fichiers obligatoires et facultatifs
$cv_path = uploadFile('cv', $dossier, true, $errors, $maxFileSize);
$lettre_path = uploadFile('lettre', $dossier, true, $errors, $maxFileSize);
$diplomes_path = uploadFile('diplomes', $dossier, true, $errors, $maxFileSize);
$certification_path = uploadFile('certification', $dossier, false, $errors, $maxFileSize);

// Vérifier le nombre de fichiers pour autre_document
$autres_documents_paths = [];
if (isset($_FILES['autre_document']) && !empty($_FILES['autre_document']['name'][0])) {
    $files = $_FILES['autre_document'];
    if (count($files['name']) > 5) {
        $errors[] = "Vous ne pouvez uploader que 5 fichiers maximum pour les autres documents.";
    } else {
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                if ($files['type'][$i] !== 'application/pdf') {
                    $errors[] = "Le fichier autre_document[{$i}] doit être un PDF.";
                    continue;
                }
                if ($files['size'][$i] > $maxFileSize) {
                    $errors[] = "Le fichier autre_document[{$i}] est trop volumineux (max 2 Mo).";
                    continue;
                }
                $nom_fichier = uniqid('file_', true) . "_" . preg_replace("/[^A-Za-z0-9._-]/", "_", basename($files['name'][$i]));
                $chemin_fichier = $dossier . $nom_fichier;
                if (move_uploaded_file($files['tmp_name'][$i], $chemin_fichier)) {
                    $autres_documents_paths[] = $chemin_fichier;
                } else {
                    $errors[] = "Erreur lors de l'enregistrement du fichier autre_document[{$i}] sur le serveur.";
                }
            } else if ($files['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                $errors[] = getUploadErrorMessage($files['error'][$i], "autre_document[{$i}]");
            }
        }
    }
}
$autres_documents_json = json_encode($autres_documents_paths, JSON_UNESCAPED_SLASHES);

// Si des erreurs ont été détectées, rediriger vers la page d'erreur
if (!empty($errors)) {
    $error_message = implode("<br>", array_map('htmlspecialchars', $errors));
    header("Location: ../error.php?message=" . urlencode($error_message) . "&slug=" . urlencode($slug));
    exit();
}

// S'assurer que la connexion utilise l'encodage UTF-8
if ($conn->set_charset("utf8mb4")) {
    // Préparer l'insertion dans la base de données
    $stmt = $conn->prepare("INSERT INTO candidatures 
        (nom, prenom, email, telephone, cv, lettre, diplomes, certification, autres_documents, date_postulation, poste) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
    if (!$stmt) {
        $error_message = "Erreur dans la requête préparée : " . htmlspecialchars($conn->error);
        header("Location: ../error.php?message=" . urlencode($error_message) . "&slug=" . urlencode($slug));
        exit();
    }

    $stmt->bind_param(
        "ssssssssss",
        $nom, $prenom, $email, $telephone,
        $cv_path, $lettre_path, $diplomes_path,
        $certification_path, $autres_documents_json,
        $titre_poste
    );

    // Exécuter et rediriger
    if ($stmt->execute()) {
        // Nettoyer la session après succès
        unset($_SESSION['form_data']);
        header("Location: ../recrutements.html?success=1");
        exit();
    } else {
        $error_message = "Erreur lors de l'enregistrement : " . htmlspecialchars($stmt->error);
        header("Location: ../error.php?message=" . urlencode($error_message) . "&slug=" . urlencode($slug));
        exit();
    }

    $stmt->close();
} else {
    $error_message = "Impossible de définir le jeu de caractères UTF-8 : " . htmlspecialchars($conn->error);
    header("Location: ../error.php?message=" . urlencode($error_message) . "&slug=" . urlencode($slug));
    exit();
}

$conn->close();
?>