<?php
include('includes/db.php');

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Vérifier si l'ID de l'admin est passé dans l'URL
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID d'administrateur manquant.";
    header("Location: parametre.php");
    exit();
}

$id = intval($_GET['id']);

// Récupérer les informations de l'administrateur à modifier
$sql = "SELECT * FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Administrateur non trouvé.";
    header("Location: parametre.php");
    exit();
}

$admin = $result->fetch_assoc();

// Étape 1 : Vérification du mot de passe
$password_verified = isset($_SESSION['password_verified']) && $_SESSION['password_verified'] === true;

if (isset($_POST['verify_password'])) {
    $current_password = trim($_POST['current_password'] ?? '');

    if (empty($current_password)) {
        $error = "Le mot de passe actuel est requis.";
    } else {
        // Vérifier si le mot de passe est haché avec MD5 (ancien format)
        if (strlen($admin['password']) === 32 && ctype_xdigit($admin['password'])) {
            // Mot de passe haché avec MD5
            if (md5($current_password) === $admin['password']) {
                // Convertir le mot de passe en bcrypt et mettre à jour la base de données
                $new_hashed_password = password_hash($current_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE admins SET password = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $new_hashed_password, $id);
                $update_stmt->execute();
                $update_stmt->close();

                // Recharger les données de l'admin
                $sql = "SELECT * FROM admins WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $admin = $result->fetch_assoc();

                $_SESSION['password_verified'] = true;
                $password_verified = true;
            } else {
                $error = "Le mot de passe actuel est incorrect.";
            }
        } else {
            // Mot de passe haché avec password_hash (bcrypt)
            if (password_verify($current_password, $admin['password'])) {
                $_SESSION['password_verified'] = true;
                $password_verified = true;
            } else {
                $error = "Le mot de passe actuel est incorrect.";
            }
        }
    }
}

// Étape 2 : Mise à jour des informations
if (isset($_POST['update_admin']) && $password_verified) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username)) {
        $error = "Le nom d'utilisateur est requis.";
    } else {
        // Mise à jour du mot de passe uniquement si un nouveau mot de passe est fourni
        $sql = "UPDATE admins SET username = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $username, $id);

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE admins SET username = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $username, $hashed_password, $id);
        }

        // Exécuter la requête de mise à jour
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['success'] = "Administrateur modifié avec succès.";
            } else {
                $_SESSION['error'] = "Aucune modification effectuée. Les données sont identiques.";
            }
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour : " . $conn->error;
        }

        // Réinitialiser la vérification et rediriger
        unset($_SESSION['password_verified']);
        header("Location: parametre.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Administrateur</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
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
            padding-right: 40px;
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
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon">
                    <sup><img src="../images/logo-198x66.png" width="100" alt="favicon"></sup>
                </div>
            </a>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">
            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Heading -->
            <div class="sidebar-heading">
                MES PAGES
            </div>
            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Pages</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Login Screens:</h6>
                        <a class="collapse-item" href="team.php">Ma Team</a>
                        <a class="collapse-item" href="team.php">Mon Blog</a>
                    </div>
                </div>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">
            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <!-- Topbar Search -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Recherche..." aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small" placeholder="Recherche..." aria-label="Search" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($_SESSION['admin']); ?></span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profil
                                </a>
                                <a class="dropdown-item" href="parametre.php">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    add admin
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Déconnexion
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Debut de la Page Contenant -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    </div>
                    <!-- Content Row -->
                    <div class="">
                        <!-- Pie Chart -->
                        <div class="">
                            <div class="card shadow mb-4">
                                <div class="container mt-5">
                                    <h2>Modifier un Administrateur</h2>
                                    <?php if (isset($success)): ?>
                                        <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                                    <?php endif; ?>

                                    <?php if (!$password_verified): ?>
                                        <!-- Étape 1 : Vérification du mot de passe -->
                                        <form method="POST" class="mb-5">
                                            <div class="form-group">
                                                <label for="current_password">Entrez votre mot de passe actuel :</label>
                                                <div class="password-container">
                                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                                    <i class="fas fa-eye toggle-password" id="toggleCurrentPassword"></i>
                                                </div>
                                                <div class="invalid-feedback">
                                                    Veuillez entrer votre mot de passe actuel.
                                                </div>
                                            </div>
                                            <button type="submit" name="verify_password" class="btn btn-success">Vérifier le mot de passe</button>
                                            <a href="parametre.php" class="btn btn-secondary">Annuler</a>
                                        </form>
                                    <?php else: ?>
                                        <!-- Étape 2 : Modification des informations -->
                                        <form method="POST" class="mb-5">
                                            <div class="form-group">
                                                <label for="username">Nom d'utilisateur :</label>
                                                <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($admin['username']); ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Nouveau mot de passe (laisser vide pour ne pas changer) :</label>
                                                <div class="password-container">
                                                    <input type="password" class="form-control" id="new_password" name="password">
                                                    <i class="fas fa-eye toggle-password" id="toggleNewPassword"></i>
                                                </div>
                                                <small class="form-text text-muted">Laissez vide si vous ne voulez pas changer le mot de passe.</small>
                                            </div>
                                            <button type="submit" name="update_admin" class="btn btn-success">Modifier</button>
                                            <a href="parametre.php" class="btn btn-secondary">Annuler</a>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->
            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>All Rights Reserved © Design by Mohamed Coulibaly</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Prêt à partir ?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    [/usr/local/bin/head]button>
                </div>
                <div class="modal-body">Sélectionnez « Déconnexion » ci-dessous si vous êtes prêt à mettre fin à votre session actuelle.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
                    <a class="btn btn-primary" href="logout.php">Déconnexion</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>
    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

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
        // Pour le champ du mot de passe actuel
        const toggleCurrentPassword = document.getElementById('toggleCurrentPassword');
        const currentPasswordInput = document.getElementById('current_password');
        if (toggleCurrentPassword && currentPasswordInput) {
            toggleCurrentPassword.addEventListener('click', function () {
                const type = currentPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                currentPasswordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }

        // Pour le champ du nouveau mot de passe
        const toggleNewPassword = document.getElementById('toggleNewPassword');
        const newPasswordInput = document.getElementById('new_password');
        if (toggleNewPassword && newPasswordInput) {
            toggleNewPassword.addEventListener('click', function () {
                const type = newPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                newPasswordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    </script>
</body>
</html>