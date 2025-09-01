<!-- delete_actuale.php -->

<?php
include('includes/db.php');
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

$sql = "DELETE FROM a_la_une WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    header('Location: actualite.php');
    exit();
} else {
    echo "Erreur de suppression: " . $conn->error;
}
?>
