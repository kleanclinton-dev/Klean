<?php
// db.php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "3h"; // Centralized Database

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("SYSTEM ERROR: NEURAL CONNECTION FAILED - " . $conn->connect_error);
}
?>