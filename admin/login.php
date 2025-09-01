<?php
session_start();
include('includes/db.php');

// Vérification de la méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Vérifier la connexion à la base de données
    if ($conn->connect_error) {
        die("Erreur de connexion à la base de données : " . $conn->connect_error);
    }

    // Préparer la requête pour éviter les injections SQL
    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    
    // Vérifier si la préparation a réussi
    if (!$stmt) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }
    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérifier si l'utilisateur existe
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Debug: afficher les informations (à supprimer en production)
        echo "Utilisateur trouvé: " . $row['username'] . "<br>";
        echo "Hash en base: " . $row['password'] . "<br>";
        echo "Mot de passe saisi: " . $password . "<br>";
        
        // Vérification du mot de passe avec password_verify
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin'] = $username;
            echo "Connexion réussie, redirection...";
            header("Location: index.php");
            exit();
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>admin - AGRIFORLAND SARL</title>
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <link rel="icon" href="../images/favicon.ico" type="../image/x-icon">
    <!-- Stylesheets-->
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/fonts.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <div class="form">
            <h4> <img class="brand-logo-dark" src="../cache/logo-198x66-1200.webp" alt=""  height="66"/> </h4>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="inputBox">
                    <input type="text" placeholder="Nom d'Utilisateur:" name="username" required><br>
                </div>
                <div class="inputBox">
                    <input type="password" placeholder="Mot de passe:" name="password" required><br>
                </div>
                <div class="inputBox">
                    <button type="submit">Se connecter</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>