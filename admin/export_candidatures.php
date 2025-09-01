<?php
include('includes/db.php');

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Augmenter les limites PHP pour gérer des fichiers volumineux
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);

// Désactiver les messages d'erreur pour éviter qu'ils ne corrompent le ZIP
error_reporting(0);
ini_set('display_errors', 0);

// Nettoyer le tampon de sortie pour éviter toute interférence
ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_candidatures'])) {
    $selected_ids = array_map('intval', $_POST['selected_candidatures']);
    if (!empty($selected_ids)) {
        // Récupérer les candidatures sélectionnées
        $ids = implode(',', $selected_ids);
        $query = "SELECT * FROM candidatures WHERE id IN ($ids)";
        $result = mysqli_query($conn, $query);

        // Créer un fichier temporaire pour le CSV
        $csv_file = tempnam(sys_get_temp_dir(), 'candidatures_export_') . '.csv';
        $output = fopen($csv_file, 'w');
        if (!$output) {
            die("Erreur lors de la création du fichier CSV temporaire");
        }

        // Ajouter l'en-tête BOM pour UTF-8 (important pour Excel)
        fwrite($output, "\xEF\xBB\xBF");

        // Définir les en-têtes du CSV
        fputcsv($output, [
            'ID', 'Prénom', 'Nom', 'Email', 'Téléphone', 'Poste', 'Date de postulation', 
            'CV', 'Lettre', 'Diplômes', 'Certification', 'Autres documents'
        ], ';');

        // Créer un fichier temporaire pour le ZIP
        $zip_file = tempnam(sys_get_temp_dir(), 'candidatures_export_') . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            fclose($output);
            unlink($csv_file);
            die("Erreur lors de la création du fichier ZIP");
        }

        // Ajouter les données des candidatures au CSV et les fichiers au ZIP
        while ($row = mysqli_fetch_assoc($result)) {
            // Ajouter les données au CSV
            fputcsv($output, [
                $row['id'],
                $row['prenom'],
                $row['nom'],
                $row['email'],
                $row['telephone'],
                $row['poste'],
                $row['date_postulation'],
                basename($row['cv'] ?? ''),
                basename($row['lettre'] ?? ''),
                basename($row['diplomes'] ?? ''),
                basename($row['certification'] ?? ''),
                $row['autres_documents'] ?? ''
            ], ';');

            // Ajouter les fichiers au ZIP
            $candidate_folder = "Candidature_" . $row['id'] . "_" . str_replace(' ', '_', $row['prenom']) . "_" . str_replace(' ', '_', $row['nom']);
            
            // Ajouter le CV
            if (!empty($row['cv']) && file_exists("../back/documents_recrutement" . $row['cv'])) {
                $zip->addFile("../back/documents_recrutement" . $row['cv'], "$candidate_folder/" . basename($row['cv']));
            }
            
            // Ajouter la lettre
            if (!empty($row['lettre']) && file_exists("../back/documents_recrutement" . $row['lettre'])) {
                $zip->addFile("../back/documents_recrutement" . $row['lettre'], "$candidate_folder/" . basename($row['lettre']));
            }
            
            // Ajouter les diplômes
            if (!empty($row['diplomes']) && file_exists("../back/documents_recrutement" . $row['diplomes'])) {
                $zip->addFile("../back/documents_recrutement" . $row['diplomes'], "$candidate_folder/" . basename($row['diplomes']));
            }
            
            // Ajouter les certifications
            if (!empty($row['certification']) && file_exists("../back/documents_recrutement" . $row['certification'])) {
                $zip->addFile("../backdocuments_recrutement" . $row['certification'], "$candidate_folder/" . basename($row['certification']));
            }
            
            // Ajouter les autres documents
            $autres_docs = json_decode($row['autres_documents'] ?? '[]', true);
            if (!empty($autres_docs) && is_array($autres_docs)) {
                foreach ($autres_docs as $index => $doc_path) {
                    if (file_exists("../back/documents_recrutement" . $doc_path)) {
                        $zip->addFile("../back/documents_recrutement" . $doc_path, "$candidate_folder/Autre_doc_" . ($index + 1) . "_" . basename($doc_path));
                    }
                }
            }
        }

        // Fermer le fichier CSV
        fclose($output);

        // Ajouter le CSV au ZIP
        $zip->addFile($csv_file, "candidatures_exportees.csv");

        // Fermer le ZIP
        $zip->close();

        // Vérifier si le fichier ZIP existe et est lisible
        if (!file_exists($zip_file) || filesize($zip_file) == 0) {
            unlink($csv_file);
            die("Le fichier ZIP est vide ou n'a pas pu être créé");
        }

        // Nettoyer le tampon de sortie avant l'envoi
        ob_clean();
        ob_end_flush();

        // Envoyer le fichier ZIP au navigateur
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="candidatures_exportees.zip"');
        header('Content-Length: ' . filesize($zip_file));
        header('Cache-Control: no-cache');
        readfile($zip_file);

        // Supprimer les fichiers temporaires
        unlink($csv_file);
        unlink($zip_file);
        exit();
    }
}

// Rediriger si aucune candidature n'est sélectionnée
header('Location: candidature_admin.php');
exit();
?>