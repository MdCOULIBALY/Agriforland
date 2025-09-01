<?php
session_start();
include('includes/db.php');

// Activer le journal des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_log("Démarrage de preview_export_consultants.php", 3, "debug.log");

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['admin'])) {
    error_log("Accès non autorisé : non admin", 3, "debug.log");
    header("Location: login.php");
    exit();
}

// Vérifier l'encodage UTF-8 pour la base de données
if (!mysqli_set_charset($conn, "utf8mb4")) {
    error_log("Erreur encodage UTF-8 : " . mysqli_error($conn), 3, "debug.log");
    die("Erreur lors de la définition de l'encodage UTF-8 : " . mysqli_error($conn));
}

// Vérifier si des consultants sont sélectionnés
$ids = isset($_GET['selected_consultants']) ? array_map('intval', (array)$_GET['selected_consultants']) : [];
error_log("IDs reçus : " . print_r($ids, true), 3, "debug.log");
if (empty($ids)) {
    error_log("Aucun consultant sélectionné", 3, "debug.log");
    header("Location: consultant_admin.php?error=" . urlencode("Aucun consultant sélectionné."));
    exit();
}

$ids_list = implode(',', $ids);

// Récupérer tous les champs des consultants
$query = "SELECT id, name, specialty, specialty_other, degree, degree_institution, experience, contract_type, availability, languages, phone, email, created_at, consultée, cv_path, diploma_path, give_trainings, training_modules 
          FROM consultants 
          WHERE id IN ($ids_list)";
$result = mysqli_query($conn, $query);

if (!$result) {
    error_log("Erreur SQL : " . mysqli_error($conn), 3, "debug.log");
    header("Location: consultant_admin.php?error=" . urlencode("Erreur lors de la récupération des consultants : " . mysqli_error($conn)));
    exit();
}
error_log("Nombre de lignes : " . mysqli_num_rows($result), 3, "debug.log");

// Variable pour le message de succès ou d'erreur
$success_message = '';
$error_message = '';

// Générer le ZIP (contenant CSV + fichiers uploadés) si demandé
if (isset($_GET['download']) && $_GET['download'] === 'export') {
    // Vérifier l'extension ZIP
    if (!extension_loaded('zip')) {
        error_log("Extension ZIP non activée", 3, "debug.log");
        $error_message = "Erreur : Extension ZIP non activée dans PHP.";
    } else {
        // Marquer les consultants comme lus
        $update_query = "UPDATE consultants SET consultée = 1 WHERE id IN ($ids_list)";
        if (mysqli_query($conn, $update_query)) {
            error_log("Consultants marqués comme lus : $ids_list", 3, "debug.log");
        } else {
            error_log("Erreur lors du marquage des consultants comme lus : " . mysqli_error($conn), 3, "debug.log");
            $error_message = "Erreur : Impossible de marquer les consultants comme lus.";
        }

        if (empty($error_message)) {
            // Noms des fichiers
            $timestamp = date('Ymd_His');
            $csv_filename = "consultants_export_{$timestamp}.csv";
            $zip_name = "consultants_export_{$timestamp}.zip";
            $temp_csv = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "temp_{$csv_filename}";

            error_log("Création CSV temporaire : $temp_csv", 3, "debug.log");

            // Générer le CSV temporaire
            $csv_handle = fopen($temp_csv, 'w');
            if ($csv_handle === false) {
                error_log("Erreur création CSV temporaire : $temp_csv", 3, "debug.log");
                $error_message = "Erreur : Impossible de créer le fichier CSV temporaire.";
            } else {
                fwrite($csv_handle, "\xEF\xBB\xBF"); // BOM pour UTF-8
                $headers = [
                    'Nom',
                    'Spécialité',
                    'Diplôme',
                    'Institution',
                    'Expérience',
                    'Type de contrat',
                    'Disponibilité',
                    'Langues',
                    'Téléphone',
                    'Email',
                    'Date d\'inscription',
                    'Formations',
                    'Modules de formation',
                    'CV',
                    'Diplôme'
                ];
                fputcsv($csv_handle, $headers, ';');

                $added_files = 0;
                $pdf_files = [];
                $missing_files = [];
                mysqli_data_seek($result, 0);
                while ($row = mysqli_fetch_assoc($result)) {
                    $specialty = $row['specialty_other'] ?: $row['specialty'];
                    $cv_path = $row['cv_path'] ?: '';
                    $diploma_path = $row['diploma_path'] ?: '';
                    $cv_filename = $cv_path ? basename($cv_path) : '';
                    $diploma_filename = $diploma_path ? basename($diploma_path) : '';
                    $training_modules = $row['give_trainings'] == 'yes' ? ($row['training_modules'] ?: '-') : '-';

                    error_log("ID {$row['id']}: Chemin CV : $cv_path, Nom fichier CV : $cv_filename", 3, "debug.log");
                    error_log("ID {$row['id']}: Chemin Diplôme : $diploma_path, Nom fichier Diplôme : $diploma_filename", 3, "debug.log");

                    $data = array_map(function($value) {
                        return mb_convert_encoding($value ?? '', 'UTF-8', 'UTF-8');
                    }, [
                        $row['name'],
                        $specialty,
                        $row['degree'],
                        $row['degree_institution'],
                        $row['experience'],
                        $row['contract_type'],
                        date('d/m/Y', strtotime($row['availability'] . '')),
                        $row['languages'],
                        '="' . $row['phone'] . '"',
                        $row['email'],
                        date('d/m/Y H:i:s', strtotime($row['created_at'])),
                        $row['give_trainings'] == 'yes' ? 'Oui' : 'Non',
                        $training_modules,
                        $cv_filename,
                        $diploma_filename
                    ]);
                    fputcsv($csv_handle, $data, ';');

                    // Collecter les fichiers
                    $safe_name = preg_replace('/[^A-Za-z0-9_-]/', '-', $row['name']);
                    $folder = "{$safe_name}_{$row['id']}/";
                    $files = array_filter([
                        $cv_path => $cv_filename ? "CV_{$cv_filename}" : '',
                        $diploma_path => $diploma_filename ? "Diplome_{$diploma_filename}" : ''
                    ]);

                    error_log("ID {$row['id']}: Fichiers à collecter : " . print_r($files, true), 3, "debug.log");

                    foreach ($files as $file_path => $file_name) {
                        if (empty($file_path)) {
                            error_log("ID {$row['id']}: Chemin vide pour $file_name", 3, "debug.log");
                            continue;
                        }

                        // Construire le chemin absolu
                        $full_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file_path);
                        error_log("ID {$row['id']}: Chemin complet testé : $full_path, Existe : " . (file_exists($full_path) ? 'oui' : 'non') . ", Lisible : " . (is_readable($full_path) ? 'oui' : 'non'), 3, "debug.log");

                        if (file_exists($full_path) && is_readable($full_path)) {
                            $pdf_files[] = ['path' => $full_path, 'zip_path' => $folder . $file_name];
                            $added_files++;
                            error_log("ID {$row['id']}: Fichier collecté pour ZIP : $full_path comme $folder$file_name", 3, "debug.log");
                        } else {
                            $missing_files[] = $file_path;
                            error_log("ID {$row['id']}: Fichier introuvable ou non lisible : $full_path", 3, "debug.log");
                        }
                    }
                }
                fclose($csv_handle);
                error_log("CSV temporaire généré : $temp_csv, Fichiers collectés : $added_files, Fichiers manquants : " . count($missing_files), 3, "debug.log");

                // Vérifier si des fichiers sont collectés
                if ($added_files === 0 && !empty($missing_files)) {
                    unlink($temp_csv);
                    error_log("Aucun fichier trouvé pour les consultants", 3, "debug.log");
                    $error_message = "Erreur : Aucun fichier trouvé pour les consultants sélectionnés. Fichiers manquants : <br>" . implode("<br>", array_map('htmlspecialchars', $missing_files));
                } else {
                    // Créer le ZIP
                    try {
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

                            // Ajouter les fichiers
                            foreach ($pdf_files as $file) {
                                if (file_exists($file['path'])) {
                                    if ($zip->addFile($file['path'], $file['zip_path'])) {
                                        error_log("Ajouté au ZIP : {$file['path']} comme {$file['zip_path']}", 3, "debug.log");
                                    } else {
                                        error_log("Échec de l'ajout au ZIP : {$file['path']}", 3, "debug.log");
                                        $missing_files[] = $file['path'];
                                    }
                                } else {
                                    error_log("Fichier non ajouté au ZIP, introuvable : {$file['path']}", 3, "debug.log");
                                    $missing_files[] = $file['path'];
                                }
                            }

                            if (!$zip->close()) {
                                unlink($temp_csv);
                                error_log("Erreur lors de la fermeture du ZIP : $zip_name", 3, "debug.log");
                                $error_message = "Erreur : Impossible de finaliser le fichier ZIP.";
                            } else {
                                // Nettoyer le buffer et envoyer le ZIP
                                if (ob_get_level()) ob_end_clean();
                                header('Content-Type: application/zip');
                                header('Content-Disposition: attachment; filename="' . $zip_name . '"');
                                header('Content-Length: ' . filesize($zip_name));
                                header('Cache-Control: no-cache, no-store, must-revalidate');
                                header('Pragma: no-cache');
                                header('Expires: 0');
                                readfile($zip_name);
                                unlink($temp_csv);
                                unlink($zip_name);
                                error_log("ZIP envoyé : $zip_name avec $added_files fichiers (hors CSV)", 3, "debug.log");
                                if (!empty($missing_files)) {
                                    $success_message = "Exportation réussie, mais certains fichiers sont manquants : <br>" . implode("<br>", array_map('htmlspecialchars', $missing_files));
                                } else {
                                    $success_message = "Exportation réussie ! Le fichier ZIP contient le CSV et $added_files documents.";
                                }
                                exit;
                            }
                        }
                    } catch (Exception $e) {
                        unlink($temp_csv);
                        if (file_exists($zip_name)) unlink($zip_name);
                        error_log("Erreur ZIP : " . $e->getMessage(), 3, "debug.log");
                        $error_message = "Erreur : " . $e->getMessage();
                    }
                    unlink($temp_csv);
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
    <title>AGRIFORLAND - Prévisualisation de l'exportation des consultants</title>
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
        .missing-file {
            color: #e74a3b;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <h1 class="h3 mb-4 text-gray-800">Prévisualisation des consultants à exporter</h1>
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
                    <h6 class="m-0 font-weight-bold text-primary">Consultants sélectionnés (<?= mysqli_num_rows($result) ?>)</h6>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Spécialité</th>
                                    <th>Diplôme</th>
                                    <th>Institution</th>
                                    <th>Expérience</th>
                                    <th>Type de contrat</th>
                                    <th>Disponibilité</th>
                                    <th>Langues</th>
                                    <th>Téléphone</th>
                                    <th>Email</th>
                                    <th>Date d'inscription</th>
                                    <th>Formations</th>
                                    <th>Modules de formation</th>
                                    <th>Consultée</th>
                                    <th>CV</th>
                                    <th>Diplôme</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <?php
                                        $specialty = $row['specialty_other'] ?: $row['specialty'];
                                        $cv_path = $row['cv_path'] ? basename($row['cv_path']) : '';
                                        $diploma_path = $row['diploma_path'] ? basename($row['diploma_path']) : '';
                                        $cv_full_path = $row['cv_path'] ? dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $row['cv_path']) : '';
                                        $diploma_full_path = $row['diploma_path'] ? dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $row['diploma_path']) : '';
                                        $training_modules = $row['give_trainings'] == 'yes' ? ($row['training_modules'] ?: '-') : '-';
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($specialty ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['degree'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['degree_institution'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['experience'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['contract_type'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['availability'] . '')) ?></td>
                                        <td><?= htmlspecialchars($row['languages'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['phone'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($row['email'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= date('d/m/Y H:i:s', strtotime($row['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($row['give_trainings'] == 'yes' ? 'Oui' : 'Non', ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= htmlspecialchars($training_modules, ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <span class="badge badge-<?= $row['consultée'] ? 'success' : 'secondary' ?> badge-consultee">
                                                <?= $row['consultée'] ? 'Oui' : 'Non' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($row['cv_path'] && file_exists($cv_full_path) && is_readable($cv_full_path)): ?>
                                                <a href="../<?= htmlspecialchars($row['cv_path'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="file-link" aria-label="Télécharger le CV de <?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?>">Télécharger</a>
                                                <span class="pdf-preview-btn" data-file="../<?= htmlspecialchars($row['cv_path'], ENT_QUOTES, 'UTF-8') ?>" aria-label="Prévisualiser le CV de <?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?>"> | Voir</span>
                                            <?php else: ?>
                                                <span class="missing-file">Non disponible</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['diploma_path'] && file_exists($diploma_full_path) && is_readable($diploma_full_path)): ?>
                                                <a href="../<?= htmlspecialchars($row['diploma_path'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="file-link" aria-label="Télécharger le diplôme de <?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?>">Télécharger</a>
                                                <span class="pdf-preview-btn" data-file="../<?= htmlspecialchars($row['diploma_path'], ENT_QUOTES, 'UTF-8') ?>" aria-label="Prévisualiser le diplôme de <?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?>"> | Voir</span>
                                            <?php else: ?>
                                                <span class="missing-file">Non disponible</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between mb-4">
                <a href="consultant_admin.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <a href="preview_export_consultants.php?download=export&<?= http_build_query(['selected_consultants' => $ids]) ?>" class="btn btn-primary" id="exportBtn">
                    <i class="fas fa-download"></i> Exporter
                </a>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">Aucun consultant trouvé pour l'exportation.</div>
            <a href="consultant_admin.php" class="btn btn-secondary">
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
                $.ajax({
                    url: fileUrl,
                    type: 'HEAD',
                    success: function() {
                        $('#pdfFrame').attr('src', fileUrl);
                        $('#pdfModal').modal('show');
                    },
                    error: function(xhr) {
                        alert('Erreur : Impossible de charger le fichier. Code erreur : ' + xhr.status);
                    }
                });
            });

            // Réinitialiser l'iframe à la fermeture du modal
            $('#pdfModal').on('hidden.bs.modal', function() {
                $('#pdfFrame').attr('src', '');
            });

            // Désactiver le bouton Exporter après clic
            $('#exportBtn').on('click', function() {
                $(this).prop('disabled', true).text('Exportation en cours...');
            });
        });
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>