<!-- edit_member.php -->

<?php
// Inclure la connexion à la base de données
include('includes/db.php');


session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Vérifier si un ID est passé dans l'URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Récupérer les informations du membre à partir de l'ID
    $sql = "SELECT * FROM team_members WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Vérifier si le membre existe
    if ($result->num_rows === 1) {
        $member = $result->fetch_assoc();
    } else {
        echo "Membre introuvable.";
        exit;
    }
} else {
    echo "ID invalide.";
    exit;
}

// Mettre à jour les informations du membre
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $position = htmlspecialchars($_POST['position']);
    $facebook = htmlspecialchars($_POST['facebook']);
    $linkedin = htmlspecialchars($_POST['linkedin']);
    $google_plus = htmlspecialchars($_POST['google_plus']);
    $image = $member['image']; // Garder l'image actuelle si aucune nouvelle n'est téléchargée

    // Vérifier si une nouvelle image est téléchargée
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "assets/images/";
        $image = time() . "_" . basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $image;

        // Déplacer le fichier dans le dossier cible
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            echo "Erreur lors du téléchargement de l'image.";
            exit;
        }
    }

    // Mettre à jour les données dans la base
    $sql = "UPDATE team_members SET name = ?, position = ?, facebook = ?, linkedin = ?, google_plus = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $name, $position, $facebook, $linkedin, $google_plus, $image, $id);

    if ($stmt->execute()) {
        header("Location: team.php?message=updated");
        exit;
    } else {
        echo "Erreur lors de la mise à jour.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un membre</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="icon" href="../images/favicon.ico" type="../images/x-icon">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body>
    
   <!-- Page Wrapper -->
   <div id="wrapper">

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon ">
                     <sup><img src="../images/logo-198x66.png" width="100" alt="favicon"></sup>
                </div>
            </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
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
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
            aria-expanded="true" aria-controls="collapsePages">
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
            <form
                class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Recherche..."
                        aria-label="Search" aria-describedby="basic-addon2">
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
                    <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-search fa-fw"></i>
                    </a>
                    <!-- Dropdown - Messages -->
                    <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                        aria-labelledby="searchDropdown">
                        <form class="form-inline mr-auto w-100 navbar-search">
                            <div class="input-group">
                                <input type="text" class="form-control bg-light border-0 small"
                                    placeholder="Recherche..." aria-label="Search"
                                    aria-describedby="basic-addon2">
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
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $_SESSION['admin']; ?></span>
                        <img class="img-profile rounded-circle"
                            src="img/undraw_profile.svg">
                    </a>

                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                        aria-labelledby="userDropdown">
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
        <h2>Modifier un membre</h2>
        <form method="POST" enctype="multipart/form-data" class="mb-5">
            <div class="form-group">
                <label for="name">Nom :</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($member['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="position">Poste :</label>
                <input type="text" class="form-control" id="position" name="position" value="<?= htmlspecialchars($member['position']); ?>" required>
            </div>
            <div class="form-group">
                <label for="facebook">Facebook :</label>
                <input type="url" class="form-control" id="facebook" name="facebook" value="<?= htmlspecialchars($member['facebook']); ?>">
            </div>
            <div class="form-group">
                <label for="linkedin">LinkedIn :</label>
                <input type="url" class="form-control" id="linkedin" name="linkedin" value="<?= htmlspecialchars($member['linkedin']); ?>">
            </div>
            <div class="form-group">
                <label for="google_plus">Email :</label>
                <input type="email" class="form-control" id="google_plus" name="google_plus" value="<?= htmlspecialchars($member['google_plus']); ?>">
            </div>
            <div class="form-group">
                <label for="image">Photo (laisser vide pour conserver l'actuelle) :</label>
                <input type="file" class="form-control" id="image" name="image">
                <img src="assets/images/<?= $member['image']; ?>" alt="Photo actuelle" width="100" class="mt-3">
            </div>
            <button type="submit" class="btn btn-success">Mettre à jour</button>
            <a href="team.php" class="btn btn-secondary">Annuler</a>
        </form>
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
                <span>All Rights Reserved &copy; Design by Mohamed Coulibaly</span>
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
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Prêt à partir ?</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body">Sélectionnez « Déconnexion » ci-dessous si vous êtes prêt à mettre fin à votre session actuelle.</div>
        <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
            <a class="btn btn-primary" href="logout.php">Deconnexion</a>
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

</body>
</html>
