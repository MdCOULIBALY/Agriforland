<?php
include('includes/db.php');
session_start();



// Traitement de l’export CSV
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['selected_ids'])) {
        // En-têtes pour forcer le téléchargement d’un fichier CSV bien interprété par Excel
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="candidatures_non_lues.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF"); // Ajout du BOM UTF-8 pour Excel

        // En-tête des colonnes avec point-virgule comme séparateur (important pour Excel FR)
        fputcsv($output, [
            'Nom complet',
            'Email',
            'Téléphone',
            'CV',
            'Lettre de motivation',
            'Date de soumission',
            'Poste visé'
        ], ';');

        foreach ($_POST['selected_ids'] as $id) {
            $id = intval($id);
            $query = "SELECT * FROM candidatures WHERE id = $id AND consultée = 0";
            $result = mysqli_query($conn, $query);

            if ($row = mysqli_fetch_assoc($result)) {
                fputcsv($output, [
                    $row['prenom'] . ' ' . $row['nom'],
                    $row['email'],
                    $row['telephone'],
                    '../back/' . $row['cv'],
                    '../back/' . $row['lettre'],
                    date('d/m/Y H:i', strtotime($row['date_postulation'])),
                    $row['poste']
                ], ';');
            }
        }

        fclose($output);
        exit;
    } else {
        echo "Aucune candidature sélectionnée.";
        exit;
    }
}

// Récupération des candidatures non lues
$query = "SELECT * FROM candidatures WHERE consultée = 0 ORDER BY date_postulation DESC";
$result = mysqli_query($conn, $query);

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Exporter les candidatures non lues</title>
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h2 class="mb-4">Sélectionnez les candidatures non lues à exporter</h2>

    <form method="POST" action="export_selection.php">
        <div class="mb-3">
            <button type="submit" class="btn btn-success">Exporter la sélection en CSV</button>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>CV</th>
                    <th>Lettre</th>
                    <th>Date</th>
                    <th>Poste</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><input type="checkbox" name="selected_ids[]" value="<?= $row['id'] ?>"></td>
                        <td><?= htmlspecialchars($row['prenom'] . ' ' . $row['nom']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['telephone']) ?></td>
                        <td>
                            <?php if (!empty($row['cv'])): ?>
                                <a href="<?= '../back/' . htmlspecialchars($row['cv']) ?>" target="_blank">Voir</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($row['lettre'])): ?>
                                <a href="<?= '../back/' . htmlspecialchars($row['lettre']) ?>" target="_blank">Voir</a>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($row['date_postulation'])) ?></td>
                        <td><?= htmlspecialchars($row['poste']) ?></td>
                    </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($result) === 0): ?>
                    <tr><td colspan="8">Aucune candidature non lue.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Exporter la sélection en CSV</button>
        </div>
    </form>

    <script>
        // Sélection/Désélection de toutes les cases
        document.getElementById('select-all').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name="selected_ids[]"]');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
</body>
</html>
