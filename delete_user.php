<?php
// delete_user.php
session_start();

// Ensure user is logged in as admin
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    die("Access Denied: You must be an administrator.");
}

$host = "localhost";
$user = "root";
$password = "";
$dbname = "3h";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $delete_id = intval($_GET['id']);
    $current_admin_id = $_SESSION['user_id'];

    // Prevent admin from deleting their own currently logged-in account
    if ($delete_id === $current_admin_id) {
        echo "<script>
                alert('Action denied: You cannot delete your own active admin account!');
                window.location.href = 'admin.php';
              </script>";
        exit();
    }

    // Delete the target user (admin or regular user)
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $stmt->close();
} else {
    header("Location: admin.php");
    exit();
}

$conn->close();
?>