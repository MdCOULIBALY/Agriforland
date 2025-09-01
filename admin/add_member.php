<!-- add_member.php -->

<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include('includes/db.php'); // Inclure la connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et sécuriser les données
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);
    $facebook = mysqli_real_escape_string($conn, $_POST['facebook']);
    $linkedin = mysqli_real_escape_string($conn, $_POST['linkedin']);
    $google_plus = mysqli_real_escape_string($conn, $_POST['google_plus']);
    $image = $_FILES['image']['name'];

    // Vérifier si une image a été téléchargée
    if (!empty($image)) {
        $target_dir = "assets/images/";
        $target_file = $target_dir . time() . '_' . basename($image); // Ajouter un timestamp pour éviter les conflits

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Préparer et exécuter la requête SQL
            $sql = "INSERT INTO team_members (name, position, facebook, linkedin, google_plus, image) 
                    VALUES ('$name', '$position', '$facebook', '$linkedin', '$google_plus', '" . basename($target_file) . "')";

            if ($conn->query($sql) === TRUE) {
                header("Location: team.php?message=success"); // Rediriger après succès
                exit();
            } else {
                $error = "Erreur lors de l'ajout : " . $conn->error;
            }
        } else {
            $error = "Erreur lors du téléchargement de l'image.";
        }
    } else {
        $error = "Veuillez sélectionner une image.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Membre</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css"> <!-- Optionnel pour un meilleur style -->
</head>
<body>
    <div class="container mt-5">
        <h2>Ajouter un Nouveau Membre</h2>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form action="add_member.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="form-group">
                <label for="name">Nom :</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="position">Poste :</label>
                <input type="text" name="position" id="position" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="facebook">Facebook :</label>
                <input type="url" name="facebook" id="facebook" class="form-control">
            </div>
            <div class="form-group">
                <label for="linkedin">LinkedIn :</label>
                <input type="url" name="linkedin" id="linkedin" class="form-control">
            </div>
            <div class="form-group">
                <label for="google_plus">Google Plus :</label>
                <input type="url" name="google_plus" id="google_plus" class="form-control">
            </div>
            <div class="form-group">
                <label for="image">Photo :</label>
                <input type="file" name="image" id="image" class="form-control-file" required>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
</body>
</html>
