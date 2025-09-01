<?php
header('Content-Type: text/html; charset=utf-8');
include('includes/db.php');

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Vérifier l'encodage de la connexion
if (!mysqli_set_charset($conn, "utf8mb4")) {
    die("Erreur lors de la définition de l'encodage UTF-8 : " . mysqli_error($conn));
}

// Paramètres de recherche
$search_text = isset($_GET['search_text']) ? mysqli_real_escape_string($conn, $_GET['search_text']) : '';
$search_date_start = isset($_GET['date_start']) ? mysqli_real_escape_string($conn, $_GET['date_start']) : '';
$search_date_end = isset($_GET['date_end']) ? mysqli_real_escape_string($conn, $_GET['date_end']) : '';
$search_favoris = isset($_GET['favoris']) && $_GET['favoris'] == '1' ? 1 : 0;

// Construire la requête SQL
$conditions = [];
if (!empty($search_text)) {
    $conditions[] = "(poste LIKE '%$search_text%' OR nom LIKE '%$search_text%' OR prenom LIKE '%$search_text%' OR email LIKE '%$search_text%' OR telephone LIKE '%$search_text%')";
}
if (!empty($search_date_start)) {
    $conditions[] = "date_postulation >= '$search_date_start 00:00:00'";
}
if (!empty($search_date_end)) {
    $conditions[] = "date_postulation <= '$search_date_end 23:59:59'";
}
if ($search_favoris) {
    $conditions[] = "favori = 1";
}

$query = "SELECT * FROM candidatures";
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}
$query .= " ORDER BY date_postulation DESC";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Erreur SQL : " . mysqli_error($conn));
}

// Charger les offres depuis le fichier JSON
$offres = json_decode(file_get_contents('../data/recrutement.json'), true);

// Marquer la candidature comme consultée ou non lue
if (isset($_POST['toggle_id'])) {
    $id = intval($_POST['toggle_id']);
    $query = "SELECT consultée FROM candidatures WHERE id = $id";
    $resultQuery = mysqli_query($conn, $query);

    if ($resultQuery && $row = mysqli_fetch_assoc($resultQuery)) {
        $currentStatus = $row['consultée'];
        $newStatus = $currentStatus == 1 ? 0 : 1;
        $updateQuery = "UPDATE candidatures SET consultée = $newStatus WHERE id = $id";
        if (mysqli_query($conn, $updateQuery)) {
            $_SESSION['success_message'] = $newStatus ? "Candidature marquée comme lue avec succès." : "Candidature marquée comme non lue avec succès.";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la mise à jour du statut.";
        }
    }
    header('Location: candidature_admin.php');
    exit();
}

// Marquer la candidature comme favori/non favori
if (isset($_POST['toggle_favori'])) {
    $id = intval($_POST['toggle_favori']);
    $query = "SELECT favori FROM candidatures WHERE id = $id";
    $resultQuery = mysqli_query($conn, $query);

    if ($resultQuery && $row = mysqli_fetch_assoc($resultQuery)) {
        $currentStatus = $row['favori'];
        $newStatus = $currentStatus == 1 ? 0 : 1;
        $updateQuery = "UPDATE candidatures SET favori = $newStatus WHERE id = $id";
        if (mysqli_query($conn, $updateQuery)) {
            $_SESSION['success_message'] = $newStatus ? "Candidature ajoutée aux favoris." : "Candidature retirée des favoris.";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la mise à jour du statut de favori.";
        }
    }
    header('Location: candidature_admin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Gestion des candidatures AGRIFORDLAND">
    <meta name="author" content="">
    <title>AGRIFORDLAND - Candidatures</title>
    
    <!-- Favicon -->
    <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
    
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
            --favori-color: #f1c40f;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fa;
        }
        
        .card-candidature {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 4px solid var(--success-color);
        }
        
        .card-candidature:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .card-candidature.read {
            border-left-color: #d1d3e2;
        }
        
        .badge-new {
            background-color: var(--success-color);
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .candidate-name {
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .candidate-position {
            color: #6c757d;
            font-size: 0.85rem;
        }
        
        .contact-info {
            font-size: 0.9rem;
        }
        
        .contact-info i {
            width: 20px;
            text-align: center;
            color: var(--success-color);
        }
        
        .btn-file {
            border-radius: 6px;
            padding: 0.25rem 0.75rem;
        }
        
        .btn-file i {
            margin-right: 5px;
        }
        
        .btn-mark, .btn-favori, .btn-delete {
            border-radius: 6px;
            font-size: 0.8rem;
            padding: 0.3rem 0.75rem;
        }
        
        .btn-favori i {
            color: var(--favori-color);
        }
        
        .filter-bar {
            background-color: #fff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .filter-bar .form-control {
            border-radius: 6px;
            border: 1px solid #d1d3e2;
        }
        
        .filter-bar .input-group-text {
            background-color: #f8f9fc;
            border: 1px solid #d1d3e2;
            border-radius: 6px 0 0 6px;
        }
        
        .filter-bar .form-check {
            margin-top: 10px;
        }
        
        .topbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .topbar .navbar-search .form-control {
            border-radius: 6px;
            border: 1px solid #d1d3e2;
        }
        
        .sidebar-brand-text {
            font-weight: 600;
        }
        
        .results-container {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
            padding-right: 10px;
        }
        
        .results-container::-webkit-scrollbar {
            width: 6px;
        }
        
        .results-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .results-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        
        .results-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        @media (max-width: 767.98px) {
            .filter-bar .col-12 {
                margin-bottom: 10px;
            }
            .filter-bar .form-check {
                margin-top: 0;
            }
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
            <li class="nav-item active">
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
                        <a class="collapse-item active" href="candidature_admin.php">Candidature</a>
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
                    
                    <!-- Search Form (Topbar) -->
                    <form class="form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search search-form" id="search-form-topbar">
                        <div class="input-group">
                            <input type="text" name="search_text" id="search-text" class="form-control bg-light border-0 small" 
                                   placeholder="Nom, prénom, email, téléphone, poste..." 
                                   value="<?= htmlspecialchars($search_text, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
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
                                <form class="form navbar-search search-form" id="search-form-mobile">
                                    <div class="form-group">
                                        <label for="search-text-mobile">Recherche</label>
                                        <input type="text" name="search_text" id="search-text-mobile" class="form-control bg-light border-0 small" 
                                               placeholder="Nom, prénom, email, téléphone, poste..." 
                                               value="<?= htmlspecialchars($search_text, ENT_QUOTES, 'UTF-8') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="date-start-mobile">Du</label>
                                        <input type="date" name="date_start" id="date-start-mobile" class="form-control bg-light border-0 small" 
                                               value="<?= htmlspecialchars($search_date_start, ENT_QUOTES, 'UTF-8') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="date-end-mobile">Au</label>
                                        <input type="date" name="date_end" id="date-end-mobile" class="form-control bg-light border-0 small" 
                                               value="<?= htmlspecialchars($search_date_end, ENT_QUOTES, 'UTF-8') ?>">
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="favoris" id="favoris-mobile" value="1" class="form-check-input" 
                                               <?= $search_favoris ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="favoris-mobile">Favoris uniquement</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block mt-3">Appliquer</button>
                                </form>
                            </div>
                        </li>
                        
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= htmlspecialchars($_SESSION['admin'], ENT_QUOTES, 'UTF-8') ?></span>
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
                    <!-- Filter Bar -->
                    <div class="filter-bar">
                        <form class="search-form" id="search-form-filters">
                            <div class="row align-items-end">
                                <div class="col-md-5 col-12">
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Du</span>
                                        </div>
                                        <input type="date" name="date_start" id="date-start" class="form-control" 
                                               value="<?= htmlspecialchars($search_date_start, ENT_QUOTES, 'UTF-8') ?>">
                                    </div>
                                </div>
                                <div class="col-md-5 col-12">
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Au</span>
                                        </div>
                                        <input type="date" name="date_end" id="date-end" class="form-control" 
                                               value="<?= htmlspecialchars($search_date_end, ENT_QUOTES, 'UTF-8') ?>">
                                    </div>
                                </div>
                                <div class="col-md-2 col-12">
                                    <div class="form-check">
                                        <input type="checkbox" name="favoris" id="favoris" value="1" class="form-check-input" 
                                               <?= $search_favoris ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="favoris">Favoris uniquement</label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Messages de succès/erreur -->
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['success_message']); ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <?php unset($_SESSION['error_message']); ?>
                    <?php endif; ?>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Gestion des Candidatures</h1>
                        <div>
                            <span class="badge badge-primary mr-2" id="total-candidatures">
                                Total: <?= mysqli_num_rows($result) ?>
                            </span>
                            <button type="button" class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#exportModal">
                                <i class="fas fa-download fa-sm text-white-50"></i> Exporter
                            </button>
                        </div>
                    </div>
                    
                    <!-- Content Row -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Dernières Candidatures</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Actions :</div>
                                            <a class="dropdown-item" href="#">Tout marquer comme lu</a>
                                            <a class="dropdown-item" href="#">Exporter en PDF</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Aide</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row results-container" id="results-container">
                                        <?php if (mysqli_num_rows($result) > 0): ?>
                                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                <?php
                                                    $posteTitre = $row['poste'] ?? 'Offre non spécifiée';
                                                    $isNew = !isset($row['consultée']) || $row['consultée'] == 0;
                                                    $isFavori = isset($row['favori']) && $row['favori'] == 1;
                                                    $cardClass = $isNew ? '' : 'read';
                                                ?>
                                                <div class="col-lg-6 mb-4">
                                                    <div class="card card-candidature <?= $cardClass ?> h-100">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                                <div>
                                                                    <input type="checkbox" class="select-candidature" name="selected_candidatures[]" value="<?= $row['id'] ?>">
                                                                    <h5 class="candidate-name mb-1">
                                                                        <?= htmlspecialchars($row['prenom'], ENT_QUOTES, 'UTF-8') ?> 
                                                                        <?= htmlspecialchars($row['nom'], ENT_QUOTES, 'UTF-8') ?>
                                                                        <?php if ($isFavori): ?>
                                                                            <i class="fas fa-star text-warning" title="Favori"></i>
                                                                        <?php endif; ?>
                                                                    </h5>
                                                                    <p class="candidate-position mb-0"><?= htmlspecialchars($posteTitre, ENT_QUOTES, 'UTF-8') ?></p>
                                                                </div>
                                                                <?php if ($isNew): ?>
                                                                    <span class="badge badge-new">Nouveau</span>
                                                                <?php endif; ?>
                                                            </div>
                                                            
                                                            <div class="contact-info mb-3">
                                                                <p class="mb-2">
                                                                    <i class="fas fa-envelope"></i> 
                                                                    <a href="mailto:<?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?>" class="text-dark"><?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?></a>
                                                                </p>
                                                                <p class="mb-2">
                                                                    <i class="fas fa-phone"></i> 
                                                                    <?= htmlspecialchars($row['telephone'], ENT_QUOTES, 'UTF-8') ?>
                                                                </p>
                                                                <p class="mb-0">
                                                                    <i class="far fa-clock"></i> 
                                                                    Postulé le <?= date('d/m/Y à H:i', strtotime($row['date_postulation'])) ?>
                                                                </p>
                                                            </div>
                                                            
                                                            <!-- Documents -->
                                                            <div class="d-flex flex-wrap gap-2 mb-3">
                                                                <a href="../back/<?= htmlspecialchars($row['cv'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-file btn-outline-success btn-sm">
                                                                    <i class="fas fa-file-pdf"></i> CV
                                                                </a>
                                                                <a href="../back/<?= htmlspecialchars($row['lettre'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-file btn-outline-primary btn-sm">
                                                                    <i class="fas fa-file-alt"></i> Lettre
                                                                </a>
                                                                <?php if (!empty($row['diplomes'])): ?>
                                                                    <a href="../back/<?= htmlspecialchars($row['diplomes'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-file btn-outline-info btn-sm">
                                                                        <i class="fas fa-graduation-cap"></i> Diplôme
                                                                    </a>
                                                                <?php endif; ?>
                                                                <?php if (!empty($row['certification'])): ?>
                                                                    <a href="../back/<?= htmlspecialchars($row['certification'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-file btn-outline-warning btn-sm">
                                                                        <i class="fas fa-certificate"></i> Certification
                                                                    </a>
                                                                <?php endif; ?>
                                                                <?php 
                                                                $autres_docs = json_decode($row['autres_documents'] ?? '[]', true);
                                                                if (!empty($autres_docs) && is_array($autres_docs)): 
                                                                    foreach($autres_docs as $index => $doc_path):
                                                                ?>
                                                                    <a href="../back/<?= htmlspecialchars($doc_path, ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-file btn-outline-secondary btn-sm">
                                                                        <i class="fas fa-file"></i> Doc <?= $index + 1 ?>
                                                                    </a>
                                                                <?php 
                                                                    endforeach;
                                                                endif; 
                                                                ?>
                                                            </div>
                                                            
                                                            <hr>
                                                            
                                                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                                                <form method="POST" action="candidature_admin.php" class="mb-0 mr-2">
                                                                    <input type="hidden" name="toggle_id" value="<?= $row['id'] ?>">
                                                                    <button type="submit" class="btn btn-mark <?= $isNew ? 'btn-success' : 'btn-outline-secondary' ?>">
                                                                        <?= $isNew ? '<i class="fas fa-check-circle"></i> Marquer comme lu' : '<i class="fas fa-eye-slash"></i> Marquer comme non lu' ?>
                                                                    </button>
                                                                </form>
                                                                <form method="POST" action="candidature_admin.php" class="mb-0 mr-2">
                                                                    <input type="hidden" name="toggle_favori" value="<?= $row['id'] ?>">
                                                                    <button type="submit" class="btn btn-favori <?= $isFavori ? 'btn-warning' : 'btn-outline-warning' ?>">
                                                                        <i class="<?= $isFavori ? 'fas' : 'far' ?> fa-star"></i> 
                                                                        <?= $isFavori ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>
                                                                    </button>
                                                                </form>
                                                                <form action="../back/supprimer_candidature.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette candidature ?');" class="mb-0">
                                                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                                    <button type="submit" class="btn btn-delete btn-outline-danger">
                                                                        <i class="fas fa-trash-alt"></i> Supprimer
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <div class="col-12 text-center py-5">
                                                <i class="fas fa-user-tie fa-4x text-gray-300 mb-4"></i>
                                                <h4 class="text-gray-500">Aucune candidature trouvée</h4>
                                                <p class="text-muted">Les nouvelles candidatures apparaîtront ici</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
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
    
    <!-- Scroll to Top Button -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    
    <!-- Logout Modal -->
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
    
    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Exporter les candidatures</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Sélectionnez les candidatures à exporter.</p>
                    <form id="exportForm" action="preview_export.php" method="GET">
                        <div class="form-group">
                            <label><input type="checkbox" id="selectAll"> Tout sélectionner</label>
                        </div>
                        <div id="candidatureList">
                            <!-- Les candidatures seront insérées ici via JavaScript -->
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary" id="exportButton" disabled>Exporter</button>
                        </div>
                    </form>
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
        $(document).ready(function() {
            // Gestion de la modale d'exportation
            $('#exportModal').on('show.bs.modal', function() {
                let candidatureList = $('#candidatureList');
                candidatureList.empty();

                $('.select-candidature').each(function() {
                    let id = $(this).val();
                    let name = $(this).closest('.card-body').find('.candidate-name').text();
                    let poste = $(this).closest('.card-body').find('.candidate-position').text();
                    let isChecked = $(this).is(':checked') ? 'checked' : '';

                    candidatureList.append(`
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="selected_candidatures[]" value="${id}" ${isChecked}>
                                ${name} - ${poste}
                            </label>
                        </div>
                    `);
                });

                updateExportButton();
            });

            // Soumission du formulaire d'exportation
            $('#exportForm').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                window.location.href = 'preview_export.php?' + form.serialize();
            });

            // Sélectionner tout/déselectionner tout
            $('#selectAll').on('change', function() {
                let isChecked = $(this).is(':checked');
                $('#candidatureList input[type="checkbox"]').prop('checked', isChecked);
                $('.select-candidature').prop('checked', isChecked);
                updateExportButton();
            });

            // Synchroniser les cases à cocher
            $('#candidatureList').on('change', 'input[type="checkbox"]', function() {
                let id = $(this).val();
                $(`.select-candidature[value="${id}"]`).prop('checked', $(this).is(':checked'));
                updateExportButton();
            });

            $('.select-candidature').on('change', function() {
                let id = $(this).val();
                $(`#candidatureList input[value="${id}"]`).prop('checked', $(this).is(':checked'));
                updateExportButton();
            });

            // Mettre à jour le bouton Exporter
            function updateExportButton() {
                let checkedCount = $('#candidatureList input[type="checkbox"]:checked').length;
                $('#exportButton').prop('disabled', checkedCount === 0);
                $('#selectAll').prop('checked', checkedCount === $('.select-candidature').length && checkedCount > 0);
            }

            // Fonction pour effectuer la recherche AJAX
            function performSearch() {
                let data = $('#search-form-topbar').serialize() + '&' + $('#search-form-filters').serialize();
                $.ajax({
                    url: 'candidature_admin.php',
                    type: 'GET',
                    data: data,
                    success: function(response) {
                        $('#results-container').html($(response).find('#results-container').html());
                        // Mettre à jour le compteur de candidatures
                        let total = $('#results-container .col-lg-6.mb-4').length;
                        if ($('#results-container .no-results').length) {
                            total = 0;
                        }
                        $('#total-candidatures').text('Total: ' + total);
                    },
                    error: function(xhr, status, error) {
                        console.error("Erreur AJAX: " + status + " - " + error);
                    }
                });
            }

            // Recherche AJAX pour le formulaire de la topbar
            $('#search-form-topbar input').on('input', function() {
                performSearch();
            });

            // Recherche AJAX pour le formulaire des filtres
            $('#search-form-filters input').on('change', function() {
                performSearch();
            });

            // Recherche pour le formulaire mobile
            $('#search-form-mobile').on('submit', function(e) {
                e.preventDefault();
                let data = $(this).serialize();
                $.ajax({
                    url: 'candidature_admin.php',
                    type: 'GET',
                    data: data,
                    success: function(response) {
                        $('#results-container').html($(response).find('#results-container').html());
                        // Mettre à jour le compteur de candidatures
                        let total = $('#results-container .col-lg-6.mb-4').length;
                        if ($('#results-container .no-results').length) {
                            total = 0;
                        }
                        $('#total-candidatures').text('Total: ' + total);
                        $('#searchDropdown').dropdown('hide');
                    },
                    error: function(xhr, status, error) {
                        console.error("Erreur AJAX: " + status + " - " + error);
                    }
                });
            });

            // Tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
</body>
</html>
