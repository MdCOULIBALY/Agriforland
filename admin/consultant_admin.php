<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'includes/db.php';

// Démarrer la session
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Générer un jeton CSRF si non défini
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Définir l'encodage UTF-8 pour la connexion MySQL
if (!mysqli_set_charset($conn, "utf8mb4")) {
    die("Erreur lors de la définition de l'encodage UTF-8 : " . mysqli_error($conn));
}

// Paramètres de pagination
$per_page = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $per_page;

// Validation des paramètres de recherche
$search_text = isset($_GET['search_text']) ? trim($_GET['search_text']) : '';
$search_date_start = isset($_GET['date_start']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date_start']) ? $_GET['date_start'] : '';
$search_date_end = isset($_GET['date_end']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date_end']) ? $_GET['date_end'] : '';
$search_favoris = isset($_GET['favoris']) && $_GET['favoris'] == '1' ? 1 : 0;
$search_give_trainings = isset($_GET['give_trainings']) && in_array($_GET['give_trainings'], ['yes', 'no']) ? $_GET['give_trainings'] : '';
$allowed_sort_columns = ['name', 'created_at', 'specialty'];
$sort_by = isset($_GET['sort_by']) && in_array($_GET['sort_by'], $allowed_sort_columns) ? $_GET['sort_by'] : 'created_at';
$sort_order = isset($_GET['sort_order']) && $_GET['sort_order'] === 'ASC' ? 'ASC' : 'DESC';

// Nettoyer la recherche (normalisation Unicode et espaces)
if ($search_text) {
    if (function_exists('normalizer_normalize')) {
        $search_text = normalizer_normalize($search_text, Normalizer::FORM_D);
    }
    // Supprimer les accents pour la recherche insensible
    $search_text = str_replace(
        ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'î', 'ï', 'ô', 'ö', 'ù', 'û', 'ü', 'ç'],
        ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'i', 'i', 'o', 'o', 'u', 'u', 'u', 'c'],
        strtolower($search_text)
    );
    $search_text = preg_replace('/\s+/', ' ', $search_text);
}
$search_like = "%$search_text%";
$search_words = array_filter(explode(' ', $search_text));

// Construire la requête SQL avec prepared statement
$conditions = [];
$params = [];
$types = '';

try {
    if (!empty($search_text)) {
        $name_conditions = [];
        foreach ($search_words as $word) {
            $name_conditions[] = "LOWER(name) LIKE ?";
            $params[] = "%$word%";
            $types .= 's';
        }
        // Ajouter LOWER pour ignorer la casse et gérer les accents dans languages
        $conditions[] = "(" . implode(" AND ", $name_conditions) . " OR LOWER(email) LIKE ? OR phone LIKE ? OR LOWER(specialty) LIKE ? OR LOWER(degree) LIKE ? OR LOWER(experience) LIKE ? OR LOWER(contract_type) LIKE ? OR LOWER(availability) LIKE ? OR LOWER(languages) LIKE ? OR LOWER(degree_institution) LIKE ?)";
        for ($i = 0; $i < 9; $i++) {
            $params[] = $search_like;
            $types .= 's';
        }
    }
    if (!empty($search_date_start)) {
        $conditions[] = "created_at >= ?";
        $params[] = "$search_date_start 00:00:00";
        $types .= 's';
    }
    if (!empty($search_date_end)) {
        $conditions[] = "created_at <= ?";
        $params[] = "$search_date_end 23:59:59";
        $types .= 's';
    }
    if ($search_favoris) {
        $conditions[] = "favori = ?";
        $params[] = 1;
        $types .= 'i';
    }
    if (!empty($search_give_trainings)) {
        $conditions[] = "give_trainings = ?";
        $params[] = $search_give_trainings;
        $types .= 's';
    }

    // Requête principale
    $query = "SELECT id, name, email, phone, specialty, specialty_other, degree, degree_institution, experience, contract_type, availability, languages, give_trainings, training_modules, cv_path, diploma_path, created_at, consultée, favori FROM consultants";
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    $query .= " ORDER BY $sort_by $sort_order LIMIT ? OFFSET ?";
    $params[] = $per_page;
    $params[] = $offset;
    $types .= 'ii';

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Erreur de préparation SQL : " . $conn->error);
    }
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) {
        throw new Exception("Erreur d'exécution SQL : " . $stmt->error);
    }
    $result = $stmt->get_result();
    $stmt->close();

    // Compter le total pour la pagination
    $count_query = "SELECT COUNT(*) as total FROM consultants";
    if (!empty($conditions)) {
        $count_query .= " WHERE " . implode(" AND ", $conditions);
    }
    $count_stmt = $conn->prepare($count_query);
    if (!$count_stmt) {
        throw new Exception("Erreur de préparation SQL (count) : " . $conn->error);
    }
    if (!empty($params)) {
        $count_params = array_slice($params, 0, count($params) - 2); // Exclure LIMIT/OFFSET
        $count_types = substr($types, 0, strlen($types) - 2);
        if ($count_types) {
            $count_stmt->bind_param($count_types, ...$count_params);
        }
    }
    if (!$count_stmt->execute()) {
        throw new Exception("Erreur d'exécution SQL (count) : " . $count_stmt->error);
    }
    $total_consultants = $count_stmt->get_result()->fetch_assoc()['total'];
    $count_stmt->close();
    $total_pages = ceil($total_consultants / $per_page);

    // Marquer comme consultée/non lue
    if (isset($_POST['toggle_id'])) {
        $id = intval($_POST['toggle_id']);
        $stmt = $conn->prepare("SELECT consultée FROM consultants WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Erreur de préparation SQL (toggle_id) : " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception("Erreur d'exécution SQL (toggle_id) : " . $stmt->error);
        }
        $row = $stmt->get_result()->fetch_assoc();
        if ($row) {
            $newStatus = $row['consultée'] == 1 ? 0 : 1;
            $updateStmt = $conn->prepare("UPDATE consultants SET consultée = ? WHERE id = ?");
            if (!$updateStmt) {
                throw new Exception("Erreur de préparation SQL (update toggle_id) : " . $conn->error);
            }
            $updateStmt->bind_param("ii", $newStatus, $id);
            if ($updateStmt->execute()) {
                $_SESSION['success_message'] = $newStatus ? "Consultant marqué comme lu." : "Consultant marqué comme non lu.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de la mise à jour du statut.";
            }
            $updateStmt->close();
        }
        $stmt->close();
        header("Location: consultant_admin.php?page=$page");
        exit();
    }

    // Marquer comme favori/non favori
    if (isset($_POST['toggle_favori'])) {
        $id = intval($_POST['toggle_favori']);
        $stmt = $conn->prepare("SELECT favori FROM consultants WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Erreur de préparation SQL (toggle_favori) : " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            throw new Exception("Erreur d'exécution SQL (toggle_favori) : " . $stmt->error);
        }
        $row = $stmt->get_result()->fetch_assoc();
        if ($row) {
            $newStatus = $row['favori'] == 1 ? 0 : 1;
            $updateStmt = $conn->prepare("UPDATE consultants SET favori = ? WHERE id = ?");
            if (!$updateStmt) {
                throw new Exception("Erreur de préparation SQL (update toggle_favori) : " . $conn->error);
            }
            $updateStmt->bind_param("ii", $newStatus, $id);
            if ($updateStmt->execute()) {
                $_SESSION['success_message'] = $newStatus ? "Consultant ajouté aux favoris." : "Consultant retiré des favoris.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de la mise à jour du statut de favori.";
            }
            $updateStmt->close();
        }
        $stmt->close();
        header("Location: consultant_admin.php?page=$page");
        exit();
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    if (isset($count_stmt) && $count_stmt instanceof mysqli_stmt) {
        $count_stmt->close();
    }
    if (isset($updateStmt) && $updateStmt instanceof mysqli_stmt) {
        $updateStmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Gestion des consultants AGRIFORLAND">
    <meta name="author" content="">
    <title>AGRIFORLAND - Consultants</title>
    <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
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
        .card-consultant {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-left: 3px solid var(--success-color);
            margin-bottom: 10px;
        }
        .card-consultant:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .card-consultant.read {
            border-left-color: #d1d3e2;
        }
        .card-consultant .card-body {
            padding: 12px;
        }
        .badge-new {
            background-color: var(--success-color);
            font-size: 0.65rem;
            font-weight: 500;
        }
        .consultant-name {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.95rem;
            margin-bottom: 2px;
        }
        .consultant-specialty {
            color: #6c757d;
            font-size: 0.75rem;
            margin-bottom: 4px;
        }
        .contact-info {
            font-size: 0.8rem;
            line-height: 1.3;
        }
        .contact-info i {
            width: 16px;
            text-align: center;
            color: var(--success-color);
        }
        .consultant-details p {
            font-size: 0.8rem;
            margin-bottom: 2px;
        }
        .btn-file {
            border-radius: 4px;
            padding: 0.2rem 0.5rem;
            font-size: 0.75rem;
        }
        .btn-file i {
            margin-right: 3px;
        }
        .btn-mark, .btn-favori, .btn-delete {
            border-radius: 4px;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
        }
        .btn-favori i {
            color: var(--favori-color);
        }
        .filter-bar {
            background-color: #fff;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .filter-bar .form-control {
            border-radius: 6px;
            border: 1px solid #d1d3e2;
            font-size: 0.85rem;
        }
        .filter-bar .input-group-text {
            background-color: #f8f9fc;
            border: 1px solid #d1d3e2;
            border-radius: 6px 0 0 6px;
            font-size: 0.85rem;
        }
        .filter-bar .form-check {
            margin-top: 8px;
        }
        .topbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .topbar .navbar-search .form-control {
            border-radius: 6px;
            border: 1px solid #d1d3e2;
            font-size: 0.85rem;
        }
        .sidebar-brand-text {
            font-weight: 600;
        }
        .results-container {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
            padding-right: 8px;
        }
        .results-container::-webkit-scrollbar {
            width: 5px;
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
                margin-bottom: 8px;
            }
            .filter-bar .form-check {
                margin-top: 0;
            }
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">AGRIFORLAND <sup><img src="../images/favicon.ico" width="50" alt="favicon"></sup></div>
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
                        <a class="collapse-item" href="candidature_admin.php">Candidature</a>
                        <a class="collapse-item active" href="consultant_admin.php">Consultants</a>
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
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <form class="form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search search-form" id="search-form-topbar">
                        <div class="input-group">
                            <input type="text" name="search_text" id="search-text" class="form-control bg-light border-0 small"
                                   placeholder="Nom, diplôme, institution, expérience, contrat, disponibilité, langues..."
                                   value="<?= htmlspecialchars($search_text, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>
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
                                               placeholder="Nom, diplôme, institution, expérience, contrat, disponibilité, langues..."
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
                                    <div class="form-group">
                                        <label for="give-trainings-mobile">Formations</label>
                                        <select name="give_trainings" id="give-trainings-mobile" class="form-control bg-light border-0 small">
                                            <option value="" <?= $search_give_trainings == '' ? 'selected' : '' ?>>Tous</option>
                                            <option value="yes" <?= $search_give_trainings == 'yes' ? 'selected' : '' ?>>Oui</option>
                                            <option value="no" <?= $search_give_trainings == 'no' ? 'selected' : '' ?>>Non</option>
                                        </select>
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
                <div class="container-fluid">
                    <div class="filter-bar">
                        <form class="search-form" id="search-form-filters">
                            <div class="row align-items-end">
                                <div class="col-md-3 col-12">
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Du</span>
                                        </div>
                                        <input type="date" name="date_start" id="date-start" class="form-control"
                                               value="<?= htmlspecialchars($search_date_start, ENT_QUOTES, 'UTF-8') ?>">
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Au</span>
                                        </div>
                                        <input type="date" name="date_end" id="date-end" class="form-control"
                                               value="<?= htmlspecialchars($search_date_end, ENT_QUOTES, 'UTF-8') ?>">
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Formations</span>
                                        </div>
                                        <select name="give_trainings" id="give-trainings" class="form-control">
                                            <option value="" <?= $search_give_trainings == '' ? 'selected' : '' ?>>Tous</option>
                                            <option value="yes" <?= $search_give_trainings == 'yes' ? 'selected' : '' ?>>Oui</option>
                                            <option value="no" <?= $search_give_trainings == 'no' ? 'selected' : '' ?>>Non</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-12">
                                    <div class="form-check">
                                        <input type="checkbox" name="favoris" id="favoris" value="1" class="form-check-input"
                                               <?= $search_favoris ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="favoris">Favoris uniquement</label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
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
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Gestion des Consultants</h1>
                        <div>
                            <span class="badge badge-primary mr-2" id="total-consultants">
                                Total: <?= $total_consultants ?>
                            </span>
                            <button type="button" class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#exportModal">
                                <i class="fas fa-download fa-sm text-white-50"></i> Exporter
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Derniers Consultants</h6>
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
                                    <div class="d-flex justify-content-end mb-3">
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-toggle="dropdown">
                                                Trier par : <?= ucfirst($sort_by) ?> (<?= $sort_order ?>)
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="sortDropdown">
                                                <a class="dropdown-item" href="?page=<?= $page ?>&search_text=<?= urlencode($search_text) ?>&date_start=<?= urlencode($search_date_start) ?>&date_end=<?= urlencode($search_date_end) ?>&favoris=<?= $search_favoris ?>&give_trainings=<?= urlencode($search_give_trainings) ?>&sort_by=name&sort_order=ASC">Nom (ASC)</a>
                                                <a class="dropdown-item" href="?page=<?= $page ?>&search_text=<?= urlencode($search_text) ?>&date_start=<?= urlencode($search_date_start) ?>&date_end=<?= urlencode($search_date_end) ?>&favoris=<?= $search_favoris ?>&give_trainings=<?= urlencode($search_give_trainings) ?>&sort_by=name&sort_order=DESC">Nom (DESC)</a>
                                                <a class="dropdown-item" href="?page=<?= $page ?>&search_text=<?= urlencode($search_text) ?>&date_start=<?= urlencode($search_date_start) ?>&date_end=<?= urlencode($search_date_end) ?>&favoris=<?= $search_favoris ?>&give_trainings=<?= urlencode($search_give_trainings) ?>&sort_by=created_at&sort_order=ASC">Date (ASC)</a>
                                                <a class="dropdown-item" href="?page=<?= $page ?>&search_text=<?= urlencode($search_text) ?>&date_start=<?= urlencode($search_date_start) ?>&date_end=<?= urlencode($search_date_end) ?>&favoris=<?= $search_favoris ?>&give_trainings=<?= urlencode($search_give_trainings) ?>&sort_by=created_at&sort_order=DESC">Date (DESC)</a>
                                                <a class="dropdown-item" href="?page=<?= $page ?>&search_text=<?= urlencode($search_text) ?>&date_start=<?= urlencode($search_date_start) ?>&date_end=<?= urlencode($search_date_end) ?>&favoris=<?= $search_favoris ?>&give_trainings=<?= urlencode($search_give_trainings) ?>&sort_by=specialty&sort_order=ASC">Spécialité (ASC)</a>
                                                <a class="dropdown-item" href="?page=<?= $page ?>&search_text=<?= urlencode($search_text) ?>&date_start=<?= urlencode($search_date_start) ?>&date_end=<?= urlencode($search_date_end) ?>&favoris=<?= $search_favoris ?>&give_trainings=<?= urlencode($search_give_trainings) ?>&sort_by=specialty&sort_order=DESC">Spécialité (DESC)</a>
                                            </div>
                                        </div>
                                    </div>
                                    <form id="bulk-delete" action="../back/supprimer_consultant.php" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                        <div class="row results-container" id="results-container">
                                            <?php if (isset($result) && $result->num_rows > 0): ?>
                                                <?php while ($row = $result->fetch_assoc()): ?>
                                                    <?php
                                                        $specialty = !empty($row['specialty_other']) ? htmlspecialchars($row['specialty_other'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($row['specialty'], ENT_QUOTES, 'UTF-8');
                                                        $isNew = !isset($row['consultée']) || $row['consultée'] == 0;
                                                        $isFavori = isset($row['favori']) && $row['favori'] == 1;
                                                        $cardClass = $isNew ? '' : 'read';
                                                        $cv_path = !empty($row['cv_path']) && file_exists("../" . $row['cv_path']) ? htmlspecialchars($row['cv_path'], ENT_QUOTES, 'UTF-8') : '#';
                                                        $diploma_path = !empty($row['diploma_path']) && file_exists("../" . $row['diploma_path']) ? htmlspecialchars($row['diploma_path'], ENT_QUOTES, 'UTF-8') : '#';
                                                    ?>
                                                    <div class="col-lg-4 col-md-6 mb-2 consultant-card" data-id="<?= $row['id'] ?>">
                                                        <div class="card card-consultant <?= $cardClass ?> h-100">
                                                            <div class="card-body">
                                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                                    <div>
                                                                        <input type="checkbox" class="select-consultant" name="ids[]" value="<?= $row['id'] ?>">
                                                                        <h5 class="consultant-name">
                                                                            <?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?>
                                                                            <?php if ($isFavori): ?>
                                                                                <i class="fas fa-star text-warning" title="Favori"></i>
                                                                            <?php endif; ?>
                                                                        </h5>
                                                                        <p class="consultant-specialty"><?= $specialty ?></p>
                                                                    </div>
                                                                    <?php if ($isNew): ?>
                                                                        <span class="badge badge-new">Nouveau</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="contact-info mb-2">
                                                                    <p class="mb-1">
                                                                        <i class="fas fa-envelope"></i>
                                                                        <a href="mailto:<?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?>" class="text-dark"><?= htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') ?></a>
                                                                    </p>
                                                                    <p class="mb-1">
                                                                        <i class="fas fa-phone"></i>
                                                                        <?= htmlspecialchars($row['phone'], ENT_QUOTES, 'UTF-8') ?>
                                                                    </p>
                                                                    <p class="mb-0">
                                                                        <i class="far fa-clock"></i>
                                                                        Inscrit le <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                                                                    </p>
                                                                </div>
                                                                <div class="consultant-details mb-2">
                                                                    <p><strong>Diplôme :</strong> <?= htmlspecialchars($row['degree'], ENT_QUOTES, 'UTF-8') ?></p>
                                                                    <p><strong>Intitulé du diplôme :</strong> <?= htmlspecialchars($row['degree_institution'], ENT_QUOTES, 'UTF-8') ?></p>
                                                                    <p><strong>Expérience :</strong> <?= htmlspecialchars($row['experience'], ENT_QUOTES, 'UTF-8') ?> ans</p>
                                                                    <p><strong>Contrat :</strong> <?= htmlspecialchars($row['contract_type'], ENT_QUOTES, 'UTF-8') ?></p>
                                                                    <p><strong>Dispo :</strong> <?= date('d/m/Y', strtotime($row['availability'])) ?></p>
                                                                    <p><strong>Langues :</strong> <?= htmlspecialchars($row['languages'], ENT_QUOTES, 'UTF-8') ?></p>
                                                                    <p><strong>Formations :</strong> <?= htmlspecialchars($row['give_trainings'] == 'yes' ? 'Oui' : 'Non', ENT_QUOTES, 'UTF-8') ?></p>
                                                                    <?php if ($row['give_trainings'] == 'yes' && !empty($row['training_modules'])): ?>
                                                                        <p><strong>Modules de formation :</strong> <?= htmlspecialchars($row['training_modules'], ENT_QUOTES, 'UTF-8') ?></p>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="d-flex flex-wrap gap-2 mb-2">
                                                                    <a href="../<?= $cv_path ?>" target="_blank" class="btn btn-file btn-outline-success btn-sm <?= $cv_path == '#' ? 'disabled' : '' ?>">
                                                                        <i class="fas fa-file-pdf"></i> CV
                                                                    </a>
                                                                    <a href="../<?= $diploma_path ?>" target="_blank" class="btn btn-file btn-outline-primary btn-sm <?= $diploma_path == '#' ? 'disabled' : '' ?>">
                                                                        <i class="fas fa-graduation-cap"></i> Diplôme
                                                                    </a>
                                                                </div>
                                                                <hr class="my-2">
                                                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                                                    <form method="POST" action="consultant_admin.php" class="mb-0 mr-1">
                                                                        <input type="hidden" name="toggle_id" value="<?= $row['id'] ?>">
                                                                        <button type="submit" class="btn btn-mark <?= $isNew ? 'btn-success' : 'btn-outline-secondary' ?>">
                                                                            <?= $isNew ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-eye-slash"></i>' ?>
                                                                        </button>
                                                                    </form>
                                                                    <form method="POST" action="consultant_admin.php" class="mb-0 mr-1">
                                                                        <input type="hidden" name="toggle_favori" value="<?= $row['id'] ?>">
                                                                        <button type="submit" class="btn btn-favori <?= $isFavori ? 'btn-warning' : 'btn-outline-warning' ?>">
                                                                            <i class="<?= $isFavori ? 'fas' : 'far' ?> fa-star"></i>
                                                                        </button>
                                                                    </form>
                                                                    <form action="../back/supprimer_consultant.php" method="POST" class="delete-form mb-0">
                                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                                        <button type="submit" class="btn btn-delete btn-outline-danger">
                                                                            <i class="fas fa-trash-alt"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <div class="col-12 text-center py-5 no-results">
                                                    <i class="fas fa-user-tie fa-4x text-gray-300 mb-4"></i>
                                                    <h4 class="text-gray-500">Aucun consultant trouvé</h4>
                                                    <p class="text-muted">Les nouveaux consultants apparaîtront ici</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <button type="submit" class="btn btn-danger btn-sm mt-3 d-none" id="bulk-delete-btn">Supprimer tout</button>
                                    </form>
                                    <nav aria-label="Pagination">
                                        <ul class="pagination justify-content-center mt-4">
                                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                                <a class="page-link" href="?page=<?= $page - 1 ?>&search_text=<?= urlencode($search_text) ?>&date_start=<?= urlencode($search_date_start) ?>&date_end=<?= urlencode($search_date_end) ?>&favoris=<?= $search_favoris ?>&give_trainings=<?= urlencode($search_give_trainings) ?>&sort_by=<?= $sort_by ?>&sort_order=<?= $sort_order ?>" aria-label="Previous">
                                                    <span aria-hidden="true">«</span>
                                                </a>
                                            </li>
                                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                    <a class="page-link" href="?page=<?= $i ?>&search_text=<?= urlencode($search_text) ?>&date_start=<?= urlencode($search_date_start) ?>&date_end=<?= urlencode($search_date_end) ?>&favoris=<?= $search_favoris ?>&give_trainings=<?= urlencode($search_give_trainings) ?>&sort_by=<?= $sort_by ?>&sort_order=<?= $sort_order ?>"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>
                                            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                                <a class="page-link" href="?page=<?= $page + 1 ?>&search_text=<?= urlencode($search_text) ?>&date_start=<?= urlencode($search_date_start) ?>&date_end=<?= urlencode($search_date_end) ?>&favoris=<?= $search_favoris ?>&give_trainings=<?= urlencode($search_give_trainings) ?>&sort_by=<?= $sort_by ?>&sort_order=<?= $sort_order ?>" aria-label="Next">
                                                    <span aria-hidden="true">»</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Tous droits réservés © AGRIFORLAND <?= date('Y') ?></span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
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
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Exporter les consultants</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Sélectionnez les consultants à exporter.</p>
                    <form id="exportForm" action="preview_export_consultants.php" method="GET">
                        <div class="form-group">
                            <label><input type="checkbox" id="selectAll"> Tout sélectionner</label>
                        </div>
                        <div id="consultantList"></div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary" id="exportButton" disabled>Exporter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Gestion du modal d'exportation
            $('#exportModal').on('show.bs.modal', function() {
                let consultantList = $('#consultantList');
                consultantList.empty();
                $('.select-consultant').each(function() {
                    let id = $(this).val();
                    let name = $(this).closest('.card-body').find('.consultant-name').text().trim();
                    let specialty = $(this).closest('.card-body').find('.consultant-specialty').text();
                    let isChecked = $(this).is(':checked') ? 'checked' : '';
                    consultantList.append(`
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="selected_consultants[]" value="${id}" ${isChecked}>
                                ${name} - ${specialty}
                            </label>
                        </div>
                    `);
                });
                updateExportButton();
                updateBulkDeleteButton();
            });

            // Gestion de la soumission du formulaire d'exportation
            $('#exportForm').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                window.location.href = 'preview_export_consultants.php?' + form.serialize();
            });

            // Gestion de la case "Tout sélectionner"
            $('#selectAll').on('change', function() {
                let isChecked = $(this).is(':checked');
                $('#consultantList input[type="checkbox"]').prop('checked', isChecked);
                $('.select-consultant').prop('checked', isChecked);
                updateExportButton();
                updateBulkDeleteButton();
            });

            // Synchronisation des cases à cocher dans le modal
            $('#consultantList').on('change', 'input[type="checkbox"]', function() {
                let id = $(this).val();
                $(`.select-consultant[value="${id}"]`).prop('checked', $(this).is(':checked'));
                updateExportButton();
                updateBulkDeleteButton();
            });

            // Synchronisation des cases à cocher dans la liste principale
            $('.select-consultant').on('change', function() {
                let id = $(this).val();
                $(`#consultantList input[value="${id}"]`).prop('checked', $(this).is(':checked'));
                updateExportButton();
                updateBulkDeleteButton();
            });

            // Activer/désactiver le bouton Exporter
            function updateExportButton() {
                let checkedCount = $('#consultantList input[type="checkbox"]:checked').length;
                $('#exportButton').prop('disabled', checkedCount === 0);
                $('#selectAll').prop('checked', checkedCount === $('.select-consultant').length && checkedCount > 0);
            }

            // Afficher/masquer le bouton "Supprimer tout"
            function updateBulkDeleteButton() {
                let checkedCount = $('.select-consultant:checked').length;
                if (checkedCount > 1) {
                    $('#bulk-delete-btn').removeClass('d-none').prop('disabled', false);
                } else {
                    $('#bulk-delete-btn').addClass('d-none').prop('disabled', true);
                }
            }

            $('.select-consultant').on('change', updateBulkDeleteButton);
            updateBulkDeleteButton();

            // Suppression unique via AJAX
            $('.delete-form').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let name = form.closest('.card-body').find('.consultant-name').text().trim();
                if (!confirm(`Êtes-vous sûr de vouloir supprimer le consultant "${name}" ?`)) {
                    return;
                }
                let button = form.find('button');
                button.prop('disabled', true);
                button.html('<i class="fas fa-spinner fa-spin"></i>');

                $.ajax({
                    url: '../back/supprimer_consultant.php',
                    type: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function(response) {
                        if (response.success) {
                            form.closest('.consultant-card').fadeOut(300, function() {
                                $(this).remove();
                                updateBulkDeleteButton();
                                window.location.href = '../admin/consultant_admin.php';
                            });
                        } else {
                            alert(response.message || 'Erreur lors de la suppression.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur AJAX :', status, error);
                        alert('Erreur réseau. Veuillez réessayer.');
                    },
                    complete: function() {
                        button.prop('disabled', false);
                        button.html('<i class="fas fa-trash-alt"></i>');
                    }
                });
            });

            // Suppression multiple via AJAX
            $('#bulk-delete').on('submit', function(e) {
                e.preventDefault();
                let checkedCount = $('.select-consultant:checked').length;
                if (!confirm(`Voulez-vous vraiment supprimer ${checkedCount} consultant(s) ?`)) {
                    return;
                }
                let form = $(this);
                let button = form.find('#bulk-delete-btn');
                button.prop('disabled', true);
                button.html('<i class="fas fa-spinner fa-spin"></i> Suppression...');

                $.ajax({
                    url: '../back/supprimer_consultant.php',
                    type: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function(response) {
                        if (response.success) {
                            $('.select-consultant:checked').closest('.consultant-card').fadeOut(300, function() {
                                $(this).remove();
                                updateBulkDeleteButton();
                                window.location.href = '../admin/consultant_admin.php';
                            });
                        } else {
                            alert(response.message || 'Erreur lors de la suppression.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur AJAX :', status, error);
                        alert('Erreur réseau. Veuillez réessayer.');
                    },
                    complete: function() {
                        button.prop('disabled', false);
                        button.html('Supprimer tout');
                    }
                });
            });

            // Fonction de recherche AJAX
            function performSearch() {
                let data = $('#search-form-topbar').serialize() + '&' + $('#search-form-filters').serialize() + '&page=<?= $page ?>&sort_by=<?= $sort_by ?>&sort_order=<?= $sort_order ?>';
                $.ajax({
                    url: 'consultant_admin.php',
                    type: 'GET',
                    data: data,
                    dataType: 'html',
                    success: function(response) {
                        try {
                            let $response = $(response);
                            $('#results-container').html($response.find('#results-container').html());
                            let total = $response.find('#total-consultants').text().match(/\d+/)[0] || 0;
                            $('#total-consultants').text('Total: ' + total);
                            let pagination = $response.find('.pagination').html();
                            if (pagination) {
                                $('.pagination').html(pagination);
                            }
                            // Réattacher les gestionnaires d'événements
                            $('.select-consultant').on('change', updateBulkDeleteButton);
                            $('.delete-form').on('submit', function(e) {
                                e.preventDefault();
                                let form = $(this);
                                let name = form.closest('.card-body').find('.consultant-name').text().trim();
                                if (!confirm(`Êtes-vous sûr de vouloir supprimer le consultant "${name}" ?`)) {
                                    return;
                                }
                                let button = form.find('button');
                                button.prop('disabled', true);
                                button.html('<i class="fas fa-spinner fa-spin"></i>');

                                $.ajax({
                                    url: '../back/supprimer_consultant.php',
                                    type: 'POST',
                                    data: form.serialize(),
                                    dataType: 'json',
                                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                                    success: function(response) {
                                        if (response.success) {
                                            form.closest('.consultant-card').fadeOut(300, function() {
                                                $(this).remove();
                                                updateBulkDeleteButton();
                                                window.location.href = '../admin/consultant_admin.php';
                                            });
                                        } else {
                                            alert(response.message || 'Erreur lors de la suppression.');
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Erreur AJAX :', status, error);
                                        alert('Erreur réseau. Veuillez réessayer.');
                                    },
                                    complete: function() {
                                        button.prop('disabled', false);
                                        button.html('<i class="fas fa-trash-alt"></i>');
                                    }
                                });
                            });
                            updateBulkDeleteButton();
                        } catch (e) {
                            console.error("Erreur de parsing AJAX : ", e);
                            $('#results-container').html('<div class="col-12 text-center py-5"><p class="text-danger">Erreur lors du chargement des résultats.</p></div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Erreur AJAX : " + status + " - " + error);
                        $('#results-container').html('<div class="col-12 text-center py-5"><p class="text-danger">Erreur serveur lors de la recherche.</p></div>');
                    }
                });
            }

            // Déclencheur pour la recherche dans la barre supérieure
            $('#search-form-topbar input').on('input', function() {
                clearTimeout(window.searchTimeout);
                window.searchTimeout = setTimeout(performSearch, 300); // Debounce
            });

            // Déclencheur pour les filtres
            $('#search-form-filters input, #search-form-filters select').on('change', function() {
                performSearch();
            });

            // Gestion du formulaire de recherche mobile
            $('#search-form-mobile').on('submit', function(e) {
                e.preventDefault();
                let data = $(this).serialize() + '&sort_by=<?= $sort_by ?>&sort_order=<?= $sort_order ?>';
                $.ajax({
                    url: 'consultant_admin.php',
                    type: 'GET',
                    data: data,
                    dataType: 'html',
                    success: function(response) {
                        try {
                            let $response = $(response);
                            $('#results-container').html($response.find('#results-container').html());
                            let total = $response.find('#total-consultants').text().match(/\d+/)[0] || 0;
                            $('#total-consultants').text('Total: ' + total);
                            let pagination = $response.find('.pagination').html();
                            if (pagination) {
                                $('.pagination').html(pagination);
                            }
                            $('#searchDropdown').dropdown('hide');
                            // Réattacher les gestionnaires d'événements
                            $('.select-consultant').on('change', updateBulkDeleteButton);
                            $('.delete-form').on('submit', function(e) {
                                e.preventDefault();
                                let form = $(this);
                                let name = form.closest('.card-body').find('.consultant-name').text().trim();
                                if (!confirm(`Êtes-vous sûr de vouloir supprimer le consultant "${name}" ?`)) {
                                    return;
                                }
                                let button = form.find('button');
                                button.prop('disabled', true);
                                button.html('<i class="fas fa-spinner fa-spin"></i>');

                                $.ajax({
                                    url: '../back/supprimer_consultant.php',
                                    type: 'POST',
                                    data: form.serialize(),
                                    dataType: 'json',
                                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                                    success: function(response) {
                                        if (response.success) {
                                            form.closest('.consultant-card').fadeOut(300, function() {
                                                $(this).remove();
                                                updateBulkDeleteButton();
                                                window.location.href = '../admin/consultant_admin.php';
                                            });
                                        } else {
                                            alert(response.message || 'Erreur lors de la suppression.');
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Erreur AJAX :', status, error);
                                        alert('Erreur réseau. Veuillez réessayer.');
                                    },
                                    complete: function() {
                                        button.prop('disabled', false);
                                        button.html('<i class="fas fa-trash-alt"></i>');
                                    }
                                });
                            });
                            updateBulkDeleteButton();
                        } catch (e) {
                            console.error("Erreur de parsing AJAX : ", e);
                            $('#results-container').html('<div class="col-12 text-center py-5"><p class="text-danger">Erreur lors du chargement des résultats.</p></div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Erreur AJAX : " + status + " - " + error);
                        $('#results-container').html('<div class="col-12 text-center py-5"><p class="text-danger">Erreur serveur lors de la recherche.</p></div>');
                    }
                });
            });

            // Activer les tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
</body>
</html>
<?php
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>