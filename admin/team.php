<?php
include('../admin/includes/db.php');

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Gestion d'équipe AGRIFORDLAND">
    <meta name="author" content="">
    <title>AGRIFORDLAND - Gestion d'Équipe</title>
    
    <!-- Favicon -->
    <link rel="icon" href="../images/favicon.ico" type="../images/x-icon">
    
    <!-- Fonts -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #2e59d9;
            --text-dark: #5a5c69;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fa;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            border-radius: 10px 10px 0 0 !important;
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .table-responsive {
            border-radius: 8px;
            border: 1px solid #e3e6f0;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            background-color: var(--secondary-color);
            color: var(--text-dark);
            font-weight: 600;
            border-top: none;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .member-photo {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #e3e6f0;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 4px;
        }
        
        .btn-social {
            min-width: 70px;
        }
        
        .form-control, .form-control-file {
            border-radius: 6px;
            border: 1px solid #d1d3e2;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .alert {
            border-radius: 6px;
        }
        
        .topbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .empty-state {
            text-align: center;
            padding: 2rem;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #d1d3e2;
            margin-bottom: 1rem;
        }
        
        .sidebar-brand-text {
            font-weight: 600;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar (conservé comme demandé) -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon">
                    <sup><img src="../images/logo-198x66.png" width="100" alt="logo"></sup>
                </div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">
                MES PAGES
            </div>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Pages</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Login Screens:</h6>
                        <a class="collapse-item active" href="team.php">Ma Team</a>
                        <a class="collapse-item" href="candidature_admin.php">Candidature</a>
                        <a class="collapse-item" href="consultant_admin.php">Consultants</a>
                        <a class="collapse-item" href="blog.php">Mon Blog</a>
                        <a class="collapse-item" href="actualite.php">A La Une</a>
                    </div>
                </div>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    
                    <!-- Search Bar -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Rechercher un membre..." aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
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
                                    Paramètres
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Journal d'activité
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
                
                <!-- Main Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Gestion de l'Équipe</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-download fa-sm text-white-50"></i> Exporter
                        </a>
                    </div>
                    
                    <!-- Content Row -->
                    <div class="row">
                        <!-- Team Members Table -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Membres de l'Équipe</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Actions :</div>
                                            <a class="dropdown-item" href="#">Tout exporter</a>
                                            <a class="dropdown-item" href="#">Filtrer</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Aide</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="15%">Photo</th>
                                                    <th width="20%">Nom</th>
                                                    <th width="20%">Poste</th>
                                                    <th width="20%">Réseaux</th>
                                                    <th width="20%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql = "SELECT * FROM team_members ORDER BY id DESC";
                                                $result = $conn->query($sql);
                                                
                                                if ($result->num_rows > 0): 
                                                    $counter = 1;
                                                    while ($row = $result->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><?= $counter++; ?></td>
                                                            <td>
                                                                <img src="assets/images/<?= htmlspecialchars($row['image']); ?>" alt="<?= htmlspecialchars($row['name']); ?>" class="member-photo">
                                                            </td>
                                                            <td><?= htmlspecialchars($row['name']); ?></td>
                                                            <td><?= htmlspecialchars($row['position']); ?></td>
                                                            <td>
                                                                <div class="d-flex">
                                                                    <?php if (!empty($row['facebook'])): ?>
                                                                        <a href="<?= htmlspecialchars($row['facebook']); ?>" target="_blank" class="btn btn-social btn-sm btn-facebook mr-1">
                                                                            <i class="fab fa-facebook-f"></i>
                                                                        </a>
                                                                    <?php endif; ?>
                                                                    <?php if (!empty($row['linkedin'])): ?>
                                                                        <a href="<?= htmlspecialchars($row['linkedin']); ?>" target="_blank" class="btn btn-social btn-sm btn-linkedin mr-1">
                                                                            <i class="fab fa-linkedin-in"></i>
                                                                        </a>
                                                                    <?php endif; ?>
                                                                    <?php if (!empty($row['google_plus'])): ?>
                                                                        <a href="mailto:<?= htmlspecialchars($row['google_plus']); ?>" class="btn btn-social btn-sm btn-danger">
                                                                            <i class="fas fa-envelope"></i>
                                                                        </a>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <a href="edit_member.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">
                                                                    <i class="fas fa-edit"></i> Modifier
                                                                </a>
                                                                <a href="delete_member.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce membre ?');">
                                                                    <i class="fas fa-trash-alt"></i> Supprimer
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="8" class="text-center py-4">
                                                            <div class="empty-state">
                                                                <i class="fas fa-users fa-3x"></i>
                                                                <h5 class="text-gray-500">Aucun membre d'équipe trouvé</h5>
                                                                <p class="text-muted">Commencez par ajouter un nouveau membre</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Add Member Form -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Ajouter un Membre</h6>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($_GET['message']) && $_GET['message'] === 'success'): ?>
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            Membre ajouté avec succès.
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php elseif (isset($error)): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?= htmlspecialchars($error) ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <form action="add_member.php" method="POST" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="name" class="small font-weight-bold">Nom complet</label>
                                            <input type="text" name="name" id="name" class="form-control" placeholder="Saisissez le nom complet..." required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="position" class="small font-weight-bold">Poste</label>
                                            <input type="text" name="position" id="position" class="form-control" placeholder="Saisissez le poste..." required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="facebook" class="small font-weight-bold">Lien Facebook (facultatif)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fab fa-facebook-f"></i></span>
                                                </div>
                                                <input type="url" name="facebook" id="facebook" class="form-control" placeholder="https://facebook.com/...">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="linkedin" class="small font-weight-bold">Lien LinkedIn (facultatif)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fab fa-linkedin-in"></i></span>
                                                </div>
                                                <input type="url" name="linkedin" id="linkedin" class="form-control" placeholder="https://linkedin.com/in/...">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="google_plus" class="small font-weight-bold">Email (facultatif)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                </div>
                                                <input type="email" name="google_plus" id="google_plus" class="form-control" placeholder="email@exemple.com">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="image" class="small font-weight-bold">Photo</label>
                                            <div class="custom-file">
                                                <input type="file" name="image" id="image" class="custom-file-input" required>
                                                <label class="custom-file-label" for="image">Choisir un fichier</label>
                                            </div>
                                            <small class="form-text text-muted">Formats acceptés : JPG, PNG (max 2MB)</small>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-block mt-4">
                                            <i class="fas fa-user-plus mr-2"></i> Ajouter le membre
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Tous droits réservés &copy; AGRIFORDLAND <?= date('Y') ?></span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
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
                    </button>
                </div>
                <div class="modal-body">Sélectionnez "Déconnexion" ci-dessous si vous êtes prêt à terminer votre session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
                    <a class="btn btn-primary" href="logout.php">Déconnexion</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    
    <script>
        // Afficher le nom du fichier sélectionné
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
        
        // Tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
    </script>
</body>
</html>