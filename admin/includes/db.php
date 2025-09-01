<!-- db.php -->
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agriforlanddb"; 
$port = 3308; 

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}
?> 

