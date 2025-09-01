<!-- edit_actualite.php -->
<?php
include('includes/db.php');
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Sécurisation de l'ID
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: actualite.php");
    exit();
}

$sql = "SELECT * FROM a_la_une WHERE id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = mysqli_real_escape_string($conn, $_POST['titre']);
    $resume = mysqli_real_escape_string($conn, $_POST['resume']);
    $contenu = mysqli_real_escape_string($conn, $_POST['contenu']);
    $lien = mysqli_real_escape_string($conn, $_POST['lien']);
    $image = $row['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['image']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Vérification du type de fichier
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image = $target_file;
                // Supprimer l’ancienne image si différente
                if ($row['image'] && $row['image'] != $image && file_exists($row['image'])) {
                    unlink($row['image']);
                }
            }
        }
    }

    $update_sql = "UPDATE a_la_une 
                   SET titre = '$titre', resume = '$resume', contenu = '$contenu', lien = '$lien', image = '$image' 
                   WHERE id = $id";

    if ($conn->query($update_sql) === TRUE) {
        $_SESSION['success'] = "Actualité modifiée avec succès";
        header('Location: actualite.php');
        exit();
    } else {
        $_SESSION['error'] = "Erreur lors de la modification: " . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Modifier une actualité AGRIFORDLAND">
    <meta name="author" content="">
    <title>AGRIFORDLAND - Modifier Actualité</title>
    
    <!-- Favicon -->
    <link rel="icon" href="../images/favicon.ico" type="../images/x-icon">
    
    <!-- Fonts -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.css" rel="stylesheet">
    
    <!-- TinyMCE -->
    <script src="js/tinymce/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: 'textarea',
            plugins: 'link image lists code table',
            toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image table | code',
            height: 300,
            content_style: 'body { font-family: "Inter", sans-serif; font-size:14px }'
        });
    </script>
    
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #2e59d9;
            --text-dark: #5a5c69;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fa;
        }
        
        .edit-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        
        .card-header {
            border-radius: 10px 10px 0 0 !important;
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .form-control, .form-control-file {
            border-radius: 6px;
            border: 1px solid #d1d3e2;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            border: 1px dashed #d1d3e2;
            margin-top: 10px;
            display: <?= $row['image'] ? 'block' : 'none' ?>;
        }
        
        .btn-submit {
            border-radius: 6px;
            padding: 0.5rem 1.5rem;
        }
        
        .custom-file-label::after {
            border-radius: 0 5px 5px 0;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <!-- Main Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Modifier l'Actualité</h1>
                        <a href="actualite.php" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour
                        </a>
                    </div>
                    
                    <!-- Content Row -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Modifier les détails de l'actualité</h6>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($_SESSION['error'])): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?= htmlspecialchars($_SESSION['error']) ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <?php unset($_SESSION['error']); ?>
                                    <?php endif; ?>
                                    
                                    <form action="edit_actualite.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="titre" class="font-weight-bold">Titre</label>
                                            <input type="text" class="form-control" id="titre" name="titre" value="<?= htmlspecialchars($row['titre']) ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="resume" class="font-weight-bold">Résumé</label>
                                            <textarea class="form-control" id="resume" name="resume" rows="3"><?= ($row['resume']) ?></textarea>
                                        </div>

                                       <div class="form-group">
                                            <label for="contenu" class="font-weight-bold">contenu</label>
                                            <textarea class="form-control" id="contenu" name="contenu" rows="3"><?= ($row['contenu']) ?></textarea>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="lien" class="font-weight-bold">Lien</label>
                                            <input type="url" class="form-control" id="lien" name="lien" value="<?= htmlspecialchars($row['lien']) ?>" placeholder="https://exemple.com">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="image" class="font-weight-bold">Image</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="image" name="image" onchange="previewImage(this)">
                                                <label class="custom-file-label" for="image"><?= $row['image'] ? basename($row['image']) : 'Choisir un fichier' ?></label>
                                            </div>
                                            <small class="form-text text-muted">Formats acceptés : JPG, PNG (max 2MB)</small>
                                            
                                            <?php if ($row['image']): ?>
                                                <div class="mt-3">
                                                    <p class="font-weight-bold">Image actuelle :</p>
                                                    <img src="<?= htmlspecialchars($row['image']) ?>" alt="Image actuelle" class="image-preview" id="imagePreview">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between mt-4">
                                            <a href="actualite.php" class="btn btn-secondary">
                                                <i class="fas fa-times mr-2"></i> Annuler
                                            </a>
                                            <button type="submit" class="btn btn-primary btn-submit">
                                                <i class="fas fa-save mr-2"></i> Enregistrer
                                            </button>
                                        </div>
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
        
        // Prévisualisation de l'image
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
    </script>
</body>
</html>