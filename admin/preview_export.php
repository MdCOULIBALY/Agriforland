
<?php
session_start();
include('includes/db.php');

// Activer le journal des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_log("Démarrage de preview_export.php", 3, "debug.log");

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['admin'])) {
    error_log("Accès non autorisé : non admin", 3, "debug.log");
    header("Location: login.php");
    exit();
}

// Vérifier l'encodage UTF-8
if (!mysqli_set_charset($conn, "utf8mb4")) {
    error_log("Erreur encodage UTF-8 : " . mysqli_error($conn), 3, "debug.log");
    die("Erreur lors de la définition de l'encodage UTF-8 : " . mysqli_error($conn));
}

// Vérifier si des candidatures sont sélectionnées
$ids = isset($_GET['selected_candidatures']) ? array_map('intval', (array)$_GET['selected_candidatures']) : [];
error_log("IDs reçus : " . print_r($ids, true), 3, "debug.log");
if (empty($ids)) {
    error_log("Aucune candidature sélectionnée", 3, "debug.log");
    header("Location: candidature_admin.php?error=" . urlencode("Aucune candidature sélectionnée."));
    exit();
}

$ids_list = implode(',', $ids);

// Récupérer les candidatures
$query = "SELECT id, nom, prenom, email, telephone, poste, date_postulation, consultée, cv, lettre, diplomes, certification, autres_documents 
          FROM candidatures 
          WHERE id IN ($ids_list)";
$result = mysqli_query($conn, $query);

if (!$result) {
    error_log("Erreur SQL : " . mysqli_error($conn), 3, "debug.log");
    header("Location: candidature_admin.php?error=" . urlencode("Erreur lors de la récupération des candidatures : " . mysqli_error($conn)));
    exit();
}
error_log("Nombre de lignes : " . mysqli_num_rows($result), 3, "debug.log");

// Variable pour le message de succès ou d'erreur
$success_message = '';
$error_message = '';

// Générer le ZIP (contenant CSV + PDF) si demandé
if (isset($_GET['download']) && $_GET['download'] === 'export') {
    // Vérifier l'extension ZIP
    if (!extension_loaded('zip')) {
        error_log("Extension ZIP non activée", 3, "debug.log");
        $error_message = "Erreur : Extension ZIP non activée dans PHP.";
    } else {
        // Marquer les candidatures comme lues
        $update_query = "UPDATE candidatures SET consultée = 1 WHERE id IN ($ids_list)";
        if (mysqli_query($conn, $update_query)) {
            error_log("Candidatures marquées comme lues : $ids_list", 3, "debug.log");
        } else {
            error_log("Erreur lors du marquage des candidatures comme lues : " . mysqli_error($conn), 3, "debug.log");
            $error_message = "Erreur : Impossible de marquer les candidatures comme lues.";
        }

        if (empty($error_message)) {
            // Noms des fichiers
            $timestamp = date('Ymd_His');
            $csv_filename = "candidatures_export_{$timestamp}.csv";
            $zip_name = "candidatures_export_{$timestamp}.zip";
            $temp_csv = sys_get_temp_dir() . "/temp_{$csv_filename}";

            error_log("Création CSV temporaire : $temp_csv", 3, "debug.log");

            // Générer le CSV temporaire
            $csv_handle = fopen($temp_csv, 'w');
            if ($csv_handle === false) {
                error_log("Erreur lors de la création du CSV temporaire : $temp_csv", 3, "debug.log");
                $error_message = "Erreur : Impossible de créer le fichier CSV temporaire.";
            } else {
                fwrite($csv_handle, "\xEF\xBB\xBF"); // BOM pour UTF-8
                $headers = [
                    'Nom',
                    'Prénom',
                    'Email',
                    'Téléphone',
                    'Poste',
                    'Date de postulation',
                    'CV',
                    'Lettre',
                    'Diplôme',
                    'Certification',
                    'Autres documents'
                ];
                fputcsv($csv_handle, $headers, ';');

                $added_files = 0;
                $pdf_files = [];
                $missing_files = [];
                mysqli_data_seek($result, 0);
                while ($row = mysqli_fetch_assoc($result)) {
                    $autres_docs = json_decode($row['autres_documents'] ?? '[]', true);
                    $autres_docs_str = !empty($autres_docs) ? implode(' | ', array_map('basename', $autres_docs)) : '';
                    $data = [
                        $row['nom'],
                        $row['prenom'],
                        $row['email'],
                        '="' . $row['telephone'] . '"',
                        $row['poste'],
                        date('d/m/Y H:i:s', strtotime($row['date_postulation'])),
                        basename($row['cv'] ?? ''),
                        basename($row['lettre'] ?? ''),
                        $row['diplomes'] ? basename($row['diplomes']) : '',
                        $row['certification'] ? basename($row['certification']) : '',
                        $autres_docs_str
                    ];
                    fputcsv($csv_handle, $data, ';');

                    // Collecter les fichiers PDF
                    $safe_nom = preg_replace('/[^A-Za-z0-9_-]/', '_', $row['nom']);
                    $safe_prenom = preg_replace('/[^A-Za-z0-9_-]/', '_', $row['prenom']);
                    $folder = "{$safe_nom}_{$safe_prenom}_{$row['id']}/";
                    $files = array_filter([
                        $row['cv'] => 'CV.pdf',
                        $row['lettre'] => 'Lettre.pdf',
                        $row['diplomes'] => 'Diplome.pdf',
                        $row['certification'] => 'Certification.pdf'
                    ]);
                    foreach ($autres_docs as $index => $doc) {
                        $files[$doc] = "Autre_document_" . ($index + 1) . ".pdf";
                    }
                    foreach ($files as $file_path => $file_name) {
                        if (empty($file_path)) {
                            error_log("Chemin vide pour $file_name (ID {$row['id']})", 3, "debug.log");
                            continue;
                        }
                        $full_path = "../back/{$file_path}";
                        if (file_exists($full_path) && is_readable($full_path)) {
                            $pdf_files[] = ['path' => $full_path, 'zip_path' => $folder . $file_name];
                            $added_files++;
                            error_log("PDF collecté : $full_path pour $folder$file_name (ID {$row['id']})", 3, "debug.log");
                        } else {
                            $missing_files[] = $full_path;
                            error_log("Fichier introuvable ou non lisible : $full_path (ID {$row['id']})", 3, "debug.log");
                        }
                    }
                }
                fclose($csv_handle);
                error_log("CSV temporaire généré : $temp_csv", 3, "debug.log");

                // Vérifier si des fichiers PDF sont absents
                if ($added_files === 0) {
                    unlink($temp_csv);
                    error_log("Aucun fichier PDF trouvé pour les candidatures", 3, "debug.log");
                    $error_message = "Erreur : Aucun fichier PDF trouvé pour les candidatures sélectionnées. Fichiers manquants : <br>" . implode("<br>", array_map('htmlspecialchars', $missing_files));
                } else {
                    // Créer le ZIP
                    $zip = new ZipArchive();
                    if ($zip->open($zip_name, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                        unlink($temp_csv);
                        error_log("Erreur lors de la création du ZIP : $zip_name", 3, "debug.log");
                        $error_message = "Erreur : Impossible de créer le fichier ZIP.";
                    } else {
                        // Ajouter le CSV
                        if (file_exists($temp_csv)) {
                            $zip->addFile($temp_csv, $csv_filename);
                            error_log("Ajouté au ZIP : $csv_filename", 3, "debug.log");
                        } else {
                            error_log("CSV temporaire introuvable : $temp_csv", 3, "debug.log");
                        }

                        // Ajouter les PDF
                        foreach ($pdf_files as $file) {
                            $zip->addFile($file['path'], $file['zip_path']);
                            error_log("Ajouté au ZIP : {$file['path']} comme {$file['zip_path']}", 3, "debug.log");
                        }

                        if ($zip->close() === false) {
                            unlink($temp_csv);
                            error_log("Erreur lors de la fermeture du ZIP : $zip_name", 3, "debug.log");
                            $error_message = "Erreur : Impossible de finaliser le fichier ZIP.";
                        } else {
                            // Nettoyer le buffer avant l'envoi
                            if (ob_get_level()) {
                                ob_end_clean();
                            }

                            // Envoyer le ZIP
                            header('Content-Type: application/zip');
                            header('Content-Disposition: attachment; filename="' . $zip_name . '"');
                            header('Content-Length: ' . filesize($zip_name));
                            header('Cache-Control: no-cache, no-store, must-revalidate');
                            header('Pragma: no-cache');
                            header('Expires: 0');
                            readfile($zip_name);
                            unlink($zip_name);
                            error_log("ZIP envoyé : $zip_name", 3, "debug.log");

                            // Définir le message de succès
                            $success_message = "Exportation réussie ! Le fichier ZIP a été téléchargé.";
                        }
                    }
                    unlink($temp_csv); // Supprimer le CSV temporaire
                }
            }
        }
    }
}

// Revenir au début du résultat pour l'affichage
mysqli_data_seek($result, 0);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AGRIFORLAND - Prévisualisation de l'exportation</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fa;
        }
        .table-container {
            max-height: 500px;
            overflow-y: auto;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
            font-size: 0.9rem;
        }
        .table th {
            background-color: #4e73df;
            color: white;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .table tr:hover {
            background-color: #f8f9fc;
        }
        .badge-consultee {
            font-size: 0.8rem;
            padding: 0.4em 0.8em;
        }
        .file-link {
            color: #1cc88a;
            text-decoration: underline;
        }
        .file-link:hover {
            color: #13855c;
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2e59d9;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
        }
        .pdf-preview-btn {
            cursor: pointer;
            color: #007bff;
            text-decoration: underline;
        }
        .pdf-preview-btn:hover {
            color: #0056b3;
        }
        #pdfModal .modal-body {
            height: 80vh;
            overflow: hidden;
        }
        #pdfModal iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <h1 class="h3 mb-4 text-gray-800">Prévisualisation des candidatures à exporter</h1>
        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars(urldecode($_GET['error']), ENT_QUOTES, 'UTF-8') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
        <?php endif; ?>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Candidatures sélectionnées (<?= mysqli_num_rows($result) ?>)</h6>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Poste</th>
                                    <th>Date de postulation</th>
                                    <th>Consultée</th>
                                    <th>CV</th>
                                    <th>Lettre</th>
                                    <th>Diplôme</th>
                                    <th>Certification</th>
                                    <th>Autres documents</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <?php
                                        $autres_docs = json_decode($row['autres_documents'] ?? '[]', true);
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['nom']) ?></td>
                                        <td><?= htmlspecialchars($row['prenom']) ?></td>
                                        <td><?= htmlspecialchars($row['email']) ?></td>
                                        <td><?= htmlspecialchars($row['telephone']) ?></td>
                                        <td><?= htmlspecialchars($row['poste']) ?></td>
                                        <td><?= date('d/m/Y H:i:s', strtotime($row['date_postulation'])) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $row['consultée'] ? 'success' : 'secondary' ?> badge-consultee">
                                                <?= $row['consultée'] ? 'Oui' : 'Non' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($row['cv']): ?>
                                                <a href="download_file.php?file=<?= urlencode($row['cv']) ?>" target="_blank" class="file-link">Télécharger</a>
                                                <span class="pdf-preview-btn" data-file="download_file.php?file=<?= urlencode($row['cv']) ?>"> | Voir</span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['lettre']): ?>
                                                <a href="download_file.php?file=<?= urlencode($row['lettre']) ?>" target="_blank" class="file-link">Télécharger</a>
                                                <span class="pdf-preview-btn" data-file="download_file.php?file=<?= urlencode($row['lettre']) ?>"> | Voir</span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['diplomes']): ?>
                                                <a href="download_file.php?file=<?= urlencode($row['diplomes']) ?>" target="_blank" class="file-link">Télécharger</a>
                                                <span class="pdf-preview-btn" data-file="download_file.php?file=<?= urlencode($row['diplomes']) ?>"> | Voir</span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['certification']): ?>
                                                <a href="download_file.php?file=<?= urlencode($row['certification']) ?>" target="_blank" class="file-link">Télécharger</a>
                                                <span class="pdf-preview-btn" data-file="download_file.php?file=<?= urlencode($row['certification']) ?>"> | Voir</span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (!empty($autres_docs)) {
                                                foreach ($autres_docs as $index => $doc) {
                                                    echo '<a href="download_file.php?file=' . urlencode($doc) . '" target="_blank" class="file-link">Télécharger Doc ' . ($index + 1) . '</a>' .
                                                         '<span class="pdf-preview-btn" data-file="download_file.php?file=' . urlencode($doc) . '"> | Voir</span><br>';
                                                }
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between mb-4">
                <a href="candidature_admin.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <a href="preview_export.php?download=export&<?= http_build_query(['selected_candidatures' => $ids]) ?>" class="btn btn-primary" id="exportBtn">
                    <i class="fas fa-download"></i> Exporter
                </a>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">Aucune candidature trouvée pour l'exportation.</div>
            <a href="candidature_admin.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        <?php endif; ?>
    </div>

    <!-- Modal pour aperçu PDF -->
    <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">Aperçu du fichier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfFrame" src=""></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Gestion de l'aperçu PDF
            $('.pdf-preview-btn').on('click', function() {
                const fileUrl = $(this).data('file');
                $('#pdfFrame').attr('src', fileUrl);
                $('#pdfModal').modal('show');
            });

            // Réinitialiser l'iframe à la fermeture du modal
            $('#pdfModal').on('hidden.bs.modal', function() {
                $('#pdfFrame').attr('src', '');
            });

            // Désactiver le bouton Exporter après clic pour éviter les doubles appels
            $('#exportBtn').on('click', function(e) {
                $(this).prop('disabled', true).text('Exportation en cours...');
            });
        });
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>
