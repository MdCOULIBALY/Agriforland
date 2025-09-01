<?php
include('includes/db.php'); 

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM a_la_une ORDER BY date_publication DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Dashboard d'administration AGRIFORDLAND">
    <meta name="author" content="">
    <title>AGRIFORDLAND - Gestion des Actualités</title>
    
    <!-- Favicon -->
    <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
    
    <!-- Fonts -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.css" rel="stylesheet">
    
    <!-- TinyMCE -->
    <script src="js/tinymce/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof tinymce !== 'undefined') {
                // Initialisation de TinyMCE pour le champ "Résumé"
                tinymce.init({
                    selector: '#resume',
                    plugins: 'link lists',
                    toolbar: 'undo redo | bold italic underline | bullist numlist | link',
                    height: 200,
                    menubar: false,
                    content_style: 'body { font-family: "Inter", sans-serif; font-size:14px }',
                    setup: function(editor) {
                        editor.on('change keyup', function() {
                            editor.save();
                        });
                    }
                });

                // Initialisation de TinyMCE pour le champ "Contenu"
                tinymce.init({
                    selector: '#contenu',
                    plugins: 'link image lists code table',
                    toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image table | code',
                    height: 250,
                    content_style: 'body { font-family: "Inter", sans-serif; font-size:14px }',
                    setup: function(editor) {
                        editor.on('change keyup', function() {
                            editor.save();
                        });
                    }
                });
            } else {
                console.log('TinyMCE n\'a pas été chargé correctement.');
            }

            // Validation du formulaire
            document.getElementById('actualiteForm').addEventListener('submit', function(e) {
                const resumeEditor = tinymce.get('resume');
                if (resumeEditor) {
                    resumeEditor.save();
                    const resumeContent = resumeEditor.getContent();
                    if (!resumeContent.trim()) {
                        e.preventDefault();
                        alert('Le résumé de l\'actualité est requis.');
                        return;
                    }
                }

                const contentEditor = tinymce.get('contenu');
                if (contentEditor) {
                    contentEditor.save();
                    const content = contentEditor.getContent();
                    if (!content.trim()) {
                        e.preventDefault();
                        alert('Le contenu de l\'actualité est requis.');
                        return;
                    }
                }
            });

            // Validation de l'image
            document.getElementById('image').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const maxSize = 2 * 1024 * 1024; // 2MB
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);

                    if (!allowedTypes.includes(file.type)) {
                        alert('Veuillez sélectionner une image au format JPG, PNG ou GIF.');
                        e.target.value = '';
                        document.getElementById('imagePreview').style.display = 'none';
                        return;
                    }
                    if (file.size > maxSize) {
                        alert(`L\'image dépasse la taille maximale autorisée de 2 Mo. Taille actuelle : ${fileSizeMB} Mo`);
                        e.target.value = '';
                        document.getElementById('imagePreview').style.display = 'none';
                        return;
                    }
                }
            });
        });

        // Image preview function
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('imagePreview');
            const fileLabel = input.nextElementSibling;
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
                fileLabel.textContent = input.files[0].name;
            } else {
                preview.src = '#';
                preview.style.display = 'none';
                fileLabel.textContent = 'Choisir un fichier';
            }
        }
    </script>
    
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
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 4px;
        }
        
        .form-control, .custom-file-input {
            border-radius: 6px;
            border: 1px solid #d1d3e2;
        }
        
        .form-control:focus, .custom-file-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .alert {
            border-radius: 6px;
        }
        
        .topbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        #imagePreview {
            border-radius: 8px;
            border: 1px dashed #d1d3e2;
            padding: 5px;
        }
        
        .action-buttons a {
            margin-right: 5px;
        }
        
        .sidebar-brand-text {
            font-weight: 600;
        }
        
        .custom-file-label::after {
            border-radius: 0 5px 5px 0;
        }

        .truncate-text {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">AGRIFORDLAND <sup><img src="../images/favicon.ico" width="50" alt="favicon"></sup></div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
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
                        <a class="collapse-item" href="team.php">Ma Team</a>
                        <a class="collapse-item" href="candidature_admin.php">Candidature</a>
                        <a class="collapse-item" href="consultant_admin.php">Consultants</a>
                        <a class="collapse-item" href="blog.php">Mon Blog</a>
                        <a class="collapse-item active" href="actualite.php">A La Une</a>
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
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($_SESSION['admin']) ?></span>
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
                        <h1 class="h3 mb-0 text-gray-800">Gestion des Actualités</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-download fa-sm text-white-50"></i> Exporter
                        </a>
                    </div>
                    
                    <!-- Content Row -->
                    <div class="row">
                        <!-- Actualités Table -->
                        <div class="col-lg-8 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Liste des Actualités</h6>
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
                                    <?php
                                    if (isset($_SESSION['success'])) {
                                        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                                ' . htmlspecialchars($_SESSION['success']) . '
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>';
                                        unset($_SESSION['success']);
                                    }
                                    if (isset($_SESSION['error'])) {
                                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                ' . htmlspecialchars($_SESSION['error']) . '
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>';
                                        unset($_SESSION['error']);
                                    }
                                    ?>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="25%">Titre</th>
                                                    <th width="15%">Image</th>
                                                    <th width="15%">Lien</th>
                                                    <th width="15%">Date</th>
                                                    <th width="25%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($result->num_rows > 0): ?>
                                                    <?php while ($row = $result->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($row['id']) ?></td>
                                                            <td class="truncate-text" title="<?= htmlspecialchars($row['titre']) ?>">
                                                                <?= htmlspecialchars($row['titre']) ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($row['image']): ?>
                                                                    <img src="<?= htmlspecialchars($row['image']) ?>" alt="Image de l'actualité <?= htmlspecialchars($row['titre']) ?>" class="img-thumbnail" style="width:50px; height:50px; object-fit: cover;">
                                                                <?php else: ?>
                                                                    <span class="text-muted">Aucune image</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($row['lien']): ?>
                                                                    <a href="<?= htmlspecialchars($row['lien']) ?>" target="_blank" class="btn btn-link btn-sm p-0">
                                                                        <i class="fas fa-external-link-alt"></i>
                                                                    </a>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?= htmlspecialchars($row['date_publication']) ?></td>
                                                            <td class="action-buttons">
                                                                <a class="btn btn-outline-warning btn-sm" href="edit_actualite.php?id=<?= $row['id'] ?>" aria-label="Modifier l'actualité <?= htmlspecialchars($row['titre']) ?>">
                                                                    <i class="fas fa-edit"></i> Modifier
                                                                </a>
                                                                <a class="btn btn-outline-danger btn-sm" href="delete_actualite.php?id=<?= $row['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette actualité ?');" aria-label="Supprimer l'actualité <?= htmlspecialchars($row['titre']) ?>">
                                                                    <i class="fas fa-trash-alt"></i> Supprimer
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center py-4">
                                                            <i class="fas fa-newspaper fa-2x text-gray-300 mb-3"></i>
                                                            <h5 class="text-gray-500">Aucune actualité trouvée</h5>
                                                            <p class="text-muted">Commencez par ajouter une nouvelle actualité</p>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Add Actualité Form -->
                        <div class="col-lg-4 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Créer une Nouvelle Actualité</h6>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($_GET['message']) && $_GET['message'] === 'success'): ?>
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            Actualité ajoutée avec succès.
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                    <?php elseif (isset($error)): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?= htmlspecialchars($error) ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <form id="actualiteForm" action="add_actualite.php" method="POST" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="titre" class="small font-weight-bold">Titre de l'actualité</label>
                                            <input type="text" name="titre" id="titre" class="form-control" placeholder="Saisissez un titre..." required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="resume" class="small font-weight-bold">Résumé</label>
                                            <textarea name="resume" id="resume" class="form-control" placeholder="Court résumé de l'actualité..."></textarea>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="contenu" class="small font-weight-bold">Contenu</label>
                                            <textarea name="contenu" id="contenu" class="form-control" placeholder="Contenu complet de l'actualité..."></textarea>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="lien" class="small font-weight-bold">Lien (facultatif)</label>
                                            <input type="url" name="lien" id="lien" class="form-control" placeholder="https://exemple.com">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="image" class="small font-weight-bold">Image (facultative)</label>
                                            <div class="custom-file">
                                                <input type="file" name="image" id="image" class="custom-file-input" onchange="previewImage(event)">
                                                <label class="custom-file-label" for="image">Choisir un fichier</label>
                                            </div>
                                            <small class="form-text text-muted">Formats acceptés : JPG, PNG, GIF (max 2MB)</small>
                                            <img id="imagePreview" src="#" alt="Aperçu de l'image" style="display:none; width:100%; max-height:150px; margin-top:10px; object-fit: contain;" />
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-block mt-4">
                                            <i class="fas fa-plus-circle mr-2"></i> Publier l'actualité
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
                        <span>Tous droits réservés © AGRIFORDLAND <?= date('Y') ?></span>
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
        // Enable tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
</body>
</html>