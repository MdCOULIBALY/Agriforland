<?php
include('includes/db.php'); // Inclure la connexion à la base de données

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et sécuriser les données
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validation des champs
    if (empty($username) || empty($password)) {
        $error = "Tous les champs (nom d'utilisateur et mot de passe) sont obligatoires.";
    } else {
        // Vérifier si l'utilisateur existe déjà
        $check_sql = "SELECT id FROM admins WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = "Cet utilisateur existe déjà.";
        } else {
            // Hacher le mot de passe avec bcrypt
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Préparer et exécuter la requête SQL
            $sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Administrateur ajouté avec succès.";
                header("Location: parametre.php");
                exit();
            } else {
                $error = "Erreur lors de l'ajout : " . $conn->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Administrateur - AGRIFORDLAND</title>
    <!-- Bootstrap CSS -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
     <link rel="icon" href="../images/favicon.ico" type="image/x-icon">

    <link href="css/sb-admin-2.css" rel="stylesheet">
    <style>
        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-control {
            border-radius: 4px;
        }
        .btn {
            border-radius: 4px;
        }
        .alert {
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .password-container {
            position: relative;
        }
        .password-container .form-control {
            padding-right: 40px; /* Espace pour l'icône */
        }
        .password-container .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Ajouter un Administrateur</h2>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="add_admin.php" method="POST" class="needs-validation" novalidate>
            <div class="form-group">
                <label for="username">Nom d'utilisateur :</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Entrez le nom d'utilisateur" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                <div class="invalid-feedback">
                    Veuillez entrer un nom d'utilisateur.
                </div>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <div class="password-container">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Entrez le mot de passe" required>
                    <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                </div>
                <div class="invalid-feedback">
                    Veuillez entrer un mot de passe.
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <a href="parametre.php" class="btn btn-secondary">Retour</a>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </div>
        </form>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Validation HTML5 -->
    <script>
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>

    <!-- Script pour basculer l'affichage du mot de passe -->
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            // Basculer le type d'input entre password et text
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Basculer l'icône entre œil ouvert et œil fermé
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>