<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>User Dashboard</title></head>
<body>
    <h2>User Dashboard</h2>
    <p>Welcome, <?php echo $_SESSION['username']; ?>!</p>
    <p>You are logged in as a regular user.</p>
    <a href="profile.php">My Profile (Proff)</a> | <a href="logout.php">Log Out</a>
</body>
</html>