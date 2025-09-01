<?php
session_start();
include('../admin/includes/db.php'); // Connexion à la base de données

// Activer le mode débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Vérifiez si un ID est passé en paramètre
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Identifiant de l'article invalide.";
    header("Location: blog.php");
    exit();
}

$article_id = intval($_GET['id']);

// Récupérer les informations de l'article
$sql = "SELECT * FROM articles WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $article_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Article introuvable.";
    header("Location: blog.php");
    exit();
}

$article = $result->fetch_assoc();
$stmt->close();

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $resume = trim($_POST['resume']);
    $contenu = trim($_POST['contenu']);
    $image_name = $article['image']; // Image existante

    // Vérification et validation des champs
    if (empty($title) || empty($resume) || empty($contenu)) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
    } else {
        // Gestion de l'image si elle est mise à jour
        if (!empty($_FILES['image']['name'])) {
            $target_dir = "img_blog/";
            // Vérifier si le dossier existe, sinon le créer
            if (!is_dir($target_dir)) {
                if (!mkdir($target_dir, 0755, true)) {
                    $_SESSION['error'] = "Impossible de créer le dossier de téléchargement.";
                    header("Location: edit_article.php?id=" . $article_id);
                    exit();
                }
            }

            // Vérifier si le dossier est accessible en écriture
            if (!is_writable($target_dir)) {
                $_SESSION['error'] = "Le dossier de téléchargement n'est pas accessible en écriture.";
                header("Location: edit_article.php?id=" . $article_id);
                exit();
            }

            $image_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $target_dir . $image_name;

            // Vérifiez si l'image est valide
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            $file_size = $_FILES['image']['size'];

            if (!in_array($imageFileType, $allowed_types)) {
                $_SESSION['error'] = "Format d'image non valide (seules les extensions JPG, JPEG, PNG, GIF sont autorisées).";
                header("Location: edit_article.php?id=" . $article_id);
                exit();
            }

            if ($file_size > $max_size) {
                $_SESSION['error'] = "L'image ne doit pas dépasser 5 Mo.";
                header("Location: edit_article.php?id=" . $article_id);
                exit();
            }

            // Déplacer la nouvelle image
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Supprimer l'ancienne image si elle existe
                if (!empty($article['image']) && file_exists($article['image'])) {
                    if (!unlink($article['image'])) {
                        $_SESSION['error'] = "Erreur lors de la suppression de l'ancienne image.";
                        header("Location: edit_article.php?id=" . $article_id);
                        exit();
                    }
                }
                $image_name = $target_file;
            } else {
                $_SESSION['error'] = "Erreur lors du téléchargement de la nouvelle image : " . $_FILES['image']['error'];
                header("Location: edit_article.php?id=" . $article_id);
                exit();
            }
        }

        // Mise à jour de l'article
        $sql = "UPDATE articles SET title = ?, resume = ?, contenu = ?, image = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $title, $resume, $contenu, $image_name, $article_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Article modifié avec succès.";
            header("Location: blog.php");
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de la modification de l'article : " . $stmt->error;
        }

        $stmt->close();
    }

    if (isset($_SESSION['error'])) {
        header("Location: edit_article.php?id=" . $article_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Modifier un article">
    <meta name="author" content="">
    <title>Modifier un Article</title>
    
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
                    height: 100,
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
                    height: 300,
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
            display: <?php echo $article['image'] ? 'block' : 'none'; ?>;
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
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Main Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Modifier l'Article</h1>
                        <a href="blog.php" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour
                        </a>
                    </div>
                    
                    <!-- Content Row -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Modifier les détails de l'article</h6>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($_SESSION['error'])): ?>
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <?php echo htmlspecialchars($_SESSION['error']); ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                        <?php unset($_SESSION['error']); ?>
                                    <?php endif; ?>
                                    <?php if (isset($_SESSION['success'])): ?>
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <?php echo htmlspecialchars($_SESSION['success']); ?>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                        <?php unset($_SESSION['success']); ?>
                                    <?php endif; ?>
                                    
                                    <form action="edit_article.php?id=<?php echo $article_id; ?>" method="POST" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="title" class="font-weight-bold">Titre</label>
                                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($article['title']); ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="resume" class="font-weight-bold">Résumé</label>
                                            <textarea class="form-control" id="resume" name="resume" rows="5" required><?php echo htmlspecialchars($article['resume']); ?></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="contenu" class="font-weight-bold">Contenu</label>
                                            <textarea class="form-control" id="contenu" name="contenu" required><?php echo htmlspecialchars($article['contenu']); ?></textarea>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="image" class="font-weight-bold">Image</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="image" name="image" onchange="previewImage(this)">
                                                <label class="custom-file-label" for="image"><?php echo $article['image'] ? basename($article['image']) : 'Choisir un fichier'; ?></label>
                                            </div>
                                            <small class="form-text text-muted">Formats acceptés : JPG, PNG, GIF (max 5MB)</small>
                                            
                                            <?php if ($article['image']): ?>
                                                <div class="mt-3">
                                                    <p class="font-weight-bold">Image actuelle :</p>
                                                    <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="Image actuelle" class="image-preview" id="imagePreview">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between mt-4">
                                            <a href="blog.php" class="btn btn-secondary">
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
                        <span>All Rights Reserved © Design by Mohamed Coulibaly <?php echo date('Y'); ?></span>
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